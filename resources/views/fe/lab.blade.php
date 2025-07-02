<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link href="{{ asset('fe/img/hyp-tam.png') }}" rel="icon">
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
