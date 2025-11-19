@extends('layouts.maquette')

@section('title', $equipment->name)

@push('styles')
<style>
    .equipment-image {
        max-width: 100%;
        height: auto;
        border-radius: 0.25rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .info-card {
        border-left: 3px solid #4e73df;
        border-radius: 0.25rem;
    }
    
    .nav-tabs .nav-link {
        color: #6c757d;
        font-weight: 500;
    }
    
    .nav-tabs .nav-link.active {
        color: #4e73df;
        font-weight: 600;
    }
    
    .specs-list dt {
        width: 150px;
        color: #6c757d;
    }
    
    .specs-list dd {
        margin-left: 170px;
    }
    
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
        background-color: #e3e6f0;
    }
    
    .timeline-item {
        position: relative;
        padding-bottom: 1.5rem;
        padding-left: 1.5rem;
    }
    
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -1.5rem;
        top: 0.25rem;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background-color: #4e73df;
        z-index: 1;
    }
    
    .timeline-item:last-child {
        padding-bottom: 0;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $equipment->name }}</h1>
        <div>
            <a href="{{ route('equipment.edit', $equipment) }}" class="btn btn-sm btn-primary">
                <i class="fas fa-edit fa-sm"></i> Modifier
            </a>
            <a href="{{ route('equipment.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left fa-sm"></i> Retour à la liste
            </a>
        </div>
    </div>
    
    <div class="row">
        <!-- Colonne de gauche -->
        <div class="col-lg-4">
            <!-- Carte d'image -->
            <div class="card shadow mb-4">
                <div class="card-body text-center">
                    @if($equipment->image_path)
                        <img src="{{ asset('storage/' . $equipment->image_path) }}" 
                             alt="{{ $equipment->name }}" 
                             class="img-fluid equipment-image mb-3"
                             style="max-height: 250px;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center" 
                             style="height: 200px; border-radius: 0.25rem; margin-bottom: 1rem;">
                            <i class="fas fa-image fa-4x text-gray-400"></i>
                        </div>
                    @endif
                    
                    <div class="d-flex justify-content-center gap-2 mb-3">
                        <span class="badge bg-{{ $equipment->status_class }} fs-6">
                            {{ $equipment->status_label }}
                        </span>
                        <span class="badge bg-{{ $equipment->condition_class }} fs-6">
                            {{ $equipment->condition_label }}
                        </span>
                    </div>
                    
                    <div class="d-flex justify-content-center gap-2">
                        @if($equipment->barcode)
                            <button type="button" 
                                    class="btn btn-sm btn-outline-primary"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#barcodeModal">
                                <i class="fas fa-barcode"></i> Code-barres
                            </button>
                        @endif
                        
                        @if($equipment->qr_code)
                            <button type="button" 
                                    class="btn btn-sm btn-outline-secondary"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#qrcodeModal">
                                <i class="fas fa-qrcode"></i> QR Code
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Informations générales -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informations générales</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="mb-0">
                                <div class="mb-2">
                                    <dt>Marque</dt>
                                    <dd>{{ $equipment->brand ?? 'Non spécifiée' }}</dd>
                                </div>
                                
                                <div class="mb-2">
                                    <dt>Modèle</dt>
                                    <dd>{{ $equipment->model ?? 'Non spécifié' }}</dd>
                                </div>
                                
                                <div class="mb-2">
                                    <dt>Type</dt>
                                    <dd>{{ $equipment->type ?? 'Non spécifié' }}</dd>
                                </div>
                                
                                <div class="mb-2">
                                    <dt>Quantité</dt>
                                    <dd>{{ $equipment->quantity }}</dd>
                                </div>
                                
                                <div class="mb-2">
                                    <dt>État</dt>
                                    <dd>
                                        @php
                                            $statusLabels = [
                                                'excellent' => ['label' => 'Excellent', 'class' => 'success'],
                                                'bon' => ['label' => 'Bon', 'class' => 'primary'],
                                                'moyen' => ['label' => 'Moyen', 'class' => 'warning'],
                                                'mauvais' => ['label' => 'Mauvais', 'class' => 'danger'],
                                                'hors_service' => ['label' => 'Hors service', 'class' => 'secondary']
                                            ];
                                            $status = $equipment->status ?? 'bon';
                                            $statusInfo = $statusLabels[$status] ?? ['label' => $status, 'class' => 'secondary'];
                                        @endphp
                                        <span class="badge bg-{{ $statusInfo['class'] }}">
                                            {{ $statusInfo['label'] }}
                                        </span>
                                    </dd>
                                </div>
                                
                                <div class="mb-2">
                                    <dt>Utilisable</dt>
                                    <dd>
                                        @if($equipment->is_usable)
                                            <span class="badge bg-success">Oui</span>
                                        @else
                                            <span class="badge bg-danger">Non</span>
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </div>
                        
                        <div class="col-md-6">
                            <dl class="mb-0">
                                @if($equipment->responsible_person)
                                <div class="mb-2">
                                    <dt>Personne responsable</dt>
                                    <dd>{{ $equipment->responsible_person }}</dd>
                                </div>
                                @endif
                                
                                @if($equipment->maintenance_frequency)
                                <div class="mb-2">
                                    <dt>Fréquence de maintenance</dt>
                                    <dd>{{ $equipment->maintenance_frequency }}</dd>
                                </div>
                                @endif
                                
                                @if($equipment->maintenance_type)
                                <div class="mb-2">
                                    <dt>Type de maintenance</dt>
                                    <dd>{{ $equipment->maintenance_type }}</dd>
                                </div>
                                @endif
                                
                                @if($equipment->maintenance_tasks)
                                <div class="mb-2">
                                    <dt>Tâches de maintenance</dt>
                                    <dd>{{ $equipment->maintenance_tasks }}</dd>
                                </div>
                                @endif
                                
                                @if($equipment->notes)
                                <div class="mb-2">
                                    <dt>Notes</dt>
                                    <dd>{{ $equipment->notes }}</dd>
                                </div>
                                @endif
                                
                                @if($equipment->suggestions)
                                <div class="mb-2">
                                    <dt>Suggestions</dt>
                                    <dd>{{ $equipment->suggestions }}</dd>
                                </div>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Garantie -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Garantie</h6>
                    @if($equipment->warranty_expires)
                        <span class="badge bg-{{ $equipment->warranty_status['expired'] ? 'danger' : 'success' }}">
                            {{ $equipment->warranty_status['expired'] ? 'Expirée' : 'Active' }}
                        </span>
                    @endif
                </div>
                <div class="card-body">
                    @if($equipment->warranty_expires)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Jours restants</span>
                                <span>{{ $equipment->warranty_status['remaining_days'] }} jours</span>
                            </div>
                            <div class="progress">
                                @php
                                    $warrantyProgress = min(100, max(0, 100 - (($equipment->warranty_status['remaining_days'] / 365) * 100)));
                                @endphp
                                <div class="progress-bar bg-{{ $equipment->warranty_status['expired'] ? 'danger' : 'success' }}" 
                                     role="progressbar" 
                                     style="width: {{ $warrantyProgress }}%" 
                                     aria-valuenow="{{ $warrantyProgress }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    {{ round($warrantyProgress) }}%
                                </div>
                            </div>
                        </div>
                        
                        <dl class="mb-0">
                            <div class="mb-2">
                                <dt>Expire le</dt>
                                <dd>{{ $equipment->warranty_expires->format('d/m/Y') }}</dd>
                            </div>
                            
                            @if($equipment->warranty_months)
                                <div class="mb-2">
                                    <dt>Durée</dt>
                                    <dd>{{ $equipment->warranty_months }} mois</dd>
                                </div>
                            @endif
                            
                            @if($equipment->warranty_notes)
                                <div class="mb-0">
                                    <dt>Notes</dt>
                                    <dd class="mb-0">{{ $equipment->warranty_notes }}</dd>
                                </div>
                            @endif
                        </dl>
                    @else
                        <p class="text-muted mb-0">Aucune information de garantie disponible.</p>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Colonne de droite -->
        <div class="col-lg-8">
            <!-- Navigation par onglets -->
            <ul class="nav nav-tabs mb-4" id="equipmentTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" 
                            id="details-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#details" 
                            type="button" 
                            role="tab" 
                            aria-controls="details" 
                            aria-selected="true">
                        Détails
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" 
                            id="location-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#location" 
                            type="button" 
                            role="tab" 
                            aria-controls="location" 
                            aria-selected="false">
                        Localisation
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" 
                            id="maintenance-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#maintenance" 
                            type="button" 
                            role="tab" 
                            aria-controls="maintenance" 
                            aria-selected="false">
                        Maintenance
                        @if($equipment->maintenances_count > 0)
                            <span class="badge bg-primary rounded-pill ms-1">
                                {{ $equipment->maintenances_count }}
                            </span>
                        @endif
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" 
                            id="history-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#history" 
                            type="button" 
                            role="tab" 
                            aria-controls="history" 
                            aria-selected="false">
                        Historique
                        @if($equipment->movements_count > 0)
                            <span class="badge bg-primary rounded-pill ms-1">
                                {{ $equipment->movements_count }}
                            </span>
                        @endif
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" 
                            id="documents-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#documents" 
                            type="button" 
                            role="tab" 
                            aria-controls="documents" 
                            aria-selected="false">
                        Documents
                        @if($equipment->documents_count > 0)
                            <span class="badge bg-primary rounded-pill ms-1">
                                {{ $equipment->documents_count }}
                            </span>
                        @endif
                    </button>
                </li>
            </ul>
            
            <!-- Contenu des onglets -->
            <div class="tab-content" id="equipmentTabsContent">
                <!-- Onglet Détails -->
                <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Description</h6>
                        </div>
                        <div class="card-body">
                            @if($equipment->description)
                                {!! nl2br(e($equipment->description)) !!}
                            @else
                                <p class="text-muted mb-0">Aucune description disponible.</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Spécifications techniques</h6>
                        </div>
                        <div class="card-body">
                            <dl class="row mb-0">
                                <dt class="col-sm-4">Modèle</dt>
                                <dd class="col-sm-8">{{ $equipment->model ?? '-' }}</dd>
                                
                                <dt class="col-sm-4">Fabricant</dt>
                                <dd class="col-sm-8">{{ $equipment->manufacturer ?? '-' }}</dd>
                                
                                <dt class="col-sm-4">N° de série</dt>
                                <dd class="col-sm-8">{{ $equipment->serial_number ?? '-' }}</dd>
                                
                                <dt class="col-sm-4">N° de commande</dt>
                                <dd class="col-sm-8">{{ $equipment->order_number ?? '-' }}</dd>
                                
                                <dt class="col-sm-4">Fournisseur</dt>
                                <dd class="col-sm-8">
                                    @if($equipment->supplier)
                                        {{ $equipment->supplier }}
                                        @if($equipment->supplier_contact)
                                            <div class="text-muted small">{{ $equipment->supplier_contact }}</div>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </dd>
                                
                                <dt class="col-sm-4">Date d'achat</dt>
                                <dd class="col-sm-8">{{ $equipment->purchase_date ? $equipment->purchase_date->format('d/m/Y') : '-' }}</dd>
                                
                                <dt class="col-sm-4">Coût d'achat</dt>
                                <dd class="col-sm-8">{{ $equipment->purchase_cost ? number_format($equipment->purchase_cost, 2, ',', ' ') . ' €' : '-' }}</dd>
                                
                                <dt class="col-sm-4">Valeur actuelle</dt>
                                <dd class="col-sm-8">{{ $equipment->current_value ? number_format($equipment->current_value, 2, ',', ' ') . ' €' : '-' }}</dd>
                                
                                <dt class="col-sm-4">Années d'amortissement</dt>
                                <dd class="col-sm-8">{{ $equipment->depreciation_years ?? '-' }}</dd>
                                
                                <dt class="col-sm-4">Valeur résiduelle</dt>
                                <dd class="col-sm-8">{{ $equipment->residual_value ? number_format($equipment->residual_value, 2, ',', ' ') . ' €' : '-' }}</dd>
                                
                                <dt class="col-sm-4">Âge</dt>
                                <dd class="col-sm-8">
                                    @if($equipment->purchase_date)
                                        {{ $equipment->purchase_date->diffForHumans() }}
                                        ({{ $equipment->purchase_date->format('d/m/Y') }})
                                    @else
                                        -
                                    @endif
                                </dd>
                                
                                <dt class="col-sm-4">Date d'ajout</dt>
                                <dd class="col-sm-8">
                                    {{ $equipment->created_at->format('d/m/Y H:i') }}
                                    <small class="text-muted">({{ $equipment->created_at->diffForHumans() }})</small>
                                </dd>
                                
                                <dt class="col-sm-4">Dernière mise à jour</dt>
                                <dd class="col-sm-8">
                                    {{ $equipment->updated_at->format('d/m/Y H:i') }}
                                    <small class="text-muted">({{ $equipment->updated_at->diffForHumans() }})</small>
                                </dd>
                                
                                <dt class="col-sm-4">Notes</dt>
                                <dd class="col-sm-8">
                                    @if($equipment->notes)
                                        {!! nl2br(e($equipment->notes)) !!}
                                    @else
                                        <span class="text-muted">Aucune note</span>
                                    @endif
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                
                <!-- Onglet Localisation -->
                <div class="tab-pane fade" id="location" role="tabpanel" aria-labelledby="location-tab">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Localisation actuelle</h6>
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#moveEquipmentModal">
                                <i class="fas fa-exchange-alt fa-sm"></i> Déplacer
                            </button>
                        </div>
                        <div class="card-body">
                            @if($equipment->location_id && $equipment->location)
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-circle bg-primary text-white me-3">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0">
                                            <a href="{{ route('locations.show', $equipment->location) }}">
                                                {{ $equipment->location->name }}
                                            </a>
                                        </h5>
                                        @if($equipment->location->address)
                                            <p class="mb-0 text-muted">
                                                <i class="fas fa-map-marker-alt fa-fw"></i> 
                                                {{ $equipment->location->address }}
                                                @if($equipment->location->city || $equipment->location->postal_code)
                                                    , {{ $equipment->location->postal_code }} {{ $equipment->location->city }}
                                                @endif
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                
                                @if($equipment->location->description)
                                    <div class="bg-light p-3 rounded mb-3">
                                        <h6 class="font-weight-bold">Description de l'emplacement</h6>
                                        <p class="mb-0">{{ $equipment->location->description }}</p>
                                    </div>
                                @endif
                                
                                <!-- Carte (à implémenter avec une API comme Google Maps ou Leaflet) -->
                                <div class="bg-light p-3 rounded text-center" style="height: 200px;">
                                    <i class="fas fa-map-marked-alt fa-3x text-gray-400 mb-3"></i>
                                    <p class="text-muted mb-0">Carte d'emplacement (intégration API à prévoir)</p>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-map-marker-alt fa-3x text-gray-400 mb-3"></i>
                                    <p class="text-muted mb-0">Cet équipement n'a pas d'emplacement défini.</p>
                                    <button type="button" class="btn btn-sm btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#moveEquipmentModal">
                                        <i class="fas fa-plus fa-sm"></i> Ajouter un emplacement
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Assignation -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Assignation</h6>
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#assignEquipmentModal">
                                <i class="fas fa-user-plus fa-sm"></i> Assigner
                            </button>
                        </div>
                        <div class="card-body">
                            @if($equipment->assignedTo)
                                <div class="d-flex align-items-center">
                                    <div class="icon-circle bg-success text-white me-3">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0">
                                            <a href="{{ route('users.show', $equipment->assignedTo) }}">
                                                {{ $equipment->assignedTo->name }}
                                            </a>
                                        </h5>
                                        <p class="mb-0 text-muted">
                                            {{ $equipment->assignedTo->email }}
                                            @if($equipment->assignedTo->phone)
                                                <span class="ms-2">
                                                    <i class="fas fa-phone fa-fw"></i> {{ $equipment->assignedTo->phone }}
                                                </span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                
                                @if($equipment->assigned_at)
                                    <div class="mt-3">
                                        <small class="text-muted">
                                            Assigné le {{ $equipment->assigned_at->format('d/m/Y') }}
                                            ({{ $equipment->assigned_at->diffForHumans() }})
                                        </small>
                                    </div>
                                @endif
                                
                                <div class="mt-3">
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#unassignEquipmentModal">
                                        <i class="fas fa-user-minus fa-sm"></i> Retirer l'assignation
                                    </button>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-user-slash fa-3x text-gray-400 mb-3"></i>
                                    <p class="text-muted mb-0">Cet équipement n'est assigné à personne.</p>
                                    <button type="button" class="btn btn-sm btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#assignEquipmentModal">
                                        <i class="fas fa-user-plus fa-sm"></i> Assigner à un utilisateur
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Onglet Maintenance -->
                <div class="tab-pane fade" id="maintenance" role="tabpanel" aria-labelledby="maintenance-tab">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0">Historique de maintenance</h5>
                        <a href="{{ route('maintenance.create', ['equipment_id' => $equipment->id]) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus fa-sm"></i> Nouvelle intervention
                        </a>
                    </div>
                    
                    <div class="card shadow mb-4">
                        <div class="card-body p-0">
                            @if($equipment->maintenances->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($equipment->maintenances as $maintenance)
                                        <a href="{{ route('maintenances.show', $maintenance) }}" 
                                           class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">
                                                    {{ $maintenance->title }}
                                                    <span class="badge bg-{{ $maintenance->status_class }} ms-2">
                                                        {{ $maintenance->status_label }}
                                                    </span>
                                                </h6>
                                                <small>{{ $maintenance->created_at->diffForHumans() }}</small>
                                            </div>
                                            <p class="mb-1">
                                                {{ Str::limit($maintenance->description, 150) }}
                                            </p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small>
                                                    <i class="fas fa-calendar-alt fa-fw"></i> 
                                                    {{ $maintenance->scheduled_date->format('d/m/Y') }}
                                                    
                                                    @if($maintenance->completed_date)
                                                        <span class="ms-2">
                                                            <i class="fas fa-check-circle fa-fw"></i> 
                                                            Terminé le {{ $maintenance->completed_date->format('d/m/Y') }}
                                                        </span>
                                                    @endif
                                                </small>
                                                <small>
                                                    <i class="fas fa-user fa-fw"></i> 
                                                    {{ $maintenance->assignedTo ? $maintenance->assignedTo->name : 'Non assigné' }}
                                                </small>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-tools fa-3x text-gray-400 mb-3"></i>
                                    <p class="text-muted mb-0">Aucune intervention de maintenance enregistrée.</p>
                                    <a href="{{ route('maintenance.create', ['equipment_id' => $equipment->id]) }}" class="btn btn-sm btn-primary mt-3">
                                        <i class="fas fa-plus fa-sm"></i> Planifier une intervention
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Statistiques de maintenance -->
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Interventions (12 mois)</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $equipment->maintenances->count() }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-tools fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Dernière maintenance</div>
                                            @if($equipment->lastMaintenance)
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    {{ $equipment->lastMaintenance->completed_date->format('d/m/Y') }}
                                                </div>
                                                <div class="text-xs text-muted">
                                                    {{ $equipment->lastMaintenance->title }}
                                                </div>
                                            @else
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    Aucune
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Onglet Historique -->
                <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Historique des mouvements</h6>
                        </div>
                        <div class="card-body">
                            @if($equipment->movements->count() > 0)
                                <div class="timeline">
                                    @foreach($equipment->movements->sortByDesc('moved_at') as $movement)
                                        <div class="timeline-item">
                                            <div class="card mb-3">
                                                <div class="card-body p-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <h6 class="mb-0">
                                                            {{ $movement->type_label }}
                                                            @if($movement->type === 'transfer')
                                                                <span class="text-muted">
                                                                    de {{ $movement->fromLocation->name ?? 'Inconnu' }}
                                                                    à {{ $movement->toLocation->name ?? 'Inconnu' }}
                                                                </span>
                                                            @endif
                                                        </h6>
                                                        <small class="text-muted">
                                                            {{ $movement->moved_at->format('d/m/Y H:i') }}
                                                        </small>
                                                    </div>
                                                    
                                                    @if($movement->notes)
                                                        <p class="mb-2">{{ $movement->notes }}</p>
                                                    @endif
                                                    
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <small class="text-muted">
                                                            <i class="fas fa-user fa-fw"></i> 
                                                            {{ $movement->movedBy->name ?? 'Système' }}
                                                        </small>
                                                        @if($movement->assignedTo)
                                                            <small class="text-muted">
                                                                <i class="fas fa-user-tag fa-fw"></i> 
                                                                Assigné à {{ $movement->assignedTo->name }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-history fa-3x text-gray-400 mb-3"></i>
                                    <p class="text-muted mb-0">Aucun mouvement enregistré pour cet équipement.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Onglet Documents -->
                <div class="tab-pane fade" id="documents" role="tabpanel" aria-labelledby="documents-tab">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Documents associés</h6>
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                                <i class="fas fa-upload fa-sm"></i> Ajouter un document
                            </button>
                        </div>
                        <div class="card-body">
                            @if($equipment->documents->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Nom</th>
                                                <th>Type</th>
                                                <th>Taille</th>
                                                <th>Ajouté le</th>
                                                <th>Ajouté par</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($equipment->documents as $document)
                                                <tr>
                                                    <td>
                                                        <i class="fas {{ $document->file_icon }} text-primary me-2"></i>
                                                        {{ $document->name }}
                                                    </td>
                                                    <td>{{ $document->file_type }}</td>
                                                    <td>{{ $document->formatted_size }}</td>
                                                    <td>{{ $document->created_at->format('d/m/Y') }}</td>
                                                    <td>{{ $document->uploadedBy->name ?? 'Système' }}</td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="{{ route('documents.download', $document) }}" 
                                                               class="btn btn-sm btn-outline-primary" 
                                                               title="Télécharger">
                                                                <i class="fas fa-download"></i>
                                                            </a>
                                                            <a href="{{ route('documents.preview', $document) }}" 
                                                               class="btn btn-sm btn-outline-secondary" 
                                                               title="Aperçu"
                                                               target="_blank">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-outline-danger" 
                                                                    title="Supprimer"
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#deleteDocumentModal{{ $document->id }}">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                        
                                                        <!-- Modal de suppression de document -->
                                                        <div class="modal fade" id="deleteDocumentModal{{ $document->id }}" tabindex="-1" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Confirmer la suppression</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p>Êtes-vous sûr de vouloir supprimer le document <strong>{{ $document->name }}</strong> ?</p>
                                                                        <p class="text-danger">Cette action est irréversible.</p>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                                        <form action="{{ route('documents.destroy', $document) }}" method="POST">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit" class="btn btn-danger">
                                                                                <i class="fas fa-trash"></i> Confirmer la suppression
                                                                            </button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-file-alt fa-3x text-gray-400 mb-3"></i>
                                    <p class="text-muted mb-0">Aucun document associé à cet équipement.</p>
                                    <button type="button" class="btn btn-sm btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                                        <i class="fas fa-upload fa-sm"></i> Ajouter un document
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
@include('equipment.modals.barcode')
@include('equipment.modals.qrcode')
@include('equipment.modals.move')
@include('equipment.modals.assign')
@include('equipment.modals.unassign')
@include('equipment.modals.upload-document')

@push('scripts')
<script>
    // Initialisation des tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Gestion des onglets avec persistance dans l'URL
        var hash = window.location.hash;
        if (hash) {
            var triggerEl = document.querySelector(`[data-bs-target="${hash}"]`);
            if (triggerEl) {
                var tab = new bootstrap.Tab(triggerEl);
                tab.show();
            }
        }
        
        // Mise à jour de l'URL lors du changement d'onglet
        var tabEls = document.querySelectorAll('button[data-bs-toggle="tab"]');
        tabEls.forEach(function(tabEl) {
            tabEl.addEventListener('click', function (e) {
                var target = e.target.getAttribute('data-bs-target');
                if (target) {
                    window.location.hash = target;
                }
            });
        });
    });
</script>
@endpush
@endsection
