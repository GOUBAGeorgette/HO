<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TemplateExport implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Nom*',
            'Bâtiment',
            'Salle',
            'Description',
            'Emplacement parent (nom)',
            'Actif (1/0)'
        ];
    }

    public function title(): string
    {
        return 'Modele_Import';
    }

    public function styles(Worksheet $sheet)
    {
        // Style de l'en-tête
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E6E6E6']
            ]
        ]);

        // Largeur des colonnes
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);

        // Ajouter des exemples de données
        $sheet->fromArray([
            ['Bureau 101', 'Bâtiment A', '101', 'Bureau du premier étage', '', '1'],
            ['Salle de réunion A', 'Bâtiment A', '201', 'Salle de réunion principale', '', '1'],
            ['Zone stockage', 'Bâtiment B', 'B1', 'Zone de stockage au sous-sol', '', '1'],
        ], null, 'A3', true);

        // Style des exemples
        $sheet->getStyle('A3:F5')->applyFromArray([
            'font' => ['color' => ['rgb' => '666666']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F9F9F9']
            ]
        ]);

        // Ajouter des instructions
        $sheet->setCellValue('A7', 'Instructions :');
        $sheet->setCellValue('A8', '- Les champs marqués d\'un * sont obligatoires');
        $sheet->setCellValue('A9', '- Pour le champ "Actif", utilisez 1 pour vrai et 0 pour faux');
        $sheet->setCellValue('A10', '- Laissez vide le champ "Emplacement parent" pour un emplacement racine');
        
        // Style des instructions
        $sheet->getStyle('A7:A10')->getFont()->setItalic(true);
        $sheet->getStyle('A7')->getFont()->setBold(true);
    }
}
