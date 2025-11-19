<?php

namespace App\Exports;

use App\Models\Location;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LocationsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $locations;

    public function __construct($locations)
    {
        $this->locations = $locations;
    }

    public function collection()
    {
        return $this->locations;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nom',
            'Bâtiment',
            'Salle',
            'Description',
            'Emplacement parent',
            'Actif',
            'Date de création',
            'Dernière mise à jour'
        ];
    }

    public function map($location): array
    {
        return [
            $location->id,
            $location->name,
            $location->building,
            $location->room,
            $location->description,
            $location->parent ? $location->parent->name : '',
            $location->is_active ? 'Oui' : 'Non',
            $location->created_at->format('d/m/Y H:i'),
            $location->updated_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style de l'en-tête
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F2F2F2']
                ]
            ],
        ];
    }
}
