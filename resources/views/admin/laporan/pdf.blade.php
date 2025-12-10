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

    <!-- ==================================
         ADMINLTE STYLE CHARTS SECTION
    =====================================-->

    <div class="chart-box">
        <h4 style="text-align:center; margin-bottom:10px;">📊 Grafik Pendapatan {{ $year }}</h4>

        @php
            // PREPARE BASIC VARIABLES
            $maxRevenue = max($chartData['pendapatanData']) ?: 1;
            $chartW = 400;
            $chartH = 180;
            $barW = $chartW / count($chartData['pendapatanLabels']) - 10;
            $adminLTEColors = ['#00a65a','#f39c12','#f56954','#00c0ef','#3c8dbc','#d2d6de','#605ca8','#39CCCC','#3c8dbc','#00a65a','#f39c12','#f56954'];
        @endphp

        <!-- =======================
             BAR CHART
        ======================== -->
        <svg width="{{ $chartW + 60 }}" height="{{ $chartH + 60 }}">
            <rect width="100%" height="100%" fill="#f8f9fa" />

            @for($i=1; $i<=5; $i++)
                <line x1="40" y1="{{ $chartH - ($i*$chartH/5) + 20 }}" x2="{{ $chartW + 40 }}" y2="{{ $chartH - ($i*$chartH/5) + 20 }}"
                      stroke="#ddd" stroke-dasharray="2,2"/>
            @endfor

            @foreach($chartData['pendapatanData'] as $i => $value)
                @php
                    $height = ($value / $maxRevenue) * $chartH;
                    $x = $i * ($barW + 10) + 50;
                    $y = $chartH + 20 - $height;
                    $color = $adminLTEColors[$i % count($adminLTEColors)];
                @endphp

                <rect x="{{ $x }}" y="{{ $y }}" width="{{ $barW }}" height="{{ $height }}"
                      fill="{{ $color }}" stroke="#ccc" rx="3"/>

                <text x="{{ $x + $barW/2 }}" y="{{ $chartH + 40 }}" text-anchor="middle" font-size="10">
                    {{ substr($chartData['pendapatanLabels'][$i], 0, 3) }}
                </text>
            @endforeach
        </svg>
    </div>

    <!-- LINE CHART -->
    <div class="chart-box">
        @php
            $maxT = max($chartData['transaksiData']) ?: 1;
            $LW = 380; $LH = 160;
            $cx = 30;
        @endphp

        <svg width="{{ $LW + 40 }}" height="{{ $LH + 60 }}">
            @for($i=1;$i<=4;$i++)
                <line x1="30" y1="{{ $LH - ($i*$LH/4) + 20 }}"
                      x2="{{ $LW + 30 }}" y2="{{ $LH - ($i*$LH/4) + 20 }}"
                      stroke="#ddd" stroke-dasharray="2,2"/>
            @endfor

            {{-- Build polyline points --}}
            @php $points = ""; @endphp
            @foreach($chartData['transaksiData'] as $i => $value)
                @php
                    $x = $i * ($LW / (count($chartData['transaksiData'])-1)) + 30;
                    $y = $LH + 20 - (($value / $maxT) * $LH);
                    $points .= "$x,$y ";
                @endphp
            @endforeach

            <polyline points="{{ $points }}" fill="none" stroke="#007bff" stroke-width="3" />

            @foreach($chartData['transaksiData'] as $i => $value)
                @php
                    $x = $i * ($LW / (count($chartData['transaksiData'])-1)) + 30;
                    $y = $LH + 20 - (($value / $maxT) * $LH);
                @endphp

                <circle cx="{{ $x }}" cy="{{ $y }}" r="4" fill="#007bff" stroke="#fff" stroke-width="2" />
                <text x="{{ $x }}" y="{{ $y - 8 }}" text-anchor="middle" font-size="9" fill="#007bff">{{ $value }}</text>

                <text x="{{ $x }}" y="{{ $LH + 40 }}" text-anchor="middle" font-size="9">
                    {{ substr($chartData['transaksiLabels'][$i], 0, 3) }}
                </text>
            @endforeach
        </svg>
    </div>

    <!-- DONUT CHART -->
    <div class="chart-box" style="text-align:center;">
        @php
            $total = array_sum($chartData['pendapatanData']);
            $top = collect($chartData['pendapatanData'])->sortDesc()->take(4)->values();
            $other = $total - $top->sum();
            $donut = [...$top, $other];
            $colors = ['#28a745','#007bff','#6f42c1','#fd7e14','#6c757d'];

            $cx = 100; $cy = 90;
            $r = 60; $r2 = 30;
            $angleNow = 0;
        @endphp

        <svg width="200" height="180">
            @foreach($donut as $i => $val)
                @php
                    if($val <= 0) continue;

                    $pct = $val / $total;
                    $angle = $pct * 360;

                    $start = $angleNow;
                    $end = $angleNow + $angle;

                    $x1 = $cx + $r * cos(deg2rad($start));
                    $y1 = $cy + $r * sin(deg2rad($start));
                    $x2 = $cx + $r * cos(deg2rad($end));
                    $y2 = $cy + $r * sin(deg2rad($end));

                    $x1i = $cx + $r2 * cos(deg2rad($start));
                    $y1i = $cy + $r2 * sin(deg2rad($start));
                    $x2i = $cx + $r2 * cos(deg2rad($end));
                    $y2i = $cy + $r2 * sin(deg2rad($end));

                    $largeArc = ($angle > 180) ? 1 : 0;
                @endphp

                <path d="
                    M {{ $x1i }} {{ $y1i }}
                    L {{ $x1 }} {{ $y1 }}
                    A {{ $r }} {{ $r }} 0 {{ $largeArc }} 1 {{ $x2 }} {{ $y2 }}
                    L {{ $x2i }} {{ $y2i }}
                    A {{ $r2 }} {{ $r2 }} 0 {{ $largeArc }} 0 {{ $x1i }} {{ $y1i }}
                "
                fill="{{ $colors[$i % count($colors)] }}" />

                @php $angleNow += $angle; @endphp
            @endforeach

            <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r2 }}" fill="#fff"/>
            <text x="{{ $cx }}" y="{{ $cy + 4 }}" text-anchor="middle" font-size="12" font-weight="bold">
                {{ number_format($total/1000000,1) }}M
            </text>
        </svg>
    </div>

    <div class="footer">
        <p>Dokumen ini digenerate otomatis oleh sistem.</p>
    </div>

</body>
</html>
