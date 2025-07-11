<?php

namespace App\Exports;

use App\Models\NotaPenjualan;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NotaGabungan2Export
{
    protected Collection $notas;

    public function __construct(Collection $notas)
    {
        $this->notas = $notas;
    }

    public function download(): StreamedResponse
    {
        $templatePath = storage_path('app/templates/Template Surat Jalan.xlsx');
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        $startRow = 14; // Baris awal untuk data barang
        $currentRow = $startRow;

        // Gabungkan semua items dari semua nota
        $allItems = collect();
        $totalKoli = 0;
        $firstNota = $this->notas->first();

        foreach ($this->notas as $nota) {
            $items = $nota->items;
            $allItems = $allItems->merge($items);
            $totalKoli += $items->count();
        }

        // Insert additional rows untuk data barang saja
        $this->insertRowsForItems($sheet, $allItems->count());

        // Header - sesuai dengan struktur Template Surat Jalan
        // Blok I sampai K
        $sheet->setCellValue("I7", $firstNota->kode_faktur . '/DO/RPN/05'); // Faktur
        $sheet->setCellValue("I8", $firstNota->tanggal->format('d F Y')); // Tanggal Kirim
        $sheet->setCellValue("I9", $firstNota->kode_faktur . '/PO/05'); // Nomor PO
        
        // Blok B sampai E
        $sheet->setCellValue("B7", $firstNota->dataPelanggan->nama ?? '-'); // Nama
        
        // Alamat diblok dari kolom 8 sampai 11
        $alamat = $firstNota->dataPelanggan->alamat ?? '-';
        $sheet->setCellValue("B8", $alamat); // Alamat
        $sheet->mergeCells("B8:B11"); // Merge alamat dari baris 8-11

        // Item Table - Fill data barang
        $row = $currentRow;
        $no = 1;

        foreach ($allItems as $item) { 
            $sheet->setCellValue("B{$row}", $no++); // NO. (kolom B)
            $sheet->setCellValue("C{$row}", $item->quantity); // Jumlah barang (kolom C)
            $sheet->setCellValue("D{$row}", "brng"); // Satuan "brng" (kolom D)
            
            // Nama barang (kolom E sampai F diblok)
            $sheet->setCellValue("E{$row}", $item->product->nama_produk ?? '-'); // Nama barang
            $sheet->mergeCells("E{$row}:F{$row}"); // Merge E-F untuk nama barang
            
            $row++;
        }

        // Style untuk tabel barang
        $this->addItemTableStyling($sheet, $startRow, $startRow + $allItems->count() - 1);

        $writer = new Xlsx($spreadsheet);
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, "SuratJalan{$firstNota->kode_faktur}.xlsx");
    }

    protected function addItemTableStyling($sheet, $startRow, $endRow)
    {
        // Style untuk tabel barang (border dan alignment)
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
        
        // Apply styling untuk area tabel barang
        $sheet->getStyle("B{$startRow}:F{$endRow}")->applyFromArray($styleArray);
        
        // Text alignment untuk kolom tertentu
        $sheet->getStyle("B{$startRow}:B{$endRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // NO
        $sheet->getStyle("C{$startRow}:C{$endRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Jumlah barang
        $sheet->getStyle("D{$startRow}:D{$endRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Satuan "brng"
        $sheet->getStyle("E{$startRow}:F{$endRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT); // Nama barang (E-F merged)
    }

    protected function insertRowsForItems($sheet, $totalItems)
    {
        // Template sudah menyediakan 9 baris kosong untuk data barang
        // Hanya insert rows jika ada lebih dari 9 item
        if ($totalItems > 9) {
            $additionalRows = $totalItems - 9;
            
            // Insert rows untuk kelebihan item
            // Dimulai dari baris 23 (setelah baris ke-9 data barang: 14+9-1=22, maka insert di 23)
            for ($i = 0; $i < $additionalRows; $i++) {
                $sheet->insertNewRowBefore(23, 1);
            }
        }
    }
}