<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="mb-0 font-weight-bold">Spesifikasi Teknis</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="kapasitas_berat">Kapasitas Berat (kg) <span class="text-danger">*</span></label>
                    <input type="number" 
                           class="form-control @error('kapasitas_berat') is-invalid @enderror" 
                           id="kapasitas_berat" 
                           name="kapasitas_berat" 
                           value="{{ old('kapasitas_berat', $rak->kapasitas_berat ?? '') }}"
                           placeholder="Contoh: 5000">
                    @error('kapasitas_berat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="jumlah_tingkat">Jumlah Tingkat <span class="text-danger">*</span></label>
                    <input type="number" 
                           class="form-control @error('jumlah_tingkat') is-invalid @enderror" 
                           id="jumlah_tingkat" 
                           name="jumlah_tingkat" 
                           value="{{ old('jumlah_tingkat', $rak->jumlah_tingkat ?? '') }}"
                           placeholder="Contoh: 5">
                    @error('jumlah_tingkat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="panjang">Panjang (meter) <span class="text-danger">*</span></label>
                    <input type="number" 
                           step="0.01"
                           class="form-control @error('panjang') is-invalid @enderror" 
                           id="panjang" 
                           name="panjang" 
                           value="{{ old('panjang', $rak->panjang ?? '') }}"
                           placeholder="Contoh: 2.5">
                    @error('panjang')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="lebar">Lebar (meter) <span class="text-danger">*</span></label>
                    <input type="number" 
                           step="0.01"
                           class="form-control @error('lebar') is-invalid @enderror" 
                           id="lebar" 
                           name="lebar" 
                           value="{{ old('lebar', $rak->lebar ?? '') }}"
                           placeholder="Contoh: 1.2">
                    @error('lebar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="tinggi">Tinggi (meter) <span class="text-danger">*</span></label>
                    <input type="number" 
                           step="0.01"
                           class="form-control @error('tinggi') is-invalid @enderror" 
                           id="tinggi" 
                           name="tinggi" 
                           value="{{ old('tinggi', $rak->tinggi ?? '') }}"
                           placeholder="Contoh: 6.0">
                    @error('tinggi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="spesifikasi_tambahan">Spesifikasi Tambahan</label>
            <textarea class="form-control @error('spesifikasi_tambahan') is-invalid @enderror" 
                      id="spesifikasi_tambahan" 
                      name="spesifikasi_tambahan" 
                      rows="3"
                      placeholder="Informasi teknis lainnya...">{{ old('spesifikasi_tambahan', $rak->spesifikasi_tambahan ?? '') }}</textarea>
            @error('spesifikasi_tambahan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>