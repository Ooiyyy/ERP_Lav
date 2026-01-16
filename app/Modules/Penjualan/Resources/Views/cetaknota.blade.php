<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    {{-- <title>Nota Pelunasan</title> --}}
</head>

<body>

    <h3 style="margin-bottom:5px;">NOTA PELUNASAN</h3>
    <p style="margin-top:0; font-size:11px;">
        Tanggal Cetak: {{ $generated_at->format('d-m-Y H:i') }}
    </p>

    <hr>

    {{-- INFO ORDER --}}
    <table width="100%" cellpadding="4" cellspacing="0">
        <tr>
            <td width="20%"><strong>ID Transaksi</strong></td>
            <td width="30%">: {{ $order->kode ?? $order->id }}</td>
            <td width="20%"><strong>Tanggal</strong></td>
            <td width="30%">
                : {{ $order->tanggal_buat ? date('d-m-Y', strtotime($order->tanggal_buat)) : '-' }}
            </td>
        </tr>
        <tr>
            <td><strong>Sales</strong></td>
            <td>: {{ optional($order->sales)->nama ?? '-' }}</td>
            <td><strong>No. HP</strong></td>
            <td>: {{ optional($order->sales)->no_hp ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Toko</strong></td>
            <td colspan="3">: {{ optional($order->toko)->nama_toko ?? '-' }}</td>
        </tr>
    </table>

    <br>

    {{-- DETAIL ITEM --}}
    <table width="100%" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Produk</th>
                <th width="10%">Qty</th>
                <th width="20%">Harga</th>
                <th width="20%">Sub Total</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach ($order->detailOrders as $i => $d)
                @php
                    $qty = $d->jumlah ?? 0;
                    $harga = $d->harga ?? 0;
                    $sub = $qty * $harga;
                    $total += $sub;
                @endphp
                <tr>
                    <td align="center">{{ $i + 1 }}</td>
                    <td>{{ $d->product->nama ?? ($d->nama ?? '-') }}</td>
                    <td align="center">{{ $qty }}</td>
                    <td align="right">Rp {{ number_format($harga, 0, ',', '.') }}</td>
                    <td align="right">Rp {{ number_format($sub, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" align="right">Total</th>
                <th align="right">Rp {{ number_format($total, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

    <br>

    {{-- RIWAYAT PEMBAYARAN --}}
    <h4 style="margin-bottom:5px;">Riwayat Pembayaran</h4>

    <table width="100%" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Tanggal</th>
                <th width="15%">Metode</th>
                <th width="25%">Petugas</th>
                <th width="20%">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @php $totalBayar = 0; @endphp
            @foreach ($payments as $i => $p)
                @php $totalBayar += $p->nominal; @endphp
                <tr>
                    <td align="center">{{ $i + 1 }}</td>
                    <td>{{ $p->tanggal ? date('d-m-Y', strtotime($p->tanggal)) : '-' }}</td>
                    <td align="center">{{ $p->method }}</td>
                    <td>{{ $p->petugas ?? '-' }}</td>
                    <td align="right">Rp {{ number_format($p->nominal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" align="right">Total Dibayar</th>
                <th align="right">Rp {{ number_format($totalBayar, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

    <br><br>

    {{-- TANDA TANGAN --}}
    <table width="100%">
        <tr>
            <td width="70%"></td>
            <td width="30%" align="center">
                <p>Sales</p>
                <br><br><br>
                <p> ({{ optional($order->sales)->nama ?? '-' }})</p>
            </td>
        </tr>
    </table>

</body>

</html>
