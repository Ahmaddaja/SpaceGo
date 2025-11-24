<aside class="main-sidebar sidebar-dark-primary elevation-4 sidebar-theme">
    <!-- Brand Logo -->
    <a href="/" class="brand-link">
        <i class="fas fa-building mr-2"></i>
        <span class="brand-text font-weight-light">{{ config('app.name') }}</span>
    </a>
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
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                        <p>
                            Transaksi
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/transaksi" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Semua Transaksi</p>
                            </a>
                        </li>
                    </ul>
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
                        {{-- <li class="nav-item">
                            <a href="/laporan/booking" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Booking</p>
                            </a>
                        </li> --}}
                    </ul>
                </li>

                <li class="nav-header">PENGATURAN</li>

                <!-- Profile -->
                <li class="nav-item">
                     <a href="{{ route('admin.profile.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Profile</p>
                    </a>
                </li>

                <!-- Logout -->
                <li class="nav-item">
                    <a href="javascript:;" class="nav-link" onclick="document.getElementById('form-logout').submit();">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
