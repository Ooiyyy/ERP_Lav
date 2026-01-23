<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Produk</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #000; padding: 6px; }
        th { background: #eee; }
        h4, h5 { margin: 5px 0; }
    </style>
</head>
<body>

<h4>LAPORAN PRODUK MASUK & KELUAR</h4>

<p>
    Periode Laporan:
    <strong>{{ $startDate }}</strong>
    s/d
    <strong>{{ $endDate }}</strong>
</p>
<h5>Produk Masuk</h5>
<table>
    <thead>
        <tr>
            <th>Nama Produk</th>
            <th>Satuan</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($masuk as $m)
        <tr>
            <td>{{ $m->nama_produk }}</td>
            <td>{{ $m->satuan }}</td>
            <td>{{ $m->total_jumlah }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<h5>Produk Keluar</h5>
<table>
    <thead>
        <tr>
            <th>Nama Produk</th>
            <th>Satuan</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($keluar as $k)
        <tr>
            <td>{{ $k->nama_produk }}</td>
            <td>{{ $k->satuan }}</td>
            <td>{{ $k->total_jumlah }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
