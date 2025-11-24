<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="mb-0 font-weight-bold">Spesifikasi Teknis</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="info-box bg-light">
                    <div class="info-box-content">
                        <span class="info-box-text">Kapasitas Berat</span>
                        <span class="info-box-number">{{ number_format($rak->kapasitas_berat) }} kg</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-box bg-light">
                    <div class="info-box-content">
                        <span class="info-box-text">Jumlah Tingkat</span>
                        <span class="info-box-number">{{ $rak->jumlah_tingkat }} tingkat</span>
                    </div>
                </div>
            </div>
        </div>

        <hr>

        <h6 class="font-weight-bold mb-3">Dimensi</h6>
        <div class="row">
            <div class="col-md-4">
                <div class="text-center p-3 border rounded">
                    <i class="fas fa-arrows-alt-h fa-2x text-primary mb-2"></i>
                    <p class="mb-0 text-muted">Panjang</p>
                    <h5 class="mb-0">{{ $rak->panjang }} m</h5>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center p-3 border rounded">
                    <i class="fas fa-arrows-alt-v fa-2x text-success mb-2"></i>
                    <p class="mb-0 text-muted">Lebar</p>
                    <h5 class="mb-0">{{ $rak->lebar }} m</h5>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center p-3 border rounded">
                    <i class="fas fa-ruler-vertical fa-2x text-warning mb-2"></i>
                    <p class="mb-0 text-muted">Tinggi</p>
                    <h5 class="mb-0">{{ $rak->tinggi }} m</h5>
                </div>
            </div>
        </div>

        <hr>

        <div class="alert alert-info">
            <i class="fas fa-info-circle mr-2"></i>
            <strong>Volume Total:</strong> {{ number_format($rak->volume, 2) }} m³
        </div>

        @if($rak->spesifikasi_tambahan)
        <hr>
        <h6 class="font-weight-bold">Spesifikasi Tambahan</h6>
        <p class="text-muted">{{ $rak->spesifikasi_tambahan }}</p>
        @endif
    </div>
</div>