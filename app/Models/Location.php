<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'building',
        'room',
        'description',
        'is_active',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Obtenir les équipements actuellement à cet emplacement.
     */
    public function equipment()
    {
        return $this->hasMany(Equipment::class);
    }

    /**
     * Obtenir le nombre total d'équipements et de mouvements pour cet emplacement.
     */
    public function getActivityCountAttribute()
    {
        return $this->equipment_count + $this->origin_movements_count + $this->destination_movements_count;
    }
    
    /**
     * Obtenir les mouvements d'équipements en provenance de cet emplacement.
     */
    public function originMovements()
    {
        return $this->hasMany(EquipmentMovement::class, 'origin_location_id');
    }
    
    /**
     * Obtenir les mouvements d'équipements à destination de cet emplacement.
     */
    public function destinationMovements()
    {
        return $this->hasMany(EquipmentMovement::class, 'destination_location_id');
    }

    /**
     * Obtenir les mouvements d'équipements à destination de cet emplacement.
     */
    public function movementsTo()
    {
        return $this->hasMany(EquipmentMovement::class, 'to_location_id');
    }

    /**
     * Obtenir l'icône de l'emplacement en fonction de son type.
     *
     * @return string
     */
    public function getIcon()
    {
        return 'fa-map-marker-alt'; // Icône par défaut
    }

    /**
     * Obtenir la couleur de l'étiquette en fonction du type d'emplacement.
     *
     * @return string
     */
    public function getTypeColor()
    {
        return 'primary'; // Couleur par défaut
    }

    /**
     * Obtenir le libellé du type d'emplacement.
     *
     * @return string
     */
    public function getTypeLabel()
    {
        return 'Emplacement';
    }

    /**
     * Obtenir l'emplacement parent.
     */
    public function parent()
    {
        return $this->belongsTo(Location::class, 'parent_id');
    }
    
    /**
     * Obtenir les emplacements enfants.
     */
    public function children()
    {
        return $this->hasMany(Location::class, 'parent_id');
    }
}
