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

function createPreviewNodeElement(node) {
    const el = document.createElement('div');
    el.className = 'node-preview shadow-sm';
    el.id = node.id;

    const fixedPower = (!isNaN(parseFloat(node.power))) ? parseFloat(node.power).toFixed(2) : '0.00';
    const type = node.type || 'Client';

    let svg = '';

    if (type === 'OLT') {
        svg = `
            <svg width="24" height="24" viewBox="0 0 36 36" fill="none">
                <rect x="4" y="12" width="28" height="12" rx="2" fill="#3B82F6" stroke="#1E3A8A" stroke-width="2"/>
                <rect x="8" y="24" width="20" height="2" rx="1" fill="#1E3A8A"/>
                <circle cx="9" cy="18" r="1.5" fill="#FBBF24"/>
                <circle cx="13" cy="18" r="1.5" fill="#FBBF24"/>
                <circle cx="17" cy="18" r="1.5" fill="#FBBF24"/>
                <rect x="22" y="16" width="8" height="4" rx="1" fill="#F1F5F9" stroke="#1E3A8A" stroke-width="1"/>
            </svg>`;
    } else if (type.startsWith('Splitter')) {
        svg = `
            <svg width="24" height="24" viewBox="0 0 36 36" fill="none">
                <rect x="8" y="10" width="20" height="16" rx="3" fill="#10B981" stroke="#047857" stroke-width="2"/>
                <line x1="18" y1="10" x2="18" y2="26" stroke="#047857" stroke-width="2"/>
                <circle cx="14" cy="18" r="2" fill="#FBBF24"/>
                <circle cx="22" cy="18" r="2" fill="#FBBF24"/>
                <rect x="13" y="25" width="10" height="2" rx="1" fill="#047857"/>
            </svg>`;
    } else if (type === 'Client') {
        svg = `
            <svg width="24" height="24" viewBox="0 0 36 36" fill="none">
                <rect x="10" y="14" width="16" height="8" rx="2" fill="#F59E42" stroke="#B45309" stroke-width="2"/>
                <rect x="13" y="23" width="10" height="2" rx="1" fill="#B45309"/>
                <circle cx="18" cy="18" r="2" fill="#FBBF24"/>
            </svg>`;
    }

    el.innerHTML = `
        <div class="d-flex flex-column align-items-center">
            ${svg}
            <strong style="font-size: 12px;" class="mt-1">${type}</strong>
            <div class="output-power text-success" style="font-size: 11px;">
                ${fixedPower} dB
            </div>
        </div>
    `;

    el.style.position = 'absolute';
    el.style.left = node.left;
    el.style.top = node.top;
    el.style.background = 'white';
    el.style.border = '1px solid #ccc';
    el.style.borderRadius = '10px';
    el.style.padding = '6px 8px';
    el.style.minWidth = '80px';
    el.style.textAlign = 'center';
    el.style.zIndex = '2';

    return el;
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
            // Hitung total loss & output power
            const totalLoss = data.connections.reduce((sum, conn) => sum + (conn.loss || 0), 0);
            const outputNode = data.nodes.find(n => n.type === 'Client') || data.nodes[data.nodes.length - 1];

            const panel = document.getElementById('preview-panel');
            panel.innerHTML = `
                <div id="canvas-preview-wrapper" style="width: 100%; height: calc(100vh - 250px); overflow: visible; position: relative; background: #f8f9fa; border-radius: 8px;">
                    <div id="canvas-preview" style="position: relative;"></div>
                </div>
                <div class="card mt-3 border-info shadow-sm" style="font-size: 0.9rem;">
                    <div class="card-header bg-info text-white fw-bold">
                        <i class="bi bi-bar-chart-fill me-2"></i> Informasi Loss
                    </div>
                    <div class="card-body">
                        <div>Total Loss: <span class="text-danger fw-bold">${totalLoss.toFixed(2)} dB</span></div>
<div>Total cable loss: 50m x ${(data.connections[0]?.cable === 'Patchcord' ? 0.0003 : 0.0002).toFixed(4)} dB/m = ${(data.connections.reduce((sum, c) => sum + (c.length || 0), 0) * (data.connections[0]?.cable === 'Patchcord' ? 0.0003 : 0.0002)).toFixed(3)} dB</div>
<div>Total loss splicing: ${data.splicing || 0} x 0.1dB = ${((data.splicing || 0) * 0.1).toFixed(2)} dB</div>
<div>Connector loss: ${data.connectors || 0} x 0.2dB = ${((data.connectors || 0) * 0.2).toFixed(2)} dB</div>
<div>Output Power: <span class="text-primary fw-bold">${(outputNode?.power ?? 0).toFixed(2)} dBm</span></div>
<div>Jalur: ${data.nodes?.[0]?.type || '?'} → ${outputNode?.type || '?'}</div>
                    </div>
                </div>
            `;

            document.getElementById('lab-author').innerHTML =
                `<i class="bi bi-person-fill me-1"></i> ${data.author || '-'}`;

            const canvas = document.getElementById('canvas-preview');
            const nodeMap = {};

            data.nodes.forEach(node => {
                const el = createPreviewNodeElement(node);
                canvas.appendChild(el);
                nodeMap[node.id] = el;
            });

            // Hitung ukuran canvas
            let maxRight = 0, maxBottom = 0;
            data.nodes.forEach(n => {
                const left = parseFloat(n.left);
                const top = parseFloat(n.top);
                if (left + 100 > maxRight) maxRight = left + 100;
                if (top + 100 > maxBottom) maxBottom = top + 100;
            });

            const canvasWrapper = document.getElementById('canvas-preview-wrapper');
            canvas.style.width = `${maxRight}px`;
            canvas.style.height = `${maxBottom}px`;
            canvas.style.margin = '0 auto';

            const scaleX = canvasWrapper.clientWidth / maxRight;
            const scaleY = canvasWrapper.clientHeight / maxBottom;
            const scale = Math.min(scaleX, scaleY, 1);
            canvas.style.transform = `scale(${scale})`;
            canvas.style.transformOrigin = 'top left';

            jsPlumb.ready(() => {
                const instance = jsPlumb.getInstance({
                    Container: "canvas-preview",
                    Connector: ["Flowchart", { cornerRadius: 5 }],
                    Anchors: ["Continuous", "Continuous"],
                    Endpoint: "Blank"
                });

                data.connections.forEach(conn => {
                    const isPatchcord = conn.cable === 'Patchcord';
                    const color = isPatchcord ? '#f1c40f' : 'black';
                    const dash = isPatchcord ? '4 4' : '0';

                    instance.connect({
                        source: nodeMap[conn.from],
                        target: nodeMap[conn.to],
                        paintStyle: {
                            stroke: color,
                            strokeWidth: 3,
                            dashstyle: dash
                        },
                        overlays: [
                            ["Arrow", {
                                location: 1,
                                width: 14,
                                length: 14,
                                direction: 1,
                                foldback: 1,
                                paintStyle: {
                                    fill: color,
                                    stroke: color
                                }
                            }],
                            ["Label", {
                                label: `-${conn.loss.toFixed(2)} dB`,
                                location: 0.5,
                                cssClass: "connection-label",
                                css: {
                                    backgroundColor: "white",
                                    color: "red",
                                    fontWeight: "bold",
                                    padding: "2px 6px",
                                    borderRadius: "4px",
                                    fontSize: "0.75rem"
                                }
                            }]
                        ]
                    });
                });
            });
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
'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
            showToast('success', 'Folder renamed successfully!');
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
                `<br><small class="text-danger">${warnings.join(' and ')} will be deleted!</small>` :
                '';

            Swal.fire({
                title: 'Delete Folder?',
                html: `You are about to delete lab <strong>${folderName}</strong>. This action cannot be undone!. ${warningHtml}`,
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
    Swal.fire({
        title: 'Restore Lab?',
        text: 'This lab will be restored to the system. Proceed?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="bi bi-arrow-clockwise"></i> Yes, restore',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#10BC69',
        cancelButtonColor: '#6c757d'
    }).then((result) => {
        if (result.isConfirmed) {
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
                    Swal.fire({
                        title: '<span style="color:#6C63FF;">Restored Successfully!</span>',
                        text: 'The lab has been restored to the system.',
                        imageUrl: '/assets/tenor.gif',
                        imageWidth: 100,
                        imageHeight: 100,
                        imageAlt: 'Success animation',
                        width: 500,
                        padding: '1.5em',
                        background: '#fff url(/images/pattern-bg.png)',
                        backdrop: `
                            rgb(1,51,104)
                            url("/assets/nyan-cat.gif")
                            left top
                            no-repeat
                        `,
                        showConfirmButton: true,
                        confirmButtonText: 'OK',
                        position: 'center'
                    });
                    loadFolder(document.getElementById('currentFolderId').value);
                } else {
                    Swal.fire('Failed', 'Failed to restore the lab.', 'error');
                }
            });
        }
    });
}

