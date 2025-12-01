<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="mb-0 font-weight-bold">Informasi Dasar</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="kode_rak">Kode Rak <span class="text-danger">*</span></label>
                    <input type="text" 
                           class="form-control @error('kode_rak') is-invalid @enderror" 
                           id="kode_rak" 
                           name="kode_rak" 
                           value="{{ old('kode_rak', $rak->kode_rak ?? '') }}"
                           placeholder="Contoh: RAK-001">
                    @error('kode_rak')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="nama_rak">Nama Rak <span class="text-danger">*</span></label>
                    <input type="text" 
                           class="form-control @error('nama_rak') is-invalid @enderror" 
                           id="nama_rak" 
                           name="nama_rak" 
                           value="{{ old('nama_rak', $rak->nama_rak ?? '') }}"
                           placeholder="Contoh: Rak Pallet A1">
                    @error('nama_rak')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="jenis_rak">Jenis Rak <span class="text-danger">*</span></label>
            <select class="form-control @error('jenis_rak') is-invalid @enderror" 
                    id="jenis_rak" 
                    name="jenis_rak">
                <option value="">Pilih Jenis Rak</option>
                <option value="Heavy Duty" {{ old('jenis_rak', $rak->jenis_rak ?? '') == 'Heavy Duty' ? 'selected' : '' }}>Heavy Duty</option>
                <option value="Medium Duty" {{ old('jenis_rak', $rak->jenis_rak ?? '') == 'Medium Duty' ? 'selected' : '' }}>Medium Duty</option>
                <option value="Light Duty" {{ old('jenis_rak', $rak->jenis_rak ?? '') == 'Light Duty' ? 'selected' : '' }}>Light Duty</option>
                {{-- <option value="Pallet Rack" {{ old('jenis_rak', $rak->jenis_rak ?? '') == 'Pallet Rack' ? 'selected' : '' }}>Pallet Rack</option> --}}
                <option value="Cantilever" {{ old('jenis_rak', $rak->jenis_rak ?? '') == 'Cantilever' ? 'selected' : '' }}>Cantilever</option>
            </select>
            @error('jenis_rak')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="deskripsi">Deskripsi</label>
            <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                      id="deskripsi" 
                      name="deskripsi" 
                      rows="3"
                      placeholder="Deskripsi singkat tentang rak...">{{ old('deskripsi', $rak->deskripsi ?? '') }}</textarea>
            @error('deskripsi')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>