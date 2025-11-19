@extends('layouts.maquette')

@section('title', 'Modifier la maintenance #' . $maintenance->reference)

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    .form-section {
        background-color: #f8f9fc;
        border-radius: 0.35rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .form-section-title {
        font-size: 1rem;
        font-weight: 600;
        color: #4e73df;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #e3e6f0;
    }
    
    .priority-indicator {
        width: 15px;
        height: 15px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
    }
    
    .priority-high { background-color: #e74a3b; }
    .priority-medium { background-color: #f6c23e; }
    .priority-low { background-color: #1cc88a; }
    
    .attachment-preview {
        width: 100%;
        height: 120px;
        object-fit: cover;
        border-radius: 0.25rem;
        cursor: pointer;
        transition: transform 0.2s;
    }
    
    .attachment-preview:hover {
        transform: scale(1.05);
    }
    
    .equipment-image {
        max-height: 200px;
        object-fit: contain;
    }
    
    .status-badge {
        font-size: 0.8rem;
        padding: 0.35em 0.65em;
    }
    
    .existing-attachment {
        transition: all 0.2s;
    }
    
    .existing-attachment:hover {
        background-color: #f8f9fc;
    }
    
    .delete-attachment {
        position: absolute;
        top: 5px;
        right: 5px;
        opacity: 0;
        transition: opacity 0.2s;
    }
    
    .existing-attachment:hover .delete-attachment {
        opacity: 1;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-edit me-2"></i> Modifier la maintenance
            <span class="badge bg-{{ $maintenance->status === 'completed' ? 'success' : ($maintenance->status === 'cancelled' ? 'secondary' : 'primary') }} ms-2">
                {{ __("maintenance.status.{$maintenance->status}") }}
            </span>
        </h1>
        <div>
            <a href="{{ route('maintenance.show', $maintenance) }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i> Retour
            </a>
            <button type="submit" form="editMaintenanceForm" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Enregistrer les modifications
            </button>
        </div>
    </div>
    
    <form id="editMaintenanceForm" action="{{ route('maintenance.update', $maintenance) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Informations générales -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-info-circle me-2"></i>Informations générales
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="maintenance_type" class="form-label">Type de maintenance <span class="text-danger">*</span></label>
                                    <select class="form-select" id="maintenance_type" name="maintenance_type" required {{ $maintenance->status === 'completed' || $maintenance->status === 'cancelled' ? 'disabled' : '' }}>
                                        <option value="preventive" {{ $maintenance->maintenance_type === 'preventive' ? 'selected' : '' }}>Maintenance préventive</option>
                                        <option value="corrective" {{ $maintenance->maintenance_type === 'corrective' ? 'selected' : '' }}>Maintenance corrective</option>
                                        <option value="inspection" {{ $maintenance->maintenance_type === 'inspection' ? 'selected' : '' }}>Inspection</option>
                                        <option value="calibration" {{ $maintenance->maintenance_type === 'calibration' ? 'selected' : '' }}>Étalonnage</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="priority" class="form-label">Priorité <span class="text-danger">*</span></label>
                                    <select class="form-select" id="priority" name="priority" required>
                                        <option value="high" {{ $maintenance->priority === 'high' ? 'selected' : '' }}>
                                            <span class="priority-indicator priority-high"></span> Haute
                                        </option>
                                        <option value="medium" {{ $maintenance->priority === 'medium' ? 'selected' : '' }}>
                                            <span class="priority-indicator priority-medium"></span> Moyenne
                                        </option>
                                        <option value="low" {{ $maintenance->priority === 'low' ? 'selected' : '' }}>
                                            <span class="priority-indicator priority-low"></span> Basse
                                        </option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="status" class="form-label">Statut <span class="text-danger">*</span></label>
                                    <select class="form-select" id="status" name="status" required {{ $maintenance->status === 'completed' || $maintenance->status === 'cancelled' ? 'disabled' : '' }}>
                                        <option value="scheduled" {{ $maintenance->status === 'scheduled' ? 'selected' : '' }}>Planifiée</option>
                                        <option value="in_progress" {{ $maintenance->status === 'in_progress' ? 'selected' : '' }}>En cours</option>
                                        @if($maintenance->status === 'completed' || $maintenance->status === 'cancelled')
                                            <option value="{{ $maintenance->status }}" selected>
                                                {{ $maintenance->status === 'completed' ? 'Terminée' : 'Annulée' }}
                                            </option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="scheduled_date" class="form-label">Date prévue <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control" id="scheduled_date" name="scheduled_date" 
                                           value="{{ old('scheduled_date', $maintenance->scheduled_date->format('Y-m-d\TH:i')) }}" required
                                           {{ $maintenance->status === 'completed' || $maintenance->status === 'cancelled' ? 'disabled' : '' }}>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="estimated_duration" class="form-label">Durée estimée (minutes)</label>
                                    <input type="number" class="form-control" id="estimated_duration" name="estimated_duration" 
                                           value="{{ old('estimated_duration', $maintenance->estimated_duration) }}" min="1">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="assigned_to" class="form-label">Assigné à</label>
                                    <select class="form-select" id="assigned_to" name="assigned_to" {{ $maintenance->status === 'completed' || $maintenance->status === 'cancelled' ? 'disabled' : '' }}>
                                        <option value="">Non assigné</option>
                                        @foreach($technicians as $technician)
                                            <option value="{{ $technician->id }}" {{ $maintenance->assigned_to == $technician->id ? 'selected' : '' }}>
                                                {{ $technician->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="3" required>{{ old('description', $maintenance->description) }}</textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes supplémentaires</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes', $maintenance->notes) }}</textarea>
                        </div>
                        
                        @if($maintenance->status === 'completed')
                            <div class="mb-3">
                                <label for="completion_notes" class="form-label">Rapport de fin <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="completion_notes" name="completion_notes" rows="3" required>{{ old('completion_notes', $maintenance->completion_notes) }}</textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="actual_duration" class="form-label">Durée réelle (minutes)</label>
                                        <input type="number" class="form-control" id="actual_duration" name="actual_duration" 
                                               value="{{ old('actual_duration', $maintenance->actual_duration) }}" min="1">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="actual_cost" class="form-label">Coût total (€)</label>
                                        <input type="number" class="form-control" id="actual_cost" name="actual_cost" 
                                               value="{{ old('actual_cost', $maintenance->total_cost) }}" min="0" step="0.01">
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        @if($maintenance->status === 'cancelled')
                            <div class="mb-3">
                                <label for="cancellation_reason" class="form-label">Raison de l'annulation <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="cancellation_reason" name="cancellation_reason" rows="3" required>{{ old('cancellation_reason', $maintenance->cancellation_reason) }}</textarea>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Pièces jointes -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-paperclip me-2"></i>Pièces jointes
                        </h6>
                        @if($maintenance->status !== 'completed' && $maintenance->status !== 'cancelled')
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addAttachmentModal">
                                <i class="fas fa-plus me-1"></i> Ajouter
                            </button>
                        @endif
                    </div>
                    <div class="card-body">
                        @if($maintenance->attachments->isEmpty())
                            <div class="text-center py-4">
                                <i class="fas fa-paperclip fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Aucune pièce jointe pour le moment</p>
                            </div>
                        @else
                            <div class="row g-3" id="attachmentsList">
                                @foreach($maintenance->attachments as $attachment)
                                    <div class="col-md-4 col-6">
                                        <div class="card h-100 position-relative existing-attachment">
                                            @if(in_array($attachment->extension, ['jpg', 'jpeg', 'png', 'gif']))
                                                <img src="{{ Storage::url($attachment->path) }}" class="card-img-top attachment-preview" alt="{{ $attachment->original_name }}">
                                            @else
                                                <div class="text-center py-4">
                                                    <i class="fas {{ getFileIconByExtension($attachment->extension) }} fa-3x text-muted mb-2"></i>
                                                    <p class="small text-truncate px-2 mb-0" title="{{ $attachment->original_name }}">
                                                        {{ $attachment->original_name }}
                                                    </p>
                                                </div>
                                            @endif
                                            <div class="card-footer bg-transparent pt-0 border-top-0">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">{{ formatFileSize($attachment->size) }}</small>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('maintenance.attachments.download', $attachment) }}" class="btn btn-sm btn-outline-primary" title="Télécharger">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                        @if($maintenance->status !== 'completed' && $maintenance->status !== 'cancelled')
                                                            <button type="button" class="btn btn-sm btn-outline-danger delete-attachment-btn" 
                                                                    data-attachment-id="{{ $attachment->id }}" 
                                                                    data-attachment-name="{{ $attachment->original_name }}">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Équipement -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-tools me-2"></i>Équipement
                        </h6>
                    </div>
                    <div class="card-body text-center">
                        @if($maintenance->equipment->image)
                            <img src="{{ Storage::url($maintenance->equipment->image) }}" class="img-fluid rounded mb-3 equipment-image" alt="{{ $maintenance->equipment->name }}">
                        @else
                            <div class="bg-light rounded p-4 mb-3">
                                <i class="fas fa-image fa-4x text-muted"></i>
                            </div>
                        @endif
                        
                        <h5>{{ $maintenance->equipment->name }}</h5>
                        <p class="text-muted mb-2">
                            <i class="fas fa-barcode me-1"></i> {{ $maintenance->equipment->serial_number }}
                        </p>
                        
                        <div class="d-flex justify-content-center gap-2 mb-3">
                            <span class="badge bg-{{ $maintenance->equipment->status === 'available' ? 'success' : 'warning' }}">
                                {{ __("equipment.status.{$maintenance->equipment->status}") }}
                            </span>
                            
                            @if($maintenance->equipment->warranty_expiry_date)
                                @php
                                    $warrantyClass = $maintenance->equipment->warranty_expiry_date->isPast() ? 'danger' : 'info';
                                @endphp
                                <span class="badge bg-{{ $warrantyClass }}">
                                    <i class="fas fa-shield-alt me-1"></i>
                                    Garantie jusqu'au {{ $maintenance->equipment->warranty_expiry_date->format('d/m/Y') }}
                                </span>
                            @endif
                        </div>
                        
                        <div class="text-start">
                            <p class="mb-1">
                                <strong>Modèle :</strong> {{ $maintenance->equipment->model ?? 'Non spécifié' }}
                            </p>
                            <p class="mb-1">
                                <strong>Marque :</strong> {{ $maintenance->equipment->brand ?? 'Non spécifiée' }}
                            </p>
                            <p class="mb-1">
                                <strong>Localisation :</strong> {{ $maintenance->equipment->location ? $maintenance->equipment->location->name : 'Non spécifiée' }}
                            </p>
                            @if($maintenance->equipment->department)
                                <p class="mb-0">
                                    <strong>Département :</strong> {{ $maintenance->equipment->department->name }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Coûts -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-euro-sign me-2"></i>Coûts
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="estimated_cost" class="form-label">Coût estimé (€)</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="estimated_cost" name="estimated_cost" 
                                       value="{{ old('estimated_cost', $maintenance->estimated_cost) }}" min="0" step="0.01">
                                <span class="input-group-text">€</span>
                            </div>
                        </div>
                        
                        @if($maintenance->status === 'completed')
                            <div class="mb-3">
                                <label for="labor_cost" class="form-label">Main d'œuvre (€)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="labor_cost" name="labor_cost" 
                                           value="{{ old('labor_cost', $maintenance->labor_cost) }}" min="0" step="0.01">
                                    <span class="input-group-text">€</span>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="other_costs" class="form-label">Autres coûts (€)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="other_costs" name="other_costs" 
                                           value="{{ old('other_costs', $maintenance->other_costs) }}" min="0" step="0.01">
                                    <span class="input-group-text">€</span>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong>Coût total :</strong>
                                <span class="h5 mb-0" id="totalCost">
                                    {{ number_format($maintenance->total_cost, 2, ',', ' ') }} €
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Pièces utilisées -->
                @if($maintenance->parts->isNotEmpty() || $maintenance->status !== 'completed')
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-cogs me-2"></i>Pièces utilisées
                            </h6>
                            @if($maintenance->status !== 'completed' && $maintenance->status !== 'cancelled')
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addPartModal">
                                    <i class="fas fa-plus me-1"></i> Ajouter
                                </button>
                            @endif
                        </div>
                        <div class="card-body">
                            @if($maintenance->parts->isEmpty())
                                <div class="text-center py-3">
                                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">Aucune pièce utilisée</p>
                                </div>
                            @else
                                <div class="list-group list-group-flush">
                                    @foreach($maintenance->parts as $part)
                                        <div class="list-group-item px-0">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1">{{ $part->name }}</h6>
                                                    <p class="small text-muted mb-0">
                                                        {{ $part->quantity }} x {{ number_format($part->unit_price, 2, ',', ' ') }} €
                                                        <span class="mx-1">•</span>
                                                        Total: {{ number_format($part->quantity * $part->unit_price, 2, ',', ' ') }} €
                                                    </p>
                                                    @if($part->reference)
                                                        <p class="small text-muted mb-0">Réf: {{ $part->reference }}</p>
                                                    @endif
                                                </div>
                                                @if($maintenance->status !== 'completed' && $maintenance->status !== 'cancelled')
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" class="btn btn-outline-primary" 
                                                                data-bs-toggle="modal" data-bs-target="#editPartModal{{ $part->id }}">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger" 
                                                                data-bs-toggle="modal" data-bs-target="#deletePartModal{{ $part->id }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <!-- Modal d'édition de pièce -->
                                        <div class="modal fade" id="editPartModal{{ $part->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="{{ route('maintenance.parts.update', [$maintenance, $part]) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Modifier la pièce</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label for="name" class="form-label">Nom</label>
                                                                <input type="text" class="form-control" id="name" name="name" value="{{ $part->name }}" required>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <label for="quantity" class="form-label">Quantité</label>
                                                                    <input type="number" class="form-control" id="quantity" name="quantity" min="1" value="{{ $part->quantity }}" required>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label for="unit_price" class="form-label">Prix unitaire (€)</label>
                                                                    <input type="number" class="form-control" id="unit_price" name="unit_price" min="0" step="0.01" value="{{ $part->unit_price }}" required>
                                                                </div>
                                                            </div>
                                                            <div class="mt-3">
                                                                <label for="reference" class="form-label">Référence</label>
                                                                <input type="text" class="form-control" id="reference" name="reference" value="{{ $part->reference }}">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Modal de suppression de pièce -->
                                        <div class="modal fade" id="deletePartModal{{ $part->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Confirmer la suppression</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Êtes-vous sûr de vouloir supprimer la pièce <strong>{{ $part->name }}</strong> ?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                        <form action="{{ route('maintenance.parts.destroy', [$maintenance, $part]) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Supprimer</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <div class="mt-3 pt-2 border-top">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong>Total des pièces :</strong>
                                        <span>{{ number_format($maintenance->parts->sum(function($part) { return $part->quantity * $part->unit_price; }), 2, ',', ' ') }} €</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- Modal d'ajout de pièce -->
<div class="modal fade" id="addPartModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('maintenance.parts.store', $maintenance) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter une pièce</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="quantity" class="form-label">Quantité <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="quantity" name="quantity" min="1" value="1" required>
                        </div>
                        <div class="col-md-6">
                            <label for="unit_price" class="form-label">Prix unitaire (€) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="unit_price" name="unit_price" min="0" step="0.01" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label for="reference" class="form-label">Référence</label>
                        <input type="text" class="form-control" id="reference" name="reference">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal d'ajout de pièce jointe -->
<div class="modal fade" id="addAttachmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('maintenance.attachments.store', $maintenance) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter des pièces jointes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="attachments" class="form-label">Sélectionner des fichiers</label>
                        <input class="form-control" type="file" id="attachments" name="attachments[]" multiple required>
                        <div class="form-text">Formats acceptés : JPG, PNG, PDF, DOC, XLS. Taille maximale : 5 Mo par fichier.</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="attachment_notes" class="form-label">Notes (optionnel)</label>
                        <textarea class="form-control" id="attachment_notes" name="notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Téléverser</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de suppression de pièce jointe -->
<div class="modal fade" id="deleteAttachmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer la pièce jointe <strong id="attachmentName"></strong> ?
                <form id="deleteAttachmentForm" method="POST" class="mt-3">
                    @csrf
                    @method('DELETE')
                    <div class="form-group">
                        <label for="delete_reason" class="form-label">Raison de la suppression <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="delete_reason" name="delete_reason" rows="2" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" form="deleteAttachmentForm" class="btn btn-danger">Supprimer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialisation de Select2
        $('#assigned_to').select2({
            theme: 'bootstrap-5',
            placeholder: 'Sélectionnez un technicien',
            allowClear: true
        });
        
        // Gestion de la suppression des pièces jointes
        $('.delete-attachment-btn').on('click', function() {
            const attachmentId = $(this).data('attachment-id');
            const attachmentName = $(this).data('attachment-name');
            
            $('#attachmentName').text(attachmentName);
            $('#deleteAttachmentForm').attr('action', `{{ url('maintenance/attachments') }}/${attachmentId}`);
            $('#deleteAttachmentModal').modal('show');
        });
        
        // Calcul du coût total
        function updateTotalCost() {
            let total = 0;
            
            // Ajouter le coût de la main d'œuvre
            const laborCost = parseFloat($('#labor_cost').val()) || 0;
            total += laborCost;
            
            // Ajouter les autres coûts
            const otherCosts = parseFloat($('#other_costs').val()) || 0;
            total += otherCosts;
            
            // Mettre à jour l'affichage
            $('#totalCost').text(total.toFixed(2).replace('.', ',') + ' €');
        }
        
        // Écouter les changements sur les champs de coût
        $('#labor_cost, #other_costs').on('change keyup', updateTotalCost);
        
        // Initialiser le calcul du coût total
        updateTotalCost();
        
        // Gestion de la soumission du formulaire
        $('#editMaintenanceForm').on('submit', function() {
            // Réactiver les champs désactivés avant la soumission
            $('select:disabled, input:disabled').each(function() {
                $(this).prop('disabled', false);
            });
            
            // Afficher un indicateur de chargement
            const submitBtn = $(this).find('button[type="submit"]');
            const originalBtnText = submitBtn.html();
            submitBtn.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Enregistrement...').prop('disabled', true);
            
            // Réinitialiser le bouton en cas d'erreur
            $(window).on('pageshow', function() {
                submitBtn.html(originalBtnText).prop('disabled', false);
            });
            
            return true;
        });
        
        // Aperçu des fichiers avant téléchargement
        $('#attachments').on('change', function() {
            const files = this.files;
            const filePreview = $('#filePreview');
            filePreview.empty();
            
            if (files.length > 0) {
                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        let previewHtml = '';
                        
                        if (file.type.startsWith('image/')) {
                            previewHtml = `
                                <div class="col-6 col-md-4">
                                    <div class="card">
                                        <img src="${e.target.result}" class="card-img-top" alt="${file.name}" style="height: 100px; object-fit: cover;">
                                        <div class="card-body p-2">
                                            <p class="card-text small text-truncate mb-0" title="${file.name}">${file.name}</p>
                                            <small class="text-muted">${formatFileSize(file.size)}</small>
                                        </div>
                                    </div>
                                </div>
                            `;
                        } else {
                            const fileIcon = getFileIcon(file);
                            previewHtml = `
                                <div class="col-6 col-md-4">
                                    <div class="card h-100">
                                        <div class="card-body text-center py-4">
                                            <i class="${fileIcon} fa-3x text-muted mb-2"></i>
                                            <p class="card-text small text-truncate mb-1" title="${file.name}">${file.name}</p>
                                            <small class="text-muted">${formatFileSize(file.size)}</small>
                                        </div>
                                    </div>
                                </div>
                            `;
                        }
                        
                        filePreview.append(previewHtml);
                    };
                    
                    reader.readAsDataURL(file);
                }
            } else {
                filePreview.html('<div class="col-12 text-muted">Aucun fichier sélectionné</div>');
            }
        });
    });
    
    // Fonction pour formater la taille des fichiers
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    // Fonction pour obtenir l'icône appropriée selon le type de fichier
    function getFileIcon(file) {
        const fileType = file.type.split('/')[0];
        const fileExtension = file.name.split('.').pop().toLowerCase();
        
        switch (fileType) {
            case 'image':
                return 'fas fa-file-image';
            case 'application':
                if (fileExtension === 'pdf') return 'fas fa-file-pdf';
                if (['doc', 'docx'].includes(fileExtension)) return 'fas fa-file-word';
                if (['xls', 'xlsx', 'csv'].includes(fileExtension)) return 'fas fa-file-excel';
                if (['ppt', 'pptx'].includes(fileExtension)) return 'fas fa-file-powerpoint';
                if (['zip', 'rar', '7z'].includes(fileExtension)) return 'fas fa-file-archive';
                return 'fas fa-file';
            case 'text':
                return 'fas fa-file-alt';
            case 'video':
                return 'fas fa-file-video';
            case 'audio':
                return 'fas fa-file-audio';
            default:
                return 'fas fa-file';
        }
    }
</script>
@endpush
