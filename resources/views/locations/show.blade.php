@extends('layouts.maquette')

@section('title', 'Détails de l\'emplacement : ' . $location->name)

@push('styles')
<style>
    .location-header {
        position: relative;
        padding: 2rem;
        border-radius: 0.5rem;
        margin-bottom: 2rem;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-left: 5px solid var(--bs-primary);
    }
    
    .location-icon {
        font-size: 3rem;
        color: var(--bs-primary);
        margin-bottom: 1rem;
    }
    
    .stat-card {
        transition: transform 0.2s, box-shadow 0.2s;
        height: 100%;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    
    .stat-icon {
        font-size: 2rem;
        opacity: 0.8;
    }
    
    .equipment-thumbnail {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 0.25rem;
    }
    
    .nav-tabs .nav-link {
        font-weight: 500;
        color: #6c757d;
        border: none;
        padding: 0.75rem 1.25rem;
        border-bottom: 3px solid transparent;
    }
    
    .nav-tabs .nav-link.active {
        color: var(--bs-primary);
        background: none;
        border-bottom: 3px solid var(--bs-primary);
    }
    
    .nav-tabs .nav-link:hover:not(.active) {
        border-bottom: 3px solid #dee2e6;
    }
    
    .qr-code {
        max-width: 200px;
        margin: 0 auto;
        padding: 1rem;
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .timeline {
        position: relative;
        padding-left: 1.5rem;
        margin-left: 1rem;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 2px;
        background-color: #e9ecef;
    }
    
    .timeline-item {
        position: relative;
        padding-bottom: 1.5rem;
        padding-left: 1.5rem;
    }
    
    .timeline-item:last-child {
        padding-bottom: 0;
    }
    
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -0.5rem;
        top: 0.25rem;
        width: 1rem;
        height: 1rem;
        border-radius: 50%;
        background-color: var(--bs-primary);
        border: 3px solid white;
    }
    
    .timeline-item .timeline-date {
        font-size: 0.75rem;
        color: #6c757d;
    }
    
    .timeline-item .timeline-content {
        background: #f8f9fa;
        padding: 0.75rem;
        border-radius: 0.25rem;
        font-size: 0.9rem;
    }
    
    .gallery-item {
        position: relative;
        margin-bottom: 1.5rem;
        overflow: hidden;
        border-radius: 0.5rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .gallery-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .gallery-item img {
        width: 100%;
        height: 150px;
        object-fit: cover;
    }
    
    .gallery-item .gallery-caption {
        padding: 0.75rem;
        background: white;
    }
    
    .gallery-item .gallery-actions {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        opacity: 0;
        transition: opacity 0.2s;
    }
    
    .gallery-item:hover .gallery-actions {
        opacity: 1;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('locations.index') }}">Emplacements</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $location->name }}</li>
            </ol>
        </nav>
        <div>
            <a href="{{ route('locations.edit', $location) }}" class="btn btn-primary me-2">
                <i class="fas fa-edit me-1"></i> Modifier
            </a>
            <a href="{{ route('locations.create', ['parent_id' => $location->id]) }}" class="btn btn-success me-2">
                <i class="fas fa-plus me-1"></i> Ajouter un sous-emplacement
            </a>
            <a href="{{ route('equipment.create', ['location_id' => $location->id]) }}" class="btn btn-info text-white">
                <i class="fas fa-laptop me-1"></i> Ajouter un équipement
            </a>
        </div>
    </div>
    
    <!-- En-tête de l'emplacement -->
    <div class="location-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center">
                    <div class="location-icon me-4">
                        <i class="fas {{ $location->getIcon() }}"></i>
                    </div>
                    <div>
                        <h1 class="h3 mb-1">{{ $location->name }}</h1>
                        <p class="text-muted mb-2">
                            <i class="fas fa-tag me-1"></i> 
                            <span class="badge bg-{{ $location->getTypeColor() }}">
                                {{ $location->getTypeLabel() }}
                            </span>
                            @if($location->code)
                                <span class="ms-2">
                                    <i class="fas fa-hashtag me-1"></i> {{ $location->code }}
                                </span>
                            @endif
                        </p>
                        @if($location->parent)
                            <p class="mb-0">
                                <i class="fas fa-level-up-alt me-1"></i> 
                                Emplacement parent : 
                                <a href="{{ route('locations.show', $location->parent) }}" class="text-primary">
                                    {{ $location->parent->name }}
                                </a>
                            </p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#qrCodeModal">
                        <i class="fas fa-qrcode me-1"></i> QR Code
                    </button>
                    <a href="#" class="btn btn-outline-primary" onclick="window.print()">
                        <i class="fas fa-print me-1"></i> Imprimer
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Cartes de statistiques -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Équipements</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $location->equipments_count }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-laptop fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Sous-emplacements</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $location->children_count }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-sitemap fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Valeur totale</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($location->total_value, 2, ',', ' ') }} €
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-euro-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Statut</div>
                            <div class="h5 mb-0">
                                @if($location->is_under_maintenance)
                                    <span class="badge bg-warning text-dark">
                                        <i class="fas fa-tools me-1"></i> En maintenance
                                    </span>
                                @elseif($location->is_active)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i> Actif
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-times-circle me-1"></i> Inactif
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-info-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-8">
            <!-- Détails de l'emplacement -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <ul class="nav nav-tabs card-header-tabs" id="locationTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="details-tab" data-bs-toggle="tab" 
                                    data-bs-target="#details" type="button" role="tab" aria-controls="details" 
                                    aria-selected="true">
                                <i class="fas fa-info-circle me-1"></i> Détails
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="equipment-tab" data-bs-toggle="tab" 
                                    data-bs-target="#equipment" type="button" role="tab" 
                                    aria-controls="equipment" aria-selected="false">
                                <i class="fas fa-laptop me-1"></i> Équipements
                                <span class="badge bg-primary rounded-pill ms-1">{{ $location->equipments_count }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="sub-locations-tab" data-bs-toggle="tab" 
                                    data-bs-target="#sub-locations" type="button" role="tab" 
                                    aria-controls="sub-locations" aria-selected="false"
                                    {{ $location->children_count == 0 ? 'disabled' : '' }}>
                                <i class="fas fa-sitemap me-1"></i> Sous-emplacements
                                @if($location->children_count > 0)
                                    <span class="badge bg-success rounded-pill ms-1">{{ $location->children_count }}</span>
                                @endif
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="history-tab" data-bs-toggle="tab" 
                                    data-bs-target="#history" type="button" role="tab" 
                                    aria-controls="history" aria-selected="false">
                                <i class="fas fa-history me-1"></i> Historique
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="locationTabsContent">
                        <!-- Onglet Détails -->
                        <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="mb-3">Informations générales</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <th style="width: 40%;">Nom :</th>
                                            <td>{{ $location->name }}</td>
                                        </tr>
                                        @if($location->code)
                                            <tr>
                                                <th>Code :</th>
                                                <td>{{ $location->code }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <th>Type :</th>
                                            <td>
                                                <span class="badge bg-{{ $location->getTypeColor() }}">
                                                    {{ $location->getTypeLabel() }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Statut :</th>
                                            <td>
                                                @if($location->is_under_maintenance)
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="fas fa-tools me-1"></i> En maintenance
                                                    </span>
                                                @elseif($location->is_active)
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check-circle me-1"></i> Actif
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">
                                                        <i class="fas fa-times-circle me-1"></i> Inactif
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                        @if($location->parent)
                                            <tr>
                                                <th>Parent :</th>
                                                <td>
                                                    <a href="{{ route('locations.show', $location->parent) }}" class="text-primary">
                                                        <i class="fas {{ $location->parent->getIcon() }} me-1"></i>
                                                        {{ $location->parent->name }}
                                                    </a>
                                                </td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="mb-3">Coordonnées</h5>
                                    <table class="table table-borderless">
                                        @if($location->address)
                                            <tr>
                                                <th style="width: 40%;">Adresse :</th>
                                                <td>{{ $location->address }}</td>
                                            </tr>
                                        @endif
                                        @if($location->postal_code || $location->city)
                                            <tr>
                                                <th>Ville :</th>
                                                <td>
                                                    @if($location->postal_code && $location->city)
                                                        {{ $location->postal_code }} {{ $location->city }}
                                                    @elseif($location->postal_code)
                                                        {{ $location->postal_code }}
                                                    @else
                                                        {{ $location->city }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                        @if($location->country)
                                            <tr>
                                                <th>Pays :</th>
                                                <td>{{ $location->country }}</td>
                                            </tr>
                                        @endif
                                        @if($location->latitude && $location->longitude)
                                            <tr>
                                                <th>Coordonnées :</th>
                                                <td>
                                                    <a href="https://www.google.com/maps?q={{ $location->latitude }},{{ $location->longitude }}" 
                                                       target="_blank" class="text-primary">
                                                        <i class="fas fa-map-marker-alt me-1"></i>
                                                        {{ $location->latitude }}, {{ $location->longitude }}
                                                    </a>
                                                </td>
                                            </tr>
                                        @endif
                                        @if($location->contact_phone || $location->contact_email)
                                            <tr>
                                                <th>Contact :</th>
                                                <td>
                                                    @if($location->contact_phone)
                                                        <div>
                                                            <i class="fas fa-phone me-1"></i> 
                                                            <a href="tel:{{ $location->contact_phone }}" class="text-primary">
                                                                {{ $location->contact_phone }}
                                                            </a>
                                                        </div>
                                                    @endif
                                                    @if($location->contact_email)
                                                        <div class="mt-1">
                                                            <i class="fas fa-envelope me-1"></i> 
                                                            <a href="mailto:{{ $location->contact_email }}" class="text-primary">
                                                                {{ $location->contact_email }}
                                                            </a>
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                            
                            @if($location->description)
                                <div class="mt-4">
                                    <h5>Description</h5>
                                    <div class="p-3 bg-light rounded">
                                        {!! nl2br(e($location->description)) !!}
                                    </div>
                                </div>
                            @endif
                            
                            @if($location->notes)
                                <div class="mt-4">
                                    <h5>Notes internes</h5>
                                    <div class="p-3 bg-light rounded">
                                        {!! nl2br(e($location->notes)) !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Onglet Équipements -->
                        <div class="tab-pane fade" id="equipment" role="tabpanel" aria-labelledby="equipment-tab">
                            @if($location->equipments_count > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Nom</th>
                                                <th>N° de série</th>
                                                <th>Catégorie</th>
                                                <th>Statut</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($location->equipments as $equipment)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('equipment.show', $equipment) }}" class="text-primary">
                                                            <i class="fas {{ $equipment->category->icon ?? 'fa-box' }} me-1"></i>
                                                            {{ $equipment->name }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $equipment->serial_number ?? 'N/A' }}</td>
                                                    <td>
                                                        @if($equipment->category)
                                                            <span class="badge bg-secondary">
                                                                {{ $equipment->category->name }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">Non catégorisé</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($equipment->is_available)
                                                            <span class="badge bg-success">Disponible</span>
                                                        @else
                                                            <span class="badge bg-warning text-dark">Indisponible</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="{{ route('equipment.show', $equipment) }}" class="btn btn-info" data-bs-toggle="tooltip" title="Voir">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="{{ route('equipment.edit', $equipment) }}" class="btn btn-primary" data-bs-toggle="tooltip" title="Modifier">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center py-4">
                                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                        <p class="text-muted">Aucun équipement trouvé dans cet emplacement</p>
                                                        <a href="{{ route('equipment.create', ['location_id' => $location->id]) }}" class="btn btn-primary">
                                                            <i class="fas fa-plus me-1"></i> Ajouter un équipement
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                
                                @if($location->equipments_count > 5)
                                    <div class="d-flex justify-content-end">
                                        <a href="{{ route('equipment.index', ['location_id' => $location->id]) }}" class="btn btn-outline-primary">
                                            Voir tous les équipements <i class="fas fa-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-laptop fa-4x text-muted mb-3"></i>
                                    <h5 class="text-gray-800">Aucun équipement dans cet emplacement</h5>
                                    <p class="text-muted mb-4">Commencez par ajouter un équipement à cet emplacement</p>
                                    <a href="{{ route('equipment.create', ['location_id' => $location->id]) }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i> Ajouter un équipement
                                    </a>
                                </div>
                            @endif
                        </div>
                        </div>
                        
                        <!-- Onglet Historique -->
                        <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
                            <div class="timeline">
                                @if($location->activities && $location->activities->count() > 0)
                                    @foreach($location->activities as $activity)
                                        <div class="timeline-item">
                                            <div class="timeline-date small text-muted mb-1">
                                                {{ $activity->created_at->format('d/m/Y H:i') }}
                                            </div>
                                            <div class="timeline-content">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <strong>{{ $activity->description }}</strong>
                                                    <span class="badge bg-light text-dark">
                                                        {{ $activity->causer->name ?? 'Système' }}
                                                    </span>
                                                </div>
                                                @if(isset($activity->properties) && is_object($activity->properties) && method_exists($activity->properties, 'has') && $activity->properties->has('attributes'))
                                                    <div class="mt-2 small">
                                                        @foreach($activity->properties['attributes'] as $key => $value)
                                                            @if(!in_array($key, ['updated_at', 'created_at']))
                                                                <div>
                                                                    <span class="text-muted">{{ $key }}:</span> 
                                                                    @if(is_bool($value))
                                                                        {{ $value ? 'Oui' : 'Non' }}
                                                                    @else
                                                                        {{ $value ?? 'N/A' }}
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                    
                                    @if($location->activities->count() > 10)
                                        <div class="text-center mt-3">
                                            <a href="#" class="btn btn-outline-primary btn-sm">
                                                Afficher plus d'activités
                                            </a>
                                        </div>
                                    @endif
                                @else
                                    <div class="text-center py-5">
                                        <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Aucune activité récente</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Galerie d'images -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Galerie</h6>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadImageModal">
                        <i class="fas fa-upload me-1"></i> Ajouter
                    </button>
                </div>
                <div class="card-body">
                    @if($location->images && $location->images->isNotEmpty())
                        <div class="row">
                            @foreach($location->images as $image)
                                <div class="col-6 col-md-4 mb-3">
                                    <div class="gallery-item">
                                        <a href="{{ Storage::url($image->path) }}" data-fancybox="gallery">
                                            <img src="{{ Storage::url($image->thumbnail_path ?? $image->path) }}" 
                                                 alt="Image de l'emplacement" class="img-fluid">
                                        </a>
                                        <div class="gallery-actions">
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    data-bs-toggle="modal" data-bs-target="#deleteImageModal{{ $image->id }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                        <div class="gallery-caption small">
                                            <div class="text-truncate" title="{{ $image->name }}">
                                                {{ $image->name }}
                                            </div>
                                            <div class="text-muted small">
                                                {{ $image->created_at->format('d/m/Y') }}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Modal de suppression d'image -->
                                    <div class="modal fade" id="deleteImageModal{{ $image->id }}" tabindex="-1" 
                                         aria-labelledby="deleteImageModalLabel{{ $image->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteImageModalLabel{{ $image->id }}">
                                                        Supprimer l'image
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Êtes-vous sûr de vouloir supprimer cette image ?</p>
                                                    <div class="text-center">
                                                        <img src="{{ Storage::url($image->path) }}" 
                                                             alt="Image à supprimer" class="img-fluid rounded">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                        <i class="fas fa-times me-1"></i> Annuler
                                                    </button>
                                                    <form action="{{ route('locations.images.destroy', [$location, $image]) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">
                                                            <i class="fas fa-trash-alt me-1"></i> Supprimer
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-images fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Aucune image n'a été ajoutée à cet emplacement</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadImageModal">
                                <i class="fas fa-upload me-1"></i> Ajouter une image
                            </button>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Fichiers joints -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Fichiers joints</h6>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadFileModal">
                        <i class="fas fa-paperclip me-1"></i> Ajouter
                    </button>
                </div>
                <div class="card-body">
                    @if($location->files && $location->files->isNotEmpty())
                        <div class="list-group list-group-flush">
                            @foreach($location->files as $file)
                                <div class="list-group-item px-0 py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <i class="fas {{ $file->getIcon() }} fa-lg me-3 text-{{ $file->getIconColor() }}"></i>
                                            <div>
                                                <div class="fw-bold">
                                                    <a href="{{ Storage::url($file->path) }}" target="_blank" class="text-dark">
                                                        {{ $file->name }}
                                                    </a>
                                                </div>
                                                <small class="text-muted">
                                                    {{ $file->size_for_humans }} • {{ $file->created_at->format('d/m/Y') }}
                                                </small>
                                            </div>
                                        </div>
                                        <div class="dropdown">
                                            <button class="btn btn-link text-muted p-0" type="button" 
                                                    id="fileActions{{ $file->id }}" data-bs-toggle="dropdown" 
                                                    aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="fileActions{{ $file->id }}">
                                                <li>
                                                    <a class="dropdown-item" href="{{ Storage::url($file->path) }}" download>
                                                        <i class="fas fa-download me-2"></i> Télécharger
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" 
                                                       data-bs-target="#editFileModal{{ $file->id }}">
                                                        <i class="fas fa-edit me-2"></i> Renommer
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" 
                                                       data-bs-target="#deleteFileModal{{ $file->id }}">
                                                        <i class="fas fa-trash-alt me-2"></i> Supprimer
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Modal d'édition de fichier -->
                                <div class="modal fade" id="editFileModal{{ $file->id }}" tabindex="-1" 
                                     aria-labelledby="editFileModalLabel{{ $file->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('locations.files.update', [$location, $file]) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editFileModalLabel{{ $file->id }}">
                                                        Renommer le fichier
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="fileName{{ $file->id }}" class="form-label">Nom du fichier</label>
                                                        <input type="text" class="form-control" id="fileName{{ $file->id }}" 
                                                               name="name" value="{{ $file->name }}" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                        <i class="fas fa-times me-1"></i> Annuler
                                                    </button>
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-save me-1"></i> Enregistrer
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Modal de suppression de fichier -->
                                <div class="modal fade" id="deleteFileModal{{ $file->id }}" tabindex="-1" 
                                     aria-labelledby="deleteFileModalLabel{{ $file->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteFileModalLabel{{ $file->id }}">
                                                    Supprimer le fichier
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Êtes-vous sûr de vouloir supprimer ce fichier ?</p>
                                                <div class="alert alert-warning mb-0">
                                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                                    Cette action est irréversible.
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    <i class="fas fa-times me-1"></i> Annuler
                                                </button>
                                                <form action="{{ route('locations.files.destroy', [$location, $file]) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="fas fa-trash-alt me-1"></i> Supprimer
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Aucun fichier joint</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadFileModal">
                                <i class="fas fa-paperclip me-1"></i> Ajouter un fichier
                            </button>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Dernières activités -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Dernières activités</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @forelse(($location->recentActivities ?? []) as $activity)
                            <div class="timeline-item">
                                <div class="timeline-date small text-muted mb-1">
                                    {{ $activity->created_at->diffForHumans() }}
                                </div>
                                <div class="timeline-content small">
                                    <div class="d-flex justify-content-between">
                                        <strong>{{ $activity->description }}</strong>
                                        <span class="text-muted">
                                            {{ $activity->causer->name ?? 'Système' }}
                                        </span>
                                    </div>
                                    @if($activity->properties->has('attributes'))
                                        <div class="mt-1 small text-muted">
                                            @php
                                                $changes = [];
                                                foreach($activity->properties['attributes'] as $key => $value) {
                                                    if(!in_array($key, ['updated_at', 'created_at'])) {
                                                        $changes[] = $key . ': ' . (is_bool($value) ? ($value ? 'Oui' : 'Non') : ($value ?? 'N/A'));
                                                    }
                                                }
                                            @endphp
                                            {{ implode(', ', $changes) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-3">
                                <i class="fas fa-history fa-2x text-muted mb-2"></i>
                                <p class="text-muted small mb-0">Aucune activité récente</p>
                            </div>
                        @endforelse
                    </div>
                    
                    @if($location->activities_count > 5)
                        <div class="text-center mt-2">
                            <a href="#" class="btn btn-sm btn-outline-primary">
                                Voir tout l'historique
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modale d'upload d'image -->
<div class="modal fade" id="uploadImageModal" tabindex="-1" aria-labelledby="uploadImageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('locations.images.store', $location) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadImageModalLabel">Ajouter une image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="image" class="form-label">Sélectionner une image</label>
                        <input class="form-control @error('image') is-invalid @enderror" type="file" id="image" name="image" accept="image/*" required>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Formats acceptés : JPG, PNG, GIF (max: 5MB)</div>
                    </div>
                    <div class="mb-3">
                        <label for="imageName" class="form-label">Nom de l'image</label>
                        <input type="text" class="form-control" id="imageName" name="name" 
                               value="Image {{ $location->name }} {{ now()->format('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="imageDescription" class="form-label">Description (optionnel)</label>
                        <textarea class="form-control" id="imageDescription" name="description" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-1"></i> Téléverser
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modale d'upload de fichier -->
<div class="modal fade" id="uploadFileModal" tabindex="-1" aria-labelledby="uploadFileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('locations.files.store', $location) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadFileModalLabel">Ajouter un fichier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file" class="form-label">Sélectionner un fichier</label>
                        <input class="form-control @error('file') is-invalid @enderror" type="file" id="file" name="file" required>
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Taille maximale : 20MB</div>
                    </div>
                    <div class="mb-3">
                        <label for="fileName" class="form-label">Nom du fichier</label>
                        <input type="text" class="form-control" id="fileName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="fileDescription" class="form-label">Description (optionnel)</label>
                        <textarea class="form-control" id="fileDescription" name="description" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-1"></i> Téléverser
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modale QR Code -->
<div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qrCodeModalLabel">Code QR de l'emplacement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body text-center">
                <div class="qr-code mb-3">
                    <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG(route('locations.show', $location), 'QRCODE', 10, 10) }}" 
                         alt="QR Code pour {{ $location->name }}" 
                         class="img-fluid"
                         style="max-width: 200px;">
                </div>
                <p class="text-muted small mb-0">Scannez ce code pour accéder rapidement à cette page</p>
            </div>
            <div class="modal-footer justify-content-center">
                <a href="data:image/png;base64,{{ DNS2D::getBarcodePNG(route('locations.show', $location), 'QRCODE', 25, 25) }}" 
                   download="QRCode-{{ Str::slug($location->name) }}.png" 
                   class="btn btn-outline-primary">
                    <i class="fas fa-download me-1"></i> Télécharger PNG
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modale de suppression -->
<div class="modal fade" id="deleteLocationModal" tabindex="-1" aria-labelledby="deleteLocationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteLocationModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cet emplacement ?</p>
                <ul class="mb-0">
                    <li>Nom : <strong>{{ $location->name }}</strong></li>
                    @if($location->code)
                        <li>Code : <strong>{{ $location->code }}</strong></li>
                    @endif
                    @if($location->children_count > 0)
                        <li class="text-danger">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            {{ $location->children_count }} sous-emplacement(s) seront également supprimé(s)
                        </li>
                    @endif
                    @if($location->equipments_count > 0)
                        <li class="text-danger">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            {{ $location->equipments_count }} équipement(s) seront déplacés vers l'emplacement parent
                        </li>
                    @endif
                </ul>
                <div class="alert alert-danger mt-3">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Attention :</strong> Cette action est irréversible.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Annuler
                </button>
                <form action="{{ route('locations.destroy', $location) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-1"></i> Confirmer la suppression
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialiser les tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Gestion du changement d'onglet et sauvegarde dans le localStorage
        const locationTabs = document.getElementById('locationTabs');
        if (locationTabs) {
            const tabPanes = locationTabs.querySelectorAll('[data-bs-toggle="tab"]');
            
            tabPanes.forEach(tab => {
                tab.addEventListener('click', function (e) {
                    const target = e.target.getAttribute('data-bs-target');
                    localStorage.setItem('locationActiveTab', target);
                });
            });
            
            // Restaurer l'onglet actif
            const activeTab = localStorage.getItem('locationActiveTab');
            if (activeTab) {
                const tabTrigger = document.querySelector(`[data-bs-target="${activeTab}"]`);
                if (tabTrigger) {
                    new bootstrap.Tab(tabTrigger).show();
                }
            }
        }
        
        // Gestion de l'affichage du nom du fichier sélectionné
        const fileInput = document.getElementById('file');
        const fileNameInput = document.getElementById('fileName');
        
        if (fileInput && fileNameInput) {
            fileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const fileName = this.files[0].name;
                    const fileNameWithoutExt = fileName.lastIndexOf('.') > 0 
                        ? fileName.substring(0, fileName.lastIndexOf('.')) 
                        : fileName;
                    fileNameInput.value = fileNameWithoutExt;
                }
            });
        }
    });
</script>
@endpush

@push('styles')
<style>
    @media print {
        .no-print, .no-print * {
            display: none !important;
        }
        
        body {
            font-size: 12px;
        }
        
        .card {
            border: none;
            box-shadow: none;
        }
        
        .card-header {
            background-color: transparent !important;
            border-bottom: 1px solid #dee2e6;
        }
    }
</style>
@endpush
@endsection