@php
    $no = 1;
    $subtotal = 0;
@endphp

<table border="0" style="width: 100%; font-family: Arial, sans-serif; font-size: 12px;">
    <tr>
        <td><strong>PT RPN PUSAT PERBELANJAAN SEPATU</strong></td>
        <td style="text-align: right;">UR #: {{ $nota->kode_faktur }}</td>
    </tr>
    <tr>
        <td>Jalan Muhahaha No. 12 Konoha – 67890</td>
        <td style="text-align: right;">Tanggal: {{ \Carbon\Carbon::parse($nota->tanggal)->format('d F Y') }}</td>
    </tr>
    <tr>
        <td>Telp: 1234567890 | Fax: +261234567890</td>
    </tr>
    <tr>
        <td>Email: finance.pps@gmail.com</td>
    </tr>
</table>

<br>

<strong>PELANGGAN:</strong>
<table border="1" cellpadding="4" width="100%">
    <tr>
        <td>Nama</td>
        <td>: {{ $nota->nama }}</td>
    </tr>
    <tr>
        <td>Alamat</td>
        <td>: {{ $nota->alamat }}</td>
    </tr>
</table>

<br>

<table border="1" cellpadding="4" width="100%">
    <thead>
        <tr>
            <th>No</th>
            <th>Keterangan</th>
            <th>Qty</th>
            <th>Harga</th>
            <th>Diskon</th>
            <th>Pajak</th>
            <th>Jumlah</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($nota->items as $item)
            @php
                $subtotal += $item->jumlah;
                $diskon = $item->quantity > 5 ? '10%' : ($item->quantity > 1 ? '5%' : '0%');
            @endphp
            <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $item->product->nama_produk }}</td>
                <td>{{ $item->quantity }}</td>
                <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                <td>{{ $diskon }}</td>
                <td>❌</td>
                <td>Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@php
    $koli = count($nota->items) * 100000;
    $ppn = (int)($subtotal * 0.12);
    $ongkir = 200000;
    $total = $subtotal + $koli + $ppn + $ongkir;
@endphp

<br>

<table border="1" cellpadding="4" width="100%">
    <tr>
        <td colspan="6" align="right">Subtotal</td>
        <td align="right">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
    </tr>
    <tr>
        <td colspan="6" align="right">Jumlah Koli ({{ count($nota->items) }} Barang)</td>
        <td align="right">Rp {{ number_format($koli, 0, ',', '.') }}</td>
    </tr>
    <tr>
        <td colspan="6" align="right">PPN 12%</td>
        <td align="right">Rp {{ number_format($ppn, 0, ',', '.') }}</td>
    </tr>
    <tr>
        <td colspan="6" align="right">Biaya Kirim</td>
        <td align="right">Rp {{ number_format($ongkir, 0, ',', '.') }}</td>
    </tr>
    <tr>
        <td colspan="6" align="right"><strong>TOTAL</strong></td>
        <td align="right"><strong>Rp {{ number_format($total, 0, ',', '.') }}</strong></td>
    </tr>
</table>

<br>

<strong>Pesan:</strong>
<ul>
    <li>Barang akan dikirim maksimal 2 hari kerja setelah pembayaran.</li>
    <li>Harap simpan faktur ini sebagai bukti pembayaran.</li>
</ul>

<br>

<strong>DETAIL PEMBAYARAN:</strong>
<table border="1" cellpadding="4">
    <tr><td>Nama Bank</td><td>: Bank Rupt</td></tr>
    <tr><td>Cabang</td><td>: Singapura</td></tr>
    <tr><td>No. Rekening</td><td>: 1234567890987654321</td></tr>
    <tr><td>Atas Nama</td><td>: PT RPN PPS</td></tr>
</table>

<br>

<strong>TERBILANG:</strong><br>
<i>Satu juta lima ratus dua puluh tujuh ribu dua ratus rupiah</i>
