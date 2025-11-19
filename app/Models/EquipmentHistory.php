<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipmentHistory extends Model
{
    protected $fillable = [
        'equipment_id',
        'action',
        'details',
        'user_id',
        'ip_address',
        'user_agent',
        'old_values',
        'new_values',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
