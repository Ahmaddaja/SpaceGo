@extends('layouts.main')

@section('title-content')
    <x-breadcrumb page="Index" />
@endsection

@section('content')
<div class="container-fluid">

    <x-alert />

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 font-weight-bold">Daftar Gudang</h5>
            <a href="{{ route('gudangs.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i>Tambah Gudang Baru
            </a>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">

                    <x-table-header />

                    <tbody>
                        @forelse($gudangs as $gudang)
                            <x-table-row :gudang="$gudang" />
                        @empty
                            <x-empty-state />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($gudangs->hasPages())
        <div class="card-footer bg-white border-0">
            {{ $gudangs->links() }}
        </div>
        @endif
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

@endsection
