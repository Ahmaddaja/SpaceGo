<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="mb-0 font-weight-bold">Lokasi & Harga</h5>
    </div>
    <div class="card-body">
        <div class="form-group">
           <label for="lokasi_gudang">Lokasi Gudang <span class="text-danger">*</span></label>
            <select class="form-control @error('lokasi_gudang') is-invalid @enderror" id="lokasi_gudang"
                name="lokasi_gudang">
                <option value="">Pilih Gudang</option>
                @foreach ($gudangs as $gudang)
                    <option value="{{ $gudang->nama_gudang }}"
                        {{ old('lokasi_gudang', $rak->lokasi_gudang ?? '') == $gudang->nama_gudang ? 'selected' : '' }}>
                        {{ $gudang->nama_gudang }} - {{ $gudang->kota }}
                    </option>
                @endforeach
            </select>

            @error('lokasi_gudang')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

                {{-- <div class="form-group">
            <label for="zona_gudang">Zona Gudang</label>
            <input type="text" 
                   class="form-control @error('zona_gudang') is-invalid @enderror" 
                   id="zona_gudang" 
                   name="zona_gudang" 
                   value="{{ old('zona_gudang', $rak->zona_gudang ?? '') }}"
                   placeholder="Contoh: A, B, C">
            @error('zona_gudang')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div> --}}

                <div class="form-group">
                    <label for="harga_sewa_perbulan">Harga Sewa per Bulan (Rp) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('harga_sewa_perbulan') is-invalid @enderror"
                        id="harga_sewa_perbulan" name="harga_sewa_perbulan"
                        value="{{ old('harga_sewa_perbulan', $rak->harga_sewa_perbulan ?? '') }}"
                        placeholder="Contoh: 5000000">
                    @error('harga_sewa_perbulan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Hanya tampilkan dropdown status saat EDIT, tidak saat CREATE -->
                @if (isset($rak))
                    <div class="form-group">
                        <label for="status">Status <span class="text-danger">*</span></label>
                        <select class="form-control @error('status') is-invalid @enderror" id="status" name="status">
                            <option value="tersedia"
                                {{ old('status', $rak->status ?? '') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                            <option value="terisi" {{ old('status', $rak->status ?? '') == 'terisi' ? 'selected' : '' }}>
                                Terisi</option>
                            <option value="maintenance"
                                {{ old('status', $rak->status ?? '') == 'maintenance' ? 'selected' : '' }}>Maintenance
                            </option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @else
                    <!-- Hidden input untuk status default saat create -->
                    <input type="hidden" name="status" value="tersedia">

                    <!-- Info bahwa status otomatis tersedia -->
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        Status rak otomatis akan diset <strong>"Tersedia"</strong>
                    </div>
                @endif

                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
                            {{ old('is_active', $rak->is_active ?? true) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_active">Aktifkan Rak</label>
                    </div>
                </div>
            </div>
        </div>
