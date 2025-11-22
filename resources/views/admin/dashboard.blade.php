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
<!-- Statistics Cards -->
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
                        <h2 class="mb-0 font-weight-bold">24</h2>
                        <p class="text-muted mb-1">Total Gedung</p>
                        <small class="text-muted">dari bulan lalu</small>
                    </div>
                    <div class="text-right">
                        <span class="badge badge-success">
                            <i class="fas fa-arrow-up"></i> +12%
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Aktif -->
    <div class="col-lg-3 col-md-6 col-12 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-success mb-2">
                            <i class="fas fa-calendar-check fa-2x"></i>
                        </div>
                        <h2 class="mb-0 font-weight-bold">156</h2>
                        <p class="text-muted mb-1">Booking Aktif</p>
                        <small class="text-muted">dari bulan lalu</small>
                    </div>
                    <div class="text-right">
                        <span class="badge badge-success">
                            <i class="fas fa-arrow-up"></i> +8%
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pendapatan -->
    <div class="col-lg-3 col-md-6 col-12 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-warning mb-2">
                            <i class="fas fa-wallet fa-2x"></i>
                        </div>
                        <h2 class="mb-0 font-weight-bold">Rp 45.2M</h2>
                        <p class="text-muted mb-1">Pendapatan</p>
                        <small class="text-muted">dari bulan lalu</small>
                    </div>
                    <div class="text-right">
                        <span class="badge badge-success">
                            <i class="fas fa-arrow-up"></i> +23%
                        </span>
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
                        <h2 class="mb-0 font-weight-bold">892</h2>
                        <p class="text-muted mb-1">Total Pelanggan</p>
                        <small class="text-muted">dari bulan lalu</small>
                    </div>
                    <div class="text-right">
                        <span class="badge badge-success">
                            <i class="fas fa-arrow-up"></i> +5%
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Booking Terbaru -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 font-weight-bold">Booking Terbaru</h5>
        <a href="/booking" class="btn btn-primary btn-sm ml-auto">Lihat Semua</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0">Pelanggan</th>
                        <th class="border-0">Gedung</th>
                        <th class="border-0">Tanggal</th>
                        <th class="border-0">Status</th>
                        <th class="border-0">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="align-middle">
                            <div class="d-flex align-items-center">
                                <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center mr-3 text-white font-weight-bold" style="width: 45px; height: 45px; min-width: 45px;">
                                    LC
                                </div>
                                <div>
                                    <div class="font-weight-bold">Lindsey Curtis</div>
                                    <small class="text-muted">Event Organizer</small>
                                </div>
                            </div>
                        </td>
                        <td class="align-middle">Gedung Serbaguna A</td>
                        <td class="align-middle">22 Nov 2025</td>
                        <td class="align-middle">
                            <span class="badge badge-success px-3 py-2">Active</span>
                        </td>
                        <td class="align-middle">
                            <strong>Rp 3.9K</strong>
                        </td>
                    </tr>
                    <tr>
                        <td class="align-middle">
                            <div class="d-flex align-items-center">
                                <div class="bg-success rounded-circle d-flex align-items-center justify-content-center mr-3 text-white font-weight-bold" style="width: 45px; height: 45px; min-width: 45px;">
                                    KG
                                </div>
                                <div>
                                    <div class="font-weight-bold">Kaiya George</div>
                                    <small class="text-muted">Wedding Planner</small>
                                </div>
                            </div>
                        </td>
                        <td class="align-middle">Ballroom Utama</td>
                        <td class="align-middle">25 Nov 2025</td>
                        <td class="align-middle">
                            <span class="badge badge-warning px-3 py-2">Pending</span>
                        </td>
                        <td class="align-middle">
                            <strong>Rp 24.9K</strong>
                        </td>
                    </tr>
                    <tr>
                        <td class="align-middle">
                            <div class="d-flex align-items-center">
                                <div class="bg-danger rounded-circle d-flex align-items-center justify-content-center mr-3 text-white font-weight-bold" style="width: 45px; height: 45px; min-width: 45px;">
                                    ZG
                                </div>
                                <div>
                                    <div class="font-weight-bold">Zain Geidt</div>
                                    <small class="text-muted">Corporate Event</small>
                                </div>
                            </div>
                        </td>
                        <td class="align-middle">Meeting Room B</td>
                        <td class="align-middle">28 Nov 2025</td>
                        <td class="align-middle">
                            <span class="badge badge-success px-3 py-2">Active</span>
                        </td>
                        <td class="align-middle">
                            <strong>Rp 12.7K</strong>
                        </td>
                    </tr>
                    <tr>
                        <td class="align-middle">
                            <div class="d-flex align-items-center">
                                <div class="bg-info rounded-circle d-flex align-items-center justify-content-center mr-3 text-white font-weight-bold" style="width: 45px; height: 45px; min-width: 45px;">
                                    AS
                                </div>
                                <div>
                                    <div class="font-weight-bold">Abram Schleifer</div>
                                    <small class="text-muted">Private Party</small>
                                </div>
                            </div>
                        </td>
                        <td class="align-middle">Rooftop Garden</td>
                        <td class="align-middle">30 Nov 2025</td>
                        <td class="align-middle">
                            <span class="badge badge-danger px-3 py-2">Cancel</span>
                        </td>
                        <td class="align-middle">
                            <strong>Rp 2.8K</strong>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection