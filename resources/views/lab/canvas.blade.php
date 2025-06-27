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
                    <i class="fas fa-broadcast-tower me-1"></i> OLT
                </button>
                <button class="btn btn-sm btn-light text-dark fw-bold" onclick="addNode('Splitter')">
                    <i class="fas fa-code-branch me-1"></i> Splitter
                </button>
                {{-- <button class="btn btn-sm btn-outline-primary text-dark" onclick="addNode('ODP')">
                    <i class="fas fa-network-wired me-1"></i> ODP
                </button> --}}
                <button class="btn btn-sm btn-light text-dark fw-bold" onclick="addNode('Client')">
                    <i class="fas fa-user me-1"></i> Client
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

                <label class="form-label text-white">Splicing</label>
                <input type="number" id="splicing" class="form-control form-control-sm mb-2" value="1">
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
                <button class="btn btn-sm"
                    style="background: linear-gradient(87deg, #2d93ce 0, #107abc 100%); border: none;"
                    onclick="exportTopology()">⬇ Export (.json)</button>
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
    </style>

    <!-- Informasi Loss Panel -->
    <div id="info-card" class="card shadow-sm border border-info d-none"
        style="position: fixed; top: 360px; right: 20px; min-width: 250px;transition: all 0.3s ease-in-out; z-index: 1;">
        <div class="card-body">
            <h5 class="card-title text-info">📊 Informasi Loss</h5>
            <hr>
            <p>Total Loss: <strong id="total-loss" class="text-danger">-</strong> dB</p>
            <p>Output Power: <strong id="power-rx" class="text-primary">-</strong> dBm</p>
            <p>Jalur: <span id="jalur-text" class="text-muted">-</span></p>
            <div id="loss-status" class="mt-3"></div>
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
                    title: '{{ session('success') }}'
                });
            });
        </script>
    @endif

    {{-- Modal untuk update name lab, author & description --}}
    <script>
        function showEditLabForm() {
            Swal.fire({
                title: '🛠 Edit Informasi Lab',
                html: `
                    <div class="text-start mb-2"><label class="form-label fw-bold">Lab Name</label>
                        <input id="lab-name" class="form-control" value="{{ $lab['name'] }}">
                    </div>
                    <div class="text-start mb-2"><label class="form-label fw-bold">Author</label>
                        <input id="lab-author" class="form-control" value="{{ $lab['author'] }}">
                    </div>
                    <div class="text-start mb-2"><label class="form-label fw-bold">Description</label>
                        <textarea id="lab-description" class="form-control" rows="3">{{ $lab['description'] }}</textarea>
                    </div>
                `,
                width: 600,
                confirmButtonText: '💾 Save Changes',
                confirmButtonColor: '#10BC69',
                cancelButtonText: 'Batal',
                showCancelButton: true,
                focusConfirm: false,
                showClass: {
                    popup: 'animate_animated animate_fadeInDown'
                },
                hideClass: {
                    popup: 'animate_animated animate_fadeOutUp'
                },
                customClass: {
                    popup: 'animate_animated animate_fadeInDown'
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
                        Swal.showValidationMessage('name dan Author tidak boleh kosong!');
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
    <!-- Ganti LeaderLine dengan jsPlumb -->
    <script src="https://unpkg.com/jsplumb/dist/js/jsplumb.min.js"></script>
    <script>
        const inputPower = document.getElementById('input-power');
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

            // Helper: Validasi input numerik
            function validateNumberInput(value, fieldName) {
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
            

            // Helper: Membuat elemen node
            function createNodeElement(nodeData) {
                const el = document.createElement('div');
                el.classList.add('position-absolute', 'p-2', 'bg-white', 'border', 'rounded', 'text-center');
                el.setAttribute('id', nodeData.id || `node-${nodeId++}`);
                // --- PATCH posisi ---
                el.style.left = (typeof nodeData.left === 'number')
    ? `${nodeData.left}px`
    : (typeof nodeData.left === 'string' && nodeData.left.endsWith('px'))
        ? nodeData.left
        : (!isNaN(Number(nodeData.left)) && nodeData.left !== '' && nodeData.left !== undefined)
            ? `${Number(nodeData.left)}px`
            : `${mapCanvas.clientWidth / 2 - 50}px`;

el.style.top = (typeof nodeData.top === 'number')
    ? `${nodeData.top}px`
    : (typeof nodeData.top === 'string' && nodeData.top.endsWith('px'))
        ? nodeData.top
        : (!isNaN(Number(nodeData.top)) && nodeData.top !== '' && nodeData.top !== undefined)
            ? `${Number(nodeData.top)}px`
            : `${mapCanvas.clientHeight / 2 - 25}px`;
                            el.dataset.loss = nodeData.loss || 0;
                            el.dataset.power = nodeData.power || 0;
                            el.dataset.type = nodeData.type || 'Client';
                            el.innerHTML = `
                        <button class="btn btn-danger btn-sm btn-delete-node" style="position: absolute; top: -8px; right: -8px; z-index: 2; border-radius: 50%; width: 22px; height: 22px; padding: 0; font-size: 14px; line-height: 1;" title="Hapus Node">×</button>
                        <strong>${nodeData.type}</strong>
                        <div class="output-power" style="font-size: 12px; color: green;">${nodeData.power ? parseFloat(nodeData.power).toFixed(2) : ''} dB</div>
                    `;

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
                            // 🧹 Hapus koneksi yang hanya terhubung ke node ini
                            const toDelete = lines.filter(conn => conn.from === el.id || conn.to === el
                                .id);
                            toDelete.forEach(conn => jsPlumb.deleteConnection(conn.conn));
                            lines = lines.filter(conn => conn.from !== el.id && conn.to !== el.id);

                            // 🧹 Hapus endpoint dan node-nya
                            jsPlumb.removeAllEndpoints(el); // Lebih aman daripada deleteEveryEndpoint
                            mapCanvas.removeChild(el); // Hapus dari DOM

                            // 🧹 Hapus dari array nodes
                            nodes = nodes.filter(n => n.id !== el.id);

                            // 🧠 Hapus dari state terpilih
                            if (selectedNode && selectedNode.id === el.id) {
                                selectedNode = null;
                            }

                            isTopologyChanged = true;
                            document.getElementById('info-card').classList.add('d-none');
                        }
                    });
                };


                // Node-to-node click
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
                            if (result.isConfirmed) {
                                const {
                                    length,
                                    type
                                } = result.value;
                                selectedCableLoss = type === 'dropcore' ? 0.2 / 1000 : 0.3 / 1000;
                                selectedCableColor = type === 'dropcore' ? 'black' : 'yellow';
                                selectedCableName = type === 'dropcore' ? 'Dropcore' : 'Patchcord';
                                connectNodeElements(selectedNode, el, length);
                                selectedNode.classList.remove('border-primary');
                                selectedNode = null;
                            }
                        });
                    } else {
                        el.classList.remove('border-primary');
                        selectedNode = null;
                    }
                };

                // Tambahkan endpoint
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
                    type,
                    loss,
                    power
                };
                const el = createNodeElement(nodeData);
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
                const lossTarget = parseFloat(target.dataset.loss || 0);
                const totalConnectors = validateNumberInput(document.getElementById('connectors')?.value,
                    'Connectors') || 0;
                const totalSplicing = validateNumberInput(document.getElementById('splicing')?.value, 'Splicing') ||
                    0;
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

                // 🔥 Tambahin overlay tombol hapus setelah koneksi dibuat
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

                document.getElementById('info-card').classList.remove('d-none');
                document.getElementById('total-loss').innerText = totalLoss.toFixed(2);
                document.getElementById('power-rx').innerText = powerRx.toFixed(2);
                document.getElementById('jalur-text').innerText =
                    `${source.querySelector('strong').innerText} → ${target.querySelector('strong').innerText}`;
                actions.push({
                    type: 'add-connection',
                    conn,
                    from: source.id,
                    to: target.id
                });
                isTopologyChanged = true;
            }

            function connectNodeElementsByData(link) {
                const source = document.getElementById(link.from);
                const target = document.getElementById(link.to);

                console.log('💡 connectNodeElementsByData');
                console.log('🧩 source?', source);
                console.log('🧩 target?', target);

                if (!source || !target) return;
                const color = getColorByCableName(link.cable);
                const lossCable = link.loss || 0;
                const paint = {
                    stroke: color,
                    strokeWidth: 2,
                    dashstyle: link.cable === 'Patchcord' ? '4 2' : undefined
                };
                            const conn = jsPlumb.connect({
                    source,
                    target,
                    anchors: ['AutoDefault', 'AutoDefault'],
                    endpoint: 'Blank',
                    connector: ['Flowchart', { cornerRadius: 2, stub: 30 }],
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
                            paintStyle: { fill: color }
                        }]
                    ]
                });

                // 🔥 Tambahin overlay tombol hapus setelah koneksi dibuat
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

                document.getElementById('info-card').classList.remove('d-none');
            document.getElementById('total-loss').innerText = lossCable.toFixed(2);

            const fromPower = parseFloat(source.dataset.power || inputPower?.value || 0);
            const powerRx = fromPower - lossCable;
            if (target.querySelector('.output-power')) {
                target.querySelector('.output-power').innerText = `${powerRx.toFixed(2)} dB`;
            }
            document.getElementById('power-rx').innerText = powerRx.toFixed(2);
            document.getElementById('jalur-text').innerText =
                `${source.querySelector('strong').innerText} → ${target.querySelector('strong').innerText}`;
                        actions.push({
                            type: 'add-connection',
                            conn,
                            from: source.id,
                            to: target.id
                        });
                        isTopologyChanged = true;

                        console.log('Koneksi berhasil dibuat?', conn);
                    }


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
                `,
                        focusConfirm: false,
                        confirmButtonText: 'Hubungkan',
                        showCancelButton: true,
                        cancelButtonText: 'Batal',
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
                            info.connection._customMeta = {
                                length,
                                type,
                                color: selectedCableColor,
                                name: selectedCableName,
                                loss: length * selectedCableLoss
                            };
                            resolve(true);
                        } else {
                            resolve(false);
                        }
                    });
                });
            });

            jsPlumb.bind('connection', (info) => {
                const conn = info.connection;
                const meta = conn._customMeta || {};
                const lossCable = meta.loss || 0;
                const color = meta.color || 'black';

                conn.setPaintStyle({
                    stroke: color,
                    strokeWidth: 2,
                    dashstyle: meta.name === 'Patchcord' ? '4 2' : undefined
                });
                conn.connector.setOptions({
                    type: 'Flowchart',
                    cornerRadius: 2,
                    stub: 30
                });

                conn.removeAllOverlays();
                conn.addOverlay(['Label', {
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
                }]);

                conn.addOverlay(['Arrow', {
                    width: 12,
                    length: 12,
                    location: 1,
                    foldback: 0.7,
                    paintStyle: {
                        fill: color
                    }
                }]);

                // 🔥 Tambahan tombol X hapus kabel
                conn.addOverlay(['Custom', {
                    create: function() {
                        const btn = document.createElement('div');
                        btn.innerHTML = '&times;';
                        btn.title = 'Hapus kabel ini';
                        btn.style.position = 'relative';
                        btn.style.zIndex = '9999';
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





                const source = conn.source;
                const target = conn.target;
                lines.push({
                    from: source.id,
                    to: target.id,
                    cable: meta.name || selectedCableName,
                    loss: lossCable,
                    length: meta.length || 0,
                    conn
                });

                document.getElementById('info-card').classList.remove('d-none');
                document.getElementById('total-loss').innerText = lossCable.toFixed(2);
                const fromPower = parseFloat(source.dataset.power || inputPower?.value || 0);
                const powerRx = fromPower - lossCable;
                if (target.querySelector('.output-power')) {
                    target.querySelector('.output-power').innerText = `${powerRx.toFixed(2)} dB`;
                }
                document.getElementById('power-rx').innerText = powerRx.toFixed(2);
                document.getElementById('jalur-text').innerText =
                    `${source.querySelector('strong').innerText} → ${target.querySelector('strong').innerText}`;
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
                const el = createNodeElement(node);
                nodes.push({
                    id: el.id,
                    type: node.type,
                    loss: parseFloat(node.loss || 0),
                    power: parseFloat(node.power || 0),
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
                            label: `-${(conn.loss || 0).toFixed(2)} dB`,
                            location: 0.5,
                            cssClass: 'myLabel',
                            css: {
                                color: 'red',
                                fontSize: '12px'
                            }
                        }]
                    ]
                });

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
            window.loadTopology = async function(id) {
                console.log('📥 Loading topology for ID:', id);
                try {
                    const res = await fetch(`/topologi/load/${id}`);
                    const data = await res.json();

                    if (!data.nodes || !data.connections) {
                        console.warn('⚠️ Data kosong (nodes atau connections)');
                        return;
                    }

                    // 🔄 Reset semua dulu
                    jsPlumb.deleteEveryEndpoint();
                    jsPlumb.deleteEveryConnection();
                    mapCanvas.innerHTML = '';
                    lines = [];
                    nodes = [];
                    nodeId = 0;
                    selectedNode = null;

                    // 🧱 Buat node dari DB
                    data.nodes.filter(n => n && n.id && n.type).forEach(node => {
                        node.type = node.type || 'Client';
                        addNodeFromDB(node);
                    });

                    // ⏳ Tunggu render node selesai
                    setTimeout(() => {
                        requestAnimationFrame(() => {
                            console.log('🔗 Mulai menggambar koneksi...');
                            data.connections.forEach(link => {
                                console.log(
                                    `🔌 Connecting: ${link.from} -> ${link.to}`);
                                connectNodeElementsByData(link);
                            });

                            // 🌟 Set power & kalkulasi ulang
                            if (inputPower) inputPower.value = data.power || 0;
                            calculateAllLoss();

                            // 🖌️ Repaint ulang setelah semuanya selesai
                            jsPlumb.repaintEverything();
                        });
                    }, 300); // Delay kecil agar semua node udah masuk ke DOM
                    console.log('✅ Topology loaded successfully!');

                } catch (error) {
                    console.error('❌ Load topology error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed to Load Topology',
                        text: 'Please try again later.'
                    });
                }
            };


            /**
             * Mengekspor topologi ke file JSON.
             */
            window.exportTopology = function() {
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
                    name: lab.name || '',
                    author: lab.author || '',
                    description: lab.description || ''
                };

                if (topology.nodes.length === 0 || topology.connections.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Data to Export',
                        text: 'Please add nodes and connections before exporting.'
                    });
                    return;
                }

                const dataStr =
                    `data:text/json;charset=utf-8,${encodeURIComponent(JSON.stringify(topology, null, 2))}`;
                const dlAnchor = document.createElement('a');
                dlAnchor.setAttribute('href', dataStr);
                dlAnchor.setAttribute('download', `topologi-${topology.name.replace(/\s+/g, '_')}.json`);
                document.body.appendChild(dlAnchor);
                dlAnchor.click();
                dlAnchor.remove();
                isTopologyChanged = false;
            };

            /**
             * Mengumpulkan dan menyimpan topologi ke server.
             */
            window.gatherAndSaveTopology = async function() {
                console.log('Save button clicked');
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

            window.onload = function() {
                const labId = document.getElementById('map-canvas').dataset.labId;
                console.log('Auto-load lab ID:', labId);
                if (labId) loadTopology(labId);
            };

            window.onbeforeunload = function() {
                if (isTopologyChanged) return 'Perubahan Anda belum disimpan. Yakin ingin keluar?';
            };
        });
    </script>
@endpush
