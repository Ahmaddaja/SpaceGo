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
        
        <!-- resources/views/components/notification-dropdown.blade.php -->
@php
    // Ambil notifikasi dengan berbagai kategori
    $notifs = \App\Models\UserNotification::latest()->take(9)->get();
    $unreadCount = $notifs->where('is_read', false)->count();
    $groupedNotifs = $notifs->groupBy('category');
    
    // Ambil data user baru (untuk 24 jam terakhir)
    $newUsers = \App\Models\User::where('created_at', '>=', now()->subDay())
                                ->orderBy('created_at', 'desc')
                                ->take(3)
                                ->get();
    
    // Ambil data transaksi baru dengan relasi yang lengkap
    $newTransactions = \App\Models\Transaction::with(['user', 'rak'])
                                ->where('created_at', '>=', now()->subDay())
                                ->orderBy('created_at', 'desc')
                                ->take(3)
                                ->get();
@endphp

<li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#" id="notificationDropdown">
        <i class="far fa-bell" id="notificationBell"></i>
        @if($unreadCount > 0)
            <span class="badge badge-warning navbar-badge" id="notificationBadge">{{ $unreadCount }}</span>
        @endif
    </a>

    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
    <div class="dropdown-header">
        <div class="d-flex justify-content-between align-items-center">
            <span>{{ $unreadCount }} Notifikasi Baru</span>
            @if($unreadCount > 0)
                <button class="btn btn-sm btn-outline-secondary" onclick="markAllAsRead()">
                    Tandai Sudah Dibaca
                </button>
            @endif
        </div>
    </div>

    <div class="dropdown-divider"></div>

    <div class="notification-content" style="max-height: 400px; overflow-y: auto;">
        <!-- Notifikasi dari Database -->
        @forelse($groupedNotifs as $category => $categoryNotifs)
            <div class="category-section">
                <div class="dropdown-item dropdown-category-header d-flex justify-content-between align-items-center" 
                     data-toggle="collapse" data-target="#collapse{{ $category }}">
                    <div>
                        <strong>
                            @switch($category)
                                @case('message')
                                    📧 Pesan
                                    @break
                                @case('friend_request')
                                    👥 Permintaan Pertemanan
                                    @break
                                @case('report')
                                    ⚠️ Laporan
                                    @break
                                @case('transaction')
                                    💰 Transaksi
                                    @break
                                @case('new_user')
                                    👤 User Baru
                                    @break
                                @default
                                    🔔 Notifikasi
                            @endswitch
                            ({{ $categoryNotifs->count() }})
                        </strong>
                    </div>
                    <div>
                        <button class="btn btn-sm btn-outline-danger" 
                                onclick="clearCategory('{{ $category }}')" 
                                title="Hapus Semua">
                            &times;
                        </button>
                    </div>
                </div>

                <div class="collapse show" id="collapse{{ $category }}">
                    @foreach($categoryNotifs as $notif)
                        <div class="dropdown-item notification-item d-flex justify-content-between align-items-center 
                                    {{ !$notif->is_read ? 'bg-light' : '' }}" 
                             data-id="{{ $notif->id }}">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center">
                                    <i class="{{ $notif->getIconClass() }} mr-2"></i>
                                    <div>
                                        <b>{{ $notif->title }}</b>
                                        <br>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}
                                            @if(!$notif->is_read)
                                                <span class="badge badge-primary badge-sm ml-1">Baru</span>
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="ml-2">
                                <button type="button" 
                                        class="btn btn-sm btn-link text-danger" 
                                        onclick="deleteNotification({{ $notif->id }})"
                                        title="Hapus">
                                    &times;
                                </button>
                            </div>
                        </div>
                        @if(!$loop->last)
                            <div class="dropdown-divider"></div>
                        @endif
                    @endforeach
                </div>
            </div>
            <div class="dropdown-divider"></div>
        @empty
            <!-- Jika tidak ada notifikasi dari database, tampilkan real-time -->
        @endforelse

        <!-- Real-time Notifications: User Baru -->
        @if($newUsers->count() > 0)
            <div class="category-section">
                <div class="dropdown-item dropdown-category-header d-flex justify-content-between align-items-center">
                    <div>
                        <strong>👤 User Baru ({{ $newUsers->count() }})</strong>
                    </div>
                </div>
                @foreach($newUsers as $user)
    <div class="dropdown-item notification-item bg-dark text-white">
        <div class="d-flex align-items-center">
            <i class="fas fa-user-plus text-success mr-2"></i>
            <div>
                <b>{{ $user->name }}</b> bergabung
                <br>
                <small class="text-light">
                    {{ $user->created_at->diffForHumans() }}
                    <span class="badge badge-info badge-sm ml-1">Baru</span>
                </small>
            </div>
        </div>
    </div>
    @if(!$loop->last)
        <div class="dropdown-divider"></div>
    @endif
@endforeach

            </div>
            <div class="dropdown-divider"></div>
        @endif

        <!-- Real-time Notifications: Transaksi Baru -->
