<div class="card border-0 shadow-sm">
    <div class="card-body">
        <button type="submit" class="btn btn-primary btn-block">
            <i class="fas fa-save mr-2"></i>{{ $submitText }}
        </button>
        <a href="{{ route('gudangs.index') }}" class="btn btn-secondary btn-block">
            <i class="fas fa-times mr-2"></i>Batal
        </a>
        @if (isset($gudang))
            <hr>
            <button type="button" class="btn btn-danger btn-block" onclick="confirmDelete({{ $gudang->id }})">
                <i class="fas fa-trash mr-2"></i>Hapus Gudang
            </button>
        @endif
    </div>
</div>

@if (isset($gudang))
    @push('scripts')
        <script>
            function confirmDelete(id) {
                if (confirm('Apakah Anda yakin ingin menghapus gudang ini?')) {
                    document.getElementById('delete-form-' + id).submit();
                }
            }
        </script>
    @endpush
@endif