<?php

namespace App\Imports;

use App\Models\Equipment;
use App\Models\Category;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\ValidationException as ExcelValidationException;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EquipmentImport implements 
    ToModel, 
    WithHeadingRow, 
    WithValidation,
    SkipsOnError,
    SkipsOnFailure,
    WithBatchInserts,
    WithChunkReading
{
    use Importable;
    
    /**
     * @var array
     */
    private $errors = [];
    
    /**
     * @var int
     */
    private $importedCount = 0;
    
    /**
     * @param \Throwable $e
     */
    public function onError(\Throwable $e)
    {
        $this->errors[] = $e->getMessage();
        
        // Si c'est une erreur de validation Excel, on la loggue
        if ($e instanceof ExcelValidationException) {
            foreach ($e->failures() as $failure) {
                $this->errors[] = sprintf(
                    'Ligne %s: %s - %s',
                    $failure->row(),
                    $failure->attribute(),
                    implode(', ', $failure->errors())
                );
            }
        }
        
        return null;
    }
    
    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @var array $customMessages
     */
    public $customMessages = [
        'required' => 'Le champ :attribute est requis.',
        'string' => 'Le champ :attribute doit être une chaîne de caractères.',
        'max' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
    ];
    
    /**
     * Get the number of imported rows
     *
     * @return int
     */
    public function getRowCount(): int
    {
        return $this->importedCount;
    }

    /**
     * Crée un modèle Equipment à partir d'une ligne de données
     */
    public function model(array $row)
    {
        try {
            // Nettoyer et formater les données
            $row = $this->cleanRowData($row);
            
            // Gérer la catégorie
            $categoryName = $row['categorie'] ?? 'Non classé';
            $category = Category::firstOrCreate(
                ['name' => $categoryName],
                ['description' => 'Catégorie créée automatiquement lors de l\'import']
            );
            
            // Gérer l'emplacement
            $location = null;
            if (!empty($row['emplacement'])) {
                $location = Location::firstOrCreate(
                    ['name' => $row['emplacement']],
                    [
                        'building' => null,
                        'room' => null,
                        'description' => 'Emplacement créé automatiquement lors de l\'import',
                        'is_active' => true
                    ]
                );
            }
            
            // Préparer les données pour l'équipement
            $equipmentData = [
                'name' => $row['nom'],
                'model' => $row['modele'] ?? null,
                'brand' => $row['marque'] ?? null,
                'type' => $row['type'] ?? null,
                'quantity' => $row['quantity'] ?? 1,
                'status' => $this->normalizeEtat($row['etat'] ?? null),
                'location' => $row['emplacement'] ?? null,
                'location_id' => $location ? $location->id : null,
                'is_usable' => isset($row['utilisabilite']) ? strtoupper($row['utilisabilite']) === 'OUI' : true,
                'responsible_person' => $row['personne_en_charge'] ?? null,
                'notes' => $row['remarque'] ?? null,
                'suggestions' => $row['suggestions'] ?? null,
                'maintenance_frequency' => $row['frequence_maintenance'] ?? null,
                'maintenance_tasks' => $row['frequence_de_maintenabilite_et_tache'] ?? null,
                'maintenance_type' => $row['type_de_maintenance'] ?? null,
                'category_id' => $category->id,
            ];

            // Vérifier si l'équipement existe déjà
            $equipment = Equipment::where('name', $equipmentData['name'])
                ->where('model', $equipmentData['model'])
                ->first();

            if ($equipment) {
                // Mettre à jour l'équipement existant
                $equipment->update($equipmentData);
                $this->importedCount++;
                return null;
            }

            // Créer un nouvel équipement
            $this->importedCount++;
            return new Equipment($equipmentData);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'importation d\'un équipement', [
                'row' => $row,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->errors[] = sprintf(
                'Erreur sur la ligne: %s - %s',
                $row['nom'] ?? 'Inconnu',
                $e->getMessage()
            );
            return null;
        }
    }

    /**
     * Règles de validation pour l'importation
     */
    public function rules(): array
    {
        return [
            'categories' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'modele' => 'nullable|string|max:255',
            'marque' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'quantite' => 'nullable|integer|min:1',
            'quantity' => 'nullable|integer|min:1',
            'etat' => [
                'nullable',
                'string',
                Rule::in(['excellent', 'bon', 'moyen', 'mauvais', 'hors_service', 'Fonctionnels', 'Usé', 'HS', 'Bugs'])
            ],
            'emplacement' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'utilisabilite' => 'nullable|string|in:OUI,NON',
            'is_usable' => 'nullable|boolean',
            'personne_en_charge_du_materiel' => 'nullable|string|max:255',
            'responsible_person' => 'nullable|string|max:255',
            'remarque' => 'nullable|string',
            'notes' => 'nullable|string',
            'suggestions' => 'nullable|string',
            'frequence_de_maintenabilite_et_tache' => 'nullable|string',
            'maintenance_tasks' => 'nullable|string',
            'frequence_maintenance' => 'nullable|string|max:255',
            'maintenance_frequency' => 'nullable|string|max:255',
            'type_de_maintenance' => 'nullable|string|max:255',
            'maintenance_type' => 'nullable|string|max:255',
        ];
    }

    /**
     * Nettoie et formate les données d'une ligne
     */
    protected function cleanRowData(array $row): array
    {
        $cleaned = [];
        
        // Nettoyer chaque champ
        foreach ($row as $key => $value) {
            // Convertir en chaîne si c'est un objet ou un tableau
            if (is_object($value) || is_array($value)) {
                $value = json_encode($value);
            }
            
            // Traiter les chaînes
            if (is_string($value)) {
                // Supprimer les espaces en début et fin de chaîne
                $value = trim($value);
                
                // Convertir les chaînes vides en null
                if ($value === '') {
                    $value = null;
                }
            } 
            // Gérer les valeurs numériques
            elseif (is_numeric($value)) {
                $value = (float)$value;
                // Convertir en entier si c'est un nombre entier
                if ($value == (int)$value) {
                    $value = (int)$value;
                }
            }
            
            $cleaned[strtolower($key)] = $value;
        }
        
        // Gérer les alias de champs
        $aliases = [
            'categories' => 'categorie',
            'personne_en_charge_du_materiel' => 'personne_en_charge',
            'frequence_de_maintenabilite_et_tache' => 'maintenance_tasks',
            'frequence_maintenance' => 'maintenance_frequency',
            'type_de_maintenance' => 'maintenance_type',
            'emplacement' => 'location',
            'quantite' => 'quantity',
            'remarque' => 'notes',
            'etat' => 'status',
            'utilisabilite' => 'is_usable',
            'marque' => 'brand',
            'modele' => 'model',
            'nom' => 'name'
        ];
        
        foreach ($aliases as $alias => $target) {
            if (isset($cleaned[$alias]) && $cleaned[$alias] !== null) {
                $cleaned[$target] = $cleaned[$alias];
            }
        }
        
        // Normaliser les valeurs booléennes
        if (isset($cleaned['is_usable'])) {
            if (is_string($cleaned['is_usable'])) {
                $cleaned['is_usable'] = in_array(strtoupper($cleaned['is_usable']), ['OUI', 'Y', 'YES', 'TRUE', '1', 'VRAI', 'FONCTIONNEL', 'FONCTIONNELS']);
            } elseif (is_numeric($cleaned['is_usable'])) {
                $cleaned['is_usable'] = (bool)$cleaned['is_usable'];
            }
        } else {
            // Essayer de déduire l'utilisabilité à partir de l'état
            $etat = strtolower($cleaned['etat'] ?? '');
            $cleaned['is_usable'] = !in_array($etat, ['hors_service', 'hs', 'usé', 'usee', 'casse', 'cassé']);
        }
        
        // Normaliser la quantité
        if (isset($cleaned['quantity'])) {
            $cleaned['quantity'] = is_numeric($cleaned['quantity']) ? max(1, (int)$cleaned['quantity']) : 1;
        } else {
            $cleaned['quantity'] = 1;
        }
        
        // Définir une catégorie par défaut si non spécifiée
        if (empty($cleaned['categorie'])) {
            $cleaned['categorie'] = 'Non classé';
        }
        
        // Tronquer les champs de texte trop longs
        $textFields = [
            'nom', 'model', 'brand', 'type', 'location', 'notes', 
            'suggestions', 'maintenance_frequency', 'maintenance_tasks', 
            'maintenance_type', 'responsible_person', 'categorie', 'personne_en_charge'
        ];
        
        foreach ($textFields as $field) {
            if (isset($cleaned[$field]) && is_string($cleaned[$field])) {
                $cleaned[$field] = Str::limit($cleaned[$field], 255);
            }
        }
        
        // Normaliser le statut
        if (!empty($cleaned['etat'])) {
            $cleaned['status'] = $this->normalizeEtat($cleaned['etat']);
        } else {
            $cleaned['status'] = 'bon'; // Valeur par défaut
        }
        
        return $cleaned;
    }
    
    /**
     * Normalise l'état de l'équipement
     */
    protected function normalizeEtat($etat): string
    {
        if (empty($etat)) {
            return 'bon'; // Valeur par défaut selon la migration
        }
        
        // Convertir en chaîne si ce n'est pas déjà le cas
        $etat = (string)$etat;
        $etat = trim($etat);
        $etat = strtolower($etat);
        
        // Mapper les valeurs courantes
        $mapping = [
            // Excellent
            'excellent' => 'excellent',
            'excellente' => 'excellent',
            'excellents' => 'excellent',
            'parfait' => 'excellent',
            'parfaite' => 'excellent',
            'neuf' => 'excellent',
            'comme neuf' => 'excellent',
            '10/10' => 'excellent',
            '100%' => 'excellent',
            
            // Bon
            'bon' => 'bon',
            'bon état' => 'bon',
            'fonctionnel' => 'bon',
            'fonctionnels' => 'bon',
            'ok' => 'bon',
            '8/10' => 'bon',
            '9/10' => 'bon',
            '80%' => 'bon',
            '90%' => 'bon',
            
            // Moyen
            'moyen' => 'moyen',
            'moyenne' => 'moyen',
            'acceptable' => 'moyen',
            'correct' => 'moyen',
            '5/10' => 'moyen',
            '6/10' => 'moyen',
            '7/10' => 'moyen',
            '50%' => 'moyen',
            '60%' => 'moyen',
            '70%' => 'moyen',
            
            // Mauvais
            'mauvais' => 'mauvais',
            'mauvaise' => 'mauvais',
            'médiocre' => 'mauvais',
            'usé' => 'mauvais',
            'usee' => 'mauvais',
            'abimé' => 'mauvais',
            'abime' => 'mauvais',
            'endommagé' => 'mauvais',
            'endommage' => 'mauvais',
            '3/10' => 'mauvais',
            '4/10' => 'mauvais',
            '30%' => 'mauvais',
            '40%' => 'mauvais',
            
            // Hors service
            'hors service' => 'hors_service',
            '2/10' => 'hors_service',
            '0%' => 'hors_service',
            '10%' => 'hors_service',
            '20%' => 'hors_service',
            'bugs' => 'hors_service',
            'bug' => 'hors_service',
            'défectueux' => 'hors_service',
            'defectueux' => 'hors_service',
            'hors d\'usage' => 'hors_service',
            'inutilisable' => 'hors_service',
        ];
        
        // Vérifier si l'état correspond directement à une valeur connue
        if (isset($mapping[$etat])) {
            return $mapping[$etat];
        }
        
        // Vérifier les correspondances partielles
        foreach ($mapping as $key => $value) {
            if (str_contains($etat, $key)) {
                return $value;
            }
        }
        
        // Essayer d'extraire un score numérique (ex: "7 sur 10" ou "70%")
        if (preg_match('/(\d+)\s*\/\s*10/i', $etat, $matches)) {
            $score = (int)$matches[1];
            if ($score >= 9) return 'excellent';
            if ($score >= 7) return 'bon';
            if ($score >= 5) return 'moyen';
            if ($score >= 3) return 'mauvais';
            return 'hors_service';
        }
        
        // Essayer d'extraire un pourcentage (ex: "70%")
        if (preg_match('/(\d+)%/', $etat, $matches)) {
            $percent = (int)$matches[1];
            if ($percent >= 90) return 'excellent';
            if ($percent >= 70) return 'bon';
            if ($percent >= 50) return 'moyen';
            if ($percent >= 30) return 'mauvais';
            return 'hors_service';
        }
        
        // Si on arrive ici, on retourne une valeur par défaut
        return 'bon';
        
        // Vérifier d'abord la correspondance exacte
        if (isset($mapping[$etat])) {
            $status = $mapping[$etat];
            // S'assurer que la valeur retournée est valide
            $validStatuses = ['excellent', 'bon', 'moyen', 'mauvais', 'hors_service'];
            if (in_array($status, $validStatuses)) {
                return $status;
            }
        }
        
        // Ensuite, vérifier les correspondances partielles
        foreach ($mapping as $key => $value) {
            if (str_contains($etat, $key)) {
                $status = $value;
                // S'assurer que la valeur retournée est valide
                $validStatuses = ['excellent', 'bon', 'moyen', 'mauvais', 'hors_service'];
                if (in_array($status, $validStatuses)) {
                    return $status;
                }
            }
        }
        
        // Si l'état contient des indices numériques (comme 7/10), essayer de le mapper
        if (preg_match('/(\d+)\s*\/\s*10/', $etat, $matches)) {
            $score = (int)$matches[1];
            if ($score >= 9) return 'excellent';
            if ($score >= 7) return 'bon';
            if ($score >= 5) return 'moyen';
            if ($score >= 3) return 'mauvais';
            return 'hors_service';
        }
        
        // Si l'état contient des pourcentages, essayer de le mapper
        if (preg_match('/(\d+)%/', $etat, $matches)) {
            $percent = (int)$matches[1];
            if ($percent >= 90) return 'excellent';
            if ($percent >= 70) return 'bon';
            if ($percent >= 50) return 'moyen';
            if ($percent >= 30) return 'mauvais';
            return 'hors_service';
        }
        
        // Si on arrive ici, on n'a pas trouvé de correspondance valide
        // Essayer de deviner à partir du texte
        if (stripos($etat, 'bon') !== false) return 'bon';
        if (stripos($etat, 'excellent') !== false) return 'excellent';
        if (stripos($etat, 'moyen') !== false) return 'moyen';
        if (stripos($etat, 'mauvais') !== false) return 'mauvais';
        if (stripos($etat, 'panne') !== false || stripos($etat, 'hs') !== false) return 'hors_service';
        
        // Valeur par défaut selon la migration
        return 'bon';
    }
    
    /**
     * Trouve l'utilisateur assigné
     */
    protected function findAssignedUser(?string $search): ?User
    {
        if (empty($search)) {
            return null;
        }
        
        return User::where('name', 'like', '%' . $search . '%')
            ->orWhere('email', 'like', '%' . $search . '%')
            ->first();
    }
    
    /**
     * Tronque une chaîne si nécessaire
     */
    protected function truncateIfNeeded(?string $value, int $maxLength): ?string
    {
        if ($value === null) {
            return null;
        }
        
        return mb_strlen($value) > $maxLength 
            ? mb_substr($value, 0, $maxLength - 3) . '...' 
            : $value;
    }
    
    /**
     * Parse une date depuis différents formats
     */
    protected function parseDate($date)
    {
        if (empty($date)) {
            return null;
        }
        
        try {
            return \Carbon\Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Trouver ou créer une catégorie
     */
    protected function findOrCreateCategory(array $row): Category
    {
        $categoryName = $row['categorie'] ?? 'Non classé';
        $categoryName = mb_substr($categoryName, 0, 100); // S'assurer que le nom ne dépasse pas 100 caractères
        
        return Category::firstOrCreate(
            ['name' => $categoryName],
            ['description' => 'Importé automatiquement']
        );
    }
    
    /**
     * Gestion des échecs de validation
     */
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            Log::warning('Échec de validation lors de l\'importation', [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors(),
                'values' => $failure->values()
            ]);
        }
    }

    /**
     * Nombre d'insertions par lot
     */
    public function batchSize(): int
    {
        return 100;
    }

    /**
     * Taille des lots pour la lecture
     */
    public function chunkSize(): int
    {
        return 100;
    }

    /**
     * Mapper le statut de l'équipement
     */
    private function mapStatus($status)
    {
        $status = strtolower(trim($status));
        $statusMap = [
            'excellent' => 'excellent',
            'bon' => 'bon',
            'moyen' => 'moyen',
            'mauvais' => 'mauvais',
            'hors service' => 'hors_service',
            'hors_service' => 'hors_service',
            'hors' => 'hors_service',
            'hs' => 'hors_service',
        ];

        return $statusMap[$status] ?? 'bon';
    }

    /**
     * Mapper l'utilisabilité
     */
    private function mapUsability($value)
    {
        $value = strtolower(trim($value));
        return in_array($value, ['1', 'true', 'oui', 'yes', 'y', 'o']);
    }
}