@if($newTransactions->count() > 0)
    <div class="category-section">
        <div class="dropdown-item dropdown-category-header d-flex justify-content-between align-items-center">
            <div>
                <strong>💰 Transaksi Baru ({{ $newTransactions->count() }})</strong>
            </div>
        </div>
        @foreach($newTransactions as $transaction)
            <div class="dropdown-item notification-item" onclick="viewTransaction({{ $transaction->id }})">
                <div class="d-flex align-items-start">
                    <i class="fas fa-shopping-cart text-primary mr-2 mt-1"></i>
                    <div class="flex-grow-1">
                        
                        <!-- Informasi User -->
                        <small class="text-muted d-block">
                            <i class="fas fa-user mr-1"></i>
                            {{ $transaction->user->name ?? 'User Tidak Diketahui' }}
                        </small>
                        
                        <!-- Informasi Rak -->
                        @if($transaction->rak)
                        <small class="text-muted d-block">
                            <i class="fas fa-cube mr-1"></i>
                            Rak: {{ $transaction->rak->nama_rak ?? 'N/A' }}
                        </small>
                        @endif
                        
                        <!-- Informasi Pembayaran -->
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <small class="font-weight-bold text-primary">
                                Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                            </small>
                            <small class="text-muted">
                                {{ $transaction->created_at->diffForHumans() }}
                                <span class="badge badge-success badge-sm ml-1">Baru</span>
                            </small>
                        </div>
                        
                        <!-- Tipe Pembayaran -->
                        @if($transaction->payment_type)
                        <small class="text-muted d-block">
                            <i class="fas fa-credit-card mr-1"></i>
                            {{ ucfirst(str_replace('_', ' ', $transaction->payment_type)) }}
                        </small>
                        @endif
                    </div>
                </div>
            </div>
            @if(!$loop->last)
                <div class="dropdown-divider"></div>
            @endif
        @endforeach
    </div>
    <div class="dropdown-divider"></div>
@endif

        @if($groupedNotifs->count() === 0 && $newUsers->count() === 0 && $newTransactions->count() === 0)
            <span class="dropdown-item text-center text-muted">
                Tidak ada notifikasi
            </span>
        @endif
    </div>
</div>
</li>

<script>
// Bell animation for new notifications
function animateBell() {
    const bell = document.getElementById('notificationBell');
    bell.style.transform = 'rotate(20deg)';
    setTimeout(() => {
        bell.style.transform = 'rotate(-20deg)';
        setTimeout(() => {
            bell.style.transform = 'rotate(0deg)';
        }, 200);
    }, 200);
}

// Auto animate if there are unread notifications
@if($unreadCount > 0)
    document.addEventListener('DOMContentLoaded', function() {
        animateBell();
        // Animate every 10 seconds if still unread
        setInterval(() => {
            if ({{ $unreadCount }} > 0) {
                animateBell();
            }
        }, 10000);
    });
@endif

// AJAX Functions
function markAllAsRead() {
    fetch('{{ route("notifications.read-all") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    }).then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function deleteNotification(id) {
    if (!confirm('Hapus notifikasi ini?')) return;
    
    fetch(`/notifications/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    }).then(response => response.json())
    .then(data => {
        if (data.success) {
            document.querySelector(`.notification-item[data-id="${id}"]`).remove();
            updateNotificationCount();
        }
    });
}

function clearCategory(category) {
    if (!confirm(`Hapus semua notifikasi ${category}?`)) return;
    
    fetch(`/notifications/category/${category}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    }).then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function updateNotificationCount() {
    fetch('/api/notifications')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('notificationBadge');
            if (data.unread_count > 0) {
                badge.textContent = data.unread_count;
            } else {
                badge.remove();
            }
        });
}

// Auto mark as read when notification is clicked
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.notification-item').forEach(item => {
        item.addEventListener('click', function(e) {
            if (!e.target.closest('button')) {
                const notifId = this.getAttribute('data-id');
                fetch(`/notifications/${notifId}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                });
                
                this.classList.remove('bg-light');
                this.querySelector('.badge')?.remove();
                updateNotificationCount();
            }
        });
    });
});

// Auto refresh notifications every 30 seconds
function startNotificationPolling() {
    setInterval(() => {
        fetch('/api/notifications/check-updates')
            .then(response => response.json())
            .then(data => {
                if (data.has_new) {
                    updateNotificationCount();
                    // Optional: show toast notification
                    showNewNotificationToast(data.latest_notification);
                }
            });
    }, 30000); // 30 seconds
}

// Show toast for new notifications
function showNewNotificationToast(notification) {
    // Implement toast notification here
    console.log('New notification:', notification);
}

// Start polling when page loads
document.addEventListener('DOMContentLoaded', function() {
    startNotificationPolling();
});
</script>

<style>
.notification-item {
    cursor: pointer;
    transition: background-color 0.2s;
}

.notification-item:hover {
    background-color: #f8f9fa !important;
}

.dropdown-category-header {
    cursor: pointer;
    background-color: #e9ecef;
}

.badge-sm {
    font-size: 0.6em;
    padding: 2px 4px;
}

#notificationBell {
    transition: transform 0.2s ease-in-out;
}

.notification-item {
    cursor: pointer;
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
    padding: 8px 12px;
}

.notification-item:hover {
    background-color: #f8f9fa !important;
    border-left-color: #007bff;
    transform: translateX(2px);
}

.transaction-status-badge {
    font-size: 0.7em;
    padding: 3px 6px;
}

/* Animation for new transactions */
@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7); }
    70% { box-shadow: 0 0 0 10px rgba(40, 167, 69, 0); }
    100% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); }
}

.new-transaction {
    animation: pulse 2s infinite;
}
</style>    
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