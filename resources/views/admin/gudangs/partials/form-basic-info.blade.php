<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="mb-0 font-weight-bold">Informasi Dasar</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="kode_gudang">Kode Gudang <span class="text-danger">*</span></label>
                    <input type="text" 
                           class="form-control @error('kode_gudang') is-invalid @enderror" 
                           id="kode_gudang" 
                           name="kode_gudang" 
                           value="{{ old('kode_gudang', $gudang->kode_gudang ?? '') }}"
                           placeholder="Contoh: GD-001">
                    @error('kode_gudang')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="nama_gudang">Nama Gudang <span class="text-danger">*</span></label>
                    <input type="text" 
                           class="form-control @error('nama_gudang') is-invalid @enderror" 
                           id="nama_gudang" 
                           name="nama_gudang" 
                           value="{{ old('nama_gudang', $gudang->nama_gudang ?? '') }}"
                           placeholder="Contoh: Gudang Utama">
                    @error('nama_gudang')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="deskripsi">Deskripsi</label>
            <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                      id="deskripsi" 
                      name="deskripsi" 
                      rows="3"
                      placeholder="Deskripsi singkat tentang gudang...">{{ old('deskripsi', $gudang->deskripsi ?? '') }}</textarea>
            @error('deskripsi')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <div class="custom-control custom-switch">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" 
                       class="custom-control-input" 
                       id="is_active" 
                       name="is_active" 
                       value="1"
                       {{ old('is_active', $gudang->is_active ?? true) ? 'checked' : '' }}>
                <label class="custom-control-label" for="is_active">Aktifkan Gudang</label>
            </div>
        </div>
    </div>
</div>