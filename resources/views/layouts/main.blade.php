<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        (function() {
            const savedMode = localStorage.getItem("darkMode");

            if (savedMode === "on") {
                document.documentElement.classList.add("dark-mode");
                document.body?.classList.add("dark-mode");
            } else if (savedMode === "off") {
                document.documentElement.classList.remove("dark-mode");
            } else {
                // jika belum ada setting, pakai mode dari sistem
                const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (systemDark) {
                    document.documentElement.classList.add("dark-mode");
                    document.body?.classList.add("dark-mode");
                }
            }
        })();
    </script>
    <title>{{ config('app.name') }}{{ isset($title) ? ' | ' . $title : '' }}</title>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
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
            justify-content: flex-start !important;
            padding-left: 20px !important;
        }

        .brand-link .brand-text {
            color: #ffffff !important;
            font-weight: 600;
            letter-spacing: .3px;
            text-align: center;
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
        .card:not(.stat-card) {
            background: #ffffff !important;
            border-radius: 12px !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08) !important;
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
        .dark-mode body,
        .dark-mode {
            background-color: #0f172a !important;
            color: #e2e8f0 !important;
        }

        /* Header Dark */
        .dark-mode .main-header {
            background-color: #1e293b !important;
            border-bottom: 1px solid #334155 !important;
        }

        .dark-mode .main-header .navbar-nav .nav-link {
            color: #e2e8f0 !important;
        }

        /* Sidebar Dark */
        .dark-mode .sidebar-theme {
            background-color: #0f172a !important;
            border-right: 1px solid #1e293b;
        }

        /* Brand Link Dark */
        .dark-mode .brand-link {
            background: #0f172a !important;
            border-bottom: 1px solid #1e293b !important;
            justify-content: flex-start !important;
            padding-left: 20px !important;
        }

        .dark-mode .brand-link .brand-text {
            color: #ffffff !important;
            text-align: center;
            font-weight: 600;
            letter-spacing: .3px;
        }

        .dark-mode .brand-link i {
            color: #ffffff !important;
        }

        /* Nav links Dark */
        .dark-mode .sidebar-theme .nav-link {
            color: #cbd5e1 !important;
        }

        .dark-mode .sidebar-theme .nav-link:hover {
            background-color: #1e293b !important;
            color: #ffffff !important;
        }

        .dark-mode .sidebar-theme .nav-link.active {
            background-color: #2563eb !important;
            color: #ffffff !important;
        }

        .dark-mode .sidebar-theme .nav-link i {
            color: #cbd5e1 !important;
        }

        .dark-mode .sidebar-theme .nav-link:hover i,
        .dark-mode .sidebar-theme .nav-link.active i {
            color: #ffffff !important;
        }

        .dark-mode .sidebar-theme .nav-header {
            color: #64748b !important;
        }

        /* Content Wrapper Dark */
        .dark-mode .content-wrapper {
            background: #0f172a !important;
        }

        /* Card Dark */
        .dark-mode .card:not(.stat-card) {
            background: #1e293b !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3) !important;
        }

        .dark-mode .card-header {
            background: #1e293b !important;
            border-bottom: 1px solid #334155 !important;
        }

        /* Table Dark */
        .dark-mode .table thead th {
            background-color: #0f172a !important;
            color: #e2e8f0 !important;
        }

        .dark-mode .table tbody td {
            color: #e2e8f0 !important;
            border-color: #334155 !important;
        }

        .dark-mode .table-hover tbody tr:hover {
            background-color: #334155 !important;
        }

        /* Text colors Dark */
        .dark-mode .text-muted {
            color: #94a3b8 !important;
        }

        .dark-mode h1,
        .dark-mode h2,
        .dark-mode h3,
        .dark-mode h4,
        .dark-mode h5 {
            color: #e2e8f0 !important;
        }

        /* Breadcrumb Dark */
        .dark-mode .breadcrumb-item a {
            color: #3b82f6 !important;
        }

        .dark-mode .breadcrumb-item.active {
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
            color: rgba(0, 0, 0, 0.15) !important;
        }

        .dark-mode .small-box .icon i {
            color: rgba(255, 255, 255, 0.15) !important;
        }

        /* Dropdown menus */
        .dark-mode .dropdown-menu {
            background-color: #1e293b !important;
            border-color: #334155 !important;
        }

        .dark-mode .dropdown-item {
            color: #e2e8f0 !important;
        }

        .dark-mode .dropdown-item:hover {
            background-color: #334155 !important;
        }

        .dark-mode .dropdown-divider {
            border-color: #334155 !important;
        }

        /* Input forms */
        .dark-mode .form-control {
            background-color: #0f172a !important;
            border-color: #334155 !important;
            color: #e2e8f0 !important;
        }

        .dark-mode .form-control:focus {
            background-color: #1e293b !important;
            border-color: #3b82f6 !important;
            color: #e2e8f0 !important;
        }

        /* Pastikan body dan wrapper relative */
        body.hold-transition {
            position: relative !important;
        }

        .wrapper {
            position: relative !important;
            min-height: 100vh !important;
        }

        /* NAVBAR FIXED */
        .main-header.navbar {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            z-index: 1030 !important;
            width: auto !important;
            transition: margin-left 0.3s ease-in-out;
        }

        /* SIDEBAR FIXED */
        .main-sidebar {
            position: fixed !important;
            top: 0 !important;
            bottom: 0 !important;
            left: 0 !important;
            width: 250px !important;
            height: 100vh !important;
            z-index: 1040 !important;
            overflow: hidden !important;
        }

        /* CONTENT WRAPPER - Beri ruang untuk navbar & sidebar */
        .content-wrapper {
            margin-left: 250px !important;
            margin-top: 57px !important;
            min-height: calc(100vh - 57px) !important;
        }

        /* FOOTER - Beri margin left */
        .main-footer {
            margin-left: 250px !important;
        }

        /* Sidebar Collapse State */
        .sidebar-collapse .main-header.navbar {
            left: 0 !important;
        }

        .sidebar-collapse .content-wrapper,
        .sidebar-collapse .main-footer {
            margin-left: 0 !important;
        }

        .sidebar-collapse .main-sidebar {
            margin-left: -250px !important;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .main-header.navbar {
                left: 0 !important;
            }

            .content-wrapper,
            .main-footer {
                margin-left: 0 !important;
            }

            .main-sidebar {
                margin-left: -250px !important;
            }

            .sidebar-open .main-sidebar {
                margin-left: 0 !important;
            }
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed" id="app-body">
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
        document.addEventListener("DOMContentLoaded", function() {
            const toggle = document.getElementById("darkToggle");
            const icon = toggle?.querySelector("i");

            function updateIcon() {
                if (!icon) return;
                if (document.documentElement.classList.contains("dark-mode")) {
                    icon.classList.remove("fa-moon");
                    icon.classList.add("fa-sun");
                } else {
                    icon.classList.remove("fa-sun");
                    icon.classList.add("fa-moon");
                }
            }

            toggle?.addEventListener("click", function(e) {
                e.preventDefault();

                document.documentElement.classList.toggle("dark-mode");

                const isDark = document.documentElement.classList.contains("dark-mode");
                localStorage.setItem("darkMode", isDark ? "on" : "off");

                updateIcon();
            });

            updateIcon();
        });
    </script>
</body>

</html>
