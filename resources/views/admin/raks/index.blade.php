@extends('layouts.main', ['title' => 'Kelola Rak'])

@section('title-content')
    <div class="d-flex justify-content-between align-items-right">
        <h1 class="m-0">Kelola Rak</h1>
        <x-breadcump-rak page="Kelola" module="Rak" routePrefix="raks" />
    </div>  
@endsection

@section('content')
    <div class="container-fluid">
        <x-alert />

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 font-weight-bold">Daftar Rak Gudang</h5>
                <a href="{{ route('raks.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-2"></i>Tambah rak baru
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
                <div class="card-footer border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Menampilkan 
                            <span class="font-weight-bold">{{ $raks->firstItem() }}</span> 
                            sampai 
                            <span class="font-weight-bold">{{ $raks->lastItem() }}</span> 
                            dari 
                            <span class="font-weight-bold">{{ $raks->total() }}</span> 
                            rak
                        </div>
                        <nav>
                            <ul class="pagination mb-0">
                                <!-- Previous Page Link -->
                                @if ($raks->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">
                                            <i class="fas fa-chevron-left"></i>
                                        </span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $raks->previousPageUrl() }}" rel="prev">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>
                                @endif

                                <!-- Pagination Elements -->
                                @php
                                    $current = $raks->currentPage();
                                    $last = $raks->lastPage();
                                    $start = max(1, $current - 2);
                                    $end = min($last, $current + 2);
                                @endphp

                                <!-- First Page Link -->
                                @if($start > 1)
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $raks->url(1) }}">1</a>
                                    </li>
                                    @if($start > 2)
                                        <li class="page-item disabled">
                                            <span class="page-link">...</span>
                                        </li>
                                    @endif
                                @endif

                                <!-- Page Number Links -->
                                @for ($page = $start; $page <= $end; $page++)
                                    @if ($page == $current)
                                        <li class="page-item active">
                                            <span class="page-link">
                                                {{ $page }}
                                            </span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $raks->url($page) }}">
                                                {{ $page }}
                                            </a>
                                        </li>
                                    @endif
                                @endfor

                                <!-- Last Page Link -->
                                @if($end < $last)
                                    @if($end < $last - 1)
                                        <li class="page-item disabled">
                                            <span class="page-link">...</span>
                                        </li>
                                    @endif
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $raks->url($last) }}">
                                            {{ $last }}
                                        </a>
                                    </li>
                                @endif

                                <!-- Next Page Link -->
                                @if ($raks->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $raks->nextPageUrl() }}" rel="next">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link">
                                            <i class="fas fa-chevron-right"></i>
                                        </span>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    </div>
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

@push('styles')
<style>
    /* Pagination styles that inherit from table */
    .card-footer {
        background-color: inherit;
    }
    
    .pagination {
        margin-bottom: 0;
    }
    
    .pagination .page-link {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        line-height: 1.5;
        border: 1px solid #dee2e6;
        color: #007bff;
        background-color: #fff;
    }
    
    .pagination .page-item:first-child .page-link,
    .pagination .page-item:last-child .page-link {
        border-radius: 0.25rem;
    }
    
    .pagination .page-link:hover {
        color: #0056b3;
        background-color: #e9ecef;
        border-color: #dee2e6;
    }
    
    .pagination .page-item.active .page-link {
        color: #fff;
        background-color: #007bff;
        border-color: #007bff;
    }
    
    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #fff;
        border-color: #dee2e6;
    }

    /* Dark mode support - akan mengikuti theme table */
    .table-dark ~ .card-footer .pagination .page-link {
        background-color: #343a40;
        border-color: #454d55;
        color: #fff;
    }
    
    .table-dark ~ .card-footer .pagination .page-link:hover {
        background-color: #495057;
        border-color: #565e64;
        color: #fff;
    }
    
    .table-dark ~ .card-footer .pagination .page-item.active .page-link {
        background-color: #007bff;
        border-color: #007bff;
        color: #fff;
    }
    
    .table-dark ~ .card-footer .pagination .page-item.disabled .page-link {
        background-color: #343a40;
        border-color: #454d55;
        color: #6c757d;
    }

    /* Jika menggunakan class dark pada parent */
    .dark .card-footer .pagination .page-link {
        background-color: #374151;
        border-color: #4b5563;
        color: #d1d5db;
    }
    
    .dark .card-footer .pagination .page-link:hover {
        background-color: #4b5563;
        border-color: #6b7280;
        color: #fff;
    }
    
    .dark .card-footer .pagination .page-item.active .page-link {
        background-color: #3b82f6;
        border-color: #3b82f6;
        color: #fff;
    }
    
    .dark .card-footer .pagination .page-item.disabled .page-link {
        background-color: #374151;
        border-color: #4b5563;
        color: #6b7280;
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .card-footer > div {
            flex-direction: column;
            gap: 1rem;
            text-align: center;
        }
        
        .pagination .page-link {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }
    }
</style>
@endpush