<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pendapatan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h2 {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-row {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
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

    <div class="footer">
        <p>Dokumen ini digenerate secara otomatis oleh sistem</p>
    </div>
</body>
</html>