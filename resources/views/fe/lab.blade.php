<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link href="{{ asset('fe/img/hyp-tam.png') }}" rel="icon">
    <!-- Bootstrap Icons CDN (harus di <head> atau sebelum panel ini) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <script src="https://unpkg.com/jsplumb@2.15.6/dist/js/jsplumb.min.js"></script>
    <link rel="stylesheet" href="{{ asset('assets/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/lab.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* Custom scrollbar for all scrollable elements */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(90, 90, 90, 0.3);
            border-radius: 8px;
            transition: background 0.3s ease;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(90, 90, 90, 0.6);
        }

        /* Firefox support */
        * {
            scrollbar-width: thin;
            scrollbar-color: rgba(90, 90, 90, 0.3) transparent;
        }


        /* Obstacle Icon */
        .kb-obstacle-icon {
            position: absolute;
            top: 18px;
            right: 18px;
            z-index: 120;
            background: #10BC69;
            color: #fff;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            font-size: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 10px rgba(16, 188, 105, 0.13);
            cursor: pointer;
            transition: box-shadow 0.2s, background 0.2s;
        }

        .kb-obstacle-icon:hover {
            background: #13d77d;
            box-shadow: 0 4px 18px rgba(16, 188, 105, 0.24);
        }

        /* Obstacle Panel */
        .kb-obstacle-panel {
            position: absolute;
            top: 70px;
            right: 26px;
            min-width: 320px;
            max-width: 370px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.15);
            z-index: 130;
            font-size: 15px;
            border: 1px solid #f0f0f0;
            animation: fadeInKbPanel 0.23s;
        }

        @keyframes fadeInKbPanel {
            from {
                opacity: 0;
                transform: translateY(-14px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Obstacle Panel Header */
        .kb-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #10BC69;
            color: #fff;
            border-radius: 16px 16px 0 0;
            padding: 13px 20px 11px 18px;
            font-weight: 700;
            font-size: 1.13rem;
            letter-spacing: 0.03em;
        }

        .kb-close {
            background: #fff2;
            border: none;
            font-size: 1.7rem;
            color: #fff;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            line-height: 0.85;
            cursor: pointer;
            margin-left: 8px;
            transition: background 0.2s, color 0.2s;
        }

        .kb-close:hover {
            background: #fff5;
            color: #ff5555;
        }

        /* Obstacle Panel Tabs */
        .kb-tabs {
            display: flex;
            background: #f7f7f7;
            border-radius: 0 0 12px 12px;
            padding: 0 4px;
            border-bottom: 1px solid #e0e0e0;
        }

        .kb-tab {
            flex: 1 1 0;
            background: none;
            border: none;
            font-weight: 600;
            color: #10BC69;
            padding: 10px 0 8px 0;
            cursor: pointer;
            border-radius: 7px 7px 0 0;
            margin: 0 2px -1px 2px;
            font-size: 1rem;
            transition: background 0.2s, color 0.2s;
        }

        .kb-tab.active,
        .kb-tab:hover {
            background: #10BC69;
            color: #fff;
        }

        /* Obstacle Panel Content */
        .kb-content {
            padding: 10px 20px 16px 20px;
        }

        .kb-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .kb-list li {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 13px;
        }

        .kb-icon {
            font-size: 1.25em;
            flex-shrink: 0;
            margin-top: 3px;
        }

        .kb-list-title {
            font-weight: 600;
            color: #222;
            display: block;
        }

        .kb-list-desc {
            color: #555;
            font-size: 0.97em;
            margin-top: 1px;
            display: block;
        }

        /* Obstacle Panel Responsive */
        @media (max-width: 480px) {
            .kb-obstacle-panel {
                right: 10px;
                min-width: 90vw;
                max-width: 98vw;
                font-size: 14px;
                padding: 0;
            }

            .kb-content {
                padding: 10px 8px 10px 12px;
            }
        }

        /* Node Label */
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

        /* Info Card */
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
</head>

<body class="bg-light">

    {{-- Navbar --}}
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container d-flex justify-content-center text-center">
            <div class="d-flex align-items-center mt-3 gap-2">
                <h3 class="fw-bold" style="color: #5f687b;">{{ $lab['name'] }}</h3>
                <button class="btn btn-sm btn-outline-secondary" onclick="showEditLabForm()" title="Edit Lab">
                    <i class="bi bi-info-circle"></i>
                </button>
            </div>
        </div>
    </nav>
    <hr> {{-- Dynamic page content --}}
    <div class="container">
        @yield('content')
    </div>

    {{-- Footer --}}
    <footer class="text-center mt-5 text-muted small">
        <hr>
        &copy; {{ date('Y') }} Splitter FTTH Tools. All rights reserved.
    </footer>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/leader-line@1.0.7/leader-line.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>
