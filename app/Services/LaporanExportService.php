<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanExportService
{
    public function __construct(private readonly TransportRequestQueryService $query) {}

    public function stream(array $filters): StreamedResponse
    {
        $items = $this->query->laporanQuery($filters)->get();

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Pengajuan');

        // === JUDUL ===
        $sheet->mergeCells('A1:X1');
        $sheet->setCellValue('A1', 'LAPORAN PENGAJUAN TRANSPORTASI');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(22);

        // Sub-judul filter info
        $filterInfo = 'Diekspor: '.now()->format('d/m/Y H:i');
        if (! blank($filters['tanggal_dari'] ?? null) || ! blank($filters['tanggal_sampai_filter'] ?? null)) {
            $filterInfo .= '  |  Periode: '.($filters['tanggal_dari'] ?? '...').' s/d '.($filters['tanggal_sampai_filter'] ?? '...');
        }
        if (! blank($filters['status'] ?? null)) {
            $filterInfo .= '  |  Status: '.ucfirst($filters['status']);
        }
        if (! blank($filters['jenis'] ?? null)) {
            $filterInfo .= '  |  Jenis: '.ucfirst($filters['jenis']);
        }
        $sheet->mergeCells('A2:X2');
        $sheet->setCellValue('A2', $filterInfo);
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '666666']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(14);

        // Baris kosong
        $sheet->getRowDimension(3)->setRowHeight(6);

        // === HEADER ===
        $headers = [
            'A' => ['No. Pengajuan', 20],
            'B' => ['Tgl Dibuat',    13],
            'C' => ['Jam Dibuat',    10],
            'D' => ['NIP Pemohon',   15],
            'E' => ['Nama Pemohon',  22],
            'F' => ['Jabatan',       18],
            'G' => ['Profesi',       18],
            'H' => ['Unit Kerja',    20],
            'I' => ['Jenis',          9],
            'J' => ['Prioritas',     12],
            'K' => ['Keperluan',     25],
            'L' => ['Tgl Berangkat', 14],
            'M' => ['Jam Berangkat', 13],
            'N' => ['Jam Sampai',    11],
            'O' => ['Tujuan',        25],
            'P' => ['Unit Kendaraan', 16],
            'Q' => ['Plat Nomor',    12],
            'R' => ['NIP Supir',     15],
            'S' => ['Nama Supir',    20],
            'T' => ['KM Awal',       10],
            'U' => ['KM Akhir',      10],
            'V' => ['Jarak (km)',    11],
            'W' => ['Tgl Kembali',   13],
            'X' => ['Jam Tiba',      10],
            'Y' => ['Status',        14],
        ];

        $headerRow = 4;
        foreach ($headers as $col => [$label, $width]) {
            $sheet->setCellValue($col.$headerRow, $label);
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        $headerRange = 'A'.$headerRow.':Y'.$headerRow;
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '007774']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
        ]);
        $sheet->getRowDimension($headerRow)->setRowHeight(28);

        // Freeze header
        $sheet->freezePane('A'.($headerRow + 1));

        // === DATA ===
        $row = $headerRow + 1;
        foreach ($items as $i => $item) {
            $statusLabel = match ($item->status) {
                'diproses' => 'Disetujui',
                'tidak_disetujui' => 'Tidak Disetujui',
                default => ucfirst($item->status),
            };
            $nipSupir = $item->driver && $item->driver->user ? $item->driver->user->nip : '';
            $jarak = ($item->km_awal && $item->km_akhir) ? ($item->km_akhir - $item->km_awal) : null;
            $tglKembali = $item->tanggal_sampai
                ? $item->tanggal_sampai->format('d/m/Y')
                : ($item->status === 'selesai' ? $item->updated_at->format('d/m/Y') : '');

            $rowBg = ($i % 2 === 0) ? 'FFFFFF' : 'F0FAFA';

            $sheet->fromArray([
                $item->nomor_pengajuan,
                $item->created_at->format('d/m/Y'),
                $item->created_at->format('H:i'),
                $item->user->nip ?? '',
                $item->user->full_name ?? $item->pemohon_nama ?? '',
                $item->user->jabatan ?? '',
                $item->user->profesi ?? '',
                $item->user->unit_kerja ?? $item->pemohon_unit ?? '',
                ucfirst($item->jenis),
                $item->prioritas === 'segera' ? 'Segera / CITO' : 'Biasa',
                $item->keperluan ?? '',
                $item->tanggal->format('d/m/Y'),
                substr($item->jam, 0, 5),
                $item->jam_sampai ? substr($item->jam_sampai, 0, 5) : '',
                $item->alamat_tujuan ?? '',
                $item->unit_mobil ? ucwords(str_replace('_', ' ', $item->unit_mobil)) : '',
                $item->plat_nomor ?? '',
                $nipSupir,
                $item->driver->name ?? '',
                $item->km_awal ?? '',
                $item->km_akhir ?? '',
                $jarak,
                $tglKembali,
                $item->jam_kedatangan ?? '',
                $statusLabel,
            ], null, 'A'.$row);

            // Zebra stripe
            $sheet->getStyle('A'.$row.':Y'.$row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $rowBg]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
                'font' => ['size' => 9],
            ]);

            // Alignment per kolom
            $sheet->getStyle('A'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B'.$row.':C'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D'.$row)->getFont()->setName('Courier New');
            $sheet->getStyle('I'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('J'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('L'.$row.':N'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('Q'.$row)->getFont()->setName('Courier New');
            $sheet->getStyle('R'.$row)->getFont()->setName('Courier New');
            $sheet->getStyle('T'.$row.':V'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('W'.$row.':X'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('Y'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $row++;
        }

        // Baris total
        if ($items->count() > 0) {
            $sheet->mergeCells('A'.$row.':S'.$row);
            $sheet->setCellValue('A'.$row, 'Total: '.$items->count().' data');
            $sheet->getStyle('A'.$row)->applyFromArray([
                'font' => ['bold' => true, 'size' => 9],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8F5F5']],
            ]);
        }

        // Auto filter
        $sheet->setAutoFilter('A'.$headerRow.':Y'.$headerRow);

        $filename = 'laporan-pengajuan-'.now()->format('Ymd-His').'.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
