<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <!-- Search Bar -->
    <form class="form-inline ml-3">
        <div class="input-group input-group-sm">
            <input class="form-control form-control-navbar" type="search" placeholder="Cari atau ketik perintah..." aria-label="Search" style="width: 300px;">
            <div class="input-group-append">
                <button class="btn btn-navbar" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </form>

    <ul class="navbar-nav ml-auto">
        <!-- Dark Mode Toggle -->
        <li class="nav-item">
            <a class="nav-link" href="javascript:;" id="darkToggle">
                <i class="fas fa-moon"></i>
            </a>
        </li>

        <!-- Notifications -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
                <i class="far fa-bell"></i>
                <span class="badge badge-warning navbar-badge">1</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">1 Notifikasi</span>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-calendar-check mr-2"></i> Booking baru
                    <span class="float-right text-muted text-sm">5 menit</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer">Lihat Semua Notifikasi</a>
            </div>
        </li>

        <!-- User Info & Avatar -->
        <li class="nav-item dropdown">
            <a class="nav-link d-flex align-items-center" data-toggle="dropdown" href="#" aria-expanded="false">
                <div class="mr-2 text-right">
                    <div class="font-weight-bold">Administrator</div>
                    <small class="text-muted">Administrator</small>
                </div>
                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white font-weight-bold" style="width: 40px; height: 40px;">
                    AD
                </div>
                <i class="fas fa-caret-down ml-2"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="/profile" class="dropdown-item">
                    <i class="fas fa-user mr-2"></i> Profile
                </a>
                <div class="dropdown-divider"></div>
                <a href="javascript:;" class="dropdown-item" onclick="document.getElementById('form-logout').submit();">
                    <i class="fas fa-sign-out-alt mr-2"></i> Sign Out
                </a>
            </div>
        </li>
    </ul>
</nav>

<form id="form-logout" action="/logout" method="POST" style="display:none">
    @csrf
</form>