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
        
        @php
            $notifs = \App\Models\UserNotification::latest()->take(5)->get();
            $notifCount = $notifs->count();
        @endphp

        <!-- Notifications -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-bell"></i>
                @if($notifCount > 0)
                    <span class="badge badge-warning navbar-badge">{{ $notifCount }}</span>
                @endif
            </a>

            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

                <span class="dropdown-item dropdown-header">
                    {{ $notifCount }} Notifikasi
                </span>

                <div class="dropdown-divider"></div>

                @forelse($notifs as $notif)
                    <div class="dropdown-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-user-plus mr-2"></i>
                            <b>{{ $notif->title }}</b><br>
                            <small class="text-muted">
                                {{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}
                            </small>
                        </div>

                        <!-- Tombol X -->
                    <form action="{{ route('notif.delete', $notif->id) }}" method="POST" class="ml-2">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="border:none;background:none;color:red;font-size:18px;">
                            &times;
                        </button>
                    </form>
                    </div>
                    <div class="dropdown-divider"></div>
                @empty
                    <span class="dropdown-item text-center text-muted">
                        Tidak ada pesan apapun
                    </span>
                @endforelse

                <a href="#" class="dropdown-item dropdown-footer">
                    Lihat Semua Notifikasi
                </a>
            </div>
        </li>
        <!-- User Info & Avatar -->
        <li class="nav-item dropdown">
            <a class="nav-link d-flex align-items-center" data-toggle="dropdown" href="#" aria-expanded="false">
                <div class="mr-2 text-right">
                    <div class="font-weight-bold">{{ Auth::user()->name }}</div>
                    <small class="text-muted">{{ Auth::user()->role }}</small>
                </div>
                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white font-weight-bold overflow-hidden" style="width: 40px; height: 40px;">
                    @if(Auth::user()->foto)
                        <img src="{{ Storage::disk('public')->exists(Auth::user()->foto) ? Storage::url(Auth::user()->foto) : asset(Auth::user()->foto) }}" 
                             alt="Profile Photo" class="w-100 h-100" style="object-fit: cover;">
                    @else
                        <span>{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
                    @endif
                </div>
                <i class="fas fa-caret-down ml-2"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="{{ route('admin.profile.index') }}" class="dropdown-item">
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