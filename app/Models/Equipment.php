<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Equipment extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'model',
        'brand',
        'type',
        'quantity',
        'status',
        'location',
        'location_id',
        'is_usable',
        'responsible_person',
        'notes',
        'suggestions',
        'maintenance_frequency',
        'maintenance_tasks',
        'maintenance_type',
        'category_id',
        'assigned_to',
    ];

    protected $attributes = [
        'quantity' => 1,
        'is_usable' => true,
        'status' => 'bon',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'is_usable' => 'boolean',
    ];

    protected $enums = [
        'status' => [
            'excellent' => 'Excellent',
            'bon' => 'Bon',
            'moyen' => 'Moyen',
            'mauvais' => 'Mauvais',
            'hors_service' => 'Hors service',
        ],
    ];

    /**
     * Obtenir la catégorie de l'équipement.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Obtenir l'emplacement actuel de l'équipement.
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Obtenir l'utilisateur auquel l'équipement est assigné.
     */
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Obtenir l'historique des mouvements de l'équipement.
     */
    public function movements()
    {
        return $this->hasMany(EquipmentMovement::class);
    }

    /**
     * Obtenir l'historique des maintenances de l'équipement.
     */
    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }

    /**
     * Obtenir l'historique des modifications de l'équipement.
     */
    public function history()
    {
        return $this->hasMany(EquipmentHistory::class)->latest();
    }

    /**
     * Obtenir les documents associés à l'équipement.
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Obtenir l'utilisateur qui a créé l'équipement.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Obtenir l'utilisateur qui a mis à jour l'équipement.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Obtenir l'état de la garantie de l'équipement.
     *
     * @return array
     */
    public function getWarrantyStatusAttribute()
    {
        if (!$this->warranty_expires) {
            return [
                'expired' => true,
                'remaining_days' => 0,
                'progress' => 0,
                'status' => 'Aucune garantie',
                'class' => 'secondary'
            ];
        }

        $now = now();
        $expires = $this->warranty_expires;
        $isExpired = $now->gt($expires);
        $remainingDays = $isExpired 
            ? 0 
            : $now->diffInDays($expires, false);
            
        $totalDays = $this->purchase_date ? $this->purchase_date->diffInDays($expires) : 365;
        $progress = $totalDays > 0 ? min(100, max(0, 100 - (($now->diffInDays($expires, false) / $totalDays) * 100))) : 100;

        return [
            'expired' => $isExpired,
            'remaining_days' => $remainingDays,
            'progress' => $progress,
            'status' => $isExpired ? 'Expirée' : 'Active',
            'class' => $isExpired ? 'danger' : ($remainingDays <= 30 ? 'warning' : 'success')
        ];
    }
}
