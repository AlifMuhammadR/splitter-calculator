/**
 * Displays a SweetAlert2 toast notification.
 * @param {string} icon - The icon to display (e.g., 'success', 'error', 'warning', 'info', 'question').
 * @param {string} title - The title text of the toast.
 */
function showToast(icon, title) {
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: icon,
        title: title,
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
    });
}

/**
 * Fetches and displays a preview of a lab topology.
 * @param {string} labId - The ID of the lab to preview.
 */
function previewLab(labId) {
    fetch(`/lab/preview/${labId}`)
        .then(res => {
            if (!res.ok) throw new Error('File not found');
            return res.json();
        })
        .then(data => {
            const panel = document.getElementById('preview-panel');
            panel.innerHTML = `
                <div id="canvas-preview-wrapper" style="width: 100%; height: calc(100vh - 250px); overflow: auto; position: relative; background: #f8f9fa; border-radius: 8px;">
                    <div id="canvas-preview" style="position: relative; width: 1000px; height: 800px;"></div>
                </div>
                <div class="card mt-3 border-info shadow-sm" style="font-size: 0.9rem;">
                    <div class="card-header bg-info text-white fw-bold">
                        <i class="bi bi-bar-chart-fill me-2"></i> Informasi Loss
                    </div>
                    <div class="card-body">
                        <div>Total Loss: <span class="text-danger fw-bold">${(data.connections?.[0]?.loss ?? 0).toFixed(2)} dB</span></div>
                        <div>Output Power: <span class="text-primary fw-bold">${(data.nodes?.[1]?.power ?? 0).toFixed(2)} dBm</span></div>
                        <div>Jalur: ${data.nodes?.[0]?.type || '?'} → ${data.nodes?.[1]?.type || '?'}</div>
                    </div>
                </div>
            `;

            // Set author
            document.getElementById('lab-author').innerHTML =
                `<i class="bi bi-person-fill me-1"></i> ${data.author || '-'}`;

            // Render node on canvas
            const canvas = document.getElementById('canvas-preview');
            const nodeMap = {};

            data.nodes.forEach(node => {
                const el = document.createElement('div');
                el.className = 'node-preview shadow-sm';
                el.innerHTML = `
                    <div class="fw-bold text-center">${node.type}</div>
                    <div class="text-center text-success small">${node.power?.toFixed(2) ?? 0} dB</div>
                `;
                el.style.position = 'absolute';
                el.style.top = node.top;
                el.style.left = node.left;
                el.style.padding = '8px 12px';
                el.style.background = 'white';
                el.style.borderRadius = '8px';
                el.style.border = '1px solid #ccc';
                el.style.minWidth = '80px';
                canvas.appendChild(el);
                nodeMap[node.id] = el;
            });

            // After rendering nodes, auto-scale if necessary
            const canvasWrapper = document.getElementById('canvas-preview-wrapper');
            const canvasInner = document.getElementById('canvas-preview');

            // Calculate bounding box of nodes
            let maxRight = 0,
                maxBottom = 0;
            data.nodes.forEach(n => {
                const left = parseFloat(n.left);
                const top = parseFloat(n.top);
                if (left + 100 > maxRight) maxRight = left + 100;
                if (top + 100 > maxBottom) maxBottom = top + 100;
            });

            // Set canvas size & scale
            canvasInner.style.width = `${maxRight}px`;
            canvasInner.style.height = `${maxBottom}px`;
            canvasInner.style.margin = '0 auto';

            // Calculate scale to fit all
            const scaleX = canvasWrapper.clientWidth / maxRight;
            const scaleY = canvasWrapper.clientHeight / maxBottom;
            const scale = Math.min(scaleX, scaleY, 1); // Do not exceed 100%

            canvasInner.style.transform = `scale(${scale})`;
            canvasInner.style.transformOrigin = 'top left';

            // Render connections using LeaderLine
            setTimeout(() => {
                data.connections.forEach(conn => {
                    const fromEl = nodeMap[conn.from];
                    const toEl = nodeMap[conn.to];
                    if (fromEl && toEl) {
                        const line = new LeaderLine(
                            LeaderLine.pointAnchor(fromEl, {
                                x: '50%',
                                y: '100%'
                            }),
                            LeaderLine.pointAnchor(toEl, {
                                x: '50%',
                                y: '0%'
                            }), {
                                dash: true,
                                color: conn.color || 'black',
                                size: 2,
                                startPlug: 'behind',
                                endPlug: 'arrow',
                                path: 'straight',
                                startLabel: LeaderLine.captionLabel(`-${conn.loss.toFixed(2)} dB`, {
                                    color: 'red'
                                })
                            }
                        );
                    }
                });
            }, 100); // Give DOM elements time to be ready
        })
        .catch(() => {
            document.getElementById('preview-panel').innerHTML =
                `<p class="text-danger">File not found or missing on disk.</p>`;
            document.getElementById('lab-author').innerHTML = '';
        });
}

