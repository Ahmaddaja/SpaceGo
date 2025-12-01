@extends('layouts.main', ['title' => 'Dashboard'])

@push('styles')
    <style>
        .card {
            transition: all 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
        }

        .chart-card .card-body {
            height: 350px !important;
        }

        .chart-card canvas {
            max-height: 100% !important;
        }


        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-end) 100%);
            color: white;
            border: none;
        }

        .stat-card .icon-box {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }

        .stat-card h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0.5rem 0;
        }

        .stat-card p {
            opacity: 0.9;
            margin: 0;
            font-size: 0.95rem;
        }

        .chart-card {
            height: 100%;
        }

        .chart-card .card-body {
            padding: 1.5rem;
        }

        .badge-status {
            font-size: 0.75rem;
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-weight: 600;
        }

        .transaction-item {
            padding: 1rem;
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.2s;
        }

        .transaction-item:hover {
            background: #f8f9fa;
        }

        .transaction-item:last-child {
            border-bottom: none;
        }
    </style>
@endpush

@section('title-content')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 mb-1">Dashboard</h1>
            <p class="text-muted mb-0">Selamat datang di sistem manajemen gudang</p>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 m-0 justify-content-end">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </nav>
    </div>
@endsection

@section('content')
    <!-- Statistik Cards -->
    <div class="row mb-4">
        <!-- Total Gedung -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card shadow-sm" style="--gradient-start: #667eea; --gradient-end: #764ba2;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-2">Total Gedung</p>
                            <h2>{{ $totalGudang }}</h2>
                        </div>
                        <div class="icon-box">
                            <i class="fas fa-building fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Rak -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card shadow-sm" style="--gradient-start: #f093fb; --gradient-end: #f5576c;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-2">Total Rak</p>
                            <h2>{{ $totalRak }}</h2>
                        </div>
                        <div class="icon-box">
                            <i class="fas fa-boxes fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Pelanggan -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card shadow-sm" style="--gradient-start: #4facfe; --gradient-end: #00f2fe;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-2">Total Pelanggan</p>
                            <h2>{{ $totalPelanggan }}</h2>
                        </div>
                        <div class="icon-box">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Transaksi -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card shadow-sm" style="--gradient-start: #43e97b; --gradient-end: #38f9d7;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-2">Total Transaksi</p>
                            <h2>{{ $totalTransaksi }}</h2>
                        </div>
                        <div class="icon-box">
                            <i class="fas fa-shopping-cart fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Cards Tambahan -->
    <div class="row mb-4">
        <!-- Transaksi Bulan Ini -->
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Transaksi Bulan Ini</p>
                            <h3 class="mb-0 font-weight-bold text-primary">{{ $transaksiBulanIni }}</h3>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-calendar-check fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pendapatan Bulan Ini -->
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Pendapatan Bulan Ini</p>
                            <h3 class="mb-0 font-weight-bold text-success">Rp
                                {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</h3>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-money-bill-wave fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Pendapatan -->
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Pendapatan</p>
                            <h3 class="mb-0 font-weight-bold text-warning">Rp
                                {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Section -->
    <div class="row mb-4">
        <!-- Grafik Transaksi Bulanan -->
        <div class="col-lg-8 mb-3">
            <div class="card border-0 shadow-sm chart-card">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 font-weight-bold">
                        <i class="fas fa-chart-area text-primary me-2"></i>
                        Transaksi 12 Bulan Terakhir
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

    <!-- Row 4: Grafik Pendapatan & Status Transaksi -->
    <div class="row mb-4">
        <!-- Grafik Pendapatan -->
        <div class="col-lg-8 mb-3">
            <div class="card border-0 shadow-sm chart-card">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0 font-weight-bold">
                        <i class="fas fa-chart-bar text-warning me-2"></i>
                        Pendapatan 6 Bulan Terakhir
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

    <!-- Transaksi Terbaru -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 font-weight-bold">
                        <i class="fas fa-history text-secondary me-2"></i>
                        Transaksi Terbaru
                    </h5>
                    <a href="{{ route('admin.transactions.index') }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    @forelse($recentTransactions as $transaction)
                        <div class="transaction-item">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <small class="text-muted d-block">Order ID</small>
                                    <strong>{{ $transaction->order_id }}</strong>
                                </div>
                                <div class="col-md-2">
                                    <small class="text-muted d-block">Pelanggan</small>
                                    <strong>{{ $transaction->user->name }}</strong>
                                </div>
                                <div class="col-md-2">
                                    <small class="text-muted d-block">Rak</small>
                                    <strong>{{ $transaction->rak->nama_rak }}</strong>
                                </div>
                                <div class="col-md-2">
                                    <small class="text-muted d-block">Jumlah</small>
                                    <strong class="text-success">Rp
                                        {{ number_format($transaction->amount, 0, ',', '.') }}</strong>
                                </div>
                                <div class="col-md-2">
                                    <small class="text-muted d-block">Status</small>
                                    @if (in_array($transaction->transaction_status, ['capture', 'settlement']))
                                        <span class="badge badge-status bg-success">Sukses</span>
                                    @elseif($transaction->transaction_status == 'pending')
                                        <span class="badge badge-status bg-warning text-dark">Pending</span>
                                    @else
                                        <span class="badge badge-status bg-danger">Gagal</span>
                                    @endif
                                </div>
                                <div class="col-md-1 text-end">
                                    <small
                                        class="text-muted">{{ $transaction->transaction_time->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada transaksi</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
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
                labels: ['Terisi', 'Tersedia', 'Kosong'],
                datasets: [{
                    data: [{{ $rakTerisi }}, {{ $rakTersedia }}, {{ $rakKosong }}],
                    backgroundColor: [
                        'rgba(245, 87, 108, 0.8)',
                        'rgba(67, 233, 123, 0.8)',
                        'rgba(108, 117, 125, 0.8)'
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
