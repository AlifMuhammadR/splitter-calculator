<?php

namespace App\Http\Controllers;

use App\Models\Topology;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TopologyController extends Controller
{
    // public function save(Request $request)
    // {
    //     // Validasi manual agar bisa tampilkan semua error
    //     $validator = Validator::make($request->all(), [
    //         'nodes' => 'required|array',
    //         'connections' => 'required|array',
    //         'power' => 'required|numeric',
    //     ]);

    //     if ($validator->fails()) {
    //         // Kirim semua error sebagai array untuk ditangani oleh SweetAlert di frontend
    //         return response()->json([
    //             'success' => false,
    //             'errors' => $validator->errors()->all(),
    //         ], 422);
    //     }

    //     try {
    //         Topology::create([
    //             'nodes' => json_encode($request->nodes),
    //             'connections' => json_encode($request->connections),
    //             'power' => $request->power,
    //         ]);

    //         return response()->json(['success' => true, 'message' => 'Topologi berhasil disimpan.']);
    //     } catch (\Throwable $e) {
    //         Log::error("Gagal simpan topologi: " . $e->getMessage());
    //         return response()->json([
    //             'success' => false,
    //             'errors' => ['Terjadi kesalahan server: ' . $e->getMessage()],
    //         ], 500);
    //     }
    // }

    public function save(Request $request, $id)
    {
        $data = $request->validate([
            'nodes' => 'required|array',
            'connections' => 'required|array',
            'power' => 'nullable|numeric',
            'name' => 'nullable|string',
            'author' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        // Simpan ke DB, update jika sudah ada
        $topo = Topology::updateOrCreate(
            ['lab_id' => $id],
            [
                'nodes' => json_encode($data['nodes']),
                'connections' => json_encode($data['connections']),
                'power' => $data['power'] ?? null,
                'name' => $data['name'] ?? null,
                'author' => $data['author'] ?? null,
                'description' => $data['description'] ?? null,
            ]
        );

        return response()->json(['success' => true, 'message' => 'Topology saved successfully.']);
    }

    public function load($lab_id)
    {
        $topology = Topology::where('lab_id', $lab_id)->latest()->first();

        if (!$topology) {
            return response()->json([
                'nodes' => [],
                'connections' => [],
            ]);
        }

        return response()->json([
            'nodes' => json_decode($topology->nodes, true),
            'connections' => json_decode($topology->connections, true),
            'power' => $topology->power,
        ]);
    }
}
