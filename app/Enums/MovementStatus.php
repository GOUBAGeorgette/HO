<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum MovementStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case REJECTED = 'rejected';

    /**
     * Obtient le libellé lisible du statut
     */
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'En attente',
            self::APPROVED => 'Approuvé',
            self::IN_PROGRESS => 'En cours',
            self::COMPLETED => 'Terminé',
            self::CANCELLED => 'Annulé',
            self::REJECTED => 'Rejeté',
        };
    }

    /**
     * Obtient la classe CSS pour le badge
     */
    public function color(): string
    {
        return match($this) {
            self::PENDING => 'warning',
            self::APPROVED => 'info',
            self::IN_PROGRESS => 'primary',
            self::COMPLETED => 'success',
            self::CANCELLED, self::REJECTED => 'danger',
        };
    }

    /**
     * Vérifie si le statut permet l'approbation
     */
    public function canBeApproved(): bool
    {
        return $this === self::PENDING;
    }

    /**
     * Vérifie si le statut permet le démarrage
     */
    public function canBeStarted(): bool
    {
        return $this === self::APPROVED;
    }

    /**
     * Vérifie si le statut permet la complétion
     */
    public function canBeCompleted(): bool
    {
        return $this === self::IN_PROGRESS;
    }

    /**
     * Vérifie si le statut permet l'annulation
     */
    public function canBeCancelled(): bool
    {
        return in_array($this, [self::PENDING, self::APPROVED, self::IN_PROGRESS]);
    }

    /**
     * Obtient la liste des statuts sous forme de tableau pour les formulaires
     */
    public static function toSelectArray(): array
    {
        $result = [];
        foreach (self::cases() as $status) {
            $result[$status->value] = $status->label();
        }
        return $result;
    }
}
