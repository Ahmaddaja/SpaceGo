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
        .chart-title {
            font-weight: bold;
            text-align: center;
            margin-bottom: 15px;
        }
        /* Bar Chart Styles */
        .bar-chart {
            display: flex;
            align-items: end;
            justify-content: space-between;
            height: 150px;
            margin: 20px 0;
            padding: 0 10px;
        }
        .bar {
            flex: 1;
            background: #43e97b;
            margin: 0 2px;
            border-radius: 4px 4px 0 0;
            min-height: 10px;
            position: relative;
        }
        .bar-label {
            position: absolute;
            bottom: -20px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 9px;
            text-align: center;
            white-space: nowrap;
        }
        .bar-value {
            position: absolute;
            top: -18px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 8px;
            text-align: center;
            font-weight: bold;
        }
        /* Doughnut Chart Styles */
        .doughnut-chart {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            margin: 20px 0;
        }
        .doughnut-legend {
            display: flex;
            flex-direction: column;
            margin-left: 20px;
        }
        .legend-item {
            display: flex;
            align-items: center;
            margin: 5px 0;
            font-size: 10px;
        }
        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 5px;
        }
        /* Simple horizontal bar for doughnut data */
        .doughnut-bar {
            width: 100%;
            display: flex;
            height: 20px;
            margin: 10px 0;
            border-radius: 10px;
            overflow: hidden;
            background: #f0f0f0;
        }
        .doughnut-segment {
            height: 100%;
            display: inline-block;
        }
        /* Container for percentages */
        .doughnut-text {
            text-align: center;
            margin-top: 10px;
            font-size: 11px;
        }
        /* Simple data table for complex charts */
        .chart-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .chart-table th, .chart-table td {
            border: 1px solid #ddd;
            padding: 4px 6px;
            font-size: 10px;
        }
        .chart-table th {
            background: #f9f9f9;
            font-weight: bold;
        }
    </style>
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
            <table class="chart-table">
                <thead>
                    <tr>
                        <th>Bulan</th>
                        <th>Jumlah Transaksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($chartData['transaksiLabels'] as $index => $label)
                    <tr>
                        <td>{{ $label }}</td>
                        <td>{{ $chartData['transaksiData'][$index] ?? 0 }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="chart-container">
            <div class="chart-title">Status Rak</div>
            <table class="chart-table">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Jumlah</th>
                        <th>Persentase</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $totalRaks = $chartData['rakTerisi'] + $chartData['rakTersedia'] + $chartData['rakMaintenance'];
                    $rakStatuses = [
                        'Terisi' => ['color' => '#f5576c', 'value' => $chartData['rakTerisi']],
                        'Tersedia' => ['color' => '#43e97b', 'value' => $chartData['rakTersedia']],
                        'Maintenance' => ['color' => '#e7ff0a', 'value' => $chartData['rakMaintenance']]
                    ];
                    @endphp
                    @foreach($rakStatuses as $name => $data)
                    <tr>
                        <td>{{ $name }}</td>
                        <td>{{ $data['value'] }}</td>
                        <td>{{ $totalRaks > 0 ? number_format(($data['value'] / $totalRaks) * 100, 1) : 0 }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="doughnut-chart">
                <div style="width: 80px; height: 80px; border-radius: 50%; margin: 20px auto; background:
                    @if($totalRaks > 0)
                        conic-gradient(
                            #f5576c 0% {{ (($chartData['rakTerisi'] / $totalRaks) * 100) }}%,
                            #43e97b {{ (($chartData['rakTerisi'] / $totalRaks) * 100) }}% {{ (($chartData['rakTerisi'] + $chartData['rakTersedia']) / $totalRaks) * 100 }}%,
                            #e7ff0a {{ (($chartData['rakTerisi'] + $chartData['rakTersedia']) / $totalRaks) * 100 }}% 100%
                        )
                    @else
                        #f0f0f0
                    @endif">
                </div>
            </div>
        </div>

        <!-- Row 2: Pendapatan and Status Transaksi -->
        <div class="chart-container">
            <div class="chart-title">Pendapatan {{ $month ? \Carbon\Carbon::create(null, $month)->translatedFormat('F') . ' ' : '' }}{{ $year }}</div>
            <table class="chart-table">
                <thead>
                    <tr>
                        <th>Bulan</th>
                        <th>Pendapatan (Rp)</th>
                        <th>Persentase</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $totalRevenue = array_sum($chartData['pendapatanData']);
                    @endphp
                    @foreach($chartData['pendapatanLabels'] as $index => $label)
                    <tr>
                        <td>{{ $label }}</td>
                        <td>Rp {{ number_format($chartData['pendapatanData'][$index] ?? 0, 0, ',', '.') }}</td>
                        <td>{{ $totalRevenue > 0 ? number_format((($chartData['pendapatanData'][$index] ?? 0) / $totalRevenue) * 100, 1) : 0 }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="chart-container">
            <div class="chart-title">Status Transaksi</div>
            <table class="chart-table">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Jumlah</th>
                        <th>Persentase</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $totalTransactions = $chartData['statusSuccess'] + $chartData['statusPending'] + $chartData['statusFailed'];
                    $statusTypes = [
                        'Sukses' => ['color' => '#43e97b', 'value' => $chartData['statusSuccess']],
                        'Pending' => ['color' => '#ffc107', 'value' => $chartData['statusPending']],
                        'Gagal' => ['color' => '#dc3545', 'value' => $chartData['statusFailed']]
                    ];
                    @endphp
                    @foreach($statusTypes as $name => $data)
                    <tr>
                        <td>{{ $name }}</td>
                        <td>{{ $data['value'] }}</td>
                        <td>{{ $totalTransactions > 0 ? number_format(($data['value'] / $totalTransactions) * 100, 1) : 0 }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="doughnut-chart">
                <div style="width: 80px; height: 80px; border-radius: 50%; margin: 20px auto; background:
                    @if($totalTransactions > 0)
                        conic-gradient(
                            #43e97b 0% {{ (($chartData['statusSuccess'] / $totalTransactions) * 100) }}%,
                            #ffc107 {{ (($chartData['statusSuccess'] / $totalTransactions) * 100) }}% {{ (($chartData['statusSuccess'] + $chartData['statusPending']) / $totalTransactions) * 100 }}%,
                            #dc3545 {{ (($chartData['statusSuccess'] + $chartData['statusPending']) / $totalTransactions) * 100 }}% 100%
                        )
                    @else
                        #f0f0f0
                    @endif">
                </div>
            </div>
        </div>
    </div>



    <div class="footer">
        <p>Dokumen ini digenerate otomatis oleh sistem.</p>
    </div>

</body>
</html>
