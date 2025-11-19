<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Maintenance extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Les statuts de maintenance possibles.
     *
     * @var array<string>
     */
    public const STATUSES = [
        'scheduled' => 'Planifiée',
        'in_progress' => 'En cours',
        'completed' => 'Terminée',
        'cancelled' => 'Annulée',
    ];

    /**
     * Les types de maintenance possibles.
     *
     * @var array<string>
     */
    public const TYPES = [
        'preventive' => 'Préventive',
        'corrective' => 'Corrective',
        'predictive' => 'Prédictive',
        'other' => 'Autre',
    ];

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'equipment_id',
        'maintenance_type',
        'title',
        'description',
        'scheduled_date',
        'completed_date',
        'status',
        'assigned_to',
        'cost',
        'notes',
        'created_by',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'scheduled_date' => 'datetime',
        'completed_date' => 'datetime',
        'cost' => 'decimal:2',
    ];

    /**
     * Obtenir l'équipement concerné par la maintenance.
     */
    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    /**
     * Obtenir l'utilisateur assigné à la maintenance.
     */
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Obtenir l'utilisateur qui a créé la maintenance.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Obtenir le libellé du statut de la maintenance.
     *
     * @return string
     */
    public function getStatusLabelAttribute()
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /**
     * Obtenir le libellé du type de maintenance.
     *
     * @return string
     */
    public function getTypeLabelAttribute()
    {
        return self::TYPES[$this->maintenance_type] ?? $this->maintenance_type;
    }

    /**
     * Vérifier si la maintenance est en retard.
     *
     * @return bool
     */
    public function getIsOverdueAttribute()
    {
        return $this->status === 'scheduled' && $this->scheduled_date < now();
    }
}
