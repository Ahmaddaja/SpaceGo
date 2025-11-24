<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="mb-0 font-weight-bold">Foto Gudang</h5>
    </div>
    <div class="card-body text-center">
        @if($gudang->foto)
            <img src="{{ asset('storage/' . $gudang->foto) }}" 
                 alt="{{ $gudang->nama_gudang }}" 
                 class="img-fluid rounded"
                 style="max-width: 100%; height: auto;">
        @else
            <div class="py-5">
                <i class="fas fa-warehouse fa-5x text-muted mb-3"></i>
                <p class="text-muted">Tidak ada foto</p>
            </div>
        @endif
    </div>
</div>