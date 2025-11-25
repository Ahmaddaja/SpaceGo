@extends('layouts.main')

@section('title-content')
    <x-breadcump-rak page="Kelola" module="Rak" routePrefix="raks" />
@endsection

@section('content')
    <div class="container-fluid">
        <x-alert />

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 font-weight-bold">Daftar Rak Gudang</h5>
                <a href="{{ route('raks.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-2"></i>Tambah Rak Baru
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        @include('admin.raks.partials.table-header')
                        <tbody>
                            @forelse($raks as $rak)
                                @include('admin.raks.partials.table-row', ['rak' => $rak])
                            @empty
                                @include('admin.raks.partials.empty-state')
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($raks->hasPages())
                <div class="card-footer bg-white border-0">
                    {{ $raks->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            function confirmDelete(id) {
                if (confirm('Apakah Anda yakin ingin menghapus rak ini?')) {
                    document.getElementById('delete-form-' + id).submit();
                }
            }
        </script>
    @endpush

@endsection
