<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}{{ isset($title) ? ' | ' . $title : '' }}</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">
    @stack('styles')
    <style>
    /* ========== GLOBAL SMOOTH ========== */
    * {
        transition: background-color .25s ease, 
                    color .25s ease, 
                    border-color .25s ease,
                    box-shadow .25s ease;
    }

    /* ========== LIGHT MODE BASE ========== */
    body {
        background-color: #f4f6f9;
        color: #1f2937;
    }

    /* Header Light */
    .main-header {
        background-color: #ffffff !important;
        border-bottom: 1px solid #e5e7eb !important;
    }

    .main-header .navbar-nav .nav-link {
        color: #374151 !important;
    }

    /* Sidebar Light */
    .sidebar-theme {
        background-color: #1e293b !important;
        border-right: none;
    }

    /* Brand Link Light */
    .brand-link {
        background: #1e293b !important;
        border-bottom: 1px solid #334155 !important;
    }

    .brand-link .brand-text {
        color: #ffffff !important;
        font-weight: 600;
        letter-spacing: .3px;
    }

    .brand-link i {
        color: #ffffff !important;
    }

    /* Nav links Light */
    .sidebar-theme .nav-link {
        color: #cbd5e1 !important;
        border-radius: 8px;
        margin: 4px 8px;
    }

    .sidebar-theme .nav-link:hover {
        background-color: #334155 !important;
        color: #ffffff !important;
    }

    .sidebar-theme .nav-link.active {
        background-color: #3b82f6 !important;
        color: #ffffff !important;
    }

    .sidebar-theme .nav-link i {
        color: #cbd5e1 !important;
    }

    .sidebar-theme .nav-link:hover i,
    .sidebar-theme .nav-link.active i {
        color: #ffffff !important;
    }

    .sidebar-theme .nav-header {
        color: #94a3b8 !important;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 12px 6px 12px;
        margin-top: 8px;
    }

    /* Content Wrapper Light */
    .content-wrapper {
        background: #f4f6f9 !important;
    }

    /* Card Light */
    .card {
        background: #ffffff !important;
        border-radius: 12px !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08) !important;
        border: none !important;
    }

    .card-header {
        background: #ffffff !important;
        border-bottom: 1px solid #e5e7eb !important;
    }

    /* Table Light */
    .table thead th {
        background-color: #f9fafb !important;
        color: #374151 !important;
        font-weight: 600;
        border: none !important;
    }

    .table tbody td {
        color: #1f2937 !important;
    }

    .table-hover tbody tr:hover {
        background-color: #f9fafb !important;
    }

    /* Text colors Light */
    .text-muted {
        color: #6b7280 !important;
    }

    /* Breadcrumb Light */
    .breadcrumb-item a {
        color: #3b82f6 !important;
    }

    .breadcrumb-item.active {
        color: #6b7280 !important;
    }

    /* ========== DARK MODE ========== */
    body.dark-mode {
        background-color: #0f172a !important;
        color: #e2e8f0 !important;
    }

    /* Header Dark */
    body.dark-mode .main-header {
        background-color: #1e293b !important;
        border-bottom: 1px solid #334155 !important;
    }

    body.dark-mode .main-header .navbar-nav .nav-link {
        color: #e2e8f0 !important;
    }

    /* Sidebar Dark */
    body.dark-mode .sidebar-theme {
        background-color: #0f172a !important;
        border-right: 1px solid #1e293b;
    }

    /* Brand Link Dark */
    body.dark-mode .brand-link {
        background: #0f172a !important;
        border-bottom: 1px solid #1e293b !important;
    }

    body.dark-mode .brand-link .brand-text {
        color: #ffffff !important;
    }

    body.dark-mode .brand-link i {
        color: #ffffff !important;
    }

    /* Nav links Dark */
    body.dark-mode .sidebar-theme .nav-link {
        color: #cbd5e1 !important;
    }

    body.dark-mode .sidebar-theme .nav-link:hover {
        background-color: #1e293b !important;
        color: #ffffff !important;
    }

    body.dark-mode .sidebar-theme .nav-link.active {
        background-color: #2563eb !important;
        color: #ffffff !important;
    }

    body.dark-mode .sidebar-theme .nav-link i {
        color: #cbd5e1 !important;
    }

    body.dark-mode .sidebar-theme .nav-link:hover i,
    body.dark-mode .sidebar-theme .nav-link.active i {
        color: #ffffff !important;
    }

    body.dark-mode .sidebar-theme .nav-header {
        color: #64748b !important;
    }

    /* Content Wrapper Dark */
    body.dark-mode .content-wrapper {
        background: #0f172a !important;
    }

    /* Card Dark */
    body.dark-mode .card {
        background: #1e293b !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.3) !important;
    }

    body.dark-mode .card-header {
        background: #1e293b !important;
        border-bottom: 1px solid #334155 !important;
    }

    /* Table Dark */
    body.dark-mode .table thead th {
        background-color: #0f172a !important;
        color: #e2e8f0 !important;
    }

    body.dark-mode .table tbody td {
        color: #e2e8f0 !important;
        border-color: #334155 !important;
    }

    body.dark-mode .table-hover tbody tr:hover {
        background-color: #334155 !important;
    }

    /* Text colors Dark */
    body.dark-mode .text-muted {
        color: #94a3b8 !important;
    }

    body.dark-mode h1,
    body.dark-mode h2,
    body.dark-mode h3,
    body.dark-mode h4,
    body.dark-mode h5 {
        color: #e2e8f0 !important;
    }

    /* Breadcrumb Dark */
    body.dark-mode .breadcrumb-item a {
        color: #3b82f6 !important;
    }

    body.dark-mode .breadcrumb-item.active {
        color: #94a3b8 !important;
    }

    /* Avatar circles - Keep original colors in both modes */
    .bg-warning,
    .bg-success,
    .bg-danger,
    .bg-info,
    .bg-primary {
        color: #ffffff !important;
    }

    /* Small box icons - Keep visible in both modes */
    .small-box .icon i {
        color: rgba(0,0,0,0.15) !important;
    }

    body.dark-mode .small-box .icon i {
        color: rgba(255,255,255,0.15) !important;
    }

    /* Dropdown menus */
    body.dark-mode .dropdown-menu {
        background-color: #1e293b !important;
        border-color: #334155 !important;
    }

    body.dark-mode .dropdown-item {
        color: #e2e8f0 !important;
    }

    body.dark-mode .dropdown-item:hover {
        background-color: #334155 !important;
    }

    body.dark-mode .dropdown-divider {
        border-color: #334155 !important;
    }

    /* Input forms */
    body.dark-mode .form-control {
        background-color: #0f172a !important;
        border-color: #334155 !important;
        color: #e2e8f0 !important;
    }

    body.dark-mode .form-control:focus {
        background-color: #1e293b !important;
        border-color: #3b82f6 !important;
        color: #e2e8f0 !important;
    }
    </style>
