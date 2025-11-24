<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="mb-0 font-weight-bold">Aksi</h5>
    </div>
    <div class="card-body">
        <a href="{{ route('gudangs.edit', $gudang->id) }}" class="btn btn-warning btn-block">
            <i class="fas fa-edit mr-2"></i>Edit Gudang
        </a>
        <a href="{{ route('gudangs.index') }}" class="btn btn-secondary btn-block">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
        <hr>
        <button type="button" 
                class="btn btn-danger btn-block" 
                onclick="confirmDelete({{ $gudang->id }})">
            <i class="fas fa-trash mr-2"></i>Hapus Gudang
        </button>
        <form id="delete-form-{{ $gudang->id }}" 
              action="{{ route('gudangs.destroy', $gudang->id) }}" 
              method="POST" 
              style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>

@push('scripts')
<script>
function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus gudang ini?')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
@endpush