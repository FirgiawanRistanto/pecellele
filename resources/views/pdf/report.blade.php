<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: 'Arial', sans-serif; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 20pt; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8pt; color: #888; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Penjualan Warung Pak Er'Te</h1>
        <p>Periode: {{ $reportPeriodText }}</p>
        <p>Tanggal Cetak: {{ $dateGenerated }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID Pesanan</th>
                <th>Tanggal</th>
                <th>Item Pesanan</th>
                <th>Total</th>
                <th>Alamat</th>
                <th>Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
                <tr>
                    <td>#{{ $order->id }}</td>
                    <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y H:i') }}</td>
                    <td>
                        @foreach ($order->items as $item)
                            {{ $item->menu->name }} (x{{ $item->quantity }}) - {{ number_format($item->price) }}<br>
                        @endforeach
                    </td>
                    <td>Rp {{ number_format($order->total) }}</td>
                    <td>{{ $order->address }}</td>
                    <td>{{ $order->payment_method }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Tidak ada data pesanan untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right"><strong>Total Penjualan Keseluruhan:</strong></td>
                <td colspan="3"><strong>Rp {{ number_format($totalSales) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Laporan Penjualan - Dibuat oleh Sistem Warung Pak Er'Te
    </div>
</body>
</html>