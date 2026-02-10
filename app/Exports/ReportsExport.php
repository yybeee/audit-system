<?php

namespace App\Exports;

use App\Models\Report;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ReportsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    protected $year;
    protected $month;
    protected $reports;

    public function __construct($year, $month = null)
    {
        $this->year = $year;
        $this->month = $month;
    }

    public function collection()
    {
        $query = Report::with(['department', 'auditType', 'auditor', 'responses'])
            ->whereYear('created_at', $this->year);
        
        if ($this->month) {
            $query->whereMonth('created_at', $this->month);
        }
        
        $this->reports = $query->orderBy('created_at', 'asc')->get();
            
        return $this->reports;
    }

    public function title(): string
    {
        if ($this->month) {
            return date('F Y', mktime(0, 0, 0, $this->month, 1, $this->year));
        }
        return "Reports {$this->year}";
    }

    public function headings(): array
    {
        return [
            'Report Number',
            'Audit Type',
            'Department',
            'Location',
            'Issue Type',
            'Description',
            'Status',
            'Submitted Date',
            'Started Date',
            'Deadline',
            'Fixed Date',
            'Approved Date',
            'Auditor',
            'Response Count',
            'Rejection Reason',
        ];
    }

    public function map($report): array
    {
        return [
            $report->report_number,
            $report->auditType->name ?? '-',
            $report->department->name ?? '-',
            $report->location,
            $report->issue_type,
            strip_tags($report->description),
            $this->getStatusLabel($report->status),
            $report->submitted_at ? $report->submitted_at->format('d/m/Y H:i') : '-',
            $report->started_at ? $report->started_at->format('d/m/Y H:i') : '-',
            $report->deadline ? \Carbon\Carbon::parse($report->deadline)->format('d/m/Y') : '-',
            $report->fixed_at ? $report->fixed_at->format('d/m/Y H:i') : '-',
            $report->approved_at ? $report->approved_at->format('d/m/Y H:i') : '-',
            $report->auditor->name ?? '-',
            $report->responses->count(),
            $report->rejection_reason ?? '-',
        ];
    }

    private function getStatusLabel($status)
    {
        $statuses = [
            'submitted' => 'Submitted',
            'in_progress' => 'In Progress',
            'fixed' => 'Fixed',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
        ];
        
        return $statuses[$status] ?? $status;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:O1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '047857'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        $rowCount = $this->reports->count() + 1;
        $sheet->getStyle('A2:O' . $rowCount)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP,
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(25);

        return [];
    }
}