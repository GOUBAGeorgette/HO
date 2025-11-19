<?php

namespace App\Exports;

use App\Models\Equipment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class EquipmentExport implements 
    FromCollection, 
    WithHeadings, 
    WithMapping,
    ShouldAutoSize,
    WithColumnFormatting,
    WithEvents
{
    protected $equipmentIds;

    public function __construct($equipmentIds = null)
    {
        $this->equipmentIds = $equipmentIds;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Equipment::with(['category', 'assignedUser']);
        
        if ($this->equipmentIds) {
            $query->whereIn('id', $this->equipmentIds);
        }
        
        return $query->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Nom',
            'Modèle',
            'Marque',
            'Type',
            'Quantité',
            'État',
            'Emplacement',
            'Utilisable',
            'Personne en charge',
            'Catégorie',
            'Fréquence de maintenance',
            'Type de maintenance',
            'Date de création',
            'Dernière mise à jour'
        ];
    }

    /**
     * @param mixed $equipment
     *
     * @return array
     */
    public function map($equipment): array
    {
        return [
            $equipment->id,
            $equipment->name,
            $equipment->model,
            $equipment->brand,
            $equipment->type,
            $equipment->quantity,
            $this->formatStatus($equipment->status),
            $equipment->location,
            $equipment->is_usable ? 'Oui' : 'Non',
            $equipment->responsible_person,
            $equipment->category->name ?? 'Non spécifiée',
            $equipment->maintenance_frequency,
            $equipment->maintenance_type,
            $equipment->created_at->format('d/m/Y H:i'),
            $equipment->updated_at->format('d/m/Y H:i')
        ];
    }

    /**
     * Formatage des colonnes
     */
    public function columnFormats(): array
    {
        return [
            'A' => '0', // ID en tant que nombre sans décimales
            'F' => '0', // Quantité en tant que nombre sans décimales
            'N' => 'dd/mm/yyyy hh:mm', // Format de date pour la création
            'O' => 'dd/mm/yyyy hh:mm', // Format de date pour la mise à jour
        ];
    }

    /**
     * Événements pour la feuille de calcul
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Style de l'en-tête
                $event->sheet->getStyle('A1:O1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '3490dc'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);

                // Ajuster automatiquement la largeur des colonnes
                $event->sheet->getDelegate()->getStyle('A:O')
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    
                // Geler la première ligne (en-têtes)
                $event->sheet->getDelegate()->freezePane('A2');
                
                // Activer le filtre sur la première ligne
                $event->sheet->setAutoFilter(
                    $event->sheet->calculateWorksheetDimension()
                );
            },
        ];
    }
    
    /**
     * Formater le statut pour l'affichage
     */
    private function formatStatus($status)
    {
        $statuses = [
            'excellent' => 'Excellent',
            'bon' => 'Bon',
            'moyen' => 'Moyen',
            'mauvais' => 'Mauvais',
            'hors_service' => 'Hors service',
        ];
        
        return $statuses[$status] ?? $status;
    }
}
