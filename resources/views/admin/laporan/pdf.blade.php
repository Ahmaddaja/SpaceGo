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

        /* New grid-based image layout for PDF-friendly charts */
        .chart-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px 0;
        }
        .chart-card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
            min-height: 140px;
        }
        .chart-card.large {
            grid-column: 1 / -1;
        }
        .chart-img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
            border-radius: 4px;
        }
        .chart-caption { font-weight: bold; margin-bottom: 8px; }
        @media print { .chart-grid { page-break-inside: avoid; } }
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
                <th class="text-center">Sukses</th>
                <th class="text-center">Pending</th>
                <th class="text-center">Gagal</th>
                <th class="text-center">Rak Disewa</th>
                <th class="text-right">Total Pendapatan</th>
                <th class="text-right">Avg Transaksi</th>
            </tr>
        </thead>
        <tbody>
            @php
                $sumSuccess = 0;
                $sumPending = 0;
                $sumFailed = 0;
                $sumTransactions = 0;
                $sumRaks = 0;
                $sumRevenue = 0;
            @endphp
            @foreach($revenues as $index => $revenue)
            @php
                $sumSuccess += $revenue->success_count ?? 0;
                $sumPending += $revenue->pending_count ?? 0;
                $sumFailed += $revenue->failed_count ?? 0;
                $sumTransactions += $revenue->total_transactions ?? 0;
                $sumRaks += $revenue->total_raks_rented ?? 0;
                $sumRevenue += $revenue->total_revenue ?? 0;
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $revenue->month_name }} {{ $revenue->year }}</td>
                <td class="text-center">{{ number_format($revenue->total_transactions) }}</td>
                <td class="text-center">{{ number_format($revenue->success_count ?? 0) }}</td>
                <td class="text-center">{{ number_format($revenue->pending_count ?? 0) }}</td>
                <td class="text-center">{{ number_format($revenue->failed_count ?? 0) }}</td>
                <td class="text-center">{{ number_format($revenue->total_raks_rented) }}</td>
                <td class="text-right">Rp {{ number_format($revenue->total_revenue, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($revenue->avg_amount ?? 0, 0, ',', '.') }}</td>
            </tr>
            @endforeach

            <tr class="total-row">
                <td colspan="2" class="text-center">TOTAL</td>
                <td class="text-center">{{ number_format($sumTransactions) }}</td>
                <td class="text-center">{{ number_format($sumSuccess) }}</td>
                <td class="text-center">{{ number_format($sumPending) }}</td>
                <td class="text-center">{{ number_format($sumFailed) }}</td>
                <td class="text-center">{{ number_format($sumRaks) }}</td>
                <td class="text-right">Rp {{ number_format($sumRevenue, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($sumTransactions ? ($sumRevenue / $sumTransactions) : 0, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- CHARTS FOR PDF (visualized from Chart.js canvases or QuickChart fallback) -->
    <div class="chart-grid">
        <div class="chart-card large">
            <div class="chart-caption">Transaksi {{ $month ? \Carbon\Carbon::create(null, $month)->translatedFormat('F') . ' ' : '' }}{{ $year }}</div>
            @if(!empty($chartData['transaksiImage']))
                <img src="{{ $chartData['transaksiImage'] }}" alt="Transaksi" class="chart-img" style="max-height:320px;" />
            @else
                @php
                    $values = $chartData['transaksiData'] ?? [];
                    $labels = $chartData['transaksiLabels'] ?? [];
                    $max = max($values) ?: 1;
                    $count = max(count($values),1);
                    $svgW = 720; $svgH = 200; $pad = 30;
                    $stepX = ($svgW - $pad*2) / ($count - 1 ?: 1);
                    $points = [];
                    foreach($values as $idx => $v) {
                        $x = $pad + ($idx * $stepX);
                        $y = $svgH - $pad - (($v / $max) * ($svgH - $pad*2));
                        $points[] = round($x,1) . ',' . round($y,1);
                    }
                    $poly = implode(' ', $points);
                    $area = '';
                    if (!empty($points)) {
                        $area = $poly . ' ' . ($pad + ($stepX*($count-1))) . ',' . ($svgH-$pad) . ' ' . $pad . ',' . ($svgH-$pad);
                    }
                @endphp
                <svg viewBox="0 0 {{ $svgW }} {{ $svgH }}" width="100%" height="220" role="img" aria-label="Transaksi Chart" xmlns="http://www.w3.org/2000/svg" style="background:#fff; border-radius:4px;">
                    <style>text{font-family:Arial, sans-serif;font-size:10px;fill:#666}</style>
                    <rect x="0" y="0" width="100%" height="100%" fill="#ffffff" />
                    @if($area)
                        <polygon points="{!! $area !!}" fill="rgba(102,126,234,0.08)" />
                    @endif
                    <polyline points="{!! $poly !!}" fill="none" stroke="#667eea" stroke-width="3" stroke-linejoin="round" stroke-linecap="round" />
                    @foreach($points as $idx => $pt)
                        @php list($px,$py) = explode(',', $pt); $val = $values[$idx] ?? 0; @endphp
                        <circle cx="{{ $px }}" cy="{{ $py }}" r="3.5" fill="#667eea" stroke="#fff" stroke-width="1.5" />
                        <text x="{{ $px }}" y="{{ $py - 8 }}" text-anchor="middle">{{ $val }}</text>
                    @endforeach
                    @foreach($labels as $idx => $lbl)
                        @php $x = $pad + ($idx * $stepX); @endphp
                        <text x="{{ $x }}" y="{{ $svgH - 6 }}" text-anchor="middle">{{ $lbl }}</text>
                    @endforeach
                </svg>
            @endif
        </div>

        <div class="chart-card">
            <div class="chart-caption">Pendapatan {{ $month ? \Carbon\Carbon::create(null, $month)->translatedFormat('F') . ' ' : '' }}{{ $year }}</div>
            @if(!empty($chartData['pendapatanImage']))
                <img src="{{ $chartData['pendapatanImage'] }}" alt="Pendapatan" class="chart-img" style="max-height:260px;" />
            @else
                @php
                    $values = $chartData['pendapatanData'] ?? [];
                    $labels = $chartData['pendapatanLabels'] ?? [];
                    $max = max($values) ?: 1;
                    $count = max(count($values),1);
                    $svgW = 360; $svgH = 200; $pad = 24;
                    $barW = max(6, ($svgW - $pad*2) / ($count * 1.6));
                @endphp
                <svg viewBox="0 0 {{ $svgW }} {{ $svgH }}" width="100%" height="220" role="img" aria-label="Pendapatan Chart" xmlns="http://www.w3.org/2000/svg" style="background:#fff;border-radius:4px;">
                    <style>text{font-family:Arial, sans-serif;font-size:10px;fill:#666}</style>
                    <rect x="0" y="0" width="100%" height="100%" fill="#ffffff" />
                    @foreach($values as $idx => $v)
                        @php
                            $x = $pad + $idx * ($barW * 1.4);
                            $barH = (($v / $max) * ($svgH - $pad*2));
                            $y = ($svgH - $pad) - $barH;
                        @endphp
                        <rect x="{{ $x }}" y="{{ $y }}" width="{{ $barW }}" height="{{ $barH }}" fill="#43e97b" rx="4"/>
                        <text x="{{ $x + ($barW/2) }}" y="{{ $y - 6 }}" text-anchor="middle">Rp {{ number_format($v,0,',','.') }}</text>
                        <text x="{{ $x + ($barW/2) }}" y="{{ $svgH - 6 }}" text-anchor="middle">{{ $labels[$idx] ?? '' }}</text>
                    @endforeach
                </svg>
            @endif
        </div>

        <div class="chart-card">
            <div class="chart-caption">Status Rak</div>
            @if(!empty($chartData['rakImage']))
                <img src="{{ $chartData['rakImage'] }}" alt="Status Rak" class="chart-img" style="max-height:180px; width:160px;" />
            @else
                @php
                    $a = $chartData['rakTerisi'] ?? 0;
                    $b = $chartData['rakTersedia'] ?? 0;
                    $c = $chartData['rakMaintenance'] ?? 0;
                    $total = max($a+$b+$c,1);
                    $angles = [$a/$total*360, $b/$total*360, $c/$total*360];
                    $colors = ['#f5576c','#43e97b','#e7ff0a'];
                    $labels = ['Terisi','Tersedia','Maintenance'];
                    $cx = 90; $cy = 90; $r = 60; $start = -90;
                @endphp
                <svg viewBox="0 0 180 180" width="160" height="160" role="img" aria-label="Status Rak" xmlns="http://www.w3.org/2000/svg">
                    <style>text{font-family:Arial, sans-serif;font-size:10px;fill:#333}</style>
                    <rect width="100%" height="100%" fill="#fff" />
                    @php $s = $start; @endphp
                    @foreach($angles as $i => $ang)
                        @php
                            $e = $s + $ang;
                            $large = $ang > 180 ? 1 : 0;
                            $sx = $cx + $r * cos(deg2rad($s));
                            $sy = $cy + $r * sin(deg2rad($s));
                            $ex = $cx + $r * cos(deg2rad($e));
                            $ey = $cy + $r * sin(deg2rad($e));
                            $path = "M {$cx} {$cy} L {$sx} {$sy} A {$r} {$r} 0 {$large} 1 {$ex} {$ey} Z";
                            $s = $e;
                        @endphp
                        <path d="{{ $path }}" fill="{{ $colors[$i] }}" />
                    @endforeach
                    <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r * 0.6 }}" fill="#fff" />
                    <g transform="translate(10,150)">
                        @foreach($labels as $i => $lbl)
                            <rect x="{{ $i*60 }}" y="-8" width="12" height="12" fill="{{ $colors[$i] }}" rx="2"/> 
                            <text x="{{ $i*60 + 18 }}" y="2">{{ $lbl }} ({{ ${['a','b','c'][$i]} }})</text>
                        @endforeach
                    </g>
                </svg>
            @endif
        </div>

        <div class="chart-card">
            <div class="chart-caption">Status Transaksi</div>
            @if(!empty($chartData['statusImage']))
                <img src="{{ $chartData['statusImage'] }}" alt="Status Transaksi" class="chart-img" style="max-height:180px; width:160px;" />
            @else
                @php
                    $a = $chartData['statusSuccess'] ?? 0;
                    $b = $chartData['statusPending'] ?? 0;
                    $c = $chartData['statusFailed'] ?? 0;
                    $total = max($a+$b+$c,1);
                    $angles = [$a/$total*360, $b/$total*360, $c/$total*360];
                    $colors = ['#43e97b','#ffc107','#dc3545'];
                    $labels = ['Sukses','Pending','Gagal'];
                    $cx = 90; $cy = 90; $r = 60; $start = -90;
                @endphp
                <svg viewBox="0 0 180 180" width="160" height="160" role="img" aria-label="Status Transaksi" xmlns="http://www.w3.org/2000/svg">
                    <style>text{font-family:Arial, sans-serif;font-size:10px;fill:#333}</style>
                    <rect width="100%" height="100%" fill="#fff" />
                    @php $s = $start; @endphp
                    @foreach($angles as $i => $ang)
                        @php
                            $e = $s + $ang;
                            $large = $ang > 180 ? 1 : 0;
                            $sx = $cx + $r * cos(deg2rad($s));
                            $sy = $cy + $r * sin(deg2rad($s));
                            $ex = $cx + $r * cos(deg2rad($e));
                            $ey = $cy + $r * sin(deg2rad($e));
                            $path = "M {$cx} {$cy} L {$sx} {$sy} A {$r} {$r} 0 {$large} 1 {$ex} {$ey} Z";
                            $s = $e;
                        @endphp
                        <path d="{{ $path }}" fill="{{ $colors[$i] }}" />
                    @endforeach
                    <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r * 0.6 }}" fill="#fff" />
                    <g transform="translate(10,150)">
                        @foreach($labels as $i => $lbl)
                            <rect x="{{ $i*60 }}" y="-8" width="12" height="12" fill="{{ $colors[$i] }}" rx="2"/> 
                            <text x="{{ $i*60 + 18 }}" y="2">{{ $lbl }} ({{ ${['a','b','c'][$i]} }})</text>
                        @endforeach
                    </g>
                </svg>
            @endif
        </div>
    </div>


</body>
</html>