/**
 * Redirects to the lab creation page, passing the current folder ID.
 */
function openLabCreate() {
    const currentId = document.getElementById('currentFolderId').value;
    window.location.href = `/lab/create?parent=${currentId}`;
}

/**
 * Redirects to the folder creation page, passing the current folder ID.
 */
function openFolderCreate() {
    const currentId = document.getElementById('currentFolderId').value;
    window.location.href = `/lab-group/create?parent=${currentId}`;
}

/**
 * Shows a SweetAlert2 prompt to rename a folder.
 * @param {string} folderId - The ID of the folder to rename.
 * @param {string} currentName - The current name of the folder.
 */
function showRenamePrompt(folderId, currentName) {
    Swal.fire({
        title: 'Rename Folder',
        input: 'text',
        inputLabel: 'New name',
        inputValue: currentName,
        showCancelButton: true,
        confirmButtonText: 'Rename',
        preConfirm: (newName) => {
            return fetch(`/lab-group/${folderId}/rename`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        name: newName
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) throw new Error(data.message);
                    return data;
                })
                .catch(error => {
                    Swal.showValidationMessage(`Rename failed: ${error.message}`);
                });
        }
    }).then(result => {
        if (result.isConfirmed && result.value?.success) {
            // Refresh folder list
            loadFolder(document.getElementById('currentFolderId').value);

            // Success toast
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Folder berhasil diubah!',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
        }
    });
}

/**
 * Confirms folder deletion, checking for contents before proceeding.
 * @param {string} folderId - The ID of the folder to delete.
 * @param {string} folderName - The name of the folder to delete.
 */
