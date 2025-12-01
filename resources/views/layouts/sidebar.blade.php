<aside class="main-sidebar sidebar-dark-primary elevation-4 sidebar-theme">
    <!-- Brand Logo -->
    <div class="brand-link" style="cursor: default;">
        <svg class="brand-icon mr-2" fill="currentColor" width="30" height="30" xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24">
            <path d="M3 10L12 3L21 10V21H3V10ZM5 12V19H19V12L12 7L5 12Z" />
        </svg>
        <span class="brand-text font-weight-light">{{ config('app.name') }}</span>
    </div>

    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <li class="nav-header">MENU</li>

                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}"
                        class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- Kelola Gudang -->
                <li class="nav-item">
                    <a href="{{ route('gudangs.index') }}"
                        class="nav-link {{ request()->routeIs('gudangs.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-warehouse"></i>
                        <p>Kelola Gudang</p>
                    </a>
                </li>

                <!-- Kelola Rak -->
                <li class="nav-item">
                    <a href="{{ route('raks.index') }}"
                        class="nav-link {{ request()->routeIs('raks.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-boxes"></i>
                        <p>Kelola Rak</p>
                    </a>
                </li>

                <!-- Transaksi -->
                <li class="nav-item">
                    <a href="{{ route('admin.transactions.index') }}"
                        class="nav-link {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                        <p>Transaksi</p>
                    </a>
                </li>

                <!-- Pelanggan -->
                <li class="nav-item">
                    <a href="{{ route('admin.pelanggan.pelanggan') }}"
                        class="nav-link {{ request()->routeIs('admin.pelanggan.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Pelanggan</p>
                    </a>
                </li>

                <!-- Laporan Pendapatan -->
                <li class="nav-item">
                    <a href="{{ route('admin.laporan.pendapatan') }}"
                        class="nav-link {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>Laporan Pendapatan</p>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>

<style>
    /* Styling untuk active state */
    .nav-sidebar .nav-link.active {
        background-color: #007bff !important;
        color: #fff !important;
    }

    .sidebar-dark-primary .nav-link.active {
        background-color: #007bff;
        color: #fff;
    }

    /* Hover effect */
    .nav-sidebar .nav-link:hover {
        background-color: rgba(255, 255, 255, .1);
    }
</style>
