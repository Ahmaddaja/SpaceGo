@extends('layouts.main', ['title' => 'Laporan Pendapatan'])

@push('styles')
    <style>
        .chart-card .card-body {
            height: 350px !important;
        }

        .chart-card canvas {
            max-height: 100% !important;
        }

        .card {
            transition: all 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
        }
    </style>
@endpush

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

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Konfigurasi Global Chart.js
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#6c757d';

        // Grafik Transaksi Bulanan
        const transaksiCtx = document.getElementById('transaksiChart').getContext('2d');
        const transaksiChart = new Chart(transaksiCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($transaksiLabels) !!},
                datasets: [{
                    label: 'Jumlah Transaksi',
                    data: {!! json_encode($transaksiData) !!},
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3,
                    pointRadius: 5,
                    pointBackgroundColor: '#667eea',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            padding: 15,
                            font: {
                                size: 12,
                                weight: '600'
                            }
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 5,
                            font: {
                                size: 11
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 11
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Grafik Status Rak
        const rakCtx = document.getElementById('rakChart').getContext('2d');
        const rakChart = new Chart(rakCtx, {
            type: 'doughnut',
            data: {
                labels: ['Terisi', 'Tersedia', 'Maintenance'],
                datasets: [{
                    data: [{{ $rakTerisi }}, {{ $rakTersedia }}, {{ $rakMaintenance }}],
                    backgroundColor: [
                        'rgba(245, 87, 108, 0.8)',
                        'rgba(67, 233, 123, 0.8)',
                        'rgba(231, 255, 10, 0.8)'
                    ],
                    borderWidth: 3,
                    borderColor: '#fff',
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: {
                                size: 12,
                                weight: '600'
                            },
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        cornerRadius: 8
                    }
                }
            }
        });

        // Grafik Pendapatan
        const pendapatanCtx = document.getElementById('pendapatanChart').getContext('2d');
        const pendapatanChart = new Chart(pendapatanCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($pendapatanLabels) !!},
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: {!! json_encode($pendapatanData) !!},
                    backgroundColor: 'rgba(67, 233, 123, 0.7)',
                    borderColor: 'rgba(67, 233, 123, 1)',
                    borderWidth: 2,
                    borderRadius: 8,
                    hoverBackgroundColor: 'rgba(67, 233, 123, 0.9)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            padding: 15,
                            font: {
                                size: 12,
                                weight: '600'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                                }
                                return 'Rp ' + (value / 1000).toFixed(0) + 'k';
                            },
                            font: {
                                size: 11
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 11
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Grafik Status Transaksi
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Sukses', 'Pending', 'Gagal'],
                datasets: [{
                    data: [{{ $statusSuccess }}, {{ $statusPending }}, {{ $statusFailed }}],
                    backgroundColor: [
                        'rgba(67, 233, 123, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(220, 53, 69, 0.8)'
                    ],
                    borderWidth: 3,
                    borderColor: '#fff',
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: {
                                size: 12,
                                weight: '600'
                            },
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        cornerRadius: 8
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

        <!-- Filter Card -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.laporan.pendapatan') }}">
                    <!-- Filter Row -->
                    <div class="row g-3 mb-3">
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
                        <div class="col-md-3">
                            <label class="form-label">Bulan</label>
                            <select name="month" class="form-control">
                                <option value="">Semua Bulan</option>
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create(null, $i)->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
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
                        <h6 class="text-muted">Total Pendapatan {{ $month ? \Carbon\Carbon::create(null, $month)->translatedFormat('F') . ' ' : '' }}{{ $year }}</h6>
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
                            Transaksi {{ $month ? \Carbon\Carbon::create(null, $month)->translatedFormat('F') . ' ' : '' }}{{ $year }}
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
