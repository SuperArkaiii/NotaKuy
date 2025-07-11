<?php

namespace App\Exports;

use App\Models\NotaPenjualan;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NotaTemplateExport
{
    protected $nota;

    public function __construct(NotaPenjualan $nota)
    {
        $this->nota = $nota;
    }

    /**
     * Export satu nota sebagai file Excel yang langsung di-download.
     */
    public function download(): StreamedResponse
    {
        $path = storage_path('app/templates/PolosanSurat.xlsx');
        $spreadsheet = IOFactory::load($path);
        $spreadsheet = $this->isiData($spreadsheet);

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'nota-' . $this->nota->kode_faktur . '.xlsx');
    }

    /**
     * Mengisi data nota ke dalam template spreadsheet.
     */
    public function isiData(Spreadsheet $spreadsheet): Spreadsheet
    {
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('H3', $this->nota->kode_faktur);
        $sheet->setCellValue('H4', $this->nota->tanggal->format('d F Y'));
        $sheet->setCellValue('H9', $this->nota->jatuh_tempo->format('d F Y'));
        $sheet->setCellValue('C9', $this->nota->nama);
        $sheet->setCellValue('C10', $this->nota->alamat);

        // Items
        $rowStart = 13;
        $subtotal = 0;
        $no = 1;

        foreach ($this->nota->items as $index => $item) {
            $row = $rowStart + $index;

            $diskonPersen = $item->quantity > 5 ? 10 : 0;
            $diskon = $item->harga * $item->quantity * ($diskonPersen / 100);
            $jumlah = ($item->harga * $item->quantity) - $diskon;

            $subtotal += $jumlah;

            $sheet->setCellValue("B$row", $no++);
            $sheet->setCellValue("C$row", $item->product->nama_produk ?? '-');
            $sheet->setCellValue("D$row", $item->quantity);
            $sheet->setCellValue("E$row", $item->harga);
            $sheet->setCellValue("F$row", $diskonPersen > 0 ? "{$diskonPersen}%" : '0%');
            $sheet->setCellValue("G$row", '❌');
            $sheet->setCellValue("H$row", $jumlah);
        }

        // Total
        $jumlahKoli = count($this->nota->items);
        $koli = $jumlahKoli * 100000;
        $ppn = (int) ($subtotal * 0.12);
        $ongkir = 200000;
        $total = $subtotal + $koli + $ppn + $ongkir;

        $sheet->setCellValue('H16', $subtotal);
        $sheet->setCellValue('H17', $ppn);
        $sheet->setCellValue('G18', "JUMLAH KOLI ({$jumlahKoli})");
        $sheet->setCellValue('H18', $koli);
        $sheet->setCellValue('H19', $total);
        $sheet->setCellValue('B31', $this->terbilang($total));

        // Format angka sebagai Rupiah
        foreach (['H16', 'H17', 'H18', 'H19'] as $cell) {
            $sheet->getStyle($cell)->getNumberFormat()->setFormatCode('"Rp" #,##0');
        }

        return $spreadsheet;
    }

    /**
     * Mengubah angka ke format huruf (terbilang).
     */
    protected function terbilang($angka): string
    {
        $f = new \NumberFormatter("id", \NumberFormatter::SPELLOUT);
        return ucfirst($f->format($angka)) . ' rupiah';
    }
}
