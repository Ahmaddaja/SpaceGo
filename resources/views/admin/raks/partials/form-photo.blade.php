<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="mb-0 font-weight-bold">Foto Rak</h5>
    </div>
    <div class="card-body">
        @if(isset($rak) && $rak->foto)
        <div class="text-center mb-3">
            <img src="{{ asset('storage/' . $rak->foto) }}" 
                 alt="{{ $rak->nama_rak }}" 
                 class="img-fluid rounded"
                 style="max-height: 200px; object-fit: cover;">
            <p class="text-muted mt-2 mb-0">Foto saat ini</p>
        </div>
        @endif

        <div class="form-group">
            <label for="foto">Upload Foto Baru</label>
            <div class="custom-file">
                <input type="file" 
                       class="custom-file-input @error('foto') is-invalid @enderror" 
                       id="foto" 
                       name="foto"
                       accept="image/jpeg,image/png,image/jpg">
                <label class="custom-file-label" for="foto">Pilih file...</label>
            </div>
            <small class="form-text text-muted">
                Format: JPG, JPEG, PNG. Maksimal: 2MB
            </small>
            @error('foto')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('foto').addEventListener('change', function(e) {
    var fileName = e.target.files[0].name;
    var label = e.target.nextElementSibling;
    label.textContent = fileName;
});
</script>
@endpush