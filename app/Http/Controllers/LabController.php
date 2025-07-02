<?php

namespace App\Http\Controllers;

use App\Models\Lab;
use App\Models\LabGroup;
use App\Models\Topology;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LabController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $labGroups = LabGroup::all();
        $selectedGroup = $request->get('group');
        $currentFolder = $selectedGroup ? LabGroup::find($selectedGroup) : null;

        return view('lab.index', [
            'title' => 'Labs Collection',
            'labGroups' => $labGroups,
            'currentFolder' => $currentFolder,
        ]);
    }

    public function saveBoth(Request $request, $id)
    {
        $lab = Lab::findOrFail($id);

        // Validasi isi
        $data = $request->validate([
            'nodes' => 'required|array',
            'connections' => 'required|array',
            'power' => 'nullable|numeric',
            'splicing' => 'nullable|integer', // ✅ Tambah
            'connectors' => 'nullable|integer', // ✅ Tambah
            'name' => 'nullable|string',
            'author' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        // ===== ✅ SIMPAN FILE JSON =====
        $jsonContent = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $slug = Str::slug($lab->name);
        $path = $lab->group
            ? 'labs/' . $lab->group->fullSlugPath() . '/' . $slug . '.json'
            : 'labs/' . $slug . '.json';

        Storage::disk('local')->put($path, $jsonContent);

        // ===== ✅ SIMPAN KE DATABASE TOPOLOGI =====
        \App\Models\Topology::updateOrCreate(
            ['lab_id' => $id],
            [
                'nodes' => json_encode($data['nodes']),
                'connections' => json_encode($data['connections']),
                'power' => $data['power'] ?? null,
                'name' => $data['name'] ?? $lab->name,
                'author' => $data['author'] ?? $lab->author,
                'description' => $data['description'] ?? $lab->description,
                'splicing' => $data['splicing'] ?? 0, // ✅ Tambah
                'connectors' => $data['connectors'] ?? 0, // ✅ Tambah
            ]
        );

        return response()->json(['success' => true, 'message' => 'Topology saved to both file and DB.']);
    }


    public function ajaxFolder($id = 0, Request $request)
    {
        $search = $request->query('search');
        $currentFolder = $id ? LabGroup::findOrFail($id) : null;

        // Filter folders
        $folderQuery = LabGroup::where('parent_id', $id ?: null);
        if ($search) {
            $folderQuery->where('name', 'like', "%$search%");
        }
        $subFolders = $folderQuery->get();

        // Filter labs
        $labQuery = Lab::where('lab_group_id', $id ?: null);
        if ($search) {
            $labQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('author', 'like', "%$search%");
            });
        }
        $labs = $labQuery->get();

        // Tandai folder yang missing
        $folders = $subFolders->map(function ($folder) {
            $folder->is_missing = !file_exists(storage_path('app/labs/' . $folder->fullSlugPath()));
            return $folder;
        });

        // Tandai lab yang missing
        $labs = $labs->map(function ($lab) {
            $slug = Str::slug($lab->name);
            $path = $lab->lab_group_id
                ? 'labs/' . $lab->group->fullSlugPath() . '/' . $slug . '.json'
                : 'labs/' . $slug . '.json';

            $lab->is_missing = !Storage::exists($path);
            return $lab;
        });

        return response()->json([
            'folders' => $folders,
            'labs' => $labs,
            'currentFolder' => $currentFolder,
            'breadcrumbs' => $this->getBreadcrumbs($currentFolder),
        ]);
    }

    public static function getBreadcrumbs($folder)
    {
        $breadcrumbs = [];

        while ($folder) {
            // ⛔️ Skip kalau folder gak ada secara fisik
            if (!$folder->existsOnDisk()) break;

            $breadcrumbs[] = ['id' => $folder->id, 'name' => $folder->name];
            $folder = $folder->parent;
        }

        return array_reverse($breadcrumbs);
    }


    public function getJsonPreview($id)
    {
        $lab = Lab::findOrFail($id);
        $path = $lab->lab_group_id
            ? 'labs/' . $lab->group->fullSlugPath() . '/' . Str::slug($lab->name) . '.json'
            : 'labs/' . Str::slug($lab->name) . '.json';

        if (Storage::exists($path)) {
            return response()->json(json_decode(Storage::get($path), true));
        } else {
            return response()->json(['error' => 'File not found.'], 404);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $labGroups = LabGroup::all();
        $parentId = $request->get('parent');
        $parentFolder = $parentId ? LabGroup::find($parentId) : null;

        return view('lab.create', [
            'title' => 'Make Your Own Lab',
            'labGroups' => $labGroups,
            'selectedGroup' => $parentId,
            'breadcrumbs' => $this->getBreadcrumbs($parentFolder),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'nullable|string',
            'lab_group_id' => 'nullable|exists:lab_groups,id',
        ]);

        $labGroup = $request->lab_group_id ? LabGroup::findOrFail($request->lab_group_id) : null;
        $labSlug = Str::slug($request->name);

        // 🔒 Cek lab duplikat dalam folder yang sama
        $existingLab = Lab::where('lab_group_id', $request->lab_group_id)
            ->where('slug', $labSlug)
            ->first();

        if ($existingLab) {
            return back()->withErrors(['name' => 'Lab dengan nama ini sudah ada di folder ini.'])->withInput();
        }

        // Simpan ke DB
        $lab = Lab::create([
            'lab_group_id' => $labGroup?->id,
            'name' => $request->name,
            'slug' => $labSlug, // ✅ SIMPAN SLUG DI SINI
            'author' => $request->author,
            'description' => $request->description,
        ]);

        // Path folder (bisa langsung di 'labs/' atau nested)
        $folderPath = $labGroup ? 'labs/' . $labGroup->fullSlugPath() : 'labs';
        Storage::makeDirectory($folderPath);

        // Buat file JSON
        $jsonPath = $folderPath . '/' . $labSlug . '.json';
        // Buat file JSON
        Storage::put($jsonPath, json_encode([
            'nodes' => [],
            'connections' => [],
            'power' => null,
            'splicing' => 0, // ✅ Tambah
            'connectors' => 0, // ✅ Tambah
            'name' => $request->name,
            'author' => $request->author,
            'description' => $request->description,
        ], JSON_PRETTY_PRINT));

        return redirect()->route('lab.canvas', $lab->id)->with('success', 'Lab successfully created!');
    }




    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $lab = Lab::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $newName = $request->name;
        $newSlug = Str::slug($newName);

        // Ambil slug lama sebelum update
        $oldSlug = $lab->slug;

        // Cek slug bentrok di folder yang sama (kecuali diri sendiri)
        $exists = Lab::where('lab_group_id', $lab->lab_group_id)
            ->where('slug', $newSlug)
            ->where('id', '!=', $lab->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['name' => 'Nama lab ini sudah ada di folder yang sama.'])->withInput();
        }

        // Hitung path lama & baru
        $folderPath = $lab->lab_group_id
            ? 'labs/' . $lab->group->fullSlugPath()
            : 'labs';

        $oldJsonPath = $folderPath . '/' . $oldSlug . '.json';
        $newJsonPath = $folderPath . '/' . $newSlug . '.json';

        // Rename file kalau path lama ada dan nama berubah
        if ($oldSlug !== $newSlug && Storage::exists($oldJsonPath)) {
            Storage::move($oldJsonPath, $newJsonPath);
        }

        // Simpan ke DB
        $lab->update([
            'name' => $newName,
            'slug' => $newSlug,
            'author' => $request->author,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Lab berhasil diubah!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $lab = Lab::findOrFail($id);

        // ✅ Hapus file JSON
        $slugPath = $lab->group?->fullSlugPath(); // null-safe
        $fileName = Str::slug($lab->name) . '.json';
        $filePath = $slugPath ? "labs/{$slugPath}/{$fileName}" : "labs/{$fileName}";

        if (Storage::exists($filePath)) {
            Storage::delete($filePath);
        }

        $lab->delete();

        return redirect()->route('lab')->with('success', 'Lab successfully deleted!');
    }

    public function topologi(Lab $lab)
    {
        $path = $lab->lab_group_id
            ? 'labs/' . $lab->group->fullSlugPath() . '/' . $lab->slug . '.json'
            : 'labs/' . $lab->slug . '.json';

        if (!Storage::exists($path)) {
            return abort(404, 'File not found');
        }

        $json = json_decode(Storage::get($path), true);

        return view('lab.canvas', [
            'lab' => $lab,
            'nodesJson' => json_encode($json['nodes'] ?? []),
            'connectionsJson' => json_encode($json['connections'] ?? []),
            'power' => $json['power'] ?? 0,
        ]);
    }

    public function getJsonExport($id)
    {
        $lab = Lab::findOrFail($id);
        $path = $lab->lab_group_id
            ? 'labs/' . $lab->group->fullSlugPath() . '/' . $lab->slug . '.json'
            : 'labs/' . $lab->slug . '.json';

        if (Storage::exists($path)) {
            return response()->json(json_decode(Storage::get($path), true));
        }

        return response()->json(['error' => 'File not found.'], 404);
    }

    public function importLab(Request $request)
    {
        $jsonString = $request->input('json_raw');
        $folderId = $request->input('lab_group_id');
        $folderId = $folderId ?: null; // Konversi "" ke null biar aman

        $json = json_decode($jsonString, true);

        if (!$json || !isset($json['name']) || !isset($json['author'])) {
            return response()->json(['success' => false, 'message' => 'Format file tidak valid.']);
        }

        $slug = Str::slug($json['name']);

        $exists = Lab::where('slug', $slug)
            ->where(function ($q) use ($folderId) {
                if (is_null($folderId)) {
                    $q->whereNull('lab_group_id');
                } else {
                    $q->where('lab_group_id', $folderId);
                }
            })
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Lab dengan nama ini sudah ada di folder ini.']);
        }

        // Buat lab
        $lab = Lab::create([
            'name' => $json['name'],
            'slug' => $slug,
            'author' => $json['author'],
            'description' => $json['description'] ?? null,
            'lab_group_id' => $folderId,
        ]);

        // Simpan ke tabel topologi
        Topology::create([
            'lab_id' => $lab->id,
            'nodes' => json_encode($json['nodes'] ?? []),
            'connections' => json_encode($json['connections'] ?? []),
            'power' => $json['power'] ?? null,
            'description' => $json['description'] ?? null,
            'splicing' => $json['splicing'] ?? 0, // ✅ Tambah
            'connectors' => $json['connectors'] ?? 0, // ✅ Tambah
            'is_autosaved' => 0,
        ]);

        // Simpan file JSON
        $folderPath = ($lab->lab_group_id && $lab->group)
            ? 'labs/' . $lab->group->fullSlugPath()
            : 'labs';

        Storage::makeDirectory($folderPath);
        $jsonPath = $folderPath . '/' . $slug . '.json';
        Storage::put($jsonPath, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return response()->json(['success' => true, 'lab_id' => $lab->id]);
    }
}
