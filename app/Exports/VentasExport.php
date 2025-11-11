<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class VentasExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithEvents, WithTitle
{
    protected $ventas;
    protected $fechaInicio;
    protected $fechaFin;
    protected $sucursal;
    protected $empleado;
    protected $total;

    public function __construct(Collection $ventas, $fechaInicio, $fechaFin, $sucursal = null, $empleado = null, $total = 0)
    {
        $this->ventas = $ventas;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
        $this->sucursal = $sucursal;
        $this->empleado = $empleado;
        $this->total = $total;
    }

    public function collection()
    {
        return collect();
    }

    public function headings(): array
    {
        return [
            'Tipo',
            'Fecha',
            'Número',
            'Cliente',
            'Estado',
            'Método de Pago',
            'Sucursal',
            'Total (S/)',
        ];
    }

    public function title(): string
    {
        return 'Reporte de Ventas';
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // ENCABEZADOS
                $sheet->mergeCells('A1:H1');
                $sheet->setCellValue('A1', 'Laundry System');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(18)->getColor()->setRGB('007bff');
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('A2:H2');
                $sheet->setCellValue('A2', 'Reporte de Ventas Detallado');
                $sheet->getStyle('A2')->getFont()->setSize(13)->getColor()->setRGB('555555');
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // FILTROS
                $sheet->mergeCells('A4:H4');
                $sheet->setCellValue('A4', "Rango de fechas: {$this->fechaInicio} al {$this->fechaFin}");

                $sheet->mergeCells('A5:H5');
                $sheet->setCellValue('A5', "Sucursal: " . ($this->sucursal ? $this->sucursal->name : 'Todas'));

                $sheet->mergeCells('A6:H6');
                $sheet->setCellValue('A6', "Empleado: " . ($this->empleado ? $this->empleado->name : 'Todos'));

                // TABLA
                $startRow = 8;
                $headerRow = $startRow;
                $dataStartRow = $headerRow + 1;

                // Encabezados
                $sheet->fromArray($this->headings(), null, "A{$headerRow}");

                // Estilo de la cabecera 
                $sheet->getStyle("A{$headerRow}:H{$headerRow}")
                    ->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
                $sheet->getStyle("A{$headerRow}:H{$headerRow}")
                    ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('007bff');
                $sheet->getStyle("A{$headerRow}:H{$headerRow}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Datos
                $data = $this->ventas->map(fn($venta) => [
                    $venta['tipo'],
                    Carbon::parse($venta['fecha'])->format('d/m/Y H:i'),
                    $venta['numero'],
                    $venta['cliente'],
                    $venta['estado'],
                    $venta['metodo_pago'],
                    $venta['sucursal'],
                    number_format($venta['total'], 2)
                ])->toArray();

                $sheet->fromArray($data, null, "A{$dataStartRow}");

                $lastRow = $sheet->getHighestRow();

                // Estilos de tabla
                $sheet->getStyle("A{$headerRow}:H{$lastRow}")
                    ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('CCCCCC'));

                for ($i = $dataStartRow; $i <= $lastRow; $i++) {
                    if ($i % 2 === 0) {
                        $sheet->getStyle("A{$i}:H{$i}")
                            ->getFill()->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('F8F9FA');
                    }
                }

                // TOTAL
                $totalRow = $lastRow + 2;
                $highestColumn = $sheet->getHighestColumn();

                // Combinar las celdas desde la penúltima hasta la última columna para el total
                $sheet->mergeCells("{$highestColumn}{$totalRow}:{$highestColumn}{$totalRow}");

                // Etiqueta "Total General" en la columna anterior a la última
                $colBefore = chr(ord($highestColumn) - 1);
                $sheet->setCellValue("{$colBefore}{$totalRow}", "Total General:");
                $sheet->setCellValue("{$highestColumn}{$totalRow}", "S/. " . number_format($this->total, 2));

                // Estilo del texto
                $sheet->getStyle("{$colBefore}{$totalRow}:{$highestColumn}{$totalRow}")
                    ->getFont()->setBold(true)->getColor()->setRGB('28A745');

                // Alinea el texto del total a la derecha
                $sheet->getStyle("{$colBefore}{$totalRow}:{$highestColumn}{$totalRow}")
                    ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                // FECHA DE GENERACIÓN 
                $sheet->setCellValue("A" . ($totalRow + 2), "Generado el: " . now()->format('d/m/Y H:i'));
                $sheet->getStyle("A" . ($totalRow + 2))->getFont()->setItalic(true)->setSize(10);
            },
        ];
    }
}
