<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Exécuter les seeds de la base de données.
     */
    public function run(): void
    {
        // Vérifier s'il existe déjà des départements
        if (Department::count() > 0) {
            return;
        }

        // Récupérer un emplacement existant ou en créer un
        $location = Location::first() ?? Location::create([
            'name' => 'Siège social',
            'description' => 'Bâtiment principal',
            'is_active' => true
        ]);

        $departments = [
            [
                'name' => 'Informatique',
                'code' => 'IT',
                'description' => 'Département des technologies de l\'information',
                'location_id' => $location->id,
                'is_active' => true
            ],
            [
                'name' => 'Ressources Humaines',
                'code' => 'RH',
                'description' => 'Département des ressources humaines',
                'location_id' => $location->id,
                'is_active' => true
            ],
            [
                'name' => 'Comptabilité',
                'code' => 'COMPTA',
                'description' => 'Département comptable',
                'location_id' => $location->id,
                'is_active' => true
            ],
            [
                'name' => 'Maintenance',
                'code' => 'MAINT',
                'description' => 'Département de maintenance technique',
                'location_id' => $location->id,
                'is_active' => true
            ],
            [
                'name' => 'Logistique',
                'code' => 'LOG',
                'description' => 'Département logistique',
                'location_id' => $location->id,
                'is_active' => true
            ]
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }

        $this->command->info(count($departments) . ' départements créés avec succès.');
    }
}
