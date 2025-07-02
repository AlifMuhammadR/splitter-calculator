@extends('fe.lab')
@section('title', 'Lab - ' . $lab['name'])
@section('content')
    <!-- sidebar -->
    <div id="sidebar" class="position-fixed sidebar-hidden">
        <div class="inner p-3">
            <!-- konten sidebar -->
            <a href="{{ route('lab') }}" class="btn w-100 text-white fw-bold"
                style="background: linear-gradient(87deg, #627594 0, #8898aa 100%);
        border-radius: 25px;
        border: none;
        pointer-events: none;">
                <i class="bi bi-person-circle me-1"></i>
                {{ $lab['author'] }}
            </a>
            <div class="divider my-3"></div>
            <div class="mb-2">
                <h5 class="fw-bold text-dark">Input Power OLT</h5>
                <input type="number" id="input-power" class="form-control form-control-sm mb-2" oninput="updateOLTPower()"
                    value="7">
                <!-- <input type="number" id="inputPowerOLT" value="0"> -->
            </div>
            <div class="mb-2">
                <h5 class="fw-bold text-dark">Splicing</h5>
                <input type="number" id="splicing" class="form-control form-control-sm mb-2" value="1">
            </div>
            <h5 class="fw-bold text-dark">Add Node</h5>
            <div class="d-grid gap-2 mb-2">
                <button class="btn btn-sm btn-light text-dark fw-bold" onclick="addNode('OLT')">
                    <!-- SVG OLT -->
                    <svg width="20" height="20" viewBox="0 0 36 36" fill="none" style="vertical-align:middle;">
                        <rect x="4" y="12" width="28" height="12" rx="2" fill="#3B82F6" stroke="#1E3A8A"
                            stroke-width="2" />
                        <rect x="8" y="24" width="20" height="2" rx="1" fill="#1E3A8A" />
                        <circle cx="9" cy="18" r="1.5" fill="#FBBF24" />
                        <circle cx="13" cy="18" r="1.5" fill="#FBBF24" />
                        <circle cx="17" cy="18" r="1.5" fill="#FBBF24" />
                        <rect x="22" y="16" width="8" height="4" rx="1" fill="#F1F5F9" stroke="#1E3A8A"
                            stroke-width="1" />
                    </svg>
                    OLT
                </button>
                <button class="btn btn-sm btn-light text-dark fw-bold" onclick="addNode('Splitter')">
                    <!-- SVG Splitter -->
                    <svg width="20" height="20" viewBox="0 0 36 36" fill="none" style="vertical-align:middle;">
                        <rect x="8" y="10" width="20" height="16" rx="3" fill="#10B981" stroke="#047857"
                            stroke-width="2" />
                        <line x1="18" y1="10" x2="18" y2="26" stroke="#047857"
                            stroke-width="2" />
                        <circle cx="14" cy="18" r="2" fill="#FBBF24" />
                        <circle cx="22" cy="18" r="2" fill="#FBBF24" />
                        <rect x="13" y="25" width="10" height="2" rx="1" fill="#047857" />
                    </svg>
                    Splitter
                </button>
                <button class="btn btn-sm btn-light text-dark fw-bold" onclick="addNode('Client')">
                    <!-- SVG Client -->
                    <svg width="20" height="20" viewBox="0 0 36 36" fill="none" style="vertical-align:middle;">
                        <rect x="10" y="14" width="16" height="8" rx="2" fill="#F59E42" stroke="#B45309"
                            stroke-width="2" />
                        <rect x="13" y="23" width="10" height="2" rx="1" fill="#B45309" />
                        <circle cx="18" cy="18" r="2" fill="#FBBF24" />
                    </svg>
                    Client
                </button>
            </div>
            <h5 class="fw-bold text-dark">Cable Type</h5>
            <div class="d-flex gap-2 mb-2">
                <div class="cable-option p-2 bg-light text-dark rounded text-center flex-fill cursor-pointer">
                    <div style="width: 30px; height: 5px; background-color: black; margin: auto;"></div>
                    <small>Dropcore</small>
                </div>
                <div class="cable-option p-2 bg-warning text-dark rounded text-center flex-fill cursor-pointer">
                    <div style="width: 30px; height: 5px; background-color: yellow; margin: auto;"></div>
                    <small>Patchcord</small>
                </div>
            </div>
            <div class="divider my-3"></div>
            <div class="hidden-ui" style="display: none">


                <label class="form-label text-white">Panjang Kabel (m)</label>
                <input type="number" id="cable-length" class="form-control form-control-sm mb-2" value="50">

                <label class="form-label text-white">Connector</label>
                <input type="number" id="connectors" class="form-control form-control-sm mb-2" value="2">
            </div>

            <div class="d-grid gap-2 mb-2">
                <button class="btn btn-sm btn-light w-100 fw-bold" onclick="gatherAndSaveTopology()"><i
                        class="bi bi-bookmark-plus-fill"></i> Save Topology</button>
                <button class="btn btn-sm btn-light w-100 fw-bold" onclick="resetTopology()">Reset</button>
                <button class="btn btn-sm btn-light w-100 fw-bold" onclick="undoAction()">↩ Undo</button>
            </div>
            <div class="divider my-3"></div>
            <h5 class="fw-bold text-dark"><i class="bi bi-file-earmark-code-fill"></i> Manage File</h5>
            <div class="d-grid gap-2 mb-2">
                <!-- Export -->
                <button class="btn btn-sm"
                    style="background: linear-gradient(87deg, #2d93ce 0, #107abc 100%); border: none;"
                    onclick="exportTopology()">⬇ Export (.json)</button>

                <!-- Import -->
                <input type="file" id="import-file" accept=".json" class="form-control form-control-sm"
                    onchange="importTopology(this.files[0])">
            </div>
            <div class="divider my-3"></div>
            <a href="{{ route('lab') }}" class="btn w-100 text-white fw-bold mb-2"
                style="background: linear-gradient(87deg, #627594 0, #8898aa 100%);border-radius: 25px; border: none;"><i
                    class="bi bi-x-circle"></i> Keluar</a>
            <img src="{{ asset('fe/img/hyp-set.png') }}" class="w-100 h-25 mt-3" alt="">
        </div>
    </div>


    <!-- Modal Pilih Splitter -->
    <div class="modal fade" id="splitterModal" tabindex="-1" aria-labelledby="splitterModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="splitterModalLabel">Pilih Tipe Splitter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <select id="splitterTypeSelect" class="form-select">
                        <option value="1:2">1:2</option>
                        <option value="1:4">1:4</option>
                        <option value="1:8">1:8</option>
                        <option value="1:16">1:16</option>
                        <option value="1:32">1:32</option>
                        <option value="1:64">1:64</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="confirmSplitter()">Pilih</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Canvas Full Area -->
    <div id="map-canvas" data-lab-id="{{ $lab['id'] }}"
        style="position: relative; height: 100vh; background: #f8fafc;">
    </div>

    <style>
        .myLabel {
            background: white !important;
            border-radius: 4px;
            border: 1px solid #ddd;
            padding: 2px 4px;
            color: #d70000 !important;
            font-size: 13px;
            font-weight: bold;
            box-shadow: 1px 1px 4px #ececec;
        }

        #info-card .card-title {
            font-size: 1rem;
            font-weight: 700;
        }

        #info-card .output-power-node {
            display: flex;
            align-items: center;
            font-size: 13px;
            gap: 0.5em;
            margin-bottom: 2px;
        }

        #info-card .output-power-node .node-name {
            min-width: 62px;
            font-weight: 500;
            color: #22c55e;
        }

        #info-card .output-power-node .node-power {
            font-weight: 500;
            color: #0ea5e9;
        }

        #info-card small.text-muted {
            font-size: 12px;
        }
    </style>

    <div id="info-card" class="card shadow-sm border border-info d-none"
        style="position: fixed; top: 360px; right: 20px; min-width: 280px; max-width:340px; transition: all 0.3s ease-in-out; z-index: 10;">
        <div class="card-body px-3 py-2">
            <h6 class="card-title text-info mb-2" style="font-weight:600;">📊 Informasi Loss</h6>
            <hr class="my-2">

            <div class="mb-2" style="font-size: 14px;">
                <div>Total Loss: <strong id="total-loss" class="text-danger" style="font-size: 1.2em;">-</strong> dB
                </div>
                <small class="text-muted" id="detail-cable-loss"></small><br>
                <small class="text-muted" id="detail-splice-loss"></small>
            </div>
            <div class="mb-2" style="font-size: 14px;">
                Input Power ONT: <strong id="power-rx" class="text-primary" style="font-size: 1.1em;">-</strong> dBm
            </div>
            <div class="mb-2" style="font-size: 13px;">
                Jalur: <span id="jalur-text" class="text-muted"></span>
            </div>
            <div id="output-power-list" class="mb-2" style="font-size:13px;"></div>
            <div id="loss-status" class="mt-2"></div>
        </div>
    </div>

    <!-- Button toggle -->
    <button onclick="toggleStatusTable()" class="btn fw-bold"
        style="background: linear-gradient(87deg, #2dce89 0, #10BC69 100%); position: fixed; bottom: 20px; right: 20px; border-radius: 25px; color: #323233;">
        <i class="bi bi-table"></i> Tabel Status
    </button>

    <!-- Tabel Status Power Loss -->
    <div id="status-table-box" class="bg-white border rounded shadow-lg p-3 mt-3"
        style="width: 350px;display: none; animation: float 2s ease-in-out infinite; z-index: 2;">
        <h5 class="mb-3 text-black text-center">📋 Tabel Status Power Loss</h5>
        <div class="table-responsive">
            <table class="table table-striped table-dark text-center align-middle rounded overflow-hidden">
                <thead class="table-success text-dark">
                    <tr>
                        <th style="font-size: 13px;">Power Loss</th>
                        <th style="font-size: 13px;">Keterangan</th>
                        <th style="font-size: 13px;">Status</th>
                    </tr>
                </thead>
                <tbody style="font-size: 13px;">
                    <tr>
                        <td>&gt; 0</td>
                        <td>Invalid</td>
                        <td><span class="badge bg-secondary px-3 py-1">⚪</span></td>
                    </tr>
                    <tr>
                        <td>&gt; -1 s/d ≤ -10</td>
                        <td>Too Strong</td>
                        <td><span class="badge px-3 py-1" style="background-color: #ebe79a;">⚠</span></td>
                    </tr>
                    <tr>
                        <td>&gt; -11 s/d ≤ -22</td>
                        <td>Good</td>
                        <td><span class="badge px-3 py-1" style="background-color: #b8eb9a;">✅</span></td>
                    </tr>
                    <tr>
                        <td>&gt; -24 s/d ≤ -32</td>
                        <td>Too Low</td>
                        <td><span class="badge px-3 py-1" style="background-color: #eb9ac4;">🛑</span></td>
                    </tr>
                    <tr>
                        <td>≤ -40</td>
                        <td>Bad</td>
                        <td><span class="badge px-3 py-1" style="background-color: #eb9a9a;">❌</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function toggleStatusTable() {
            const box = document.getElementById("status-table-box");
            box.style.display = (box.style.display === "none") ? "block" : "none";
        }
    </script>

    {{-- Form untuk update name lab, author & description --}}
    <form id="lab-update-form" method="POST" action="{{ route('lab.update', $lab['id']) }}" style="display: none;">
        @csrf
        @method('PUT')
        <input type="hidden" name="name" id="lab-update-name">
        <input type="hidden" name="author" id="lab-update-author">
        <input type="hidden" name="description" id="lab-update-description">
    </form>

    {{-- Session untuk "Success" yang memunculkan swall --}}
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true,
                });

                Toast.fire({
                    icon: 'success',
                    title: '{{ session('
                                                                                                                                                                                                                                                                                                                                                                                                        success ') }}'
                });
            });
        </script>
    @endif

    {{-- Modal untuk update name lab, author & description --}}
    <script>
        const labData = @json($lab, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    </script>
    <script>
        function showEditLabForm() {
            Swal.fire({
                title: '🛠 Edit Lab Information',
                html: `
                <div class="text-start mb-2">
                    <label class="form-label fw-bold">Lab Name</label>
                    <input id="lab-name" class="form-control" value="${labData.name}">
                </div>
                <div class="text-start mb-2">
                    <label class="form-label fw-bold">Author</label>
                    <input id="lab-author" class="form-control" value="${labData.author}">
                </div>
                <div class="text-start mb-2">
                    <label class="form-label fw-bold">Description</label>
                    <textarea id="lab-description" class="form-control" rows="3">${labData.description ?? ''}</textarea>
                </div>
            `,
                width: 600,
                confirmButtonText: '💾 Save Changes',
                confirmButtonColor: '#10BC69',
                cancelButtonText: 'Cancel',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                focusConfirm: false,
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                },
                customClass: {
                    popup: 'custom-float-alert', // nempel ke CSS kita sendiri nanti
                },
                didOpen: () => {
                    document.querySelector('.swal2-popup')?.classList.add('float-style');
                },
                willClose: () => {
                    document.querySelector('.swal2-popup')?.classList.remove('float-style');
                },
                preConfirm: () => {
                    const name = document.getElementById('lab-name').value.trim();
                    const author = document.getElementById('lab-author').value.trim();
                    const description = document.getElementById('lab-description').value.trim();

                    if (!name || !author) {
                        Swal.showValidationMessage('Lab Name and Author cannot be empty!');
                        return false;
                    }

                    return {
                        name,
                        author,
                        description
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('lab-update-name').value = result.value.name;
                    document.getElementById('lab-update-author').value = result.value.author;
                    document.getElementById('lab-update-description').value = result.value.description;
                    document.getElementById('lab-update-form').submit();
                }
            });
        }
    </script>


@endsection
{{-- Script untuk membuat dan mengedit canvas --}}
@push('scripts')
    <script>
        let lab = {
            name: '{{ $lab['name'] ?? '' }}',
            author: '{{ $lab['author'] ?? '' }}',
            description: '{{ $lab['description'] ?? '' }}'
        };
        const rawNodes = {!! $nodesJson !!};
        const rawConnections = {!! $connectionsJson !!};
        const defaultPower = {{ $power }};
        const inputPower = document.getElementById('input-power');
        const inputSplicing = document.getElementById('splicing');
        const mapCanvas = document.getElementById('map-canvas');
        let nodeId = 0;
        let selectedNode = null;
        let pendingSplitter = false;
        let selectedCableLoss = 0.2 / 1000;
        let selectedCableColor = 'black';
        let selectedCableName = 'Dropcore';
        let actions = [];
        let isTopologyChanged = false;
        let nodes = []; // {id, type, loss, power, top, left}
        let lines = []; // {from, to, cable, loss, length, conn(jsPlumb)}

        // Helper: Mendapatkan warna kabel
        function getColorByCableName(name) {
            if (!name) return 'black';
            name = name.toLowerCase();
            if (name.includes('dropcore')) return 'black';
            if (name.includes('patchcord')) return 'yellow';
            return 'black';
        }

        jsPlumb.ready(() => {
            jsPlumb.setContainer(mapCanvas);

            // Helper: Ambil tipe node dengan aman
            function getNodeType(node) {
                return node.dataset.type || (node.querySelector('strong')?.innerText || '');
            }

            // Helper: Validasi input
            function validateNumberInput(value, fieldName = "Value") {
                const num = parseFloat(value);
                if (isNaN(num) || num < 0) {
                    Swal.fire({
                        icon: 'error',
                        title: `Invalid ${fieldName}`,
                        text: `Please enter a valid number for ${fieldName.toLowerCase()}.`
                    });
                    return null;
                }
                return num;
            }
            // Tambahkan di dalam jsPlumb.ready, sebelum createNodeElement:
            function getDeviceIcon(type) {
                if (type === 'OLT') {
                    return `<svg width="55" height="36" viewBox="0 0 36 36" fill="none">
                        <rect x="4" y="12" width="28" height="12" rx="2" fill="#3B82F6" stroke="#1E3A8A" stroke-width="2"/>
                        <rect x="8" y="24" width="20" height="2" rx="1" fill="#1E3A8A"/>
                        <circle cx="9" cy="18" r="1.5" fill="#FBBF24"/>
                        <circle cx="13" cy="18" r="1.5" fill="#FBBF24"/>
                        <circle cx="17" cy="18" r="1.5" fill="#FBBF24"/>
                        <rect x="22" y="16" width="8" height="4" rx="1" fill="#F1F5F9" stroke="#1E3A8A" stroke-width="1"/>
                    </svg>`;
                } else if (type && type.startsWith('Splitter')) {
                    return `<svg width="36" height="36" viewBox="0 0 36 36" fill="none">
                        <rect x="8" y="10" width="20" height="16" rx="3" fill="#10B981" stroke="#047857" stroke-width="2"/>
                        <line x1="18" y1="10" x2="18" y2="26" stroke="#047857" stroke-width="2"/>
                        <circle cx="14" cy="18" r="2" fill="#FBBF24"/>
                        <circle cx="22" cy="18" r="2" fill="#FBBF24"/>
                        <rect x="13" y="25" width="10" height="2" rx="1" fill="#047857"/>
                    </svg>`;
                } else if (type === 'Client' || type === 'ONT') {
                    return `<svg width="36" height="36" viewBox="0 0 36 36" fill="none">
                        <rect x="10" y="14" width="16" height="8" rx="2" fill="#F59E42" stroke="#B45309" stroke-width="2"/>
                        <rect x="13" y="23" width="10" height="2" rx="1" fill="#B45309"/>
                        <circle cx="18" cy="18" r="2" fill="#FBBF24"/>
                    </svg>`;
                }
                return '';
            }

            function getFullPathNodeIds(targetId) {
                let ids = [];
                let currId = targetId;
                while (currId) {
                    ids.unshift(currId);
                    const parentConn = lines.find(l => l.to === currId);
                    currId = parentConn ? parentConn.from : null;
                }
                return ids;
            }

            /**
             * Mengembalikan path lengkap dari root (OLT) ke node target.
             * @param {string} targetId - id node tujuan.
             * @returns {string} path dengan format "OLT → Splitter 1:8 → Splitter 1:16 → Client"
             */
            // Helper: Path OLT ke ONT/Client dalam string
            function getFullPathToNode(targetId) {
                let path = [];
                let currId = targetId;
                while (currId) {
                    const nodeEl = document.getElementById(currId);
                    if (!nodeEl) break;
                    const nodeName = nodeEl.querySelector('strong')?.innerText || currId;
                    path.unshift(nodeName);
                    const parentConn = lines.find(l => l.to === currId);
                    currId = parentConn ? parentConn.from : null;
                }
                return path.join(' → ');
            }


            // Ganti function createNodeElement menjadi:
            function createNodeElement(nodeData) {
                const el = document.createElement('div');
                el.classList.add('position-absolute', 'p-2', 'bg-white', 'border', 'rounded', 'text-center');
                if (!nodeData.id) {
                    console.error("Node ID wajib ada di nodeData!");
                    return null;
                }
                el.setAttribute('id', nodeData.id);

                el.style.left = (typeof nodeData.left === 'number') ?
                    `${nodeData.left}px` :
                    (typeof nodeData.left === 'string' && nodeData.left.endsWith('px')) ?
                    nodeData.left :
                    (!isNaN(Number(nodeData.left)) && nodeData.left !== '' && nodeData.left !== undefined) ?
                    `${Number(nodeData.left)}px` :
                    `${mapCanvas.clientWidth / 2 - 50}px`;

                el.style.top = (typeof nodeData.top === 'number') ?
                    `${nodeData.top}px` :
                    (typeof nodeData.top === 'string' && nodeData.top.endsWith('px')) ?
                    nodeData.top :
                    (!isNaN(Number(nodeData.top)) && nodeData.top !== '' && nodeData.top !== undefined) ?
                    `${Number(nodeData.top)}px` :
                    `${mapCanvas.clientHeight / 2 - 25}px`;

                const fixedPower = (!isNaN(parseFloat(nodeData.power))) ? parseFloat(nodeData.power).toFixed(2) :
                    '0.00';
                const fixedLoss = (!isNaN(parseFloat(nodeData.loss))) ? parseFloat(nodeData.loss).toFixed(2) :
                    '0.00';

                el.dataset.loss = fixedLoss;
                el.dataset.power = fixedPower;
                el.dataset.type = nodeData.type || 'Client';

                el.innerHTML = `
            <button class="btn btn-danger btn-sm btn-delete-node" style="position: absolute; top: -8px; right: -8px; z-index: 2; border-radius: 50%; width: 22px; height: 22px; padding: 0; font-size: 14px; line-height: 1;" title="Hapus Node">×</button>
            <div class="node-icon" style="height:36px; margin-bottom: 4px;">${getDeviceIcon(nodeData.type)}</div>
            <strong>${nodeData.type}</strong>
            <div class="output-power" style="font-size: 12px; color: green;">
                ${fixedPower} dB
            </div>`;

                mapCanvas.appendChild(el);
                jsPlumb.draggable(el, {
                    containment: 'parent'
                });

                // Tombol hapus node
                el.querySelector('.btn-delete-node').onclick = (e) => {
                    e.stopPropagation();
                    Swal.fire({
                        title: 'Hapus node ini?',
                        text: 'Semua kabel yang terhubung ke node ini akan dihapus.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, hapus!',
                        confirmButtonColor: 'red',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const toDelete = lines.filter(conn => conn.from === el.id || conn.to === el
                                .id);
                            toDelete.forEach(conn => jsPlumb.deleteConnection(conn.conn));
                            lines = lines.filter(conn => conn.from !== el.id && conn.to !== el.id);
                            jsPlumb.removeAllEndpoints(el);
                            mapCanvas.removeChild(el);
                            nodes = nodes.filter(n => n.id !== el.id);
                            if (selectedNode && selectedNode.id === el.id) selectedNode = null;
                            isTopologyChanged = true;
                            document.getElementById('info-card').classList.add('d-none');
                        }
                    });
                };

                // Event klik node → koneksi kabel
                el.onclick = (e) => {
                    if (e.target.classList.contains('jsplumb-endpoint') || e.target.classList.contains(
                            'btn-delete-node')) return;
                    if (!selectedNode) {
                        selectedNode = el;
                        el.classList.add('border-primary');
                    } else if (selectedNode !== el) {
                        Swal.fire({
                            title: 'Connect Nodes',
                            html: `
                            <div class="text-start mb-2">Cable Length (meters)</div>
                            <input id="swal-length" type="number" class="swal2-input" value="50">
                            <div class="text-start mb-2 mt-2">Cable Type</div>
                            <select id="swal-cable" class="swal2-select">
                                <option value="dropcore" selected>Dropcore (0.2 dB/km)</option>
                                <option value="patchcord">Patchcord (0.3 dB/km)</option>
                            </select>
                        `,
                            focusConfirm: false,
                            confirmButtonText: 'Connect',
                            showCancelButton: true,
                            cancelButtonText: 'Cancel',
                            preConfirm: () => {
                                const length = validateNumberInput(document.getElementById(
                                    'swal-length').value, 'Cable Length');
                                const type = document.getElementById('swal-cable').value;
                                if (!length) return false;
                                return {
                                    length,
                                    type
                                };
                            }
                        }).then(result => {
                            if (result.isConfirmed && selectedNode && selectedNode !== el) {
                                selectedCableLoss = result.value.type === 'dropcore' ? 0.2 / 1000 :
                                    0.3 / 1000;
                                selectedCableColor = result.value.type === 'dropcore' ? 'black' :
                                    'yellow';
                                selectedCableName = result.value.type === 'dropcore' ? 'Dropcore' :
                                    'Patchcord';
                                connectNodeElements(selectedNode, el, result.value.length);
                            }
                            if (selectedNode) selectedNode.classList.remove('border-primary');
                            selectedNode = null;
                        });
                    } else {
                        el.classList.remove('border-primary');
                        selectedNode = null;
                    }
                };

                const endpointCount = nodeData.type.startsWith('Splitter') ? parseInt(nodeData.type.split(' ')[1]
                    .split(':')[1]) || 4 : 1;
                const anchors = nodeData.type.startsWith('Splitter') ?
                    Array.from({
                        length: endpointCount
                    }, (_, i) => [(i + 1) / (endpointCount + 1), 1, 0, 1]) : ['Top', 'Right', 'Bottom', 'Left'];

                for (let i = 0; i < endpointCount; i++) {
                    jsPlumb.addEndpoint(el, {
                        anchor: anchors[i % anchors.length],
                        isSource: true,
                        isTarget: true,
                        maxConnections: -1,
                        endpoint: 'Blank',
                        paintStyle: {
                            fill: '#3e4651'
                        },
                        connector: ['Flowchart', {
                            cornerRadius: 2,
                            stub: 30
                        }]
                    });
                }

                return el;
            }

            /**
             * Menambahkan node baru ke canvas.
             * @param {string} type - Tipe node (OLT, Splitter, Client).
             */
            window.addNode = function(type) {
                if (type === 'Splitter') {
                    pendingSplitter = true;
                    const modal = new bootstrap.Modal(document.getElementById('splitterModal'));
                    if (modal) modal.show();
                    else console.warn('Splitter modal not found');
                    return;
                }

                let loss = 0;
                let power = 0;
                if (type.startsWith('Splitter')) {
                    const splitRatio = type.split(' ')[1];
                    const splitLoss = {
                        '1:2': 3.5,
                        '1:4': 7.2,
                        '1:8': 10.5,
                        '1:16': 13.5,
                        '1:32': 17.0,
                        '1:64': 20.5
                    };
                    loss = splitLoss[splitRatio] || 0;
                } else if (type === 'OLT') {
                    power = validateNumberInput(inputPower?.value, 'Power') || 0;
                }

                const nodeData = {
                    id: 'node-' + nodeId++, // ✅ ID unik otomatis
                    type,
                    loss,
                    power
                };

                const el = createNodeElement(nodeData); // harus pakai ID ini

                nodes.push({
                    id: el.id,
                    type,
                    loss,
                    power,
                    top: el.style.top,
                    left: el.style.left
                });

                actions.push({
                    type: 'add-node',
                    node: el
                });

                isTopologyChanged = true;
            };

            /**
             * Mengkonfirmasi tipe splitter dan menambahkan node.
             */
            window.confirmSplitter = function() {
                const splitterType = document.getElementById('splitterTypeSelect')?.value;
                const modal = bootstrap.Modal.getInstance(document.getElementById('splitterModal'));
                if (modal) modal.hide();
                else console.warn('Splitter modal not found');
                if (splitterType) addNode(`Splitter ${splitterType}`);
            };

            /**
             * Menghubungkan dua node dengan kabel.
             * @param {HTMLElement} source - Node sumber.
             * @param {HTMLElement} target - Node tujuan.
             * @param {number} length - Panjang kabel.
             */
            function connectNodeElements(source, target, length) {
                const lossCable = length * selectedCableLoss;
                console.log(lossCable); // → 0.1 (di console)
                const lossTarget = parseFloat(target.dataset.loss || 0);
                const totalConnectors = validateNumberInput(document.getElementById('connectors')?.value,
                    'Connectors') || 0;
                const totalSplicing = validateNumberInput(inputSplicing?.value, 'Splicing') || 0;
                const connectorLoss = totalConnectors * 0.2;
                const splicingLoss = totalSplicing * 0.1;
                const totalLoss = lossCable + lossTarget + connectorLoss + splicingLoss;
                const sourcePower = parseFloat(source.dataset.power || inputPower?.value || 0);
                const powerRx = sourcePower - totalLoss;
                target.dataset.power = powerRx.toFixed(2);
                if (target.querySelector('.output-power')) {
                    target.querySelector('.output-power').innerText = `${powerRx.toFixed(2)} dB`;
                }

                const paint = {
                    stroke: selectedCableColor,
                    strokeWidth: 2,
                    dashstyle: selectedCableName === 'Patchcord' ? '4 2' : undefined
                };

                const conn = jsPlumb.connect({
                    source,
                    target,
                    anchors: ['AutoDefault', 'AutoDefault'],
                    endpoint: 'Blank',
                    connector: ['Flowchart', {
                        cornerRadius: 2,
                        stub: 30
                    }],
                    paintStyle: paint,
                    overlays: [
                        ['Label', {
                            label: `-${lossCable.toFixed(2)} dB`,
                            location: 0.5,
                            cssClass: 'myLabel',
                            css: {
                                color: 'red',
                                fontSize: '13px',
                                fontWeight: 'bold',
                                background: 'white',
                                padding: '2px 4px',
                                borderRadius: '4px',
                                border: '1px solid #ddd'
                            }
                        }],
                        ['Arrow', {
                            width: 12,
                            length: 12,
                            location: 1,
                            foldback: 0.7,
                            paintStyle: {
                                fill: selectedCableColor
                            }
                        }]
                    ]
                });
                conn._customMeta = {
                    loss: lossCable,
                    name: selectedCableName,
                    color: selectedCableColor,
                    length: length
                };

                // Overlay hapus kabel
                conn.addOverlay(['Custom', {
                    create: function() {
                        const btn = document.createElement('div');
                        btn.innerHTML = '&times;';
                        btn.title = 'Hapus kabel ini';
                        btn.style.background = 'red';
                        btn.style.color = 'white';
                        btn.style.width = '16px';
                        btn.style.height = '16px';
                        btn.style.display = 'flex';
                        btn.style.alignItems = 'center';
                        btn.style.justifyContent = 'center';
                        btn.style.borderRadius = '50%';
                        btn.style.cursor = 'pointer';
                        btn.style.fontSize = '12px';
                        btn.style.boxShadow = '0 0 3px rgba(0,0,0,0.3)';
                        btn.style.zIndex = '9999';
                        btn.onclick = (e) => {
                            e.stopPropagation();
                            Swal.fire({
                                title: 'Hapus Kabel Ini?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Ya, hapus!',
                                cancelButtonText: 'Batal'
                            }).then(result => {
                                if (result.isConfirmed) {
                                    jsPlumb.deleteConnection(conn);
                                    lines = lines.filter(l => l.conn !== conn);
                                    isTopologyChanged = true;
                                }
                            });
                        };
                        return btn;
                    },
                    location: 0.75,
                    id: 'delete-button'
                }]);

                conn.bind('contextmenu', (conn, e) => {
                    e.preventDefault();
                    if (confirm('Hapus kabel ini?')) {
                        jsPlumb.deleteConnection(conn);
                        lines = lines.filter(l => l.conn !== conn);
                        isTopologyChanged = true;
                    }
                });

                lines.push({
                    from: source.id,
                    to: target.id,
                    cable: selectedCableName,
                    loss: lossCable,
                    length,
                    conn
                });

                // Update info loss panel
                document.getElementById('info-card').classList.remove('d-none');
                document.getElementById('total-loss').innerText = totalLoss.toFixed(2);
                document.getElementById('power-rx').innerText = powerRx.toFixed(2);
                document.getElementById('jalur-text').innerText = getFullPathToNode(target.id);

                // Output power list
                const pathNodeIds = getFullPathNodeIds(target.id);
                let outputListHtml = '';
                pathNodeIds.forEach(nodeId => {
                    const nodeEl = document.getElementById(nodeId);
                    if (nodeEl) {
                        const name = nodeEl.querySelector('strong')?.innerText || nodeId;
                        const power = nodeEl.dataset.power || '-';
                        outputListHtml +=
                            `<div class="output-power-node"><span class="node-name">${name}</span><span>=</span><span class="node-power">${power} dB</span></div>`;
                    }
                });
                document.getElementById('output-power-list').innerHTML = outputListHtml;

                actions.push({
                    type: 'add-connection',
                    conn,
                    from: source.id,
                    to: target.id
                });
                isTopologyChanged = true;
            }

            function connectNodeElementsByData(link, options = {}) {
                const source = document.getElementById(link.from);
                const target = document.getElementById(link.to);

                if (!source || !target) return;

                const length = parseFloat(link.length || 0);
                const cableType = link.cable || "Dropcore";
                const color = getColorByCableName(cableType);

                let cableLoss = cableType === 'Patchcord' ? 0.3 / 1000 : 0.2 / 1000;
                let lossCable = parseFloat(link.loss);
                if (!lossCable || lossCable === 0) {
                    lossCable = length * cableLoss;
                }

                const paint = {
                    stroke: color,
                    strokeWidth: 2,
                    dashstyle: cableType === 'Patchcord' ? '4 2' : undefined
                };

                const totalSplicing = parseInt(document.getElementById('splicing')?.value) || 0;

                const connOptions = {
                    source,
                    target,
                    anchors: ['AutoDefault', 'AutoDefault'],
                    endpoint: 'Blank',
                    connector: ['Flowchart', {
                        cornerRadius: 2,
                        stub: 30
                    }],
                    paintStyle: paint,
                    overlays: [
                        ['Label', {
                            label: `-${lossCable.toFixed(2)} dB`,
                            location: 0.5,
                            cssClass: 'myLabel',
                            css: {
                                color: 'red',
                                fontSize: '13px',
                                fontWeight: 'bold',
                                background: 'white',
                                padding: '2px 4px',
                                borderRadius: '4px',
                                border: '1px solid #ddd'
                            }
                        }],
                        ['Arrow', {
                            width: 12,
                            length: 12,
                            location: 1,
                            foldback: 0.7,
                            paintStyle: {
                                fill: color
                            }
                        }]
                    ],
                    _customMeta: { // <-- set di sini!
                        loss: lossCable,
                        name: cableType,
                        color: color,
                        length: length
                    }
                };
                const conn = jsPlumb.connect(connOptions);

                conn.addOverlay(['Custom', {
                    create: function() {
                        const btn = document.createElement('div');
                        btn.innerHTML = '&times;';
                        btn.title = 'Hapus kabel ini';
                        btn.style.background = 'red';
                        btn.style.color = 'white';
                        btn.style.width = '16px';
                        btn.style.height = '16px';
                        btn.style.display = 'flex';
                        btn.style.alignItems = 'center';
                        btn.style.justifyContent = 'center';
                        btn.style.borderRadius = '50%';
                        btn.style.cursor = 'pointer';
                        btn.style.fontSize = '12px';
                        btn.style.boxShadow = '0 0 3px rgba(0,0,0,0.3)';
                        btn.style.zIndex = '9999';
                        btn.onclick = (e) => {
                            e.stopPropagation();
                            Swal.fire({
                                title: 'Hapus Kabel Ini?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Ya, hapus!',
                                cancelButtonText: 'Batal'
                            }).then(result => {
                                if (result.isConfirmed) {
                                    jsPlumb.deleteConnection(conn);
                                    lines = lines.filter(l => l.conn !== conn);
                                    isTopologyChanged = true;
                                }
                            });
                        };
                        return btn;
                    },
                    location: 0.75,
                    id: 'delete-button'
                }]);

                conn.bind('contextmenu', (conn, e) => {
                    e.preventDefault();
                    if (confirm('Hapus kabel ini?')) {
                        jsPlumb.deleteConnection(conn);
                        lines = lines.filter(l => l.conn !== conn);
                        isTopologyChanged = true;
                    }
                });

                lines.push({
                    from: source.id,
                    to: target.id,
                    cable: cableType,
                    loss: lossCable,
                    length: length,
                    conn
                });

                // ⬇️ POWER HANDLING
                const fromPower = options.skipPowerCalc ?
                    parseFloat(source.dataset.power || 0) :
                    parseFloat(source.dataset.power || inputPower?.value || 0);

                let powerRx = fromPower - lossCable;

                if (!options.skipPowerCalc) {
                    target.dataset.power = powerRx.toFixed(2);
                    if (target.querySelector('.output-power')) {
                        target.querySelector('.output-power').innerText = `${powerRx.toFixed(2)} dB`;
                    }
                } else {
                    powerRx = parseFloat(target.dataset.power || 0); // jaga-jaga kalau memang udah diset
                }

                // Update detail cable loss dan splice loss
                document.getElementById('detail-cable-loss').innerText =
                    `Total cable loss: ${length}m x ${(cableLoss).toFixed(4)} dB/m = ${(length * cableLoss).toFixed(3)} dB`;
                document.getElementById('detail-splice-loss').innerText =
                    `Total loss splicing: ${totalSplicing} x 0.1dB = ${(totalSplicing * 0.1).toFixed(2)} dB`;
                document.getElementById('info-card')?.classList.remove('d-none');
                document.getElementById('total-loss').innerText = lossCable.toFixed(2);
                document.getElementById('power-rx').innerText = powerRx.toFixed(2);
                document.getElementById('jalur-text').innerText =
                    getFullPathToNode(target.id);
                actions.push({
                    type: 'add-connection',
                    conn,
                    from: source.id,
                    to: target.id
                });

                isTopologyChanged = true;
                console.log('✅ Koneksi berhasil dibuat!', conn);
            }

            // 🔄 Auto-render saat pertama kali buka lab
            setTimeout(() => {
                console.log('📦 Mulai render dari Blade...');
                console.log(`🧪 Lab: ${lab.name} by ${lab.author}`);
                console.log(`⚡ Default power: ${defaultPower}`);
                console.log(`🧱 Jumlah Node: ${rawNodes.length}`);
                console.log(`🔌 Jumlah Koneksi: ${rawConnections.length}`);

                rawNodes.forEach(node => addNodeFromDB(node));

                setTimeout(() => {
                    rawConnections.forEach(conn => {
                        connectNodeElementsByData(conn, {
                            skipPowerCalc: true
                        });
                    });

                    inputPower.value = defaultPower;
                    jsPlumb.repaintEverything();

                    // ✅ Pertama kali buka, anggap belum ada perubahan
                    isTopologyChanged = false;
                    console.log('✅ Topologi siap dirender!');
                }, 300);
            }, 100);

            jsPlumb.bind('beforeDrop', (info) => {
                return new Promise((resolve) => {
                    Swal.fire({
                        title: 'Pilih Jenis Kabel',
                        html: `
                    <div class="text-start mb-2">Panjang Kabel (meter)</div>
                    <input id="swal-length" type="number" class="swal2-input" value="50">
                    <div class="text-start mb-2 mt-2">Jenis Kabel</div>
                    <select id="swal-cable" class="swal2-select">
                        <option value="dropcore" selected>Dropcore (0.2 dB/km)</option>
                        <option value="patchcord">Patchcord (0.3 dB/km)</option>
                    </select>
                `, // isian swal
                        preConfirm: () => {
                            const length = validateNumberInput(document.getElementById(
                                'swal-length').value, 'Cable Length');
                            const type = document.getElementById('swal-cable').value;
                            if (!length) return false;
                            return {
                                length,
                                type
                            };
                        }
                    }).then(result => {
                        if (result.isConfirmed) {
                            const {
                                length,
                                type
                            } = result.value;

                            selectedCableLoss = type === 'dropcore' ? 0.2 / 1000 : 0.3 /
                                1000;
                            selectedCableColor = type === 'dropcore' ? 'black' : 'yellow';
                            selectedCableName = type === 'dropcore' ? 'Dropcore' :
                                'Patchcord';

                            // ❗⛔ JANGAN LANJUTKAN DARI JSPLUMB
                            // ❗✅ KITA YANG HANDLE MANUAL
                            connectNodeElements(info.source, info.target, length);

                            resolve(false); // BATALKAN connect default JSPlumb
                        } else {
                            resolve(false); // User batalin
                        }
                    });
                });
            });

            /**
             * Menghapus aksi terakhir (undo).
             */
            window.undoAction = function() {
                if (actions.length === 0) return;
                const last = actions.pop();
                if (last.type === 'add-connection') {
                    const lineIndex = lines.findIndex(l => l.conn === last.conn);
                    if (lineIndex !== -1) {
                        jsPlumb.deleteConnection(lines[lineIndex].conn);
                        lines.splice(lineIndex, 1);
                    }
                } else if (last.type === 'add-node') {
                    const node = last.node;
                    lines = lines.filter(conn => {
                        if (conn.from === node.id || conn.to === node.id) {
                            jsPlumb.deleteConnection(conn.conn);
                            return false;
                        }
                        return true;
                    });
                    jsPlumb.deleteEveryEndpoint(node);
                    jsPlumb.remove(node);
                    nodes = nodes.filter(n => n.id !== node.id);
                }
                document.getElementById('info-card').classList.add('d-none');
                isTopologyChanged = true;
            };

            /**
             * Mengatur ulang topologi.
             */
            window.resetTopology = function() {
                Swal.fire({
                    title: 'Are you sure you want to reset?',
                    text: 'All nodes and connections will be deleted!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, reset!',
                    confirmButtonColor: 'red',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        lines.forEach(link => jsPlumb.deleteConnection(link.conn));
                        lines = [];
                        nodes = [];
                        mapCanvas.innerHTML = '';
                        nodeId = 0;
                        selectedNode = null;
                        isTopologyChanged = true;
                        document.getElementById('info-card').classList.add('d-none');
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'The topology has been reset!',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                });
            };

            /**
             * Menambahkan node dari database.
             * @param {Object} node - Data node dari database.
             */
            window.addNodeFromDB = function(node) {
                node.type = node.type || 'Client';

                // Pastikan node.id itu unik
                if (!node.id) {
                    node.id = `node-${nodeId++}`;
                } else {
                    // Cek apakah ID ini sudah ada di canvas
                    const existing = document.getElementById(node.id);
                    if (existing) {
                        node.id = `node-${nodeId++}`; // Ganti ID biar gak duplikat
                    } else {
                        // Update nodeId supaya gak bentrok di masa depan
                        const match = node.id.match(/node-(\d+)/);
                        if (match) {
                            const num = parseInt(match[1]);
                            if (!isNaN(num) && num >= nodeId) {
                                nodeId = num + 1;
                            }
                        }
                    }
                }

                const el = createNodeElement(node);
                if (!el) return; // Buat jaga-jaga kalau gagal bikin elemen

                nodes.push({
                    id: el.id,
                    type: node.type,
                    loss: parseFloat(node.loss || 0),
                    power: node.power !== undefined && node.power !== null ? parseFloat(node.power) :
                        undefined,
                    top: node.top,
                    left: node.left
                });

                isTopologyChanged = true;
            };

            /**
             * Menggambar koneksi dari database.
             * @param {Object} conn - Data koneksi dari database.
             */
            window.drawConnectionFromDB = function(conn) {
                const fromEl = document.getElementById(conn.from);
                const toEl = document.getElementById(conn.to);
                if (!fromEl || !toEl) return;
                const color = conn.cable === 'Dropcore' ? 'black' : 'yellow';
                const jsConn = jsPlumb.connect({
                    source: fromEl,
                    target: toEl,
                    anchors: ['AutoDefault', 'AutoDefault'],
                    endpoint: 'Blank',
                    connector: ['Flowchart', {
                        cornerRadius: 2,
                        stub: 30
                    }],
                    paintStyle: {
                        stroke: color,
                        strokeWidth: 2,
                        dashstyle: conn.cable === 'Patchcord' ? '4 2' : undefined
                    },
                    overlays: [
                        ['Label', {
                            label: `-${(conn.loss || 0).toFixed(3)} dB`,
                            location: 0.5,
                            cssClass: 'myLabel',
                            css: {
                                color: 'red',
                                fontSize: '12px'
                            }
                        }]
                    ]
                });

                // Tambahkan ini!
                jsConn._customMeta = {
                    loss: conn.loss,
                    name: conn.cable,
                    color: color,
                    length: conn.length
                };

                jsConn.bind('contextmenu', (conn, e) => {
                    e.preventDefault();
                    if (confirm('Hapus kabel ini?')) {
                        jsPlumb.deleteConnection(conn);
                        lines = lines.filter(l => l.conn !== conn);
                        isTopologyChanged = true;
                    }
                });

                lines.push({
                    from: fromEl.id,
                    to: toEl.id,
                    cable: conn.cable,
                    loss: conn.loss,
                    length: conn.length,
                    conn: jsConn
                });
                isTopologyChanged = true;
            };

            /**
             * Menghitung rugi-rugi untuk semua koneksi.
             */
            window.calculateAllLoss = function() {
                if (options.skipIfPowerExists) {
                    const allNodesHavePower = nodes.every(n => typeof n.power !== 'undefined' && n.power !==
                        null);
                    if (allNodesHavePower) {
                        console.log('[INFO] Skip loss calculation — power udah ada semua');
                        return;
                    }
                }
                lines.forEach(conn => {
                    const from = document.getElementById(conn.from);
                    const to = document.getElementById(conn.to);
                    const loss = parseFloat(conn.loss || 0);
                    const sourcePower = parseFloat(from.dataset.power || inputPower?.value || 0);
                    const powerRx = sourcePower - loss;
                    if (to.querySelector('.output-power')) {
                        to.querySelector('.output-power').innerText = `${powerRx.toFixed(2)} dB`;
                    }
                });
            };

            /**
             * Memuat topologi dari database.
             * @param {string} id - ID topologi.
             */
            /**
             * Mengekspor topologi ke file JSON.
             */
            window.exportTopology = function() {
                console.log('✅ Export function called');
                // Pastikan variabel global
                if (typeof nodes === 'undefined' || typeof lines === 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Export Failed',
                        text: 'Node atau koneksi belum dibuat.'
                    });
                    return;
                }

                const topology = {
                    nodes,
                    connections: lines.map(link => ({
                        from: link.from,
                        to: link.to,
                        cable: link.cable,
                        loss: link.loss,
                        length: link.length
                    })),
                    power: parseFloat(inputPower?.value || 0),
                    splicing: parseInt(inputSplicing?.value || 0), // ✅ Tambahan
                    connectors: parseInt(document.getElementById('connectors')?.value || 0), // ✅ Tambahan
                    name: lab?.name || 'topologi',
                    author: lab?.author || '',
                    description: lab?.description || ''
                };


                if (topology.nodes.length === 0 || topology.connections.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Data to Export',
                        text: 'Silakan tambahkan node dan koneksi terlebih dahulu.'
                    });
                    return;
                }

                const filename = `topologi-${(topology.name || 'export').replace(/\\s+/g, '_')}.json`;
                const dataStr =
                    `data:text/json;charset=utf-8,${encodeURIComponent(JSON.stringify(topology, null, 2))}`;

                const dlAnchor = document.createElement('a');
                dlAnchor.setAttribute('href', dataStr);
                dlAnchor.setAttribute('download', filename);
                document.body.appendChild(dlAnchor);
                dlAnchor.click();
                dlAnchor.remove();

                isTopologyChanged = false;
            };

            window.importTopology = function(file) {
                if (!file) return;

                const reader = new FileReader();
                reader.onload = function(e) {
                    try {
                        const imported = JSON.parse(e.target.result);

                        // Reset canvas dulu
                        jsPlumb.deleteEveryEndpoint();
                        jsPlumb.deleteEveryConnection();
                        mapCanvas.innerHTML = '';
                        nodes = [];
                        lines = [];

                        nodeId = 0;
                        selectedNode = null;
                        actions = [];
                        lab = {
                            name: '',
                            author: '',
                            description: ''
                        };
                        inputPower.value = imported.power || 0;

                        // Load data node dari JSON
                        imported.nodes?.forEach(node => addNodeFromDB(node));

                        // Delay agar node sudah dirender dulu
                        setTimeout(() => {
                            window.__skipPowerCalcOnImport__ = true;
                            imported.connections?.forEach(link => connectNodeElementsByData(link, {
                                skipPowerCalc: true
                            }));
                            window.__skipPowerCalcOnImport__ = false;

                            // Ambil power dari JSON langsung, bukan dihitung ulang
                            inputPower.value = imported.power || 0;

                            // Simpan info lab
                            lab.name = imported.name || '';
                            lab.author = imported.author || '';
                            lab.description = imported.description || '';

                            // Jangan hitung ulang biar power asli dari file gak ketiban!
                            // calculateAllLoss(); ← ini sengaja DIHAPUS atau DIKOMENTARI

                            jsPlumb.repaintEverything();

                            Swal.fire({
                                icon: 'success',
                                title: 'Import Berhasil',
                                text: `Berhasil memuat topologi "${lab.name}"`
                            });

                            isTopologyChanged = true;
                        }, 300);
                    } catch (err) {
                        console.error(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Import',
                            text: 'File tidak valid atau rusak.'
                        });
                    }
                };

                reader.readAsText(file);
                isTopologyChanged = true;
                document.getElementById('import-file').value = ''; // Reset file input sesuai ID input
            };

            /**
             * Mengumpulkan dan menyimpan topologi ke server.
             */
            window.gatherAndSaveTopology = async function() {
                console.log('Save button clicked');
                const topology = {
                    nodes: Array.from(document.querySelectorAll('.position-absolute')).map(el => ({
                        id: el.id,
                        type: el.dataset.type || '',
                        loss: parseFloat(el.dataset.loss || 0),
                        power: parseFloat(el.dataset.power || 0),
                        top: el.style.top || el.offsetTop + 'px',
                        left: el.style.left || el.offsetLeft + 'px'
                    })),
                    connections: lines.map(link => ({
                        from: link.from,
                        to: link.to,
                        cable: link.cable,
                        loss: link.loss,
                        length: link.length
                    })),
                    power: parseFloat(inputPower?.value || 0),
                    splicing: parseInt(inputSplicing?.value || 0), // ✅ Tambahan
                    connectors: parseInt(document.getElementById('connectors')?.value ||
                        0), // ✅ Tambahan
                    name: '{{ $lab['name'] ?? '' }}',
                    author: '{{ $lab['author'] ?? '' }}',
                    description: '{{ $lab['description'] ?? '' }}'
                };

                console.log('Topology data:', topology);

                const labId = mapCanvas.dataset.labId;
                console.log('Lab ID:', labId);
                if (topology.nodes.length === 0 || topology.connections.length === 0) {
                    console.log('No nodes or connections to save');
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Data to Save',
                        text: 'Please add nodes and connections before saving.'
                    });
                    return;
                }

                try {
                    console.log('Sending fetch request to /lab/' + labId + '/save-both');
                    const res = await fetch(`/lab/${labId}/save-both`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .content
                        },
                        body: JSON.stringify(topology)
                    });
                    console.log('Response status:', res.status);
                    const data = await res.json();
                    console.log('Response data:', data);
                    if (data.success) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'The topology is saved into a lab file!',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        isTopologyChanged = false;
                    } else {
                        throw new Error(data.message || 'Failed to save topology');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed to Save',
                        text: error.message || 'An error occurred while saving the topology.'
                    });
                }
            };

            // 🛑 Cegah user nutup tab/kembali kalau ada perubahan
            window.addEventListener('beforeunload', function(e) {
                if (isTopologyChanged && !isTryingToLeave) {
                    e.preventDefault();
                    e.returnValue = ''; // Wajib ada buat trigger native dialog
                }
            });

            // 🔁 Tangkap semua link internal di halaman
            document.querySelectorAll('a[href]').forEach(link => {
                link.addEventListener('click', function(e) {
                    const target = link.getAttribute('target');

                    // Lewati kalau link ke tab baru
                    if (target === '_blank') return;

                    // Lewati link anchor dalam halaman (#)
                    if (link.getAttribute('href').startsWith('#')) return;

                    if (isTopologyChanged) {
                        e.preventDefault();

                        Swal.fire({
                            title: 'Keluar tanpa menyimpan?',
                            text: 'Perubahan Anda belum disimpan!',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, keluar',
                            cancelButtonText: 'Batal'
                        }).then(result => {
                            if (result.isConfirmed) {
                                isTryingToLeave = true;
                                window.location.href = link.href;
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
