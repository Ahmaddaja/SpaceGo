@extends('layouts.main', ['title' => 'Analisis Performa & Tren'])

@push('styles')
    <style>
        .kpi-card {
            transition: all 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
        }

        .kpi-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
        }

        .accent-bar {
            height: 4px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        }

        .trend-up {
            color: #28a745;
        }

        .trend-down {
            color: #dc3545;
        }

        .chart-container {
            position: relative;
            height: 400px;
        }
    </style>
@endpush

@section('title-content')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">Analisis Performa & Tren</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.laporan.pendapatan') }}">Laporan Pendapatan</a></li>
                <li class="breadcrumb-item active">Analisis Performa</li>
            </ol>
        </nav>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Customer Growth Chart
        const customerGrowthCtx = document.getElementById('customerGrowthChart').getContext('2d');
        const customerGrowthChart = new Chart(customerGrowthCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($growthLabels) !!},
                datasets: [{
                    label: 'Total Customer',
                    data: {!! json_encode($newCustomerData) !!},
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Customer Berulang',
                    data: {!! json_encode($repeatCustomerData) !!},
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // Revenue Trend Chart
        const revenueTrendCtx = document.getElementById('revenueTrendChart').getContext('2d');
        const revenueTrendChart = new Chart(revenueTrendCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($trendLabels) !!},
                datasets: [{
                    label: 'Pendapatan Bulanan',
                    data: {!! json_encode($monthlyRevenueData) !!},
                    backgroundColor: 'rgba(255, 193, 7, 0.7)',
                    borderColor: 'rgba(255, 193, 7, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                            }
                        }
                    }
                }
            }
        });

        // Occupancy Rate Chart
        const occupancyCtx = document.getElementById('occupancyChart').getContext('2d');
        const occupancyChart = new Chart(occupancyCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($occupancyLabels) !!},
                datasets: [{
                    label: 'Tingkat Hunian (%)',
                    data: {!! json_encode($occupancyData) !!},
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });
    </script>
@endpush

@section('content')
    <div class="container-fluid">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        <!-- KPI Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card kpi-card border-0 shadow-sm">
                    <div class="accent-bar"></div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-muted mb-1">Rata-rata Pendapatan per Transaksi</h6>
                                <h4 class="text-success mb-0">
                                    Rp {{ number_format($avgRevenuePerTransaction, 0, ',', '.') }}
                                </h4>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-dollar-sign text-success fa-2x"></i>
                            </div>
                        </div>
                        <small class="text-muted">
                            @if($revenueGrowth > 0)
                                <span class="trend-up"><i class="fas fa-arrow-up"></i> {{ number_format($revenueGrowth, 1) }}%</span> vs bulan lalu
                            @elseif($revenueGrowth < 0)
                                <span class="trend-down"><i class="fas fa-arrow-down"></i> {{ number_format(abs($revenueGrowth), 1) }}%</span> vs bulan lalu
                            @else
                                <span>-</span> vs bulan lalu
                            @endif
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card kpi-card border-0 shadow-sm">
                    <div class="accent-bar"></div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-muted mb-1">Tingkat Retensi Customer</h6>
                                <h4 class="text-primary mb-0">
                                    {{ number_format($customerRetentionRate, 1) }}%
                                </h4>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-users text-primary fa-2x"></i>
                            </div>
                        </div>
                        <small class="text-muted">
                            {{ $totalRepeatCustomers }} customer berulang
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card kpi-card border-0 shadow-sm">
                    <div class="accent-bar"></div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-muted mb-1">Tingkat Hunian</h6>
                                <h4 class="text-warning mb-0">
                                    {{ number_format($currentOccupancyRate, 1) }}%
                                </h4>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-chart-pie text-warning fa-2x"></i>
                            </div>
                        </div>
                        <small class="text-muted">
                            {{ $occupiedRaks }}/{{ $totalRaks }} rak terisi
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card kpi-card border-0 shadow-sm">
                    <div class="accent-bar"></div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-muted mb-1">Tingkat Keberhasilan Transaksi</h6>
                                <h4 class="text-info mb-0">
                                    {{ number_format($transactionSuccessRate, 1) }}%
                                </h4>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-check-circle text-info fa-2x"></i>
                            </div>
                        </div>
                        <small class="text-muted">
                            {{ $successfulTransactions }}/{{ $totalTransactions }} transaksi berhasil
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.laporan.performance') }}">
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
                            <label class="form-label">Dari Bulan</label>
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
                            <label class="form-label">Sampai Bulan</label>
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
                                @foreach (\App\Models\Transaction::selectRaw('DISTINCT YEAR(transaction_time) as year')->orderBy('year', 'desc')->pluck('year') as $y)
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
                            <a href="{{ route('admin.laporan.performance') }}" class="btn btn-secondary">
                                <i class="fas fa-undo"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Analysis Section -->
        <div class="row mb-4">
            <!-- Customer Growth Analysis -->
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-0 bg-white">
                        <h5 class="mb-0">
                            <i class="fas fa-users text-primary me-2"></i>
                            Analisis Pertumbuhan Customer
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="customerGrowthChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gudang Performance Table -->
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-0 bg-white">
                        <h5 class="mb-0">
                            <i class="fas fa-building text-warning me-2"></i>
                            Performa Gudang
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 400px;">
                            <table class="table table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Gudang</th>
                                        <th class="text-center">Pendapatan</th>
                                        <th class="text-center">Hunian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($gudangPerformance as $performance)
                                    <tr>
                                        <td class="font-weight-bold">{{ $performance->nama_gudang }}</td>
                                        <td class="text-center text-success">
                                            Rp {{ number_format($performance->total_revenue, 0, ',', '.') }}
                                        </td>
                                        <td class="text-center">
                                            {{ number_format($performance->occupancy_rate, 1) }}%
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">
                                            Tidak ada data performa gudang
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Charts -->
        <div class="row mb-4">
            <!-- Revenue Trends -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-0 bg-white">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-line text-success me-2"></i>
                            Tren Pendapatan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="revenueTrendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Occupancy Trends -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-0 bg-white">
                        <h5 class="mb-0">
                            <i class="fas fa-home text-danger me-2"></i>
                            Tren Tingkat Hunian
                        </h5>
                        <small class="text-muted">Persentase rak yang terisi per bulan</small>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="occupancyChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back Button -->
        <div class="row">
            <div class="col-12">
                <a href="{{ route('admin.laporan.pendapatan') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali ke Laporan Pendapatan
                </a>
            </div>
        </div>

    </div>
@endsection
