<aside class="main-sidebar sidebar-dark-primary elevation-4 sidebar-theme">
    <!-- Brand Logo - Diubah dari <a> menjadi <div> agar tidak bisa diklik -->
    <div class="brand-link" style="cursor: default;">
        <!-- Logo yang telah diganti dengan ukuran lebih besar -->
        <svg class="brand-icon mr-2" fill="currentColor" width="30" height="30" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M3 10L12 3L21 10V21H3V10ZM5 12V19H19V12L12 7L5 12Z"/>
        </svg>
        <span class="brand-text font-weight-light">{{ config('app.name') }}</span>
    </div>
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-header">MENU</li>

                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                {{-- <!-- Jadwal Booking -->
                <li class="nav-item">
                    <a href="/jadwal-booking" class="nav-link">
                        <i class="nav-icon fas fa-calendar-check"></i>
                        <p>Jadwal Booking</p>
                    </a>
                </li> --}}

                <!-- Kelola Gudang -->
                <li class="nav-item">
                    <a href="{{ route('gudangs.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-warehouse"></i>
                        <p>Kelola Gudang</p>
                    </a>
                </li>

                <!-- Kelola Rak -->
                <li class="nav-item">
                    <a href="{{ route('raks.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-boxes"></i>
                        <p>Kelola Rak</p>
                    </a>
                </li>

                <!-- Transaksi -->
                <li class="nav-item">
                    <a href="{{ route('admin.transactions.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                        <p>Transaksi</p>
                    </a>
                </li>

                <!-- Pelanggan -->
                <li class="nav-item">
                    <a href="{{ route('admin.pelanggan.pelanggan') }}" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Pelanggan</p>
                    </a>
                </li>


                <!-- Laporan -->
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>
                            Laporan
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/laporan/pendapatan" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Pendapatan</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/laporan/booking" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Booking</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/laporan/semua" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Semua Laporan</p>
                            </a>
                        </li>
                    </ul>
                </li>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</aside>