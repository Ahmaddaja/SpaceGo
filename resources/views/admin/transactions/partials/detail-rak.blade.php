<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="mb-0 font-weight-bold">
            <i class="fas fa-box text-primary mr-2"></i>Informasi Rak
        </h5>
    </div>
    <div class="card-body">
        @if($transaction->rak)
            <div class="mb-3">
                <label class="text-muted small">Nama Rak</label>
                <div class="font-weight-bold">{{ $transaction->rak->nama_rak }}</div>
            </div>
            <div class="mb-3">
                <label class="text-muted small">Kode Rak</label>
                <div class="font-weight-bold">{{ $transaction->rak->kode_rak ?? '-' }}</div>
            </div>
            <div class="mb-3">
                <label class="text-muted small">Lokasi Gudang</label>
                <div class="font-weight-bold">
                    <i class="fas fa-map-marker-alt text-danger mr-1"></i>
                    {{ $transaction->rak->gudang->nama_gudang ?? '-' }}
                </div>
            </div>
            <div class="mb-3">
                <label class="text-muted small">Status Rak</label>
                <div>
                    <span class="badge badge-{{ $transaction->rak->status == 'tersedia' ? 'success' : 'danger' }}">
                        {{ ucfirst($transaction->rak->status) }}
                    </span>
                </div>
            </div>
            <div class="mb-0">
                <label class="text-muted small">Harga Sewa</label>
                <div class="font-weight-bold text-success">
                    Rp {{ number_format($transaction->rak->harga_sewa_perbulan, 0, ',', '.') }}/bulan
                </div>
            </div>
        @else
            <p class="text-muted mb-0">Data rak tidak tersedia</p>
        @endif
    </div>
</div>