<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="mb-0 font-weight-bold">
            <i class="fas fa-user text-primary mr-2"></i>Informasi Customer
        </h5>
    </div>
    <div class="card-body text-center">
        @if($transaction->user)
            @if($transaction->user->foto)
                <!-- Tampilkan foto profil jika ada -->
                <img src="{{ asset('storage/' . $transaction->user->foto) }}" 
                     alt="Profile {{ $transaction->user->name }}" 
                     class="rounded-circle mx-auto mb-3"
                     style="width: 80px; height: 80px; object-fit: cover; border: 3px solid #fff; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
            @else
                <!-- Tampilkan inisial jika tidak ada foto -->
                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white font-weight-bold mx-auto mb-3" 
                     style="width: 80px; height: 80px; font-size: 24px; border: 3px solid #fff; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                    {{ strtoupper(substr($transaction->user->name, 0, 2)) }}
                </div>
            @endif
            <h5 class="font-weight-bold mb-1">{{ $transaction->user->name }}</h5>
            <p class="text-muted mb-2">{{ $transaction->user->email }}</p>
            <small class="text-muted">
                <i class="fas fa-phone mr-1"></i>{{ $transaction->user->phone ?? '-' }}
            </small>
        @else
            <p class="text-muted">Data customer tidak tersedia</p>
        @endif
    </div>
</div>