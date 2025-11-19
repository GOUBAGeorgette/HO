@extends('layouts.maquette')

@section('title', $category->name)

@push('styles')
<style>
    .category-image {
        max-width: 100%;
        height: auto;
        border-radius: 0.5rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .info-card {
        border-left: 3px solid #4e73df;
        border-radius: 0.25rem;
        transition: all 0.3s ease;
    }
    
    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    }
    
    .nav-tabs .nav-link {
        color: #6c757d;
        font-weight: 500;
        border: none;
        border-bottom: 2px solid transparent;
        padding: 0.75rem 1.25rem;
    }
    
    .nav-tabs .nav-link.active {
        color: #4e73df;
        font-weight: 600;
        background: none;
        border: none;
        border-bottom: 2px solid #4e73df;
    }
    
    .specs-list dt {
        width: 200px;
        color: #6c757d;
    }
    
    .specs-list dd {
        margin-left: 220px;
    }
    
    .equipment-card {
        transition: all 0.3s ease;
        border: 1px solid #e3e6f0;
    }
    
    .equipment-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    }
    
    .equipment-status {
        position: absolute;
        top: 10px;
        right: 10px;
    }
    
    .subcategory-badge {
        font-size: 0.75rem;
        margin-right: 0.25rem;
        margin-bottom: 0.25rem;
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
        top: 0.5rem;
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
    <!-- En-tête -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">Catégories</a></li>
                @php
                    $ancestors = $category->ancestors();
                @endphp
                @if($ancestors && $ancestors->isNotEmpty())
                    @foreach($ancestors as $ancestor)
                        <li class="breadcrumb-item"><a href="{{ route('categories.show', $ancestor) }}">{{ $ancestor->name }}</a></li>
                    @endforeach
                @endif
                <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
            </ol>
        </nav>
        <div>
            <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-primary me-2">
                <i class="fas fa-edit fa-sm"></i> Modifier
            </a>
            <a href="{{ route('categories.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left fa-sm"></i> Retour
            </a>
        </div>
    </div>
    
    <div class="row">
        <!-- Colonne de gauche -->
        <div class="col-lg-4">
            <!-- Carte d'image -->
            <div class="card shadow mb-4">
                <div class="card-body text-center">
                    @if($category->image_path)
                        <img src="{{ asset('storage/' . $category->image_path) }}" 
                             alt="{{ $category->name }}" 
                             class="img-fluid category-image mb-3"
                             style="max-height: 250px;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center" 
                             style="height: 200px; border-radius: 0.5rem; margin-bottom: 1rem;">
                            <i class="fas fa-folder-open fa-4x text-gray-400"></i>
                        </div>
                    @endif
                    
                    <h4 class="mb-3">{{ $category->name }}</h4>
                    
                    <div class="d-flex justify-content-center gap-2 mb-3">
                        <span class="badge bg-{{ $category->is_active ? 'success' : 'secondary' }}">
                            {{ $category->is_active ? 'Actif' : 'Inactif' }}
                        </span>
                        <span class="badge bg-primary">
                            {{ $category->equipments_count }} équipement(s)
                        </span>
                        @if($category->children_count > 0)
                            <span class="badge bg-info">
                                {{ $category->children_count }} sous-catégorie(s)
                            </span>
                        @endif
                    </div>
                    
                    @if($category->parent)
                        <div class="mt-3">
                            <p class="mb-1 text-muted">Catégorie parente :</p>
                            <a href="{{ route('categories.show', $category->parent) }}" class="text-decoration-none">
                                <i class="fas fa-level-up-alt me-1"></i> {{ $category->parent->name }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Informations générales -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informations générales</h6>
                </div>
                <div class="card-body">
                    <dl class="mb-0">
                        <div class="mb-2">
                            <dt>Nom</dt>
                            <dd>{{ $category->name }}</dd>
                        </div>
                        
                        @if($category->code)
                            <div class="mb-2">
                                <dt>Code</dt>
                                <dd><code>{{ $category->code }}</code></dd>
                            </div>
                        @endif
                        
                        <div class="mb-2">
                            <dt>Description</dt>
                            <dd>{{ $category->description ?? 'Aucune description' }}</dd>
                        </div>
                        
                        <div class="mb-2">
                            <dt>Statut</dt>
                            <dd>
                                <span class="badge bg-{{ $category->is_active ? 'success' : 'secondary' }}">
                                    {{ $category->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </dd>
                        </div>
                        
                        @if($category->parent)
                            <div class="mb-2">
                                <dt>Catégorie parente</dt>
                                <dd>
                                    <a href="{{ route('categories.show', $category->parent) }}" class="text-decoration-none">
                                        <i class="fas fa-folder me-1"></i> {{ $category->parent->name }}
                                    </a>
                                </dd>
                            </div>
                        @endif
                        
                        <div class="mb-2">
                            <dt>Date de création</dt>
                            <dd>{{ $category->created_at->format('d/m/Y H:i') }}</dd>
                        </div>
                        
                        <div class="mb-0">
                            <dt>Dernière mise à jour</dt>
                            <dd>{{ $category->updated_at->format('d/m/Y H:i') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
            
            <!-- Statistiques -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistiques</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <div class="card bg-primary text-white h-100">
                                <div class="card-body py-2">
                                    <div class="text-center">
                                        <div class="h5 mb-0 font-weight-bold">{{ $category->equipments_count }}</div>
                                        <div class="small">Équipements</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        @if($category->children_count > 0)
                            <div class="col-6 mb-3">
                                <div class="card bg-info text-white h-100">
                                    <div class="card-body py-2">
                                        <div class="text-center">
                                            <div class="h5 mb-0 font-weight-bold">{{ $category->children_count }}</div>
                                            <div class="small">Sous-catégories</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        @php
                            $maintenanceStats = $category->maintenance_stats ?? [
                                'total' => 0,
                                'pending' => 0,
                                'completed' => 0
                            ];
                        @endphp
                        
                        @if(isset($maintenanceStats['total']) && $maintenanceStats['total'] > 0)
                            <div class="col-6">
                                <div class="card bg-warning text-dark h-100">
                                    <div class="card-body py-2">
                                        <div class="text-center">
                                            <div class="h5 mb-0 font-weight-bold">
                                                {{ $maintenanceStats['pending'] ?? 0 }}
                                            </div>
                                            <div class="small">Maintenances en cours</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-6">
                                <div class="card bg-success text-white h-100">
                                    <div class="card-body py-2">
                                        <div class="text-center">
                                            <div class="h5 mb-0 font-weight-bold">
                                                {{ $maintenanceStats['completed'] ?? 0 }}
                                            </div>
                                            <div class="small">Maintenances terminées</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Sous-catégories -->
            @if($category->children->isNotEmpty())
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Sous-catégories</h6>
                        <a href="{{ route('categories.create', ['parent_id' => $category->id]) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus fa-sm"></i> Ajouter
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @foreach($category->children as $child)
                                <a href="{{ route('categories.show', $child) }}" 
                                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-folder me-2 text-warning"></i>
                                        {{ $child->name }}
                                    </div>
                                    <div>
                                        <span class="badge bg-primary rounded-pill">{{ $child->equipments_count }}</span>
                                        @if($child->children_count > 0)
                                            <span class="badge bg-info rounded-pill ms-1">{{ $child->children_count }}</span>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        <!-- Colonne de droite -->
        <div class="col-lg-8">
            <!-- Navigation par onglets -->
            <ul class="nav nav-tabs mb-4" id="categoryTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" 
                            id="equipments-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#equipments" 
                            type="button" 
                            role="tab" 
                            aria-controls="equipments" 
                            aria-selected="true">
                        Équipements
                        <span class="badge bg-primary rounded-pill ms-1">{{ $category->equipments_count }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" 
                            id="subcategories-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#subcategories" 
                            type="button" 
                            role="tab" 
                            aria-controls="subcategories" 
                            aria-selected="false">
                        Sous-catégories
                        @if($category->children_count > 0)
                            <span class="badge bg-info rounded-pill ms-1">{{ $category->children_count }}</span>
                        @endif
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
                        @php
                            $maintenanceStats = $category->maintenance_stats ?? ['total' => 0, 'pending' => 0];
                        @endphp
                        @if(isset($maintenanceStats['total']) && $maintenanceStats['total'] > 0)
                            <span class="badge bg-{{ ($maintenanceStats['pending'] ?? 0) > 0 ? 'warning' : 'success' }} rounded-pill ms-1">
                                {{ $maintenanceStats['total'] }}
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
                    </button>
                </li>
            </ul>
            
            <!-- Contenu des onglets -->
            <div class="tab-content" id="categoryTabsContent">
                <!-- Onglet Équipements -->
                <div class="tab-pane fade show active" id="equipments" role="tabpanel" aria-labelledby="equipments-tab">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Équipements de la catégorie</h6>
                            <div>
                                <a href="{{ route('equipment.create', ['category_id' => $category->id]) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus fa-sm"></i> Ajouter un équipement
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($equipment && $equipment->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Nom</th>
                                                <th>N° de série</th>
                                                <th>Modèle</th>
                                                <th>Statut</th>
                                                <th>Dernière maintenance</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($equipment as $item)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('equipment.show', $item) }}" class="text-decoration-none">
                                                            {{ $item->name }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $item->serial_number ?? '-' }}</td>
                                                    <td>{{ $item->model ?? '-' }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $item->status_class }}">
                                                            {{ $item->status_label }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if($item->last_maintenance)
                                                            <span data-bs-toggle="tooltip" 
                                                                  data-bs-placement="top" 
                                                                  title="{{ $item->last_maintenance->completed_date->format('d/m/Y') }} - {{ $item->last_maintenance->title }}">
                                                                {{ $item->last_maintenance->completed_date->diffForHumans() }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">Jamais</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="{{ route('equipment.show', $item) }}" 
                                                               class="btn btn-info" 
                                                               data-bs-toggle="tooltip" 
                                                               title="Voir les détails">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="{{ route('equipment.edit', $item) }}" 
                                                               class="btn btn-warning" 
                                                               data-bs-toggle="tooltip" 
                                                               title="Modifier">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div class="text-muted">
                                        Affichage de {{ $equipment->firstItem() }} à {{ $equipment->lastItem() }} sur {{ $equipment->total() }} équipements
                                    </div>
                                    <div>
                                        {{ $equipment->withQueryString()->links() }}
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-box-open fa-3x text-gray-400 mb-3"></i>
                                    <p class="text-muted mb-0">Aucun équipement trouvé dans cette catégorie.</p>
                                    <a href="{{ route('equipment.create', ['category_id' => $category->id]) }}" class="btn btn-primary mt-3">
                                        <i class="fas fa-plus fa-sm"></i> Ajouter un équipement
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Onglet Sous-catégories -->
                <div class="tab-pane fade" id="subcategories" role="tabpanel" aria-labelledby="subcategories-tab">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Sous-catégories</h6>
                            <a href="{{ route('categories.create', ['parent_id' => $category->id]) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus fa-sm"></i> Ajouter une sous-catégorie
                            </a>
                        </div>
                        <div class="card-body">
                            @if($category->children->isNotEmpty())
                                <div class="row">
                                    @foreach($category->children as $child)
                                        <div class="col-md-6 mb-4">
                                            <div class="card h-100 border-left-primary shadow-sm">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <h5 class="card-title mb-1">
                                                                <a href="{{ route('categories.show', $child) }}" class="text-decoration-none">
                                                                    <i class="fas fa-folder me-2 text-warning"></i>
                                                                    {{ $child->name }}
                                                                </a>
                                                            </h5>
                                                            @if($child->description)
                                                                <p class="card-text text-muted small mb-2">
                                                                    {{ Str::limit($child->description, 100) }}
                                                                </p>
                                                            @endif
                                                            <div class="d-flex flex-wrap gap-2">
                                                                <span class="badge bg-primary">
                                                                    <i class="fas fa-box me-1"></i> {{ $child->equipments_count }} équipement(s)
                                                                </span>
                                                                @if($child->children_count > 0)
                                                                    <span class="badge bg-info">
                                                                        <i class="fas fa-sitemap me-1"></i> {{ $child->children_count }} sous-catégorie(s)
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="dropdown
                                                            <button class="btn btn-sm btn-link text-muted p-0" 
                                                                    type="button" 
                                                                    id="dropdownMenuButton{{ $child->id }}" 
                                                                    data-bs-toggle="dropdown" 
                                                                    aria-expanded="false">
                                                                <i class="fas fa-ellipsis-v"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton{{ $child->id }}">
                                                                <li>
                                                                    <a class="dropdown-item" href="{{ route('categories.edit', $child) }}">
                                                                        <i class="fas fa-edit fa-sm me-2"></i> Modifier
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item" href="{{ route('categories.show', $child) }}">
                                                                        <i class="fas fa-eye fa-sm me-2"></i> Voir les détails
                                                                    </a>
                                                                </li>
                                                                <li><hr class="dropdown-divider"></li>
                                                                <li>
                                                                    <form action="{{ route('categories.destroy', $child) }}" method="POST" class="d-inline">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" 
                                                                                class="dropdown-item text-danger"
                                                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')">
                                                                            <i class="fas fa-trash-alt fa-sm me-2"></i> Supprimer
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-folder-open fa-3x text-gray-400 mb-3"></i>
                                    <p class="text-muted mb-0">Aucune sous-catégorie n'a été créée pour le moment.</p>
                                    <a href="{{ route('categories.create', ['parent_id' => $category->id]) }}" class="btn btn-primary mt-3">
                                        <i class="fas fa-plus fa-sm"></i> Créer une sous-catégorie
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Onglet Maintenance -->
                <div class="tab-pane fade" id="maintenance" role="tabpanel" aria-labelledby="maintenance-tab">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Historique de maintenance</h6>
                            <a href="{{ route('maintenance.create', ['category_id' => $category->id]) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus fa-sm"></i> Nouvelle intervention
                            </a>
                        </div>
                        <div class="card-body">
                            @if($maintenances->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Équipement</th>
                                                <th>Type</th>
                                                <th>Statut</th>
                                                <th>Coût</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($maintenances as $maintenance)
                                                <tr>
                                                    <td>
                                                        <span data-bs-toggle="tooltip" 
                                                              data-bs-placement="top" 
                                                              title="{{ $maintenance->scheduled_date->format('d/m/Y') }}">
                                                            {{ $maintenance->scheduled_date->format('d/m/Y') }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('equipment.show', $maintenance->equipment) }}" class="text-decoration-none">
                                                            {{ $maintenance->equipment->name }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $maintenance->type_label }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $maintenance->status_class }}">
                                                            {{ $maintenance->status_label }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if($maintenance->cost)
                                                            {{ number_format($maintenance->cost, 2, ',', ' ') }} €
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('maintenances.show', $maintenance) }}" 
                                                           class="btn btn-sm btn-info" 
                                                           data-bs-toggle="tooltip" 
                                                           title="Voir les détails">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div class="text-muted">
                                        Affichage de {{ $maintenances->firstItem() }} à {{ $maintenances->lastItem() }} sur {{ $maintenances->total() }} interventions
                                    </div>
                                    <div>
                                        {{ $maintenances->withQueryString()->links() }}
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-tools fa-3x text-gray-400 mb-3"></i>
                                    <p class="text-muted mb-0">Aucune intervention de maintenance enregistrée pour cette catégorie.</p>
                                    <a href="{{ route('maintenance.create', ['category_id' => $category->id]) }}" class="btn btn-primary mt-3">
                                        <i class="fas fa-plus fa-sm"></i> Planifier une intervention
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Statistiques de maintenance -->
                    <div class="row
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Interventions (12 mois)</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $maintenanceStats['total'] }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-tools fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Coût total</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ isset($maintenanceStats['total_cost']) ? number_format($maintenanceStats['total_cost'], 2, ',', ' ') . ' €' : '0,00 €' }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-euro-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                En attente</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $maintenanceStats['pending'] }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                            <h6 class="m-0 font-weight-bold text-primary">Historique des modifications</h6>
                        </div>
                        <div class="card-body">
                            @if(isset($category->audits) && $category->audits->count() > 0)
                                <div class="timeline">
                                    @foreach($category->audits->sortByDesc('created_at') as $audit)
                                        <div class="timeline-item">
                                            <div class="card mb-3">
                                                <div class="card-body p-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <h6 class="mb-0">
                                                            @if($audit->event === 'created')
                                                                <i class="fas fa-plus-circle text-success me-1"></i> Création
                                                            @elseif($audit->event === 'updated')
                                                                <i class="fas fa-edit text-primary me-1"></i> Modification
                                                            @elseif($audit->event === 'deleted')
                                                                <i class="fas fa-trash-alt text-danger me-1"></i> Suppression
                                                            @elseif($audit->event === 'restored')
                                                                <i class="fas fa-trash-restore text-info me-1"></i> Restauration
                                                            @endif
                                                            de la catégorie
                                                        </h6>
                                                        <small class="text-muted">
                                                            {{ $audit->created_at->format('d/m/Y H:i') }}
                                                        </small>
                                                    </div>
                                                    
                                                    @if($audit->user)
                                                        <p class="mb-2 small">
                                                            <i class="fas fa-user fa-fw"></i> 
                                                            {{ $audit->user->name }}
                                                            <span class="text-muted">({{ $audit->user->email }})</span>
                                                        </p>
                                                    @endif
                                                    
                                                    @if($audit->event === 'updated' && count($audit->getModified()) > 0)
                                                        <div class="border rounded p-2 bg-light">
                                                            <p class="mb-1 small font-weight-bold">Modifications :</p>
                                                            <ul class="mb-0 small">
                                                                @foreach($audit->getModified() as $attribute => $modified)
                                                                    <li>
                                                                        <strong>{{ $attribute }} :</strong>
                                                                        @if(isset($modified['old']) && $modified['old'] === null)
                                                                            <span class="text-muted">(vide)</span>
                                                                        @else
                                                                            {{ is_array($modified['old']) ? json_encode($modified['old']) : $modified['old'] }}
                                                                        @endif
                                                                        <i class="fas fa-arrow-right mx-2 text-muted"></i>
                                                                        @if(isset($modified['new']) && $modified['new'] === null)
                                                                            <span class="text-muted">(vide)</span>
                                                                        @else
                                                                            {{ is_array($modified['new']) ? json_encode($modified['new']) : $modified['new'] }}
                                                                        @endif
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-history fa-3x text-gray-400 mb-3"></i>
                                    <p class="text-muted mb-0">Aucun historique disponible pour cette catégorie.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialisation des tooltips
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