/**
 * Restores a deleted folder.
 * @param {string} id - The ID of the folder to restore.
 */
function restoreFolder(id) {
    Swal.fire({
        title: 'Restore Folder?',
        text: 'This folder will be restored to the system. Proceed?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="bi bi-arrow-clockwise"></i> Yes, restore',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#10BC69',
        cancelButtonColor: '#6c757d'
    }).then((result) => {
        if (result.isConfirmed) {
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
                    Swal.fire({
                        title: '<span style="color:#6C63FF;">Restored Successfully!</span>',
                        text: 'The folder has been restored to the system.',
                        imageUrl: '/assets/tenor.gif',
                        imageWidth: 100,
                        imageHeight: 100,
                        imageAlt: 'Success animation',
                        width: 500,
                        padding: '1.5em',
                        background: '#fff url(/images/pattern-bg.png)',
                        backdrop: `
                            rgb(1,51,104)
                            url("/assets/nyan-cat.gif")
                            left top
                            no-repeat
                        `,
                        showConfirmButton: true,
                        confirmButtonText: 'OK',
                        position: 'center'
                    });
                    loadFolder(document.getElementById('currentFolderId').value);
                } else {
                    Swal.fire('Failed', 'Failed to restore the folder.', 'error');
                }
            });
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
            const fileName = `topology-${data.name.replace(/\s+/g, '_')}.json`;
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

            // ✅ Show toast after successful export
            Swal.fire({
                icon: 'success',
                title: 'Lab exported successfully!',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Failed', 'Failed to export the lab.', 'error');
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

            // ✅ 1. Tampilkan toast “Importing...”
            Swal.fire({
                icon: 'info',
                title: 'Importing...',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });

            // ✅ 2. Lakukan proses import
            fetch('/lab/import', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
            .then(res => res.json())
            .then(responseData => {
                if (responseData?.success) {
                    // ✅ 3. Tampilkan popup GIF saat sukses
                    Swal.fire({
                        title: '<span style="color:#6C63FF;">Successfully Imported!</span>',
                        text: 'Lab file has been imported into the system.',
                        imageUrl: '/assets/tenor.gif', // ganti dengan GIF milikmu
                        imageWidth: 100,
                        imageHeight: 100,
                        imageAlt: 'Success animation',
                        width: 500,
                        padding: '1.5em',
                        background: '#fff url(/images/pattern-bg.png)', // opsional
                        backdrop: `
                            rgb(1,51,104, 0.4)
                            url("/assets/nyan-cat.gif")
                            left top
                            no-repeat
                        `,
                        showConfirmButton: true,
                        position: 'center'
                    });

                    loadFolder(folderId);
                } else {
                    showToast('error', responseData?.message || 'Import failed.');
                }
            })
            .catch(err => {
                showToast('error', err.message || 'Something went wrong during import.');
            })
            .finally(() => {
                document.getElementById('importFileInput').value = ''; // reset input
            });
        } catch (err) {
            showToast('error', 'Invalid JSON file!');
            document.getElementById('importFileInput').value = ''; // reset input
        }
    };
    reader.readAsText(file);
}