</head>
<body class="hold-transition sidebar-mini" id="app-body">
    <div class="wrapper">
        @include('layouts.navbar')
        @include('layouts.sidebar')
        <div class="content-wrapper">
            @hasSection('title-content')
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-12">
                            @yield('title-content')
                        </div>
                    </div>
                </div>
            </section>
            @endif
            <section class="content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </section>
        </div>
        @include('layouts.footer')
    </div>
    @stack('modals')
    <script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('adminlte/dist/js/adminlte.min.js') }}"></script>
    @stack('scripts')
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const toggle = document.getElementById("darkToggle");
        const icon = toggle?.querySelector("i");

        function updateIcon() {
            if (!icon) return;

            if (document.body.classList.contains("dark-mode")) {
                icon.classList.remove("fa-moon");
                icon.classList.add("fa-sun");
            } else {
                icon.classList.remove("fa-sun");
                icon.classList.add("fa-moon");
            }
        }

        if (!toggle) return;

        toggle.addEventListener("click", function (e) {
            e.preventDefault();

            document.body.classList.toggle("dark-mode");

            localStorage.setItem(
                "darkMode",
                document.body.classList.contains("dark-mode")
                    ? "on"
                    : "off"
            );

            updateIcon();
        });

        if (localStorage.getItem("darkMode") === "on") {
            document.body.classList.add("dark-mode");
        }

        updateIcon();
    });
    </script>
</body>
</html>