@extends('fe.master')

@section('content')
    <style>
        /* General List Item Styling */
        #lab-list .list-group-item {
            transition: all 0.2s ease-in-out;
            border: none;
            border-radius: 8px;
            margin-bottom: 5px;
            background-color: #fdfdfd;
            padding: 10px 14px;
        }

        .jtk-connector {
    z-index: 1000 !important;
}
.jtk-overlay {
    z-index: 1001 !important;
}

.connection-label {
    font-size: 0.8rem;
    background: white;
    padding: 2px 6px;
    border-radius: 4px;
    border: 1px solid #ccc;
    color: red;
}


        #lab-list .list-group-item:hover {
            background-color: #f1f3f5;
            box-shadow: 0 0.3rem 0.5rem rgba(0, 0, 0, 0.03);
        }

        /* Item Actions (hidden by default, visible on hover) */
        #lab-list .item-actions {
            opacity: 0;
            transition: opacity 0.2s ease-in-out;
        }

        #lab-list .list-group-item:hover .item-actions {
            opacity: 1;
        }

        /* Icons in list items */
        #lab-list .list-group-item i {
            font-size: 1.2rem;
        }

        /* Item Name and Badge Styling */
        .item-name {
            font-weight: 500;
            font-size: 0.95rem;
        }

        .badge {
            font-size: 0.75rem;
            vertical-align: middle;
        }

        /* Card Styling */
        .card {
            border-radius: 12px;
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
        }

        .card-header {
            border-bottom: none;
            border-radius: 12px 12px 0 0;
        }

        .card-body {
            border-radius: 0 0 12px 12px;
        }

        /* Form Control Styling */
        input.form-control {
            border-radius: 20px;
            border: 1px solid #dee2e6;
            padding-left: 14px;
            font-size: 0.9rem;
        }

        /* Button and Dropdown Styling */
        .btn-outline-primary,
        .btn-outline-secondary,
        .dropdown-toggle {
            border-radius: 30px !important;
            /* !important to override other styles */
        }

        .btn-sm {
            padding: 4px 12px;
            font-size: 0.85rem;
        }

        /* Breadcrumb Path Styling */
        #breadcrumb-path a {
            text-decoration: none;
            color: #0d6efd;
            font-weight: 500;
        }

        #breadcrumb-path a:hover {
            text-decoration: underline;
        }

        .node-preview {
            transition: all 0.2s ease-in-out;
        }

        .node-preview:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
    </style>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-5">
                <div class="card shadow" style="border-radius: 16px;">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center"
                        style="border-radius: 16px 16px 0 0;">
                        <strong><i class="bi bi-folder2-open me-2"></i> Workspace</strong>
                        <div>
                            <div class="d-flex gap-2 align-items-center">
                                <button class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1"
                                    onclick="triggerImport()">
                                    <i class="bi bi-upload"></i> Import
                                </button>
                                <input type="file" id="importFileInput" class="d-none" accept=".json"
                                    onchange="handleImport(event)">
                                <div class="dropdown">
                                    <button class="btn fw-bold dropdown-toggle text-white px-3 py-1" type="button"
                                        data-bs-toggle="dropdown"
                                        style="border-color: #10BC69; border-radius: 25px; background: #10BC69;">
                                        + New
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" onclick="openLabCreate()">🧪 New Lab</a>
                                        </li>
                                        <li><a class="dropdown-item" href="#" onclick="openFolderCreate()">📁 New
                                                Folder</a></li>
                                    </ul>
                                </div>
                            </div>
                            <input type="hidden" id="currentFolderId" value="0">
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-signpost-2" style="color: #10BC69"></i>
                                <strong class="me-1">Path:</strong>
                                <span id="breadcrumb-path" class="text-dark-emphasis">root</span>
                            </div>
                            <button onclick="loadFolder(0)" class="btn btn-sm btn-outline-secondary">⬅ Back to Root</button>
                        </div>
                        <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search Labs...">
                        <ul class="list-group" id="lab-list">
                            <li class="list-group-item text-center text-muted">
                                <i class="bi bi-hourglass-split"></i> Loading...
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="card h-100 border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
                    <div class="card-header d-flex justify-content-between align-items-center"
                        style="background: linear-gradient(135deg, #6d9d6b, #c5eacb); color: white;">
                        <div class="fw-bold d-flex align-items-center gap-2">
                            <i class="bi bi-grid fs-5"></i> Lab Preview
                        </div>
                        <div id="lab-author" class="text-end fw-bold text-dark opacity-75"></div>
                    </div>
                    <div class="card-body bg-light" id="preview-panel" style="min-height: 300px; padding: 1.5rem;">
                        <div class="d-flex flex-column align-items-center justify-content-center text-muted h-100">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            });
        </script>
    @endif

       <script src="https://unpkg.com/jsplumb@2.15.6/dist/js/jsplumb.min.js"></script>
    <script>
        /**
         * Loads folders and labs based on the given folder ID and search query.
         * @param {number} folderId - The ID of the folder to load. Defaults to 0 (root).
         * @param {string} search - The search query to filter labs/folders. Defaults to an empty string.
         */
        function loadFolder(folderId = 0, search = '') {
            document.getElementById('currentFolderId').value = folderId;
            const url = `/lab/folder/${folderId}?search=${encodeURIComponent(search)}`;
            const labList = document.getElementById('lab-list');
            const previewPanel = document.getElementById('preview-panel');

            // Show loading state
            labList.innerHTML = `<li class="list-group-item text-center text-muted">
                                    <i class="bi bi-hourglass-split"></i> Loading...
                                 </li>`;
            previewPanel.innerHTML = `<div class="d-flex flex-column align-items-center justify-content-center text-muted h-100" style="animation: float 2s ease-in-out infinite;" data-aos="fade-up">
                                        <i class="bi bi-box-seam" style="font-size: 2.5rem;"></i>
                                        <p class="mt-2">No preview available</p>
                                      </div>`;

            fetch(url)
                .then(res => res.json())
                .then(res => {
                    // Update breadcrumb
                    const breadcrumbHtml = res.breadcrumbs.map(b =>
                        `<a href="#" onclick="loadFolder(${b.id})" class="text-primary">${b.name}</a>`
                    ).join(" / ");
                    document.getElementById('breadcrumb-path').innerHTML = 'root' + (breadcrumbHtml ? ' / ' +
                        breadcrumbHtml : '');

                    const listItems = [];

                    // Add "Back to Parent Folder" button if not in root
                    if (res.currentFolder && res.currentFolder.id !== 0) {
                        const parentId = res.currentFolder.parent_id ?? 0;
                        listItems.push(`
                            <li class="list-group-item" onclick="loadFolder(${parentId})" style="cursor: pointer;">
                                <i class="bi bi-folder-fill text-warning me-2"></i> ..
                            </li>
                        `);
                    }

                    // Render folders
                    res.folders.forEach(folder => {
                        const isMissing = folder.is_missing;
                        listItems.push(`
                            <li class="list-group-item d-flex justify-content-between align-items-center ${isMissing ? 'bg-warning-subtle' : ''}"
                                ${isMissing ? '' : `onclick="loadFolder(${folder.id})"`}
                                style="cursor: ${isMissing ? 'not-allowed' : 'pointer'}">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-folder-fill text-warning me-2"></i>
                                    <span class="item-name">${folder.name}</span>
                                    ${isMissing ? `
                                                <span class="badge bg-danger ms-2">Missing</span>
                                                <i class="bi bi-info-circle-fill text-secondary ms-1" data-bs-toggle="tooltip" title="This folder is missing from the file system but still exists in the database."></i>
                                            ` : ''}
                                </div>
                                <div class="item-actions">
                                    ${isMissing
                                        ? `
                                                    <a href="#" onclick="event.stopPropagation(); restoreFolder(${folder.id})" data-bs-toggle="tooltip" title="Restore Folder" class="text-success me-2">
                                                        <i class="bi bi-arrow-clockwise"></i>
                                                    </a>
                                                    <a href="#" onclick="event.stopPropagation(); deleteFolderOnlyDb(${folder.id}, '${folder.name}')" data-bs-toggle="tooltip" title="Delete from DB" class="text-danger">
                                                        <i class="bi bi-x-circle-fill"></i>
                                                    </a>`
                                        : `
                                                    <a href="#" onclick="event.stopPropagation(); showRenamePrompt(${folder.id}, '${folder.name}')" data-bs-toggle="tooltip" title="Rename Folder" class="text-primary me-2">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    <a href="#" onclick="event.stopPropagation(); confirmDeleteFolder(${folder.id}, '${folder.name}')" data-bs-toggle="tooltip" title="Delete Folder" class="text-danger">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </a>
                                                    <form id="form-delete-folder-${folder.id}" action="/lab-group/${folder.id}" method="POST" class="d-none">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>`
                                    }
                                </div>
                            </li>
                        `);
                    });

                    // Render labs
                    res.labs.forEach(lab => {
                        const isMissing = lab.is_missing;
                        listItems.push(`
                            <li class="list-group-item d-flex justify-content-between align-items-center ${isMissing ? 'bg-warning-subtle' : ''}"
                                ${isMissing ? '' : `onclick="previewLab(${lab.id})"`}
                                style="cursor: ${isMissing ? 'default' : 'pointer'}">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-file-earmark-fill me-2" style="color: #10BC69"></i>
                                    <span class="item-name">${lab.name}</span>
                                    ${isMissing ? `
                                                <span class="badge bg-danger ms-2">Missing</span>
                                                <i class="bi bi-info-circle-fill text-secondary ms-1" data-bs-toggle="tooltip" title="This lab file is not found on the server, but its data still exists in the database."></i>
                                            ` : ''}
                                </div>
                                <div class="item-actions">
                                    ${isMissing
                                        ? `
                                                    <a href="#" onclick="event.preventDefault(); event.stopPropagation(); restoreLab(${lab.id})" data-bs-toggle="tooltip" title="Restore Lab" class="text-success me-2">
                                                        <i class="bi bi-arrow-clockwise"></i>
                                                    </a>
                                                    <a href="#" onclick="event.preventDefault(); event.stopPropagation(); deleteLabOnlyDb(${lab.id}, '${lab.name}')" data-bs-toggle="tooltip" title="Delete from DB" class="text-danger">
                                                        <i class="bi bi-x-circle-fill"></i>
                                                    </a>`
                                        : `
                                                    <a href="#" onclick="event.preventDefault(); event.stopPropagation(); exportLab(${lab.id})" class="text-info me-2" data-bs-toggle="tooltip" title="Export Lab">
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                    <a href="/lab/${lab.id}/topologi" class="text-success me-2" onclick="event.stopPropagation()" data-bs-toggle="tooltip" title="Edit Lab">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    <a href="#" class="text-danger" onclick="event.preventDefault(); event.stopPropagation(); confirmDelete(${lab.id}, '${lab.name}')" data-bs-toggle="tooltip" title="Delete Lab">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </a>
                                                    <form id="form-delete-${lab.id}" action="/lab/${lab.id}" method="POST" class="d-none">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>`
                                    }
                                </div>
                            </li>
                        `);
                    });

                    if (listItems.length === 0) {
                        listItems.push(`
                            <li class="list-group-item text-center text-muted">
                                <i class="bi bi-emoji-frown"></i> No labs or folders found.
                            </li>
                        `);
                    }

                    labList.innerHTML = listItems.join("");

                    // Activate tooltips
                    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                    tooltipTriggerList.map(function(el) {
                        return new bootstrap.Tooltip(el);
                    });
                })
                .catch(error => {
                    console.error('Error loading folder data:', error);
                    labList.innerHTML = `<li class="list-group-item text-center text-danger">
                                            <i class="bi bi-exclamation-triangle"></i> Failed to load data.
                                         </li>`;
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            searchInput.addEventListener('input', function() {
                const currentFolderId = document.getElementById('currentFolderId').value || 0;
                loadFolder(currentFolderId, this.value.trim());
            });

            // Initial load of the root folder
            loadFolder(0);
        });

        // Placeholder functions for actions (assuming they are defined elsewhere)
        function triggerImport() {
            document.getElementById('importFileInput').click();
        }

        function handleImport(event) {
            // Logic for handling file import
            console.log('Importing file:', event.target.files[0]);
        }

        function openLabCreate() {
            // Logic for opening new lab creation modal/page
            console.log('Open New Lab Create');
        }

        function openFolderCreate() {
            // Logic for opening new folder creation modal/page
            console.log('Open New Folder Create');
        }
    </script>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@endsection
