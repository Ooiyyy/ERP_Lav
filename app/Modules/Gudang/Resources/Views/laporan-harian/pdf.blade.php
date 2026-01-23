<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Arus Keluar Masuk Bahan baku</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h2 { text-align: center; margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; }
        th { background: #eee; }
    </style>
</head>
<body>

<h2>LAPORAN ARUS KELUAR MASUK BAHAN BAKU</h2>

<p>
    Periode Laporan:
    <strong>{{ $startDate }}</strong> s/d
    <strong>{{ $endDate }}</strong>
</p>

<h4>Bahan Baku Masuk</h4>
<table>
    <thead>
        <tr>
            <th>Nama Bahan</th>
            <th>Satuan</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($masuk as $m)
        <tr>
            <td>{{ $m->nama_bahan }}</td>
            <td>{{ $m->satuan }}</td>
            <td>{{ $m->total_jumlah }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<h4>Bahan Baku Keluar</h4>
<table>
    <thead>
        <tr>
            <th>Nama Bahan</th>
            <th>Satuan</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($keluar as $k)
        <tr>
            <td>{{ $k->nama_bahan }}</td>
            <td>{{ $k->satuan }}</td>
            <td>{{ $k->total_jumlah }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
