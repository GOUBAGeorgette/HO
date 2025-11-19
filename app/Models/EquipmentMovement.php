<?php

namespace App\Models;

use App\Models\Department;
use App\Models\Equipment;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EquipmentMovement extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Les types de mouvements possibles.
     *
     * @var array<string>
     */
    public const TYPES = [
        'checkout' => 'Sortie',
        'checkin' => 'Retour',
        'transfer' => 'Transfert',
        'maintenance' => 'Maintenance',
    ];

    /**
     * Les relations à charger automatiquement.
     *
     * @var array
     */
    protected $with = [
        'equipment',
        'originLocation',
        'destinationLocation',
        'requester',
        'approver'
    ];

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'equipment_id',
        'origin_type',
        'origin_location_id',
        'origin_department_id',
        'origin_external',
        'origin_contact',
        'destination_type',
        'destination_location_id',
        'destination_department_id',
        'destination_external',
        'destination_contact',
        'type',
        'priority',
        'scheduled_date',
        'completed_at',
        'reason',
        'notes',
        'status',
        'requested_by',
        'approved_by',
        'approved_at',
        'cancelled_at',
        'cancellation_reason',
    ];
    
    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'scheduled_date' => 'datetime',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];
    
    /**
     * Les attributs qui doivent être traités comme des dates.
     *
     * @var array
     */
    protected $dates = [
        'scheduled_date',
        'approved_at',
        'completed_at',
        'cancelled_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    
    // Types de mouvements
    const TYPE_CHECKOUT = 'checkout';
    const TYPE_CHECKIN = 'checkin';
    const TYPE_TRANSFER = 'transfer';
    const TYPE_MAINTENANCE = 'maintenance';
    
    // Statuts
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    
    // Priorités
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';

    /**
     * Obtenir l'équipement concerné par le mouvement.
     */
    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    /**
     * Obtenir l'emplacement d'origine du mouvement.
     */
    public function originLocation()
    {
        return $this->belongsTo(Location::class, 'origin_location_id');
    }

    /**
     * Obtenir le département d'origine du mouvement.
     */
    public function originDepartment()
    {
        return $this->belongsTo(Department::class, 'origin_department_id');
    }

    /**
     * Obtenir l'emplacement de destination du mouvement.
     */
    public function destinationLocation()
    {
        return $this->belongsTo(Location::class, 'destination_location_id');
    }

    /**
     * Obtenir le département de destination du mouvement.
     */
    public function destinationDepartment()
    {
        return $this->belongsTo(Department::class, 'destination_department_id');
    }

    /**
     * Obtenir l'utilisateur qui a demandé le mouvement.
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * Obtenir l'utilisateur qui a approuvé le mouvement.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Obtenir le libellé du type de mouvement.
     *
     * @return string
     */
    public function getTypeLabelAttribute()
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    /**
     * Obtenir l'icône du type de mouvement.
     *
     * @return string
     */
    public function getTypeIconAttribute()
    {
        $icons = [
            'checkout' => 'sign-out-alt',
            'checkin' => 'sign-in-alt',
            'transfer' => 'exchange-alt',
            'maintenance' => 'tools',
            'repair' => 'wrench',
            'loan' => 'hand-holding',
            'return' => 'undo',
            'other' => 'ellipsis-h'
        ];
        
        return $icons[$this->type] ?? 'exchange-alt';
    }

    /**
     * Obtenir la couleur du type de mouvement.
     *
     * @return string
     */
    public function getTypeColorAttribute()
    {
        $colors = [
            'checkout' => 'primary',
            'checkin' => 'success',
            'transfer' => 'info',
            'maintenance' => 'warning',
            'repair' => 'danger',
            'loan' => 'secondary',
            'return' => 'success',
            'other' => 'dark'
        ];
        
        return $colors[$this->type] ?? 'secondary';
    }

    /**
     * Obtenir la couleur du statut.
     *
     * @return string
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'warning',
            'approved' => 'info',
            'in_progress' => 'primary',
            'completed' => 'success',
            'cancelled' => 'secondary',
        ];
        
        return $colors[$this->status] ?? 'secondary';
    }

    /**
     * Obtenir le libellé du statut.
     *
     * @return string
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'En attente',
            'approved' => 'Approuvé',
            'in_progress' => 'En cours',
            'completed' => 'Terminé',
            'cancelled' => 'Annulé',
        ];
        
        return $labels[$this->status] ?? $this->status;
    }

    /**
     * Vérifier si le mouvement est en retard.
     *
     * @return bool
     */
    public function getIsOverdueAttribute()
    {
        return $this->scheduled_date < now() && $this->status === 'scheduled';
    }
    
    /**
     * Obtenir le nom du département d'origine.
     *
     * @return string
     */
    public function getOriginDepartmentNameAttribute()
    {
        if ($this->origin_department) {
            return $this->origin_department->name;
        }
        
        return $this->origin_external ?: 'Externe';
    }
    
    /**
     * Obtenir le nom du département de destination.
     *
     * @return string
     */
    public function getDestinationDepartmentNameAttribute()
    {
        if ($this->destination_department) {
            return $this->destination_department->name;
        }
        
        return $this->destination_external ?: 'Externe';
    }
}
