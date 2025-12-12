@extends('layouts.main', ['title' => 'Laporan Pendapatan'])

@section('title-content')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">Laporan Pendapatan</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Laporan Pendapatan</li>
            </ol>
        </nav>
    </div>
@endsection

@section('content')
    <div class="container-fluid">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        <!-- Filter Card -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.laporan.pendapatan') }}">
                    <!-- Filter Row -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Gudang</label>
                            <select name="gudang_id" class="form-control">
                                <option value="">Semua Gudang</option>
                                @foreach(\App\Models\Gudang::all() as $gudang)
                                    <option value="{{ $gudang->id }}" {{ request('gudang_id') == $gudang->id ? 'selected' : '' }}>
                                        {{ $gudang->nama_gudang }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control">
                                <option value="">Semua Status</option>
                                <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Berhasil</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Gagal</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Bulan Awal</label>
                            <select name="month_from" class="form-control">
                                <option value="">Pilih Bulan Awal</option>
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ request('month_from') == $i ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create(null, $i)->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Bulan Akhir</label>
                            <select name="month_to" class="form-control">
                                <option value="">Pilih Bulan Akhir</option>
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ request('month_to') == $i ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create(null, $i)->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tahun</label>
                            <select name="year" class="form-control">
                                @foreach ($availableYears as $y)
                                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Action Buttons Row -->
                    <div class="row">
                        <div class="col-12 d-flex justify-content-end align-items-center gap-2 flex-wrap">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Tampilkan
                            </button>
                            <a href="{{ route('admin.laporan.pendapatan') }}" class="btn btn-secondary">
                                Reset
                            </a>
                            <a href="{{ route('admin.laporan.performance', ['year' => $year]) }}" class="btn btn-success">
                                <i class="fas fa-chart-line"></i> Analisis Performa
                            </a>
                            <a href="{{ route('admin.laporan.view.pdf', ['year' => $year, 'month' => $month]) }}" class="btn btn-info" target="_blank">
                                <i class="fas fa-eye"></i> PDF
                            </a>
                            <a href="{{ route('admin.laporan.export.pdf', ['year' => $year, 'month' => $month]) }}" class="btn btn-danger">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">Total Pendapatan {{ $month ? \Carbon\Carbon::create(null, $month)->translatedFormat('F') . ' ' : (($monthFrom && $monthTo) ? \Carbon\Carbon::create(null, $monthFrom)->translatedFormat('M') . '-' . \Carbon\Carbon::create(null, $monthTo)->translatedFormat('M') . ' ' : '') }}{{ $year }}</h6>
                        <h2 class="text-success mb-0">
                            Rp {{ number_format($yearlyTotal, 0, ',', '.') }}
                        </h2>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">Total Transaksi {{ $month ? \Carbon\Carbon::create(null, $month)->translatedFormat('F') . ' ' : '' }}{{ $year }}</h6>
                        <h2 class="text-primary mb-0">
                            {{ number_format($yearlyTransactions) }}
                        </h2>
                    </div>
                </div>
            </div>
        </div>
        <!-- Charts Section -->
        <div class="row mb-4">
            <!-- Grafik Transaksi Bulanan -->
            <div class="col-lg-8 mb-3">
                <div class="card border-0 shadow-sm chart-card">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 font-weight-bold">
                            <i class="fas fa-chart-area text-primary me-2"></i>
                            Transaksi {{ $month ? \Carbon\Carbon::create(null, $month)->translatedFormat('F') . ' ' : (($monthFrom && $monthTo) ? \Carbon\Carbon::create(null, $monthFrom)->translatedFormat('M') . '-' . \Carbon\Carbon::create(null, $monthTo)->translatedFormat('M') . ' ' : '') }}{{ $year }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="transaksiChart" height="80"></canvas>
                    </div>
                </div>
            </div>

            <!-- Grafik Status Rak -->
            <div class="col-lg-4 mb-3">
                <div class="card border-0 shadow-sm chart-card">
                    <div class="card-header bg-white border-0">
                        <h5 class="mb-0 font-weight-bold">
                            <i class="fas fa-chart-pie text-success me-2"></i>
                            Status Rak
                        </h5>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <canvas id="rakChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row Pendapatan & Status Transaksi -->
        <div class="row mb-4">
            <!-- Grafik Pendapatan -->
            <div class="col-lg-8 mb-3">
                <div class="card border-0 shadow-sm chart-card">
                    <div class="card-header bg-white border-0">
                        <h5 class="mb-0 font-weight-bold">
                            <i class="fas fa-chart-bar text-warning me-2"></i>
                            Pendapatan {{ $month ? \Carbon\Carbon::create(null, $month)->translatedFormat('F') . ' ' : '' }}{{ $year }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="pendapatanChart" height="80"></canvas>
                    </div>
                </div>
            </div>

            <!-- Status Transaksi -->
            <div class="col-lg-4 mb-3">
                <div class="card border-0 shadow-sm chart-card">
                    <div class="card-header bg-white border-0">
                        <h5 class="mb-0 font-weight-bold">
                            <i class="fas fa-tasks text-info me-2"></i>
                            Status Transaksi
                        </h5>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <!-- Table -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Detail Pendapatan {{ $month ? 'Bulan ' . \Carbon\Carbon::create(null, $month)->translatedFormat('F') : 'per Bulan' }}</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Bulan</th>
                                <th>Tahun</th>
                                <th class="text-center">Total Transaksi</th>
                                <th class="text-center">Rak Disewa</th>
                                <th class="text-right">Total Pendapatan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($revenues as $revenue)
                                <tr>
                                    <td>{{ $revenue->month_name }}</td>
                                    <td>{{ $revenue->year }}</td>
                                    <td class="text-center">{{ number_format($revenue->total_transactions) }}</td>
                                    <td class="text-center">{{ number_format($revenue->total_raks_rented) }}</td>
                                    <td class="text-right text-success">
                                        {{ $revenue->formatted_revenue }}
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.laporan.detail', ['year' => $revenue->year, 'month' => $revenue->month]) }}"
                                            class="btn btn-sm btn-info">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        Tidak ada data untuk tahun {{ $year }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if ($revenues->isNotEmpty())
                            <tfoot class="thead-light">
                                <tr class="font-weight-bold">
                                    <td colspan="2">TOTAL</td>
                                    <td class="text-center">{{ number_format($yearlyTransactions) }}</td>
                                    <td class="text-center">{{ number_format($revenues->sum('total_raks_rented')) }}</td>
                                    <td class="text-right text-success">
                                        Rp {{ number_format($yearlyTotal, 0, ',', '.') }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection