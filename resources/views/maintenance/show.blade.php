@extends('layouts.maquette')

@section('title', 'Détails de la maintenance #' . $maintenance->reference)

@push('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 1.5rem;
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
        padding-bottom: 1.5rem;
        padding-left: 2rem;
    }
    
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -0.5rem;
        top: 0.25rem;
        width: 1rem;
        height: 1rem;
        border-radius: 50%;
        background-color: #0d6efd;
    }
    
    .timeline-item.completed::before {
        background-color: #198754;
    }
    
    .timeline-item.cancelled::before {
        background-color: #6c757d;
    }
    
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
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-tools me-2"></i> Détails de la maintenance
            <span class="badge bg-{{ $maintenance->status === 'completed' ? 'success' : ($maintenance->status === 'cancelled' ? 'secondary' : 'primary') }} ms-2">
                {{ __("maintenance.status.{$maintenance->status}") }}
            </span>
        </h1>
        <div>
            <a href="{{ route('maintenance.index') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i> Retour
            </a>
            
            @can('update', $maintenance)
                <a href="{{ route('maintenance.edit', $maintenance) }}" class="btn btn-primary me-2">
                    <i class="fas fa-edit me-1"></i> Modifier
                </a>
            @endcan
            
            @if($maintenance->status === 'scheduled')
                @can('start', $maintenance)
                    <form action="{{ route('maintenance.start', $maintenance) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-warning me-2">
                            <i class="fas fa-play me-1"></i> Démarrer
                        </button>
                    </form>
                @endcan
            @elseif($maintenance->status === 'in_progress')
                @can('complete', $maintenance)
                    <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#completeModal">
                        <i class="fas fa-check-circle me-1"></i> Terminer
                    </button>
                @endcan
            @endif
            
            @if(in_array($maintenance->status, ['scheduled', 'in_progress']))
                @can('cancel', $maintenance)
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                        <i class="fas fa-times-circle me-1"></i> Annuler
                    </button>
                @endcan
            @endif
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-8">
            <!-- Détails de la maintenance -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i>Informations générales
                    </h6>
                    <span class="badge bg-{{ $maintenance->priority === 'high' ? 'danger' : ($maintenance->priority === 'medium' ? 'warning' : 'success') }}">
                        {{ __("maintenance.priorities.{$maintenance->priority}") }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Référence</h5>
                            <p class="text-muted">{{ $maintenance->reference }}</p>
                            
                            <h5>Type de maintenance</h5>
                            <p class="text-muted">{{ __("maintenance.types.{$maintenance->maintenance_type}") }}</p>
                            
                            <h5>Date de planification</h5>
                            <p class="text-muted">{{ $maintenance->scheduled_date->format('d/m/Y H:i') }}</p>
                            
                            <h5>Date de début</h5>
                            <p class="text-muted">
                                {{ $maintenance->started_at ? $maintenance->started_at->format('d/m/Y H:i') : 'Non commencée' }}
                            </p>
                            
                            <h5>Date de fin</h5>
                            <p class="text-muted">
                                {{ $maintenance->completed_at ? $maintenance->completed_at->format('d/m/Y H:i') : 'Non terminée' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h5>Équipement</h5>
                            <div class="d-flex align-items-center mb-3">
                                @if($maintenance->equipment->image)
                                    <img src="{{ Storage::url($maintenance->equipment->image) }}" 
                                         class="img-thumbnail equipment-image me-3" 
                                         alt="{{ $maintenance->equipment->name }}">
                                @endif
                                <div>
                                    <a href="{{ route('equipment.show', $maintenance->equipment) }}" class="h5 mb-1">
                                        {{ $maintenance->equipment->name }}
                                    </a>
                                    <div class="text-muted small">
                                        {{ $maintenance->equipment->model }} - {{ $maintenance->equipment->serial_number }}
                                    </div>
                                    <div class="mt-1">
                                        <span class="badge bg-{{ $maintenance->equipment->status === 'available' ? 'success' : 'warning' }}">
                                            {{ __("equipment.status.{$maintenance->equipment->status}") }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <h5>Localisation</h5>
                            <p class="text-muted">
                                {{ $maintenance->equipment->location ? $maintenance->equipment->location->name : 'Non spécifiée' }}
                                @if($maintenance->equipment->department)
                                    <br>
                                    <small class="text-muted">{{ $maintenance->equipment->department->name }}</small>
                                @endif
                            </p>
                            
                            <h5>Responsable</h5>
                            <p class="text-muted">
                                @if($maintenance->assignedTo)
                                    {{ $maintenance->assignedTo->name }}
                                    <br>
                                    <small class="text-muted">{{ $maintenance->assignedTo->email }}</small>
                                    @if($maintenance->assignedTo->phone)
                                        <br>
                                        <small class="text-muted">{{ $maintenance->assignedTo->phone }}</small>
                                    @endif
                                @else
                                    Non assigné
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h5>Description</h5>
                        <div class="border rounded p-3 bg-light">
                            {!! nl2br(e($maintenance->description)) !!}
                        </div>
                    </div>
                    
                    @if($maintenance->notes)
                        <div class="mt-4">
                            <h5>Notes supplémentaires</h5>
                            <div class="border rounded p-3 bg-light">
                                {!! nl2br(e($maintenance->notes)) !!}
                            </div>
                        </div>
                    @endif
                    
                    @if($maintenance->completion_notes)
                        <div class="mt-4">
                            <h5>Rapport de fin</h5>
                            <div class="border rounded p-3 bg-light">
                                {!! nl2br(e($maintenance->completion_notes)) !!}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Pièces jointes -->
            @if($maintenance->attachments->isNotEmpty())
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-paperclip me-2"></i>Pièces jointes
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($maintenance->attachments as $attachment)
                                <div class="col-md-3 mb-3">
                                    <div class="card h-100">
                                        @if(in_array($attachment->extension, ['jpg', 'jpeg', 'png', 'gif']))
                                            <img src="{{ Storage::url($attachment->path) }}" 
                                                 class="card-img-top attachment-preview" 
                                                 alt="{{ $attachment->original_name }}"
                                                 data-bs-toggle="modal" 
                                                 data-bs-target="#imageModal"
                                                 data-img-src="{{ Storage::url($attachment->path) }}"
                                                 data-img-title="{{ $attachment->original_name }}">
                                        @else
                                            <div class="text-center py-4">
                                                <i class="fas fa-file fa-3x text-muted mb-2"></i>
                                                <p class="mb-0 small text-truncate px-2">{{ $attachment->original_name }}</p>
                                            </div>
                                        @endif
                                        <div class="card-footer bg-transparent border-top-0">
                                            <div class="btn-group w-100">
                                                <a href="{{ route('maintenance.attachments.download', $attachment) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="Télécharger">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                @can('delete', $maintenance)
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-danger" 
                                                            title="Supprimer"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#deleteAttachmentModal{{ $attachment->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Modal de confirmation de suppression -->
                                    @can('delete', $maintenance)
                                        <div class="modal fade" id="deleteAttachmentModal{{ $attachment->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Confirmer la suppression</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Êtes-vous sûr de vouloir supprimer le fichier <strong>{{ $attachment->original_name }}</strong> ?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                        <form action="{{ route('maintenance.attachments.destroy', $attachment) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Supprimer</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endcan
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Historique -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history me-2"></i>Historique
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach($maintenance->history as $history)
                            <div class="timeline-item {{ $history->event === 'completed' ? 'completed' : ($history->event === 'cancelled' ? 'cancelled' : '') }}">
                                <h6 class="mb-1">
                                    {{ $history->getEventLabel() }}
                                    <small class="text-muted ms-2">{{ $history->created_at->diffForHumans() }}</small>
                                </h6>
                                <p class="mb-1">
                                    <small class="text-muted">
                                        Par {{ $history->user->name }}
                                    </small>
                                </p>
                                @if($history->notes)
                                    <div class="alert alert-light p-2 small">
                                        {!! nl2br(e($history->notes)) !!}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                        
                        <div class="timeline-item">
                            <h6 class="mb-1">
                                Demande de maintenance créée
                                <small class="text-muted ms-2">{{ $maintenance->created_at->diffForHumans() }}</small>
                            </h6>
                            <p class="mb-0">
                                <small class="text-muted">
                                    Par {{ $maintenance->createdBy->name ?? 'Système' }}
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Actions rapides -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt me-2"></i>Actions rapides
                    </h6>
                </div>
                <div class="card-body">
                    @if($maintenance->status === 'scheduled')
                        @can('start', $maintenance)
                            <form action="{{ route('maintenance.start', $maintenance) }}" method="POST" class="mb-3">
                                @csrf
                                <button type="submit" class="btn btn-warning w-100 mb-2">
                                    <i class="fas fa-play me-1"></i> Démarrer la maintenance
                                </button>
                            </form>
                        @endcan
                    @elseif($maintenance->status === 'in_progress')
                        @can('complete', $maintenance)
                            <button type="button" 
                                    class="btn btn-success w-100 mb-2" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#completeModal">
                                <i class="fas fa-check-circle me-1"></i> Terminer la maintenance
                            </button>
                        @endcan
                    @endif
                    
                    @if(in_array($maintenance->status, ['scheduled', 'in_progress']))
                        @can('cancel', $maintenance)
                            <button type="button" 
                                    class="btn btn-outline-danger w-100" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#cancelModal">
                                <i class="fas fa-times-circle me-1"></i> Annuler la maintenance
                            </button>
                        @endcan
                    @endif
                    
                    <hr>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('maintenance.report', $maintenance) }}" 
                           class="btn btn-outline-primary" 
                           target="_blank">
                            <i class="fas fa-file-pdf me-1"></i> Générer un rapport
                        </a>
                        
                        @can('update', $maintenance)
                            <a href="{{ route('maintenance.edit', $maintenance) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-edit me-1"></i> Modifier la maintenance
                            </a>
                        @endcan
                        
                        @can('delete', $maintenance)
                            <button type="button" 
                                    class="btn btn-outline-danger" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#deleteModal">
                                <i class="fas fa-trash me-1"></i> Supprimer la maintenance
                            </button>
                        @endcan
                    </div>
                </div>
            </div>
            
            <!-- Statistiques -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar me-2"></i>Statistiques
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Progression</span>
                            <span>{{ $maintenance->progress }}%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-{{ $maintenance->progress < 30 ? 'danger' : ($maintenance->progress < 70 ? 'warning' : 'success') }}" 
                                 role="progressbar" 
                                 style="width: {{ $maintenance->progress }}%" 
                                 aria-valuenow="{{ $maintenance->progress }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                    
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Durée prévue
                            <span>{{ $maintenance->estimated_duration ? $maintenance->estimated_duration . ' minutes' : 'Non spécifiée' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Durée réelle
                            <span>
                                @if($maintenance->started_at && $maintenance->completed_at)
                                    {{ $maintenance->started_at->diffInMinutes($maintenance->completed_at) }} minutes
                                @else
                                    Non disponible
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Coût estimé
                            <span>{{ $maintenance->estimated_cost ? number_format($maintenance->estimated_cost, 2, ',', ' ') . ' €' : 'Non spécifié' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Coût réel
                            <span>{{ $maintenance->actual_cost ? number_format($maintenance->actual_cost, 2, ',', ' ') . ' €' : 'Non spécifié' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Pièces utilisées -->
            @if($maintenance->parts->isNotEmpty())
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-cogs me-2"></i>Pièces utilisées
                        </h6>
                        @can('update', $maintenance)
                            <button type="button" 
                                    class="btn btn-sm btn-outline-primary" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#addPartModal">
                                <i class="fas fa-plus"></i>
                            </button>
                        @endcan
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach($maintenance->parts as $part)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $part->name }}</h6>
                                            <p class="mb-0 small text-muted">
                                                {{ $part->quantity }} x {{ number_format($part->unit_price, 2, ',', ' ') }} €
                                                <span class="mx-1">•</span>
                                                Total: {{ number_format($part->quantity * $part->unit_price, 2, ',', ' ') }} €
                                            </p>
                                            @if($part->reference)
                                                <p class="mb-0 small text-muted">Réf: {{ $part->reference }}</p>
                                            @endif
                                        </div>
                                        @can('update', $maintenance)
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" 
                                                        class="btn btn-outline-primary" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editPartModal{{ $part->id }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-outline-danger" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#deletePartModal{{ $part->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
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
                                        @endcan
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="card-footer bg-transparent
                    ">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong>Total des pièces :</strong>
                            <span>{{ number_format($maintenance->parts->sum(function($part) { return $part->quantity * $part->unit_price; }), 2, ',', ' ') }} €</span>
                        </div>
                        
                        @if($maintenance->labor_cost)
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <strong>Main d'œuvre :</strong>
                                <span>{{ number_format($maintenance->labor_cost, 2, ',', ' ') }} €</span>
                            </div>
                        @endif
                        
                        @if($maintenance->other_costs)
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <strong>Autres coûts :</strong>
                                <span>{{ number_format($maintenance->other_costs, 2, ',', ' ') }} €</span>
                            </div>
                        @endif
                        
                        <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                            <strong>Coût total :</strong>
                            <span class="h5 mb-0">{{ number_format($maintenance->total_cost, 2, ',', ' ') }} €</span>
                        </div>
                    </div>
                </div>
            @else
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-cogs me-2"></i>Pièces utilisées
                        </h6>
                        @can('update', $maintenance)
                            <button type="button" 
                                    class="btn btn-sm btn-outline-primary" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#addPartModal">
                                <i class="fas fa-plus"></i>
                            </button>
                        @endcan
                    </div>
                    <div class="card-body text-center py-5">
                        <i class="fas fa-tools fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-0">Aucune pièce utilisée pour cette maintenance</p>
                        @can('update', $maintenance)
                            <button type="button" 
                                    class="btn btn-primary mt-3" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#addPartModal">
                                <i class="fas fa-plus me-1"></i> Ajouter une pièce
                            </button>
                        @endcan
                    </div>
                </div>
            @endif
            
            <!-- Modal d'ajout de pièce -->
            @can('update', $maintenance)
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
            @endcan
        </div>
    </div>
</div>

<!-- Modal de complétion -->
@can('complete', $maintenance)
    <div class="modal fade" id="completeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('maintenance.complete', $maintenance) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Terminer la maintenance</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="completion_notes" class="form-label">Rapport de fin <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="completion_notes" name="completion_notes" rows="5" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="actual_duration" class="form-label">Durée réelle (minutes)</label>
                            <input type="number" class="form-control" id="actual_duration" name="actual_duration" min="1" value="{{ $maintenance->estimated_duration }}">
                        </div>
                        
                        <div class="mb-3">
                            <label for="labor_cost" class="form-label">Coût de la main d'œuvre (€)</label>
                            <input type="number" class="form-control" id="labor_cost" name="labor_cost" min="0" step="0.01">
                        </div>
                        
                        <div class="mb-3">
                            <label for="other_costs" class="form-label">Autres coûts (€)</label>
                            <input type="number" class="form-control" id="other_costs" name="other_costs" min="0" step="0.01">
                        </div>
                        
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="equipment_available" name="equipment_available" value="1" checked>
                            <label class="form-check-label" for="equipment_available">
                                L'équipement est de nouveau disponible
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success">Confirmer la fin de la maintenance</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endcan

<!-- Modal d'annulation -->
@can('cancel', $maintenance)
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('maintenance.cancel', $maintenance) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Annuler la maintenance</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="cancellation_reason" class="form-label">Raison de l'annulation <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="cancellation_reason" name="cancellation_reason" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-danger">Confirmer l'annulation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endcan

<!-- Modal de suppression -->
@can('delete', $maintenance)
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('maintenance.destroy', $maintenance) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmer la suppression</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir supprimer cette maintenance ? Cette action est irréversible.</p>
                        <div class="form-group">
                            <label for="delete_reason" class="form-label">Raison de la suppression <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="delete_reason" name="delete_reason" rows="2" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endcan

<!-- Modal d'affichage d'image -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body text-center">
                <img src="" class="img-fluid" id="modalImage">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Gestion du modal d'affichage d'image
    document.addEventListener('DOMContentLoaded', function() {
        const imageModal = document.getElementById('imageModal');
        if (imageModal) {
            imageModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const imgSrc = button.getAttribute('data-img-src');
                const imgTitle = button.getAttribute('data-img-title');
                
                const modalTitle = imageModal.querySelector('.modal-title');
                const modalImage = imageModal.querySelector('#modalImage');
                
                modalTitle.textContent = imgTitle;
                modalImage.src = imgSrc;
                modalImage.alt = imgTitle;
            });
        }
        
        // Initialisation des tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
