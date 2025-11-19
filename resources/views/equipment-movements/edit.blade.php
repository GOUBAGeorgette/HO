@extends('layouts.maquette')

@section('title', 'Modifier le mouvement #' . $movement->reference)

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    /* Styles identiques à la vue create.blade.php */
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 38px;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        color: #212529;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    
    .equipment-image {
        height: 120px;
        object-fit: cover;
        border-radius: 0.25rem;
    }
    
    .location-card {
        transition: all 0.2s ease-in-out;
        cursor: pointer;
    }
    
    .location-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
    }
    
    .location-card.active {
        border-color: #0d6efd !important;
        background-color: rgba(13, 110, 253, 0.05);
    }
    
    .nav-tabs .nav-link {
        color: #6c757d;
        font-weight: 500;
    }
    
    .nav-tabs .nav-link.active {
        color: #0d6efd;
        border-bottom: 3px solid #0d6efd;
    }
    
    .nav-tabs .nav-link:not(.active):hover {
        border-bottom: 3px solid #dee2e6;
        margin-bottom: -1px;
    }
    
    .form-section {
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .form-section-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 1.25rem;
        color: #495057;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 0.5rem;
    }
    
    /* Ajoutez ici les autres styles de la vue create.blade.php */
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-edit me-2"></i> Modifier le mouvement #{{ $movement->reference }}
            <span class="badge bg-{{ $movement->status_color }} align-middle ms-2">
                {{ $movement->status_label }}
            </span>
        </h1>
        <div>
            <a href="{{ route('equipment-movements.show', $movement) }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-times me-1"></i> Annuler
            </a>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteMovementModal">
                <i class="fas fa-trash-alt me-1"></i> Supprimer
            </button>
        </div>
    </div>
    
    <!-- Barre de progression -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="step-indicator">
                <div class="step active" id="step-1">
                    <div class="step-number">1</div>
                    <div class="step-label">Équipement</div>
                </div>
                <div class="step" id="step-2">
                    <div class="step-number">2</div>
                    <div class="step-label">Origine & Destination</div>
                </div>
                <div class="step" id="step-3">
                    <div class="step-number">3</div>
                    <div class="step-label">Détails</div>
                </div>
                <div class="step" id="step-4">
                    <div class="step-number">4</div>
                    <div class="step-label">Confirmation</div>
                </div>
            </div>
            <div class="progress">
                <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>
    </div>
    
    <!-- Formulaire de modification -->
    <form id="movementForm" action="{{ route('equipment-movements.update', $movement) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <!-- Section 1: Équipement -->
        <div class="section active" id="section-1">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-box me-2"></i> Équipement à déplacer
                    </h6>
                </div>
                <div class="card-body">
                    <div class="selected-equipment">
                        <div class="d-flex align-items-center">
                            <div class="me-4">
                                @if($movement->equipment->image_url)
                                    <img src="{{ asset('storage/' . $movement->equipment->image_url) }}" alt="{{ $movement->equipment->name }}" class="equipment-image" style="width: 120px;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center" style="width: 120px; height: 120px; border-radius: 0.25rem;">
                                        <i class="fas fa-box fa-3x text-muted"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <h4>{{ $movement->equipment->name }}</h4>
                                <p class="text-muted mb-2">
                                    <i class="fas fa-tag me-1"></i> {{ $movement->equipment->category->name ?? 'Non catégorisé' }}
                                    @if($movement->equipment->model)
                                        <span class="mx-2">•</span>
                                        <i class="fas fa-cube me-1"></i> {{ $movement->equipment->model }}
                                    @endif
                                    @if($movement->equipment->serial_number)
                                        <span class="mx-2">•</span>
                                        <i class="fas fa-barcode me-1"></i> {{ $movement->equipment->serial_number }}
                                    @endif
                                </p>
                                <p class="mb-0">
                                    <span class="badge bg-{{ $movement->equipment->status_color }}">
                                        {{ $movement->equipment->status_label }}
                                    </span>
                                    
                                    @if($movement->equipment->current_location)
                                        <span class="ms-2">
                                            <i class="fas fa-map-marker-alt me-1"></i> {{ $movement->equipment->current_location->name }}
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('equipment.show', $movement->equipment) }}" class="btn btn-outline-primary" target="_blank">
                            <i class="fas fa-external-link-alt me-1"></i> Voir la fiche complète
                        </a>
                    </div>
                    
                    <input type="hidden" name="equipment_id" value="{{ $movement->equipment_id }}">
                </div>
            </div>
            
            <div class="form-navigation">
                <div></div>
                <button type="button" class="btn btn-primary" id="nextToStep2">
                    Suivant <i class="fas fa-arrow-right ms-1"></i>
                </button>
            </div>
        </div>
        
        <!-- Section 2: Origine et destination -->
        <div class="section" id="section-2">
            <div class="row">
                <div class="col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-map-marker-alt me-2"></i> Lieu d'origine
                            </h6>
                        </div>
                        <div class="card-body">
                            @if($movement->origin_location_id)
                                <!-- Afficher l'emplacement d'origine -->
                                <div class="selected-location active">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3">
                                            <i class="fas fa-building fa-2x text-primary"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1">{{ $movement->origin_location->name }}</h5>
                                            <p class="text-muted mb-1">
                                                {{ $movement->origin_location->full_address }}
                                            </p>
                                            @if($movement->origin_location->contact_phone)
                                                <p class="mb-0">
                                                    <i class="fas fa-phone me-1"></i> {{ $movement->origin_location->contact_phone }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    <input type="hidden" name="origin_location_id" value="{{ $movement->origin_location_id }}">
                                </div>
                            @elseif($movement->origin_department)
                                <!-- Afficher le service/personne d'origine -->
                                <div class="selected-location active">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3">
                                            <i class="fas fa-users fa-2x text-primary"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1">{{ $movement->origin_department }}</h5>
                                            @if($movement->origin_contact)
                                                <p class="text-muted mb-0">
                                                    <i class="fas fa-user me-1"></i> {{ $movement->origin_contact }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    <input type="hidden" name="origin_department" value="{{ $movement->origin_department }}">
                                    <input type="hidden" name="origin_contact" value="{{ $movement->origin_contact }}">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-flag-checkered me-2"></i> Destination
                            </h6>
                        </div>
                        <div class="card-body">
                            @if($movement->destination_location_id)
                                <!-- Afficher l'emplacement de destination -->
                                <div class="selected-location active">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3">
                                            <i class="fas fa-building fa-2x text-success"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1">{{ $movement->destination_location->name }}</h5>
                                            <p class="text-muted mb-1">
                                                {{ $movement->destination_location->full_address }}
                                            </p>
                                            @if($movement->destination_location->contact_phone)
                                                <p class="mb-0">
                                                    <i class="fas fa-phone me-1"></i> {{ $movement->destination_location->contact_phone }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    <input type="hidden" name="destination_location_id" value="{{ $movement->destination_location_id }}">
                                </div>
                            @elseif($movement->destination_department)
                                <!-- Afficher le service/personne de destination -->
                                <div class="selected-location active">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3">
                                            <i class="fas fa-users fa-2x text-success"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1">{{ $movement->destination_department }}</h5>
                                            @if($movement->destination_contact)
                                                <p class="text-muted mb-0">
                                                    <i class="fas fa-user me-1"></i> {{ $movement->destination_contact }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    <input type="hidden" name="destination_department" value="{{ $movement->destination_department }}">
                                    <input type="hidden" name="destination_contact" value="{{ $movement->destination_contact }}">
                                </div>
                            @elseif($movement->external_destination)
                                <!-- Afficher la destination externe -->
                                <div class="selected-location active">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3">
                                            <i class="fas fa-truck fa-2x text-warning"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1">{{ $movement->external_destination }}</h5>
                                            @if($movement->external_contact)
                                                <p class="text-muted mb-1">
                                                    <i class="fas fa-user me-1"></i> {{ $movement->external_contact }}
                                                    @if($movement->external_phone)
                                                        <span class="ms-2">
                                                            <i class="fas fa-phone me-1"></i> {{ $movement->external_phone }}
                                                        </span>
                                                    @endif
                                                </p>
                                            @endif
                                            @if($movement->external_address)
                                                <p class="text-muted mb-0">
                                                    <i class="fas fa-map-marker-alt me-1"></i> {{ $movement->external_address }}
                                                </p>
                                            @endif
                                            @if($movement->expected_return_date)
                                                <p class="text-muted mb-0 mt-2">
                                                    <i class="far fa-calendar-alt me-1"></i> 
                                                    Retour prévu: {{ \Carbon\Carbon::parse($movement->expected_return_date)->format('d/m/Y H:i') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    <input type="hidden" name="external_destination" value="{{ $movement->external_destination }}">
                                    <input type="hidden" name="external_contact" value="{{ $movement->external_contact }}">
                                    <input type="hidden" name="external_phone" value="{{ $movement->external_phone }}">
                                    <input type="hidden" name="external_address" value="{{ $movement->external_address }}">
                                    @if($movement->expected_return_date)
                                        <input type="hidden" name="expected_return_date" value="{{ $movement->expected_return_date }}">
                                    @endif
                                </div>
                            @endif
                            
                            <div class="mt-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="isTemporary" name="is_temporary" {{ $movement->is_temporary ? 'checked' : '' }}>
                                    <label class="form-check-label" for="isTemporary">
                                        Déplacement temporaire
                                    </label>
                                </div>
                                
                                <div id="temporaryFields" style="display: {{ $movement->is_temporary ? 'block' : 'none' }}; margin-top: 1rem;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="startDate" class="form-label">Date de début</label>
                                                <input type="datetime-local" class="form-control" id="startDate" name="start_date" 
                                                       value="{{ $movement->start_date ? \Carbon\Carbon::parse($movement->start_date)->format('Y-m-d\TH:i') : '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="endDate" class="form-label">Date de fin</label>
                                                <input type="datetime-local" class="form-control" id="endDate" name="end_date"
                                                       value="{{ $movement->end_date ? \Carbon\Carbon::parse($movement->end_date)->format('Y-m-d\TH:i') : '' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="temporaryReason" class="form-label">Raison du déplacement temporaire</label>
                                        <textarea class="form-control" id="temporaryReason" name="temporary_reason" rows="2">{{ $movement->temporary_reason }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-navigation">
                <button type="button" class="btn btn-outline-secondary" id="backToStep1">
                    <i class="fas fa-arrow-left me-1"></i> Précédent
                </button>
                <button type="button" class="btn btn-primary" id="nextToStep3">
                    Suivant <i class="fas fa-arrow-right ms-1"></i>
                </button>
            </div>
        </div>
        
        <!-- Section 3: Détails du mouvement -->
        <div class="section" id="section-3">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-info-circle me-2"></i> Détails du mouvement
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="movementType" class="form-label">Type de mouvement <span class="text-danger">*</span></label>
                                        <select class="form-select" id="movementType" name="movement_type" required>
                                            <option value="internal" {{ $movement->movement_type == 'internal' ? 'selected' : '' }}>Interne</option>
                                            <option value="external" {{ $movement->movement_type == 'external' ? 'selected' : '' }}>Externe</option>
                                            <option value="loan" {{ $movement->movement_type == 'loan' ? 'selected' : '' }}>Prêt</option>
                                            <option value="maintenance" {{ $movement->movement_type == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                            <option value="repair" {{ $movement->movement_type == 'repair' ? 'selected' : '' }}>Réparation</option>
                                            <option value="inventory" {{ $movement->movement_type == 'inventory' ? 'selected' : '' }}>Inventaire</option>
                                            <option value="other" {{ $movement->movement_type == 'other' ? 'selected' : '' }}>Autre</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="priority" class="form-label">Priorité <span class="text-danger">*</span></label>
                                        <select class="form-select" id="priority" name="priority" required>
                                            <option value="low" {{ $movement->priority == 'low' ? 'selected' : '' }}>Basse</option>
                                            <option value="medium" {{ $movement->priority == 'medium' ? 'selected' : '' }}>Moyenne</option>
                                            <option value="high" {{ $movement->priority == 'high' ? 'selected' : '' }}>Haute</option>
                                            <option value="urgent" {{ $movement->priority == 'urgent' ? 'selected' : '' }}>Urgente</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="scheduledDate" class="form-label">Date et heure prévues <span class="text-danger">*</span></label>
                                        <input type="datetime-local" class="form-control" id="scheduledDate" name="scheduled_date" 
                                               value="{{ \Carbon\Carbon::parse($movement->scheduled_date)->format('Y-m-d\TH:i') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="assignedTo" class="form-label">Assigné à</label>
                                        <select class="form-select" id="assignedTo" name="assigned_to">
                                            <option value="">Non assigné</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ $movement->assigned_to == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="reason" class="form-label">Raison du déplacement <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="reason" name="reason" rows="2" required>{{ $movement->reason }}</textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes supplémentaires</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3">{{ $movement->notes }}</textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="attachments" class="form-label">Pièces jointes</label>
                                <input type="file" class="form-control" id="attachments" name="attachments[]" multiple>
                                <div class="form-text">Vous pouvez sélectionner plusieurs fichiers (max 5 Mo par fichier)</div>
                                
                                @if($movement->attachments->count() > 0)
                                    <div class="mt-3">
                                        <h6>Fichiers joints existants :</h6>
                                        <ul class="list-group">
                                            @foreach($movement->attachments as $attachment)
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <i class="fas fa-paperclip me-2"></i>
                                                        <a href="{{ route('attachments.download', $attachment) }}" target="_blank">
                                                            {{ $attachment->original_filename }}
                                                        </a>
                                                        <span class="text-muted ms-2">({{ $attachment->size_for_humans }})</span>
                                                    </div>
                                                    <div class="form-check
                                                        <input class="form-check-input" type="checkbox" name="delete_attachments[]" value="{{ $attachment->id }}" id="deleteAttachment{{ $attachment->id }}">
                                                        <label class="form-check-label text-danger" for="deleteAttachment{{ $attachment->id }}" title="Supprimer">
                                                            <i class="fas fa-trash"></i>
                                                        </label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="requiresApproval" name="requires_approval" {{ $movement->requires_approval ? 'checked' : '' }}>
                                <label class="form-check-label" for="requiresApproval">
                                    Nécessite une approbation
                                </label>
                            </div>
                            
                            <div id="approvalFields" style="display: {{ $movement->requires_approval ? 'block' : 'none' }}; margin-top: 1rem;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="approverId" class="form-label">Approbateur</label>
                                            <select class="form-select" id="approverId" name="approver_id">
                                                <option value="">Sélectionnez un approbateur</option>
                                                @foreach($approvers as $approver)
                                                    <option value="{{ $approver->id }}" {{ $movement->approver_id == $approver->id ? 'selected' : '' }}>
                                                        {{ $approver->name }} ({{ $approver->role->name }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="requiredByDate" class="form-label">Date limite d'approbation</label>
                                            <input type="datetime-local" class="form-control" id="requiredByDate" name="required_by_date"
                                                   value="{{ $movement->required_by_date ? \Carbon\Carbon::parse($movement->required_by_date)->format('Y-m-d\TH:i') : '' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="approvalNotes" class="form-label">Notes pour l'approbation</label>
                                    <textarea class="form-control" id="approvalNotes" name="approval_notes" rows="2">{{ $movement->approval_notes }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Section pour l'état du mouvement -->
                    @if($movement->status != 'completed' && $movement->status != 'cancelled')
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-tasks me-2"></i> Mise à jour de l'état
                                </h6>
                            </div>
                            <div class="card-body">
                                @if($movement->status == 'pending_approval')
                                    <div class="alert alert-warning">
                                        <i class="fas fa-clock me-2"></i> Ce mouvement est en attente d'approbation.
                                    </div>
                                    
                                    @if(auth()->user()->can('approve', $movement))
                                        <div class="mb-3">
                                            <label class="form-label">Action d'approbation</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="approval_action" id="approveAction" value="approve">
                                                <label class="form-check-label" for="approveAction">
                                                    <i class="fas fa-check-circle text-success me-1"></i> Approuver le mouvement
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="approval_action" id="rejectAction" value="reject">
                                                <label class="form-check-label" for="rejectAction">
                                                    <i class="fas fa-times-circle text-danger me-1"></i> Rejeter la demande
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3" id="approvalCommentContainer" style="display: none;">
                                            <label for="approvalComment" class="form-label">Commentaire</label>
                                            <textarea class="form-control" id="approvalComment" name="approval_comment" rows="2" placeholder="Ajoutez un commentaire (optionnel)"></textarea>
                                        </div>
                                    @endif
                                @elseif($movement->status == 'approved' || $movement->status == 'scheduled')
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i> 
                                        @if($movement->status == 'approved')
                                            Ce mouvement a été approuvé et est prêt à être planifié.
                                        @else
                                            Ce mouvement est planifié pour le {{ \Carbon\Carbon::parse($movement->scheduled_date)->format('d/m/Y à H:i') }}.
                                        @endif
                                    </div>
                                    
                                    @if(auth()->user()->can('start', $movement))
                                        <div class="mb-3">
                                            <label class="form-label">Mettre à jour l'état</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="status_action" id="startAction" value="start">
                                                <label class="form-check-label" for="startAction">
                                                    <i class="fas fa-play-circle text-primary me-1"></i> Démarrer le mouvement
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="status_action" id="cancelAction" value="cancel">
                                                <label class="form-check-label" for="cancelAction">
                                                    <i class="fas fa-ban text-danger me-1"></i> Annuler le mouvement
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3" id="statusCommentContainer" style="display: none;">
                                            <label for="statusComment" class="form-label">Raison</label>
                                            <textarea class="form-control" id="statusComment" name="status_comment" rows="2" placeholder="Pourquoi annulez-vous ce mouvement ?"></textarea>
                                        </div>
                                    @endif
                                @elseif($movement->status == 'in_progress')
                                    <div class="alert alert-primary">
                                        <i class="fas fa-sync-alt me-2"></i> Ce mouvement est en cours depuis le {{ \Carbon\Carbon::parse($movement->started_at)->format('d/m/Y à H:i') }}.
                                    </div>
                                    
                                    @if(auth()->user()->can('complete', $movement))
                                        <div class="mb-3">
                                            <label class="form-label">Mettre à jour l'état</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="status_action" id="completeAction" value="complete">
                                                <label class="form-check-label" for="completeAction">
                                                    <i class="fas fa-check-circle text-success me-1"></i> Marquer comme terminé
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="status_action" id="cancelInProgressAction" value="cancel">
                                                <label class="form-check-label" for="cancelInProgressAction">
                                                    <i class="fas fa-ban text-danger me-1"></i> Annuler le mouvement
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3" id="completionNotesContainer" style="display: none;">
                                            <label for="completionNotes" class="form-label">Notes de fin de mission</label>
                                            <textarea class="form-control" id="completionNotes" name="completion_notes" rows="3" placeholder="Ajoutez des détails sur le déroulement du mouvement"></textarea>
                                        </div>
                                        
                                        <div class="mb-3" id="cancellationReasonContainer" style="display: none;">
                                            <label for="cancellationReason" class="form-label">Raison de l'annulation</label>
                                            <textarea class="form-control" id="cancellationReason" name="cancellation_reason" rows="3" placeholder="Pourquoi annulez-vous ce mouvement ?"></textarea>
                                        </div>
                                        
                                        <div class="mb-3" id="signatureContainer" style="display: none;">
                                            <label class="form-label">Signature</label>
                                            <div class="border rounded p-3 text-center" style="height: 150px; background-color: #f8f9fa;">
                                                <div id="signaturePad">
                                                    <p class="text-muted">Signez ici</p>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="clearSignature">
                                                    <i class="fas fa-eraser me-1"></i> Effacer
                                                </button>
                                            </div>
                                            <input type="hidden" name="signature" id="signatureData">
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
                
                <div class="col-lg-4">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-eye me-2"></i> Aperçu
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="preview-section">
                                <div class="preview-item">
                                    <div class="preview-label">Équipement</div>
                                    <div class="preview-equipment">
                                        <div class="d-flex align-items-center">
                                            @if($movement->equipment->image_url)
                                                <img src="{{ asset('storage/' . $movement->equipment->image_url) }}" alt="{{ $movement->equipment->name }}" class="me-3" style="width: 50px; height: 50px; object-fit: cover; border-radius: 0.25rem;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; border-radius: 0.25rem;">
                                                    <i class="fas fa-box text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-bold">{{ $movement->equipment->name }}</div>
                                                <div class="small text-muted">
                                                    {{ $movement->equipment->category->name ?? 'Non catégorisé' }}
                                                    @if($movement->equipment->model)
                                                        • {{ $movement->equipment->model }}
                                                    @endif
                                                    @if($movement->equipment->serial_number)
                                                        • {{ $movement->equipment->serial_number }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="preview-arrow">
                                    <i class="fas fa-arrow-down"></i>
                                </div>
                                
                                <div class="preview-item">
                                    <div class="preview-label">Origine</div>
                                    <div class="preview-location">
                                        @if($movement->origin_location)
                                            <div class="d-flex align-items-start">
                                                <i class="fas fa-building text-primary me-2 mt-1"></i>
                                                <div>
                                                    <div class="fw-bold">{{ $movement->origin_location->name }}</div>
                                                    <div class="small text-muted">{{ $movement->origin_location->full_address }}</div>
                                                    @if($movement->origin_location->contact_phone)
                                                        <div class="small text-muted"><i class="fas fa-phone me-1"></i> {{ $movement->origin_location->contact_phone }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        @elseif($movement->origin_department)
                                            <div class="d-flex align-items-start">
                                                <i class="fas fa-users text-primary me-2 mt-1"></i>
                                                <div>
                                                    <div class="fw-bold">{{ $movement->origin_department }}</div>
                                                    @if($movement->origin_contact)
                                                        <div class="small text-muted"><i class="fas fa-user me-1"></i> {{ $movement->origin_contact }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <div class="text-muted">Non spécifiée</div>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="preview-arrow">
                                    <i class="fas fa-arrow-down"></i>
                                </div>
                                
                                <div class="preview-item">
                                    <div class="preview-label">Destination</div>
                                    <div class="preview-location">
                                        @if($movement->destination_location)
                                            <div class="d-flex align-items-start">
                                                <i class="fas fa-building text-success me-2 mt-1"></i>
                                                <div>
                                                    <div class="fw-bold">{{ $movement->destination_location->name }}</div>
                                                    <div class="small text-muted">{{ $movement->destination_location->full_address }}</div>
                                                    @if($movement->destination_location->contact_phone)
                                                        <div class="small text-muted"><i class="fas fa-phone me-1"></i> {{ $movement->destination_location->contact_phone }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        @elseif($movement->destination_department)
                                            <div class="d-flex align-items-start">
                                                <i class="fas fa-users text-success me-2 mt-1"></i>
                                                <div>
                                                    <div class="fw-bold">{{ $movement->destination_department }}</div>
                                                    @if($movement->destination_contact)
                                                        <div class="small text-muted"><i class="fas fa-user me-1"></i> {{ $movement->destination_contact }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        @elseif($movement->external_destination)
                                            <div class="d-flex align-items-start">
                                                <i class="fas fa-truck text-warning me-2 mt-1"></i>
                                                <div>
                                                    <div class="fw-bold">{{ $movement->external_destination }}</div>
                                                    @if($movement->external_contact)
                                                        <div class="small text-muted">
                                                            <i class="fas fa-user me-1"></i> {{ $movement->external_contact }}
                                                            @if($movement->external_phone)
                                                                <span class="ms-2"><i class="fas fa-phone me-1"></i> {{ $movement->external_phone }}</span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                    @if($movement->external_address)
                                                        <div class="small text-muted"><i class="fas fa-map-marker-alt me-1"></i> {{ $movement->external_address }}</div>
                                                    @endif
                                                    @if($movement->expected_return_date)
                                                        <div class="small text-muted"><i class="far fa-calendar-alt me-1"></i> Retour prévu: {{ \Carbon\Carbon::parse($movement->expected_return_date)->format('d/m/Y H:i') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <div class="text-muted">Non spécifiée</div>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="preview-item">
                                    <div class="preview-label">Détails</div>
                                    <div>
                                        <div><strong>Type:</strong> {{ ucfirst($movement->movement_type) }}</div>
                                        <div><strong>Priorité:</strong> {{ ucfirst($movement->priority) }}</div>
                                        <div><strong>Date prévue:</strong> {{ $movement->scheduled_date->format('d/m/Y H:i') }}</div>
                                        @if($movement->assigned_to)
                                            <div><strong>Assigné à:</strong> {{ $movement->assignedTo->name }}</div>
                                        @endif
                                        @if($movement->is_temporary)
                                            <div class="mt-2">
                                                <span class="badge bg-info">Temporaire</span>
                                                @if($movement->start_date && $movement->end_date)
                                                    <div class="small text-muted mt-1">
                                                        Du {{ $movement->start_date->format('d/m/Y H:i') }} au {{ $movement->end_date->format('d/m/Y H:i') }}
                                                    </div>
                                                    @if($movement->temporary_reason)
                                                        <div class="small text-muted">
                                                            <i class="fas fa-info-circle me-1"></i> {{ $movement->temporary_reason }}
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                @if($movement->reason || $movement->notes)
                                    <div class="preview-item">
                                        <div class="preview-label">
                                            {{ $movement->reason ? 'Raison' : 'Notes' }}
                                        </div>
                                        <div class="bg-light p-2 rounded">
                                            @if($movement->reason)
                                                <p class="mb-1"><strong>Raison:</strong> {{ $movement->reason }}</p>
                                            @endif
                                            @if($movement->notes)
                                                <p class="mb-0">{!! nl2br(e($movement->notes)) !!}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                
                                @if($movement->status == 'completed' && $movement->completion_notes)
                                    <div class="preview-item">
                                        <div class="preview-label">Notes de fin de mission</div>
                                        <div class="bg-light p-2 rounded">
                                            {!! nl2br(e($movement->completion_notes)) !!}
                                        </div>
                                    </div>
                                @endif
                                
                                @if($movement->status == 'cancelled' && $movement->cancellation_reason)
                                    <div class="preview-item">
                                        <div class="preview-label">Raison de l'annulation</div>
                                        <div class="bg-light p-2 rounded">
                                            {!! nl2br(e($movement->cancellation_reason)) !!}
                                            @if($movement->cancelled_by)
                                                <div class="text-muted small mt-1">
                                                    Annulé par {{ $movement->cancelledBy->name }} le {{ $movement->cancelled_at->format('d/m/Y à H:i') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                
                                @if($movement->requires_approval)
                                    <div class="preview-item">
                                        <div class="preview-label">Approbation</div>
                                        <div class="bg-light p-2 rounded">
                                            @if($movement->status == 'pending_approval')
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-warning me-2">En attente</span>
                                                    <span class="small">
                                                        En attente d'approbation par {{ $movement->approver->name ?? 'un approbateur' }}
                                                        @if($movement->required_by_date)
                                                            avant le {{ \Carbon\Carbon::parse($movement->required_by_date)->format('d/m/Y H:i') }}
                                                        @endif
                                                    </span>
                                                </div>
                                                @if($movement->approval_notes)
                                                    <div class="mt-2 small">
                                                        <strong>Notes:</strong> {!! nl2br(e($movement->approval_notes)) !!}
                                                    </div>
                                                @endif
                                            @elseif($movement->status == 'approved')
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-success me-2">Approuvé</span>
                                                    <span class="small">
                                                        Approuvé par {{ $movement->approvedBy->name ?? 'un approbateur' }} 
                                                        le {{ $movement->approved_at->format('d/m/Y à H:i') }}
                                                    </span>
                                                </div>
                                                @if($movement->approval_comment)
                                                    <div class="mt-2 small">
                                                        <strong>Commentaire:</strong> {!! nl2br(e($movement->approval_comment)) !!}
                                                    </div>
                                                @endif
                                            @elseif($movement->status == 'rejected')
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-danger me-2">Rejeté</span>
                                                    <span class="small">
                                                        Rejeté par {{ $movement->rejectedBy->name ?? 'un approbateur' }} 
                                                        le {{ $movement->rejected_at->format('d/m/Y à H:i') }}
                                                    </span>
                                                </div>
                                                @if($movement->approval_comment)
                                                    <div class="mt-2 small">
                                                        <strong>Raison du rejet:</strong> {!! nl2br(e($movement->approval_comment)) !!}
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="preview-item">
                                    <div class="preview-label">Historique</div>
                                    <div class="timeline">
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
                                        
                                        @if($movement->assigned_at)
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
                                        
                                        @if($movement->started_at)
                                            <div class="timeline-item">
                                                <div class="timeline-marker"></div>
                                                <div class="timeline-content">
                                                    <p class="mb-0">
                                                        <strong>Démarrage</strong> du mouvement
                                                        <br>
                                                        <small class="text-muted">{{ $movement->started_at->format('d/m/Y H:i') }}</small>
                                                    </p>
                                                    @if($movement->start_notes)
                                                        <div class="alert alert-light p-2 mt-1 mb-0 small">
                                                            {!! nl2br(e($movement->start_notes)) !!}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                        
                                        @if($movement->completed_at)
                                            <div class="timeline-item">
                                                <div class="timeline-marker"></div>
                                                <div class="timeline-content">
                                                    <p class="mb-0">
                                                        <strong>Terminé</strong> le {{ $movement->completed_at->format('d/m/Y à H:i') }}
                                                        @if($movement->completedBy)
                                                            par {{ $movement->completedBy->name }}
                                                        @endif
                                                    </p>
                                                    @if($movement->completion_notes)
                                                        <div class="alert alert-light p-2 mt-1 mb-0 small">
                                                            {!! nl2br(e($movement->completion_notes)) !!}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                        
                                        @if($movement->cancelled_at)
                                            <div class="timeline-item">
                                                <div class="timeline-marker bg-danger"></div>
                                                <div class="timeline-content">
                                                    <p class="mb-0">
                                                        <strong>Annulé</strong> le {{ $movement->cancelled_at->format('d/m/Y à H:i') }}
                                                        @if($movement->cancelledBy)
                                                            par {{ $movement->cancelledBy->name }}
                                                        @endif
                                                    </p>
                                                    @if($movement->cancellation_reason)
                                                        <div class="alert alert-light p-2 mt-1 mb-0 small">
                                                            <strong>Raison:</strong> {!! nl2br(e($movement->cancellation_reason)) !!}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-navigation">
                <button type="button" class="btn btn-outline-secondary" id="backToStep2">
                    <i class="fas fa-arrow-left me-1"></i> Précédent
                </button>
                <button type="submit" class="btn btn-primary" id="saveMovement">
                    <i class="fas fa-save me-1"></i> Enregistrer les modifications
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Modal de suppression -->
<div class="modal fade" id="deleteMovementModal" tabindex="-1" aria-labelledby="deleteMovementModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteMovementModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer ce mouvement ? Cette action est irréversible.</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    La suppression du mouvement ne supprimera pas l'équipement associé, mais l'historique de ce mouvement sera perdu.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('equipment-movements.destroy', $movement) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-1"></i> Supprimer définitivement
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- Signature Pad -->
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialiser Select2
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Sélectionnez une option',
            allowClear: true
        });
        
        // Gestion des étapes du formulaire
        let currentStep = 1;
        const totalSteps = 3; // Moins d'étapes que dans le formulaire de création
        
        // Mettre à jour la barre de progression
        function updateProgress() {
            // Mettre à jour les étapes actives
            $('.step').removeClass('active completed');
            
            for (let i = 1; i <= 4; i++) {
                if (i < currentStep) {
                    $(`#step-${i}`).addClass('completed');
                } else if (i === currentStep) {
                    $(`#step-${i}`).addClass('active');
                }
            }
            
            // Mettre à jour la barre de progression
            const progress = ((currentStep - 1) / 3) * 100; // Basé sur 3 étapes principales
            $('.progress-bar').css('width', progress + '%').attr('aria-valuenow', progress);
            
            // Afficher/masquer les sections
            $('.section').removeClass('active');
            $(`#section-${currentStep}`).addClass('active');
            
            // Faire défiler vers le haut de la page
            window.scrollTo(0, 0);
        }
        
        // Navigation entre les étapes
        $('#nextToStep2').on('click', function() {
            currentStep = 2;
            updateProgress();
        });
        
        $('#nextToStep3').on('click', function() {
            currentStep = 3;
            updateProgress();
        });
        
        $('#backToStep1').on('click', function() {
            currentStep = 1;
            updateProgress();
        });
        
        $('#backToStep2').on('click', function() {
            currentStep = 2;
            updateProgress();
        });
        
        // Gestion des champs conditionnels
        $('#isTemporary').on('change', function() {
            if ($(this).is(':checked')) {
                $('#temporaryFields').slideDown();
            } else {
                $('#temporaryFields').slideUp();
            }
        });
        
        $('#requiresApproval').on('change', function() {
            if ($(this).is(':checked')) {
                $('#approvalFields').slideDown();
            } else {
                $('#approvalFields').slideUp();
            }
        });
        
        // Gestion des actions d'approbation
        $('input[name="approval_action"]').on('change', function() {
            $('#approvalCommentContainer').slideDown();
        });
        
        // Gestion des actions de statut
        $('input[name="status_action"]').on('change', function() {
            const value = $(this).val();
            
            if (value === 'complete') {
                $('#completionNotesContainer').slideDown();
                $('#cancellationReasonContainer').slideUp();
                $('#signatureContainer').slideDown();
                initializeSignaturePad();
            } else if (value === 'cancel') {
                $('#completionNotesContainer').slideUp();
                $('#cancellationReasonContainer').slideDown();
                $('#signatureContainer').slideUp();
            } else {
                $('#completionNotesContainer').slideUp();
                $('#cancellationReasonContainer').slideUp();
                $('#signatureContainer').slideUp();
            }
        });
        
        // Initialiser le pad de signature
        let signaturePad;
        
        function initializeSignaturePad() {
            const canvas = document.getElementById('signaturePad');
            
            // Vider le conteneur et ajouter un nouveau canvas
            $(canvas).empty();
            $(canvas).html('<canvas id="signatureCanvas" style="border: 1px solid #ddd; width: 100%; height: 150px;"></canvas>');
            
            const signatureCanvas = document.getElementById('signatureCanvas');
            signaturePad = new SignaturePad(signatureCanvas);
            
            // Redimensionner le canvas pour qu'il s'adapte à son conteneur
            function resizeCanvas() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                signatureCanvas.width = signatureCanvas.offsetWidth * ratio;
                signatureCanvas.height = signatureCanvas.offsetHeight * ratio;
                signatureCanvas.getContext("2d").scale(ratio, ratio);
                signaturePad.clear(); // Effacer le contenu précédent après le redimensionnement
            }
            
            window.addEventListener("resize", resizeCanvas);
            resizeCanvas();
            
            // Gérer le bouton d'effacement
            $('#clearSignature').on('click', function() {
                signaturePad.clear();
            });
        }
        
        // Gestion de la soumission du formulaire
        $('#movementForm').on('submit', function(e) {
            // Si le pad de signature est visible, ajouter la signature aux données du formulaire
            if ($('#signatureContainer').is(':visible') && signaturePad) {
                if (signaturePad.isEmpty()) {
                    e.preventDefault();
                    alert('Veuillez signer pour confirmer la fin du mouvement.');
                    return false;
                } else {
                    $('#signatureData').val(signaturePad.toDataURL());
                }
            }
            
            // Afficher un indicateur de chargement
            const submitButton = $(this).find('button[type="submit"]');
            const originalText = submitButton.html();
            submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Enregistrement...');
        });
        
        // Initialiser la vue
        updateProgress();
    });
</script>
@endpush
@endsection