function confirmDeleteFolder(folderId, folderName) {
    fetch(`/lab-group/${folderId}/check-contents`)
        .then(res => res.json())
        .then(data => {
            let warnings = [];

            if (data.hasLabs) {
                warnings.push(`⚠ ${data.totalLabs} lab`);
            }

            if (data.hasFolders) {
                warnings.push(`⚠ ${data.totalFolders} subfolder`);
            }

            const warningHtml = warnings.length > 0 ?
                `<br><small class="text-danger">${warnings.join(' dan ')} akan ikut terhapus!</small>` :
                '';

            Swal.fire({
                title: 'Delete Folder?',
                html: `Folder <strong>${folderName}</strong> akan dihapus.${warningHtml}`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then(result => {
                if (result.isConfirmed) {
                    document.getElementById(`form-delete-folder-${folderId}`).submit();
                }
            });
        })
        .catch(() => {
            Swal.fire('Error', 'Gagal memeriksa isi folder.', 'error');
        });
}

/**
 * Confirms lab deletion.
 * @param {string} id - The ID of the lab to delete.
 * @param {string} name - The name of the lab to delete.
 */
function confirmDelete(id, name) {
    Swal.fire({
        title: 'Delete Lab?',
        html: `<strong>${name}</strong> will be removed.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(`form-delete-${id}`).submit();
        }
    });
}

// Initial load
window.addEventListener("DOMContentLoaded", () => loadFolder());

/**
 * Retrieves the CSRF token from the meta tag.
 * @returns {string} The CSRF token.
 */
function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
}

/**
 * Restores a deleted lab.
 * @param {string} id - The ID of the lab to restore.
 */
function restoreLab(id) {
    fetch(`/restore/lab/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Restored!', 'Lab berhasil direstore.', 'success');
                loadFolder(document.getElementById('currentFolderId').value);
            }
        });
}

/**
 * Restores a deleted folder.
 * @param {string} id - The ID of the folder to restore.
 */
function restoreFolder(id) {
    fetch(`/restore/folder/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Restored!', 'Folder berhasil dibuat ulang.', 'success');
                loadFolder(document.getElementById('currentFolderId').value);
            }
        });
}

/**
 * Prompts to confirm deletion of a lab from the database only (not from disk).
 * @param {string} id - The ID of the lab to delete from the database.
 * @param {string} name - The name of the lab.
 */
function deleteLabOnlyDb(id, name) {
    Swal.fire({
        title: `Hapus lab "${name}" dari database?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal'
    }).then(result => {
        if (result.isConfirmed) {
            fetch(`/delete-only-db/lab/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Deleted!', 'Lab dihapus dari database.', 'success');
                        loadFolder(document.getElementById('currentFolderId').value);
                    }
                });
        }
    });
}

/**
 * Prompts to confirm deletion of a folder from the database only (not from disk).
 * @param {string} id - The ID of the folder to delete from the database.
 * @param {string} name - The name of the folder.
 */
function deleteFolderOnlyDb(id, name) {
    Swal.fire({
        title: `Hapus folder "${name}" dari database?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal'
    }).then(result => {
        if (result.isConfirmed) {
            fetch(`/delete-only-db/folder/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Deleted!', 'Folder dihapus dari database.', 'success');
                        loadFolder(document.getElementById('currentFolderId').value);
                    }
                });
        }
    });
}

/**
 * Exports lab data as a JSON file.
 * @param {string} id - The ID of the lab to export.
 */
function exportLab(id) {
    fetch(`/lab/${id}/json`)
        .then(res => res.json())
        .then(data => {
            const fileName = `topologi-${data.name.replace(/\s+/g, '_')}.json`;
            const blob = new Blob([JSON.stringify(data, null, 2)], {
                type: "application/json"
            });
            const url = URL.createObjectURL(blob);

            const link = document.createElement('a');
            link.href = url;
            link.download = fileName;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Gagal', 'Gagal melakukan export.', 'error');
        });
}

/**
 * Triggers the hidden file input for importing labs.
 */
function triggerImport() {
    document.getElementById('importFileInput').click();
}

/**
 * Handles the import of a lab from a selected JSON file.
 * @param {Event} event - The change event from the file input.
 */
function handleImport(event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(e) {
        try {
            const json = JSON.parse(e.target.result);
            const folderId = document.getElementById('currentFolderId')?.value || null;

            const formData = new FormData();
            formData.append('lab_group_id', folderId);
            formData.append('json_raw', JSON.stringify(json));

            Swal.fire({
                title: 'Importing...',
                text: 'Please wait a moment.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('/lab/import', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(responseData => {
                    Swal.close();
                    if (responseData?.success) {
                        showToast('success', 'Lab berhasil diimport!');
                        loadFolder(folderId);
                    } else {
                        showToast('error', responseData?.message || 'Import gagal');
                    }
                })
                .catch(err => {
                    Swal.close();
                    showToast('error', err.message || 'Terjadi kesalahan saat import');
                })
                .finally(() => {
                    document.getElementById('importFileInput').value = ''; // reset input
                });
        } catch (err) {
            showToast('error', 'File tidak valid!');
            document.getElementById('importFileInput').value = ''; // reset input
        }
    };
    reader.readAsText(file);
}
