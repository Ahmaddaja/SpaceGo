<div class="card border-0 shadow-sm">
    <div class="card-body">
        <button type="submit" class="btn btn-primary btn-block">
            <i class="fas fa-save mr-2"></i>{{ $submitText }}
        </button>
        {{-- ✅ UBAH dari <a> ke <button> --}}
        <button type="button" class="btn btn-secondary btn-block" id="cancel-button" onclick="handleCancelWithCleanup()">
            <i class="fas fa-times mr-2"></i>Batal
        </button>
        @if (isset($rak))
            <hr>
            <button type="button" class="btn btn-danger btn-block" onclick="confirmDelete({{ $rak->id }})">
                <i class="fas fa-trash mr-2"></i>Hapus Rak
            </button>
        @endif
    </div>
</div>

@if (isset($rak))
    @push('scripts')
        <script>
            function confirmDelete(id) {
                if (confirm('Apakah Anda yakin ingin menghapus rak ini?')) {
                    document.getElementById('delete-form-' + id).submit();
                }
            }
        </script>
    @endpush
@endif

@include('admin.raks.partials.scripts')