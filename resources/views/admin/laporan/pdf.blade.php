<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pendapatan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h2 { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table th, table td { border: 1px solid #ddd; padding: 8px; }
        table th { background-color: #f4f4f4; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background-color: #f9f9f9; font-weight: bold; }
        .chart-box {
            margin: 20px 0;
            padding: 15px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .charts-section {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin: 20px 0;
        }
        .chart-container {
            flex: 1 1 45%;
            min-height: 250px;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            background: #fff;
        }
        .chart-container canvas {
            max-width: 100% !important;
        }
        .chart-title {
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <!-- HEADER -->
    <div class="header">
        <h2>LAPORAN PENDAPATAN PENYEWAAN RAK</h2>
        <h3>
            @if($month)
                {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }} {{ $year }}
            @else
                Tahun {{ $year }}
            @endif
        </h3>
        <p>Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <!-- TABLE -->
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Bulan</th>
                <th class="text-center">Total Transaksi</th>
                <th class="text-center">Rak Disewa</th>
                <th class="text-right">Total Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($revenues as $index => $revenue)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $revenue->month_name }} {{ $revenue->year }}</td>
                <td class="text-center">{{ number_format($revenue->total_transactions) }}</td>
                <td class="text-center">{{ number_format($revenue->total_raks_rented) }}</td>
                <td class="text-right">Rp {{ number_format($revenue->total_revenue, 0, ',', '.') }}</td>
            </tr>
            @endforeach

            <tr class="total-row">
                <td colspan="2" class="text-center">TOTAL</td>
                <td class="text-center">{{ number_format($revenues->sum('total_transactions')) }}</td>
                <td class="text-center">{{ number_format($revenues->sum('total_raks_rented')) }}</td>
                <td class="text-right">Rp {{ number_format($yearlyTotal, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- CHARTS SECTION -->
    <div class="charts-section">
        <!-- Row 1: Transaksi Bulanan and Status Rak -->
        <div class="chart-container">
            <div class="chart-title">Transaksi {{ $month ? \Carbon\Carbon::create(null, $month)->translatedFormat('F') . ' ' : '' }}{{ $year }}</div>
            <canvas id="transaksiChart" width="300" height="200"></canvas>
        </div>

        <div class="chart-container">
            <div class="chart-title">Status Rak</div>
            <canvas id="rakChart" width="300" height="200"></canvas>
        </div>

        <!-- Row 2: Pendapatan and Status Transaksi -->
        <div class="chart-container">
            <div class="chart-title">Pendapatan {{ $month ? \Carbon\Carbon::create(null, $month)->translatedFormat('F') . ' ' : '' }}{{ $year }}</div>
            <canvas id="pendapatanChart" width="300" height="200"></canvas>
        </div>

        <div class="chart-container">
            <div class="chart-title">Status Transaksi</div>
            <canvas id="statusChart" width="300" height="200"></canvas>
        </div>
    </div>

    <script>
        // Grafik Transaksi Bulanan
        const transaksiCtx = document.getElementById('transaksiChart').getContext('2d');
        const transaksiChart = new Chart(transaksiCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartData['transaksiLabels']) !!},
                datasets: [{
                    label: 'Jumlah Transaksi',
                    data: {!! json_encode($chartData['transaksiData']) !!},
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 2,
                    pointRadius: 4,
                    pointBackgroundColor: '#667eea',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
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
                    data: [{{ $chartData['rakTerisi'] }}, {{ $chartData['rakTersedia'] }}, {{ $chartData['rakMaintenance'] }}],
                    backgroundColor: [
                        'rgba(245, 87, 108, 0.8)',
                        'rgba(67, 233, 123, 0.8)',
                        'rgba(231, 255, 10, 0.8)'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    }
                }
            }
        });

        // Grafik Pendapatan
        const pendapatanCtx = document.getElementById('pendapatanChart').getContext('2d');
        const pendapatanChart = new Chart(pendapatanCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartData['pendapatanLabels']) !!},
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: {!! json_encode($chartData['pendapatanData']) !!},
                    backgroundColor: 'rgba(67, 233, 123, 0.7)',
                    borderColor: 'rgba(67, 233, 123, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
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
                    data: [{{ $chartData['statusSuccess'] }}, {{ $chartData['statusPending'] }}, {{ $chartData['statusFailed'] }}],
                    backgroundColor: [
                        'rgba(67, 233, 123, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(220, 53, 69, 0.8)'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    }
                }
            }
        });
    </script>

    <div class="footer">
        <p>Dokumen ini digenerate otomatis oleh sistem.</p>
    </div>

</body>
</html>
