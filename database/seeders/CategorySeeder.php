<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Exécute le seeder.
     */
    public function run(): void
    {
        // Désactiver les événements du modèle pour améliorer les performances
        Category::withoutEvents(function() {
            // Catégories parentes
            $categories = [
                [
                    'name' => 'Informatique',
                    'description' => 'Équipements et accessoires informatiques',
                    'is_active' => true,
                    'children' => [
                        ['name' => 'Ordinateurs portables', 'description' => 'PC portables et ultrabooks', 'is_active' => true],
                        ['name' => 'Ordinateurs de bureau', 'description' => 'Tours et unités centrales', 'is_active' => true],
                        ['name' => 'Périphériques', 'description' => 'Souris, claviers, écrans, etc.', 'is_active' => true],
                    ]
                ],
                [
                    'name' => 'Mobilier de bureau',
                    'description' => 'Meubles et équipements de bureau',
                    'is_active' => true,
                    'children' => [
                        ['name' => 'Bureaux', 'description' => 'Bureaux assis-debout et traditionnels', 'is_active' => true],
                        ['name' => 'Chaises', 'description' => 'Chaises de bureau ergonomiques', 'is_active' => true],
                        ['name' => 'Rangements', 'description' => 'Étagères et classeurs', 'is_active' => true],
                    ]
                ],
                [
                    'name' => 'Réseau',
                    'description' => 'Équipements réseau et téléphonie',
                    'is_active' => true,
                    'children' => [
                        ['name' => 'Routeurs', 'description' => 'Routeurs et points d\'accès', 'is_active' => true],
                        ['name' => 'Switchs', 'description' => 'Commutateurs réseau', 'is_active' => true],
                        ['name' => 'Téléphonie IP', 'description' => 'Téléphones et équipements VoIP', 'is_active' => true],
                    ]
                ],
                [
                    'name' => 'Imagerie',
                    'description' => 'Équipements d\'impression et de numérisation',
                    'is_active' => true,
                    'children' => [
                        ['name' => 'Imprimantes', 'description' => 'Imprimantes laser et jet d\'encre', 'is_active' => true],
                        ['name' => 'Scanners', 'description' => 'Numériseurs de documents', 'is_active' => true],
                        ['name' => 'Photocopieurs', 'description' => 'Photocopieurs multifonctions', 'is_active' => true],
                    ]
                ],
                [
                    'name' => 'Sécurité',
                    'description' => 'Équipements de sécurité informatique',
                    'is_active' => true,
                    'children' => [
                        ['name' => 'Caméras', 'description' => 'Caméras de surveillance', 'is_active' => true],
                        ['name' => 'Contrôle d\'accès', 'description' => 'Badgeuses et lecteurs', 'is_active' => true],
                        ['name' => 'Coffres-forts', 'description' => 'Coffres-forts numériques', 'is_active' => true],
                    ]
                ],
            ];

            foreach ($categories as $categoryData) {
                $children = $categoryData['children'] ?? [];
                unset($categoryData['children']);
                
                // Créer la catégorie parente
                $parent = Category::create($categoryData);
                
                // Créer les sous-catégories
                foreach ($children as $childData) {
                    $parent->children()->create($childData);
                }
            }

            // Ajouter quelques catégories sans parent
            $rootCategories = [
                ['name' => 'Fournitures', 'description' => 'Fournitures de bureau générales', 'is_active' => true],
                ['name' => 'Salle de réunion', 'description' => 'Équipements pour salles de réunion', 'is_active' => true],
                ['name' => 'Maintenance', 'description' => 'Équipements de maintenance', 'is_active' => false],
            ];

            foreach ($rootCategories as $category) {
                Category::create($category);
            }
        });
    }
}
