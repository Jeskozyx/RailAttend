<?php

namespace App\Exports;

use App\Models\RekapWaktuKereta;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class RekapWaktuExport
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function download($filename)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 1. Set Header Columns
        $headers = [
            'Tanggal', 'Putaran', 'Nama User', 'Jabatan', 
            'Waktu Scan', 'Durasi', 'Jarak Waktu', 
            'Nama Kereta', 'No KA', 'Status'
        ];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        // Style Header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4B5563']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ];
        $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);

        // 2. Fetch Data
        $query = RekapWaktuKereta::with(['user.roles', 'schedule.train']);

        if ($this->request->user_id) $query->where('user_id', $this->request->user_id);
        if ($this->request->schedule_id) $query->where('schedule_id', $this->request->schedule_id);
        
        $dateFrom = $this->request->date_from ?? now()->format('Y-m-d');
        $dateTo = $this->request->date_to ?? now()->format('Y-m-d');
        
        $query->whereDate('tanggal', '>=', $dateFrom);
        $query->whereDate('tanggal', '<=', $dateTo);
        
        if ($this->request->role_id) {
            $query->whereHas('user.roles', function($q) {
                $q->where('id', $this->request->role_id);
            });
        }

        $data = $query->orderBy('waktu_awal', 'asc')->get();

        // 3. Populate Data
        $row = 2;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item->tanggal);
            $sheet->setCellValue('B' . $row, $item->ronde_ke);
            $sheet->setCellValue('C' . $row, $item->user->name ?? '-');
            $sheet->setCellValue('D' . $row, $item->user->roles->first()->name ?? '-');
            $sheet->setCellValue('E' . $row, \Carbon\Carbon::parse($item->waktu_awal)->format('H:i:s') . ' - ' . \Carbon\Carbon::parse($item->waktu_akhir)->format('H:i:s'));
            $sheet->setCellValue('F' . $row, gmdate('H:i:s', $item->durasi_detik));
            $sheet->setCellValue('G' . $row, gmdate('H:i:s', $item->jarak_waktu_detik));
            $sheet->setCellValue('H' . $row, $item->schedule->train->name ?? '-');
            $sheet->setCellValue('I' . $row, $item->schedule->no_ka ?? '-');
            $sheet->setCellValue('J' . $row, ucfirst($item->status));

            // Color status row if not normal
            if ($item->status == 'danger') {
                $sheet->getStyle("A$row:J$row")->getFont()->getColor()->setARGB('DC2626'); // Red text
            } elseif ($item->status == 'warning') {
                $sheet->getStyle("A$row:J$row")->getFont()->getColor()->setARGB('D97706'); // Orange text
            }

            $row++;
        }

        // Border for all data
        $lastRow = $row - 1;
        $sheet->getStyle("A1:J$lastRow")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // 4. Return Output Stream
        return response()->streamDownload(function() use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename);
    }
}
