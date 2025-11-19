<?php

namespace App\Imports;

use App\Models\Location;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class LocationsImport implements ToModel, WithHeadingRow, WithValidation
{
    protected $updateExisting;
    protected $parentCache = [];

    public function __construct($updateExisting = false)
    {
        $this->updateExisting = $updateExisting;
    }

    public function model(array $row)
    {
        $parentId = null;
        
        // Gérer le parent si spécifié
        if (!empty($row['parent_name'])) {
            $parentName = trim($row['parent_name']);
            
            // Utiliser le cache pour éviter les requêtes inutiles
            if (!isset($this->parentCache[$parentName])) {
                $parent = Location::where('name', $parentName)->first();
                if ($parent) {
                    $this->parentCache[$parentName] = $parent->id;
                } else {
                    Log::warning("Emplacement parent non trouvé : " . $parentName);
                    return null;
                }
            }
            
            $parentId = $this->parentCache[$parentName];
        }
        
        $data = [
            'name' => $row['name'],
            'building' => $row['building'] ?? null,
            'room' => $row['room'] ?? null,
            'description' => $row['description'] ?? null,
            'parent_id' => $parentId,
            'is_active' => isset($row['is_active']) ? (bool)$row['is_active'] : true,
        ];
        
        if ($this->updateExisting) {
            return Location::updateOrCreate(['name' => $row['name']], $data);
        }
        
        return new Location($data);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'building' => 'nullable|string|max:255',
            'room' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'parent_name' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ];
    }
}
