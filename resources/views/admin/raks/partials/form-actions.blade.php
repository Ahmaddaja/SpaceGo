{{-- resources/views/admin/raks/partials/form-actions.blade.php --}}

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        @if (isset($rak))
            {{-- MODE EDIT: 3 Tombol (Vertikal OK) --}}
            <button type="submit" class="btn btn-primary btn-block mb-2">
                <i class="fas fa-save mr-2"></i>{{ $submitText ?? 'Update Rak' }}
            </button>

            <a href="{{ route('raks.index') }}" class="btn btn-secondary btn-block mb-2">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>

            <hr>

            <form id="delete-form-{{ $rak->id }}" action="{{ route('raks.destroy', $rak->id) }}" method="POST"
                style="display: inline;">
                @csrf
                @method('DELETE')
            </form>

            <button type="button" class="btn btn-danger btn-block" onclick="confirmDelete({{ $rak->id }})">
                <i class="fas fa-trash mr-2"></i>Hapus Rak
            </button>
        @else
            {{-- MODE CREATE: 2 Tombol (Horizontal) --}}
            <div class="d-flex justify-content-between align-items-center">
                {{-- Tombol Batal (Kiri) --}}
                <button type="button" onclick="cancelCreateRak()" class="btn btn-secondary" style="min-width: 120px;">
                    <i class="fas fa-arrow-left mr-2"></i>Batal
                </button>

                {{-- Tombol Submit (Kanan) --}}
                <button type="submit" class="btn btn-primary" style="min-width: 150px;">
                    <i class="fas fa-save mr-2"></i>{{ $submitText ?? 'Simpan Rak' }}
                </button>
            </div>
        @endif
    </div>
</div>

{{-- JavaScript untuk Delete (hanya di mode EDIT) --}}
@if (isset($rak))
    @push('scripts')
        <script>
            function confirmDelete(id) {
                if (confirm('Apakah Anda yakin ingin menghapus rak ini? Data yang sudah dihapus tidak dapat dikembalikan.')) {
                    document.getElementById('delete-form-' + id).submit();
                }
            }
        </script>
    @endpush
@endif

{{-- ❌ HAPUS BARIS INI - Menyebabkan duplikasi --}}
{{-- @include('admin.raks.partials.scripts') --}}
