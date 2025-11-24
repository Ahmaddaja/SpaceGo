<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="mb-0 font-weight-bold">Lokasi</h5>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label for="alamat">Alamat Lengkap <span class="text-danger">*</span></label>
            <textarea class="form-control @error('alamat') is-invalid @enderror" 
                      id="alamat" 
                      name="alamat" 
                      rows="3"
                      placeholder="Masukkan alamat lengkap gudang...">{{ old('alamat', $gudang->alamat ?? '') }}</textarea>
            @error('alamat')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="kota">Kota <span class="text-danger">*</span></label>
                    <input type="text" 
                           class="form-control @error('kota') is-invalid @enderror" 
                           id="kota" 
                           name="kota" 
                           value="{{ old('kota', $gudang->kota ?? '') }}"
                           placeholder="Contoh: Bandung">
                    @error('kota')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="provinsi">Provinsi <span class="text-danger">*</span></label>
                    <input type="text" 
                           class="form-control @error('provinsi') is-invalid @enderror" 
                           id="provinsi" 
                           name="provinsi" 
                           value="{{ old('provinsi', $gudang->provinsi ?? '') }}"
                           placeholder="Contoh: Jawa Barat">
                    @error('provinsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="kode_pos">Kode Pos</label>
            <input type="text" 
                   class="form-control @error('kode_pos') is-invalid @enderror" 
                   id="kode_pos" 
                   name="kode_pos" 
                   value="{{ old('kode_pos', $gudang->kode_pos ?? '') }}"
                   placeholder="Contoh: 40123">
            @error('kode_pos')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>