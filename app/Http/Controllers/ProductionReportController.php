<?php

namespace App\Http\Controllers;

use App\Models\Operator;
use App\Models\WorkPlace;
use Illuminate\Http\Request;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductionReportController extends Controller
{
    public function index(Request $request)
    {
        $workPlaces = WorkPlace::all();
        $operators = null;

        if ($request->hasAny(['start_date', 'end_date', 'start_time', 'end_time', 'kategori', 'work_place_id'])) {
            $q = $this->getFilteredQuery($request);
            \Log::info('SQL: ' . $q->toSql());
            \Log::info('Bindings: ', $q->getBindings());

            $operators = $q->paginate(20)->withQueryString();
        }

        return view('production_reports.index', compact('workPlaces', 'operators'));
    }

    public function export(Request $request)
    {
        $operators = $this->getFilteredQuery($request)->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Production Report');

        // Header styling
        $headers = ['Nama Barang', 'Kategori', 'Target Qty', 'Selesai', 'Tanggal Selesai', 'Jam Selesai', 'Lokasi'];
        foreach ($headers as $index => $header) {
            $col = chr(65 + $index);
            $sheet->setCellValue("{$col}1", $header);
            $sheet->getStyle("{$col}1")->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF4472C4'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ]);
        }

        // Data rows
        // Data rows
        $row = 2;
        foreach ($operators as $operator) {
            $isWip = $operator->wip_schedule_id !== null;
            $schedule = $isWip ? $operator->wipSchedule : $operator->finishGoodSchedule;
            $targetQty = $isWip ? ($schedule?->qty ?? 0) : ($schedule?->quantity ?? 0);

            $sheet->setCellValue("A{$row}", $schedule?->name ?? '-');
            $sheet->setCellValue("B{$row}", $isWip ? 'WIP' : 'Finish Good');
            $sheet->setCellValue("C{$row}", $targetQty);
            $sheet->setCellValue("D{$row}", $operator->total_selesai ?? 0);
            $sheet->setCellValue("E{$row}", $operator->tanggal_selesai ? \Carbon\Carbon::parse($operator->tanggal_selesai)->format('d/m/Y') : '-');
            $sheet->setCellValue("F{$row}", $operator->waktu_selesai ? \Carbon\Carbon::parse($operator->waktu_selesai)->format('H:i') : '-');
            $sheet->setCellValue("G{$row}", $schedule?->workPlace?->name ?? '-');

            // Zebra striping
            if ($row % 2 === 0) {
                $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFF2F2F2'],
                    ],
                ]);
            }

            $row++;
        }

        // Border seluruh tabel
        if ($row > 2) {
            $sheet->getStyle("A1:G" . ($row - 1))->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => 'FFCCCCCC'],
                    ],
                ],
            ]);
        }

        // Auto size kolom
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Freeze header row
        $sheet->freezePane('A2');

        $filename = 'production_report_' . now()->format('Ymd_His') . '.xlsx';

        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'max-age=0',
            'Pragma'              => 'no-cache',
            'Expires'             => '0',
        ]);
    }

    private function getFilteredQuery(Request $request)
    {
        $query = Operator::query()
            ->with(['finishGoodSchedule.workPlace', 'wipSchedule.workPlace'])
            ->where('status_production', true)
            ->select(
                'wip_schedule_id',
                'finish_good_schedule_id',
                \DB::raw('MIN(tanggal_selesai) as tanggal_selesai'),
                \DB::raw('MIN(waktu_selesai) as waktu_selesai'),
                \DB::raw('COUNT(*) as total_selesai')
            )
            ->groupBy('wip_schedule_id', 'finish_good_schedule_id');

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_selesai', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_selesai', '<=', $request->end_date);
        }

        if ($request->filled('start_time')) {
            $query->whereRaw('TIME(waktu_selesai) >= ?', [$request->start_time . ':00']);
        }

        if ($request->filled('end_time')) {
            $query->whereRaw('TIME(waktu_selesai) <= ?', [$request->end_time . ':59']);
        }

        if ($request->filled('kategori')) {
            if ($request->kategori === 'wip') {
                $query->whereNotNull('wip_schedule_id')->whereNull('finish_good_schedule_id');
            } elseif ($request->kategori === 'finish_good') {
                $query->whereNotNull('finish_good_schedule_id')->whereNull('wip_schedule_id');
            }
        }

        if ($request->filled('work_place_id')) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('wipSchedule', fn($s) => $s->where('work_place_id', $request->work_place_id))
                    ->orWhereHas('finishGoodSchedule', fn($s) => $s->where('work_place_id', $request->work_place_id));
            });
        }

        return $query->latest('tanggal_selesai');
    }
}
