@extends('layouts.main', ['title' => 'Dashboard'])

@section('title-content')
<div class="d-flex justify-content-between align-items-right">
    <h1 class="m-0">Dashboard</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent p-0 m-0 justify-content-end small">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </nav>
</div>
@endsection

@section('content')
<div class="row">

    <!-- Total Gedung -->
    <div class="col-lg-3 col-md-6 col-12 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-primary mb-2">
                            <i class="fas fa-building fa-2x"></i>
                        </div>
                        <h2 class="mb-0 font-weight-bold">{{ $totalGudang }}</h2>
                        <p class="text-muted mb-1">Total Gedung</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Pelanggan -->
    <div class="col-lg-3 col-md-6 col-12 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-info mb-2">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                        <h2 class="mb-0 font-weight-bold">{{ $totalPelanggan }}</h2>
                        <p class="text-muted mb-1">Total Pelanggan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
