@extends('layouts.maquette')

@section('title', 'Détails du mouvement #' . $movement->reference)

@push('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 2rem;
        margin: 2rem 0;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        left: 0.5rem;
        top: 0;
        bottom: 0;
        width: 2px;
        background-color: #e9ecef;
    }
    
    .timeline-item {
        position: relative;
        padding-bottom: 2rem;
    }
    
    .timeline-marker {
        position: absolute;
        left: -2rem;
        width: 1rem;
        height: 1rem;
        border-radius: 50%;
        background-color: #0d6efd;
        border: 2px solid #fff;
        z-index: 1;
    }
    
    .timeline-content {
        padding: 0.5rem 1rem;
        background-color: #f8f9fa;
        border-radius: 0.25rem;
        margin-bottom: 1rem;
    }
    
    .equipment-image {
        height: 120px;
        object-fit: cover;
        border-radius: 0.25rem;
    }
    
    .status-badge {
        font-size: 0.9rem;
        padding: 0.35rem 0.75rem;
    }
    
    .info-card {
        transition: all 0.2s ease-in-out;
        height: 100%;
    }
    
    .info-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
    }
    
    .attachment-thumbnail {
        width: 100%;
        height: 120px;
        object-fit: cover;
        border-radius: 0.25rem;
    }
    
    .signature-preview {
        max-width: 100%;
        max-height: 150px;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-exchange-alt me-2"></i> Détails du mouvement #{{ $movement->reference }}
            <span class="badge bg-{{ $movement->status_color }} status-badge align-middle ms-2">
                {{ $movement->status_label }}
            </span>
        </h1>
        <div>
            <a href="{{ route('equipment-movements.index') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i> Retour à la liste
            </a>
            @can('update', $movement)
                <a href="{{ route('equipment-movements.edit', $movement) }}" class="btn btn-primary me-2">
                    <i class="fas fa-edit me-1"></i> Modifier
                </a>
            @endcan
            @can('delete', $movement)
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteMovementModal">
                    <i class="fas fa-trash-alt me-1"></i> Supprimer
                </button>
            @endcan
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-8">
            <!-- Carte d'information principale -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i> Détails du mouvement
                    </h6>
                    <span class="badge bg-{{ $movement->is_temporary ? 'info' : 'secondary' }}">
                        {{ $movement->is_temporary ? 'Mouvement temporaire' : 'Mouvement permanent' }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Équipement</h5>
                            <div class="d-flex align-items-center">
                                @if($movement->equipment->image_url)
                                    <img src="{{ asset('storage/' . $movement->equipment->image_url) }}" 
                                         alt="{{ $movement->equipment->name }}" 
                                         class="equipment-image me-3" 
                                         style="width: 80px;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center me-3" 
                                         style="width: 80px; height: 80px; border-radius: 0.25rem;">
                                        <i class="fas fa-box fa-2x text-muted"></i>
                                    </div>
                                @endif
                                <div>
                                    <h6 class="mb-1">
                                        <a href="{{ route('equipment.show', $movement->equipment) }}" class="text-decoration-none">
                                            {{ $movement->equipment->name }}
                                        </a>
                                    </h6>
                                    <p class="mb-1 text-muted small">
                                        <i class="fas fa-tag me-1"></i> {{ $movement->equipment->category->name ?? 'Non catégorisé' }}
                                    </p>
                                    <p class="mb-0 text-muted small">
                                        @if($movement->equipment->model)
                                            <i class="fas fa-cube me-1"></i> {{ $movement->equipment->model }}
                                        @endif
                                        @if($movement->equipment->serial_number)
                                            <span class="ms-2">
                                                <i class="fas fa-barcode me-1"></i> {{ $movement->equipment->serial_number }}
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5>Informations du mouvement</h5>
                            <dl class="row mb-0">
                                <dt class="col-sm-5">Type de mouvement</dt>
                                <dd class="col-sm-7">{{ ucfirst($movement->movement_type) }}</dd>
                                
                                <dt class="col-sm-5">Priorité</dt>
                                <dd class="col-sm-7">
                                    @php
                                        $priorityClasses = [
                                            'low' => 'bg-secondary',
                                            'medium' => 'bg-info',
                                            'high' => 'bg-warning',
                                            'urgent' => 'bg-danger'
                                        ];
                                    @endphp
                                    <span class="badge {{ $priorityClasses[$movement->priority] ?? 'bg-secondary' }}">
                                        {{ ucfirst($movement->priority) }}
                                    </span>
                                </dd>
                                
                                <dt class="col-sm-5">Date prévue</dt>
                                <dd class="col-sm-7">{{ $movement->scheduled_date->format('d/m/Y H:i') }}</dd>
                                
                                @if($movement->assigned_to)
                                    <dt class="col-sm-5">Assigné à</dt>
                                    <dd class="col-sm-7">
                                        <i class="fas fa-user me-1"></i> {{ $movement->assignedTo->name }}
                                    </dd>
                                @endif
                                
                                @if($movement->is_temporary && $movement->start_date && $movement->end_date)
                                    <dt class="col-sm-5">Période</dt>
                                    <dd class="col-sm-7">
                                        Du {{ $movement->start_date->format('d/m/Y H:i') }}<br>
                                        au {{ $movement->end_date->format('d/m/Y H:i') }}
                                    </dd>
                                    
                                    @if($movement->temporary_reason)
                                        <dt class="col-sm-5">Raison</dt>
                                        <dd class="col-sm-7">{{ $movement->temporary_reason }}</dd>
                                    @endif
                                @endif
                            </dl>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card h-100 border-start border-4 border-primary">
                                <div class="card-body">
                                    <h6 class="card-title text-primary">
                                        <i class="fas fa-map-marker-alt me-2"></i> Origine
                                    </h6>
                                    @if($movement->origin_location)
                                        <div class="d-flex align-items-start">
                                            <i class="fas fa-building text-primary me-2 mt-1"></i>
                                            <div>
                                                <h6 class="mb-1">{{ $movement->origin_location->name }}</h6>
                                                <p class="mb-1 text-muted small">
                                                    {{ $movement->origin_location->full_address }}
                                                </p>
                                                @if($movement->origin_location->contact_phone)
                                                    <p class="mb-0 text-muted small">
                                                        <i class="fas fa-phone me-1"></i> {{ $movement->origin_location->contact_phone }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    @elseif($movement->origin_department)
                                        <div class="d-flex align-items-start">
                                            <i class="fas fa-users text-primary me-2 mt-1"></i>
                                            <div>
                                                <h6 class="mb-1">{{ $movement->origin_department }}</h6>
                                                @if($movement->origin_contact)
                                                    <p class="mb-0 text-muted small">
                                                        <i class="fas fa-user me-1"></i> {{ $movement->origin_contact }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-muted mb-0">Non spécifiée</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mt-3 mt-md-0">
                            <div class="card h-100 border-start border-4 border-success">
                                <div class="card-body">
                                    <h6 class="card-title text-success">
                                        <i class="fas fa-flag-checkered me-2"></i> Destination
                                    </h6>
                                    @if($movement->destination_location)
                                        <div class="d-flex align-items-start">
                                            <i class="fas fa-building text-success me-2 mt-1"></i>
                                            <div>
                                                <h6 class="mb-1">{{ $movement->destination_location->name }}</h6>
                                                <p class="mb-1 text-muted small">
                                                    {{ $movement->destination_location->full_address }}
                                                </p>
                                                @if($movement->destination_location->contact_phone)
                                                    <p class="mb-0 text-muted small">
                                                        <i class="fas fa-phone me-1"></i> {{ $movement->destination_location->contact_phone }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    @elseif($movement->destination_department)
                                        <div class="d-flex align-items-start">
                                            <i class="fas fa-users text-success me-2 mt-1"></i>
                                            <div>
                                                <h6 class="mb-1">{{ $movement->destination_department }}</h6>
                                                @if($movement->destination_contact)
                                                    <p class="mb-0 text-muted small">
                                                        <i class="fas fa-user me-1"></i> {{ $movement->destination_contact }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    @elseif($movement->external_destination)
                                        <div class="d-flex align-items-start">
                                            <i class="fas fa-truck text-warning me-2 mt-1"></i>
                                            <div>
                                                <h6 class="mb-1">{{ $movement->external_destination }}</h6>
                                                @if($movement->external_contact)
                                                    <p class="mb-1 text-muted small">
                                                        <i class="fas fa-user me-1"></i> {{ $movement->external_contact }}
                                                        @if($movement->external_phone)
                                                            <span class="ms-2">
                                                                <i class="fas fa-phone me-1"></i> {{ $movement->external_phone }}
                                                            </span>
                                                        @endif
                                                    </p>
                                                @endif
                                                @if($movement->external_address)
                                                    <p class="mb-0 text-muted small">
                                                        <i class="fas fa-map-marker-alt me-1"></i> {{ $movement->external_address }}
                                                    </p>
                                                @endif
                                                @if($movement->expected_return_date)
                                                    <p class="mb-0 mt-2 text-muted small">
                                                        <i class="far fa-calendar-alt me-1"></i> 
                                                        Retour prévu: {{ $movement->expected_return_date->format('d/m/Y H:i') }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-muted mb-0">Non spécifiée</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($movement->reason || $movement->notes)
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        @if($movement->reason)
                                            <h6 class="card-title">Raison du mouvement</h6>
                                            <p class="card-text">{{ $movement->reason }}</p>
                                        @endif
                                        
                                        @if($movement->notes)
                                            <h6 class="card-title mt-3">Notes supplémentaires</h6>
                                            <p class="card-text">{!! nl2br(e($movement->notes)) !!}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    @if($movement->attachments->count() > 0)
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="m-0 font-weight-bold">
                                            <i class="fas fa-paperclip me-2"></i> Pièces jointes
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach($movement->attachments as $attachment)
                                                <div class="col-md-4 mb-3">
                                                    <div class="card h-100">
                                                        @if(in_array(strtolower(pathinfo($attachment->filename, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']))
                                                            <img src="{{ asset('storage/' . $attachment->path) }}" 
                                                                 class="card-img-top attachment-thumbnail" 
                                                                 alt="{{ $attachment->original_filename }}">
                                                        @else
                                                            <div class="card-body text-center">
                                                                <i class="fas fa-file fa-4x text-muted mb-2"></i>
                                                                <p class="card-text small text-truncate">{{ $attachment->original_filename }}</p>
                                                            </div>
                                                        @endif
                                                        <div class="card-footer bg-transparent border-top-0">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <a href="{{ route('attachments.download', $attachment) }}" 
                                                                   class="btn btn-sm btn-outline-primary" 
                                                                   target="_blank">
                                                                    <i class="fas fa-download me-1"></i> Télécharger
                                                                </a>
                                                                <span class="text-muted small">{{ $attachment->size_for_humans }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    @if($movement->completion_notes || $movement->signature_path)
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="m-0">
                                            <i class="fas fa-check-circle me-2"></i> Rapport de fin de mission
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        @if($movement->completion_notes)
                                            <h6>Notes de fin de mission</h6>
                                            <p>{!! nl2br(e($movement->completion_notes)) !!}</p>
                                        @endif
                                        
                                        @if($movement->signature_path)
                                            <h6 class="mt-4">Signature</h6>
                                            <div class="border rounded p-3 d-inline-block">
                                                <img src="{{ asset('storage/' . $movement->signature_path) }}" 
                                                     alt="Signature" 
                                                     class="signature-preview">
                                                @if($movement->completedBy)
                                                    <p class="text-muted small mt-2 mb-0">
                                                        Signé par {{ $movement->completedBy->name }} 
                                                        le {{ $movement->completed_at->format('d/m/Y à H:i') }}
                                                    </p>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    @if($movement->cancellation_reason)
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card border-danger">
                                    <div class="card-header bg-danger text-white">
                                        <h6 class="m-0">
                                            <i class="fas fa-ban me-2"></i> Annulation du mouvement
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <h6>Raison de l'annulation</h6>
                                        <p>{!! nl2br(e($movement->cancellation_reason)) !!}</p>
                                        
                                        @if($movement->cancelledBy)
                                            <p class="text-muted small mb-0">
                                                Annulé par {{ $movement->cancelledBy->name }} 
                                                le {{ $movement->cancelled_at->format('d/m/Y à H:i') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    @if($movement->requires_approval)
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="m-0 font-weight-bold">
                                            <i class="fas fa-clipboard-check me-2"></i> Approbation
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        @if($movement->status == 'pending_approval')
                                            <div class="alert alert-warning mb-0">
                                                <i class="fas fa-clock me-2"></i>
                                                En attente d'approbation par {{ $movement->approver->name ?? 'un approbateur' }}
                                                @if($movement->required_by_date)
                                                    avant le {{ $movement->required_by_date->format('d/m/Y H:i') }}
                                                @endif
                                                
                                                @if($movement->approval_notes)
                                                    <div class="mt-2">
                                                        <strong>Notes pour l'approbation :</strong>
                                                        <p class="mb-0">{!! nl2br(e($movement->approval_notes)) !!}</p>
                                                    </div>
                                                @endif
                                                
                                                @can('approve', $movement)
                                                    <div class="mt-3">
                                                        <button type="button" class="btn btn-success btn-sm me-2" 
                                                                data-bs-toggle="modal" data-bs-target="#approveModal">
                                                            <i class="fas fa-check me-1"></i> Approuver
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-sm" 
                                                                data-bs-toggle="modal" data-bs-target="#rejectModal">
                                                            <i class="fas fa-times me-1"></i> Rejeter
                                                        </button>
                                                    </div>
                                                @endcan
                                            </div>
                                        @elseif($movement->status == 'approved')
                                            <div class="alert alert-success mb-0">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-check-circle me-2"></i>
                                                    <div>
                                                        Approuvé par {{ $movement->approvedBy->name ?? 'un approbateur' }} 
                                                        le {{ $movement->approved_at->format('d/m/Y à H:i') }}
                                                        
                                                        @if($movement->approval_comment)
                                                            <div class="mt-2">
                                                                <strong>Commentaire :</strong>
                                                                <p class="mb-0">{!! nl2br(e($movement->approval_comment)) !!}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif($movement->status == 'rejected')
                                            <div class="alert alert-danger mb-0">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-times-circle me-2"></i>
                                                    <div>
                                                        Rejeté par {{ $movement->rejectedBy->name ?? 'un approbateur' }} 
                                                        le {{ $movement->rejected_at->format('d/m/Y à H:i') }}
                                                        
                                                        @if($movement->approval_comment)
                                                            <div class="mt-2">
                                                                <strong>Raison du rejet :</strong>
                                                                <p class="mb-0">{!! nl2br(e($movement->approval_comment)) !!}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                
                                                @can('update', $movement)
                                                    <div class="mt-3">
                                                        <a href="{{ route('equipment-movements.edit', $movement) }}" class="btn btn-primary btn-sm">
                                                            <i class="fas fa-edit me-1"></i> Modifier et renvoyer pour approbation
                                                        </a>
                                                    </div>
                                                @endcan
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Actions -->
            @if($movement->status == 'approved' || $movement->status == 'scheduled')
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-tasks me-2"></i> Actions
                        </h6>
                    </div>
                    <div class="card-body">
                        @if($movement->status == 'approved' || $movement->status == 'scheduled')
                            @can('start', $movement)
                                <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#startModal">
                                    <i class="fas fa-play me-1"></i> Démarrer le mouvement
                                </button>
                            @endcan
                            
                            @can('cancel', $movement)
                                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                    <i class="fas fa-ban me-1"></i> Annuler le mouvement
                                </button>
                            @endcan
                        @endif
                        
                        @if($movement->status == 'in_progress')
                            @can('complete', $movement)
                                <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#completeModal">
                                    <i class="fas fa-check-circle me-1"></i> Marquer comme terminé
                                </button>
                            @endcan
                            
                            @can('cancel', $movement)
                                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                    <i class="fas fa-ban me-1"></i> Annuler le mouvement
                                </button>
                            @endcan
                        @endif
                    </div>
                </div>
            @endif
            
            <!-- Historique -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history me-2"></i> Historique
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <!-- Création -->
                        <div class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <p class="mb-0">
                                    <strong>Créé</strong> par {{ $movement->createdBy->name ?? 'système' }}
                                    <br>
                                    <small class="text-muted">{{ $movement->created_at->format('d/m/Y H:i') }}</small>
                                </p>
                            </div>
                        </div>
                        
                        <!-- Affectation -->
                        @if($movement->assigned_at && $movement->assignedTo)
                            <div class="timeline-item">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <p class="mb-0">
                                        <strong>Assigné</strong> à {{ $movement->assignedTo->name }}
                                        <br>
                                        <small class="text-muted">{{ $movement->assigned_at->format('d/m/Y H:i') }}</small>
                                    </p>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Approbation -->
                        @if($movement->approved_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <p class="mb-0">
                                        <strong>Approuvé</strong> par {{ $movement->approvedBy->name ?? 'un approbateur' }}
                                        @if($movement->approval_comment)
                                            <div class="alert alert-light p-2 mt-1 mb-0 small">
                                                {!! nl2br(e($movement->approval_comment)) !!}
                                            </div>
                                        @endif
                                        <br>
                                        <small class="text-muted">{{ $movement->approved_at->format('d/m/Y H:i') }}</small>
                                    </p>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Rejet -->
                        @if($movement->rejected_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-danger"></div>
                                <div class="timeline-content">
                                    <p class="mb-0">
                                        <strong>Rejeté</strong> par {{ $movement->rejectedBy->name ?? 'un approbateur' }}
                                        @if($movement->approval_comment)
                                            <div class="alert alert-light p-2 mt-1 mb-0 small">
                                                {!! nl2br(e($movement->approval_comment)) !!}
                                            </div>
                                        @endif
                                        <br>
                                        <small class="text-muted">{{ $movement->rejected_at->format('d/m/Y H:i') }}</small>
                                    </p>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Démarrage -->
                        @if($movement->started_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <p class="mb-0">
                                        <strong>Démarrage</strong> du mouvement
                                        @if($movement->start_notes)
                                            <div class="alert alert-light p-2 mt-1 mb-0 small">
                                                {!! nl2br(e($movement->start_notes)) !!}
                                            </div>
                                        @endif
                                        <br>
                                        <small class="text-muted">{{ $movement->started_at->format('d/m/Y H:i') }}</small>
                                    </p>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Achèvement -->
                        @if($movement->completed_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <p class="mb-0">
                                        <strong>Terminé</strong> par {{ $movement->completedBy->name ?? 'un utilisateur' }}
                                        @if($movement->completion_notes)
                                            <div class="alert alert-light p-2 mt-1 mb-0 small">
                                                {!! nl2br(e($movement->completion_notes)) !!}
                                            </div>
                                        @endif
                                        <br>
                                        <small class="text-muted">{{ $movement->completed_at->format('d/m/Y H:i') }}</small>
                                    </p>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Annulation -->
                        @if($movement->cancelled_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-danger"></div>
                                <div class="timeline-content">
                                    <p class="mb-0">
                                        <strong>Annulé</strong> par {{ $movement->cancelledBy->name ?? 'un utilisateur' }}
                                        @if($movement->cancellation_reason)
                                            <div class="alert alert-light p-2 mt-1 mb-0 small">
                                                <strong>Raison :</strong> {!! nl2br(e($movement->cancellation_reason)) !!}
                                            </div>
                                        @endif
                                        <br>
                                        <small class="text-muted">{{ $movement->cancelled_at->format('d/m/Y H:i') }}</small>
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Statut -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i> Statut
                    </h6>
                    <span class="badge bg-{{ $movement->status_color }} status-badge">
                        {{ $movement->status_label }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="progress">
                            @php
                                $progress = [
                                    'pending_approval' => 25,
                                    'approved' => 50,
                                    'scheduled' => 50,
                                    'in_progress' => 75,
                                    'completed' => 100,
                                    'cancelled' => 100,
                                    'rejected' => 100
                                ][$movement->status] ?? 0;
                                
                                $progressClass = [
                                    'pending_approval' => 'bg-warning',
                                    'approved' => 'bg-info',
                                    'scheduled' => 'bg-primary',
                                    'in_progress' => 'bg-primary',
                                    'completed' => 'bg-success',
                                    'cancelled' => 'bg-danger',
                                    'rejected' => 'bg-danger'
                                ][$movement->status] ?? 'bg-secondary';
                            @endphp
                            <div class="progress-bar {{ $progressClass }}" role="progressbar" 
                                 style="width: {{ $progress }}%" 
                                 aria-valuenow="{{ $progress }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                {{ $progress }}%
                            </div>
                        </div>
                    </div>
                    
                    <dl class="mb-0">
                        <dt>Dernière mise à jour</dt>
                        <dd>{{ $movement->updated_at->format('d/m/Y H:i') }}</dd>
                        
                        @if($movement->status == 'in_progress' && $movement->started_at)
                            <dt class="mt-2">En cours depuis</dt>
                            <dd>{{ $movement->started_at->diffForHumans() }}</dd>
                        @endif
                        
                        @if($movement->status == 'completed' && $movement->completed_at)
                            <dt class="mt-2">Terminé le</dt>
                            <dd>{{ $movement->completed_at->format('d/m/Y H:i') }}</dd>
                            <dd class="text-muted small">({{ $movement->completed_at->diffForHumans() }})</dd>
                        @endif
                        
                        @if($movement->status == 'cancelled' && $movement->cancelled_at)
                            <dt class="mt-2">Annulé le</dt>
                            <dd>{{ $movement->cancelled_at->format('d/m/Y H:i') }}</dd>
                            <dd class="text-muted small">({{ $movement->cancelled_at->diffForHumans() }})</dd>
                        @endif
                    </dl>
                </div>
            </div>
            
            <!-- Informations supplémentaires -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i> Informations
                    </h6>
                </div>
                <div class="card-body">
                    <dl class="mb-0">
                        <dt>Référence</dt>
                        <dd>{{ $movement->reference }}</dd>
                        
                        <dt class="mt-2">Type de mouvement</dt>
                        <dd>{{ ucfirst($movement->movement_type) }}</dd>
                        
                        <dt class="mt-2">Priorité</dt>
                        <dd>
                            @php
                                $priorityClasses = [
                                    'low' => 'bg-secondary',
                                    'medium' => 'bg-info',
                                    'high' => 'bg-warning',
                                    'urgent' => 'bg-danger'
                                ];
                            @endphp
                            <span class="badge {{ $priorityClasses[$movement->priority] ?? 'bg-secondary' }}">
                                {{ ucfirst($movement->priority) }}
                            </span>
                        </dd>
                        
                        <dt class="mt-2">Date de création</dt>
                        <dd>{{ $movement->created_at->format('d/m/Y H:i') }}</dd>
                        <dd class="text-muted small">({{ $movement->created_at->diffForHumans() }})</dd>
                        
                        @if($movement->is_temporary)
                            <dt class="mt-2">Type</dt>
                            <dd><span class="badge bg-info">Mouvement temporaire</span></dd>
                            
                            @if($movement->start_date && $movement->end_date)
                                <dt class="mt-2">Période</dt>
                                <dd>
                                    {{ $movement->start_date->format('d/m/Y H:i') }}<br>
                                    au {{ $movement->end_date->format('d/m/Y H:i') }}
                                    
                                    @php
                                        $now = now();
                                        $start = $movement->start_date;
                                        $end = $movement->end_date;
                                        $status = '';
                                        
                                        if ($now < $start) {
                                            $status = 'Début dans ' . $now->diffInDays($start) . ' jours';
                                            $badgeClass = 'bg-info';
                                        } elseif ($now >= $start && $now <= $end) {
                                            $status = 'En cours (reste ' . $now->diffInDays($end) . ' jours)';
                                            $badgeClass = 'bg-success';
                                        } else {
                                            $status = 'Terminé depuis ' . $now->diffInDays($end) . ' jours';
                                            $badgeClass = 'bg-secondary';
                                        }
                                    @endphp
                                    
                                    <div class="mt-1">
                                        <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                                    </div>
                                </dd>
                                
                                @if($movement->temporary_reason)
                                    <dt class="mt-2">Raison</dt>
                                    <dd>{{ $movement->temporary_reason }}</dd>
                                @endif
                            @endif
                        @else
                            <dt class="mt-2">Type</dt>
                            <dd><span class="badge bg-secondary">Mouvement permanent</span></dd>
                        @endif
                        
                        @if($movement->assignedTo)
                            <dt class="mt-2">Assigné à</dt>
                            <dd>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user me-2"></i>
                                    {{ $movement->assignedTo->name }}
                                </div>
                                @if($movement->assigned_at)
                                    <small class="text-muted">
                                        Assigné le {{ $movement->assigned_at->format('d/m/Y') }}
                                    </small>
                                @endif
                            </dd>
                        @endif
                        
                        @if($movement->createdBy)
                            <dt class="mt-2">Créé par</dt>
                            <dd>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user-edit me-2"></i>
                                    {{ $movement->createdBy->name }}
                                </div>
                                <small class="text-muted">
                                    Le {{ $movement->created_at->format('d/m/Y à H:i') }}
                                </small>
                            </dd>
                        @endif
                    </dl>
                </div>
            </div>
            
            <!-- Actions rapides -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt me-2"></i> Actions rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($movement->status == 'pending_approval' && auth()->user()->can('approve', $movement))
                            <button type="button" class="btn btn-success mb-2" data-bs-toggle="modal" data-bs-target="#approveModal">
                                <i class="fas fa-check me-1"></i> Approuver
                            </button>
                            <button type="button" class="btn btn-danger mb-2" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i class="fas fa-times me-1"></i> Rejeter
                            </button>
                        @endif
                        
                        @if(($movement->status == 'approved' || $movement->status == 'scheduled') && auth()->user()->can('start', $movement))
                            <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#startModal">
                                <i class="fas fa-play me-1"></i> Démarrer
                            </button>
                        @endif
                        
                        @if($movement->status == 'in_progress' && auth()->user()->can('complete', $movement))
                            <button type="button" class="btn btn-success mb-2" data-bs-toggle="modal" data-bs-target="#completeModal">
                                <i class="fas fa-check-circle me-1"></i> Terminer
                            </button>
                        @endif
                        
                        @if(in_array($movement->status, ['pending_approval', 'approved', 'scheduled', 'in_progress']) && auth()->user()->can('cancel', $movement))
                            <button type="button" class="btn btn-outline-danger mb-2" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                <i class="fas fa-ban me-1"></i> Annuler
                            </button>
                        @endif
                        
                        @can('update', $movement)
                            <a href="{{ route('equipment-movements.edit', $movement) }}" class="btn btn-outline-primary mb-2">
                                <i class="fas fa-edit me-1"></i> Modifier
                            </a>
                        @endcan
                        
                        @can('delete', $movement)
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteMovementModal">
                                <i class="fas fa-trash-alt me-1"></i> Supprimer
                            </button>
                        @endcan
                    </div>
                </div>
            </div>
            
            <!-- Fichiers joints -->
            @if($movement->attachments->count() > 0)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-paperclip me-2"></i> Fichiers joints
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @foreach($movement->attachments as $attachment)
                                <div class="list-group-item px-0 py-2">
                                    <div class="d-flex align-items-center">
                                        @if(in_array(strtolower(pathinfo($attachment->filename, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']))
                                            <img src="{{ asset('storage/' . $attachment->path) }}" 
                                                 class="me-3" 
                                                 style="width: 40px; height: 40px; object-fit: cover; border-radius: 0.25rem;"
                                                 alt="{{ $attachment->original_filename }}">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center me-3" 
                                                 style="width: 40px; height: 40px; border-radius: 0.25rem;">
                                                <i class="fas fa-file text-muted"></i>
                                            </div>
                                        @endif
                                        
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0 text-truncate" style="max-width: 200px;" 
                                                    title="{{ $attachment->original_filename }}">
                                                    {{ $attachment->original_filename }}
                                                </h6>
                                                <span class="badge bg-light text-dark ms-2">
                                                    {{ $attachment->size_for_humans }}
                                                </span>
                                            </div>
                                            <div class="mt-1">
                                                <a href="{{ route('attachments.download', $attachment) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   target="_blank">
                                                    <i class="fas fa-download me-1"></i> Télécharger
                                                </a>
                                                
                                                @can('delete', $attachment)
                                                    <form action="{{ route('attachments.destroy', $attachment) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce fichier ?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-trash-alt me-1"></i> Supprimer
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modals -->
@include('equipment-movements.modals.approve')
@include('equipment-movements.modals.reject')
@include('equipment-movements.modals.start')
@include('equipment-movements.modals.complete')
@include('equipment-movements.modals.cancel')
@include('equipment-movements.modals.delete')

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialiser les tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();
        
        // Gérer l'affichage des champs conditionnels
        function toggleConditionalFields() {
            // Exemple de gestion des champs conditionnels
            // À adapter selon vos besoins spécifiques
        }
        
        // Initialiser
        toggleConditionalFields();
    });
</script>
@endpush
