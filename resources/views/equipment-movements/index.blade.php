@extends('layouts.maquette')

@section('title', 'Gestion des mouvements d\'équipements')

@push('styles')
<style>
    .status-badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
    }
    
    .movement-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border-left: 4px solid;
    }
    
    .movement-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
    }
    
    .movement-card.scheduled {
        border-left-color: #ffc107;
    }
    
    .movement-card.in-progress {
        border-left-color: #0dcaf0;
    }
    
    .movement-card.completed {
        border-left-color: #198754;
    }
    
    .movement-card.cancelled {
        border-left-color: #6c757d;
    }
    
    .equipment-thumbnail {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 0.25rem;
    }
    
    .user-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .timeline {
        position: relative;
        padding-left: 1.5rem;
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
        background-color: #0d6efd;
        border: 3px solid white;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestion des mouvements</h1>
        <div>
            <a href="{{ route('equipment-movements.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Nouveau mouvement
            </a>
            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#importMovementsModal">
                <i class="fas fa-file-import me-1"></i> Importer
            </button>
        </div>
    </div>
    
    <!-- Filtres -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Filtres de recherche</h6>
            <button class="btn btn-link text-primary" type="button" data-bs-toggle="collapse" data-bs-target="#filtersCollapse" aria-expanded="true" aria-controls="filtersCollapse">
                <i class="fas fa-filter"></i>
            </button>
        </div>
        <div class="collapse show" id="filtersCollapse">
            <div class="card-body">
                <form action="{{ route('equipment-movements.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Recherche</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Référence, équipement, responsable...">
                    </div>
                    
                    <div class="col-md-3">
                        <label for="status" class="form-label">Statut</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Tous les statuts</option>
                            <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Planifié</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>En cours</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Terminé</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulé</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="type" class="form-label">Type de mouvement</label>
                        <select class="form-select" id="type" name="type">
                            <option value="">Tous les types</option>
                            <option value="internal" {{ request('type') == 'internal' ? 'selected' : '' }}>Interne</option>
                            <option value="external" {{ request('type') == 'external' ? 'selected' : '' }}>Externe</option>
                            <option value="loan" {{ request('type') == 'loan' ? 'selected' : '' }}>Prêt</option>
                            <option value="maintenance" {{ request('type') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="date_range" class="form-label">Période</label>
                        <select class="form-select" id="date_range" name="date_range">
                            <option value="">Toutes les périodes</option>
                            <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Aujourd'hui</option>
                            <option value="this_week" {{ request('date_range') == 'this_week' ? 'selected' : '' }}>Cette semaine</option>
                            <option value="this_month" {{ request('date_range') == 'this_month' ? 'selected' : '' }}>Ce mois</option>
                            <option value="custom" {{ request('date_from') || request('date_to') ? 'selected' : '' }}>Personnalisée</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6 date-range-fields" style="display: {{ request('date_from') || request('date_to') ? 'block' : 'none' }};">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="date_from" class="form-label">Du</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" 
                                       value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-6">
                                <label for="date_to" class="form-label">Au</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" 
                                       value="{{ request('date_to') }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-1"></i> Appliquer
                        </button>
                        <a href="{{ route('equipment-movements.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-undo me-1"></i> Réinitialiser
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Cartes de statistiques -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Mouvements ce mois</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['this_month'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                En cours</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['in_progress'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-sync-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Prévus cette semaine</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['scheduled_this_week'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                En retard</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['overdue'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-8">
            <!-- Liste des mouvements -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Derniers mouvements</h6>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                                id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-download me-1"></i> Exporter
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                            <li><a class="dropdown-item" href="#" onclick="exportTo('pdf')">PDF</a></li>
                            <li><a class="dropdown-item" href="#" onclick="exportTo('excel')">Excel</a></li>
                            <li><a class="dropdown-item" href="#" onclick="exportTo('csv')">CSV</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    @if($movements->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-exchange-alt fa-4x text-muted mb-3"></i>
                            <h5 class="text-gray-800">Aucun mouvement trouvé</h5>
                            <p class="text-muted">Commencez par créer votre premier mouvement</p>
                            <a href="{{ route('equipment-movements.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Nouveau mouvement
                            </a>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($movements as $movement)
                                <div class="list-group-item px-0 py-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="d-flex">
                                            <div class="me-3 text-center" style="width: 60px;">
                                                <div class="text-muted small">
                                                    {{ $movement->scheduled_date->format('d M') }}
                                                </div>
                                                <div class="h5 mb-0">
                                                    {{ $movement->scheduled_date->format('H:i') }}
                                                </div>
                                                @if($movement->is_overdue && $movement->status == 'scheduled')
                                                    <span class="badge bg-danger mt-1">En retard</span>
                                                @endif
                                            </div>
                                            <div>
                                                <h6 class="mb-1">
                                                    <a href="{{ route('equipment-movements.show', $movement) }}" class="text-dark">
                                                        {{ $movement->reference }}
                                                    </a>
                                                </h6>
                                                <p class="mb-1">
                                                    <i class="fas fa-{{ $movement->type_icon }} me-1 text-{{ $movement->type_color }}"></i>
                                                    {{ $movement->type_label }}
                                                    
                                                    @if($movement->equipment)
                                                        <span class="mx-2">•</span>
                                                        <a href="{{ route('equipment.show', $movement->equipment) }}" class="text-primary">
                                                            <i class="fas fa-{{ $movement->equipment->category->icon ?? 'box' }} me-1"></i>
                                                            {{ $movement->equipment->name }}
                                                        </a>
                                                        
                                                        @if($movement->equipment->serial_number)
                                                            <small class="text-muted ms-1">
                                                                ({{ $movement->equipment->serial_number }})
                                                            </small>
                                                        @endif
                                                    @endif
                                                </p>
                                                <div class="small text-muted">
                                                    <i class="fas fa-user me-1"></i> 
                                                    {{ $movement->requester->name }}
                                                    
                                                    <span class="mx-2">•</span>
                                                    
                                                    @if($movement->origin_type === 'location' && $movement->origin_location)
                                                        <i class="fas fa-map-marker-alt me-1"></i>
                                                        {{ $movement->origin_location->name }}
                                                    @elseif($movement->origin_type === 'department' && $movement->origin_department)
                                                        <i class="fas fa-building me-1"></i>
                                                        {{ $movement->origin_department_name }}
                                                    @else
                                                        <i class="fas fa-external-link-alt me-1"></i>
                                                        {{ $movement->origin_department_name }}
                                                    @endif
                                                    
                                                    <i class="fas fa-arrow-right mx-2"></i>
                                                    
                                                    @if($movement->destination_type === 'location' && $movement->destination_location)
                                                        <i class="fas fa-map-marker-alt me-1"></i>
                                                        {{ $movement->destination_location->name }}
                                                    @elseif($movement->destination_type === 'department' && $movement->destination_department)
                                                        <i class="fas fa-building me-1"></i>
                                                        {{ $movement->destination_department_name }}
                                                    @else
                                                        <i class="fas fa-external-link-alt me-1"></i>
                                                        {{ $movement->destination_department_name }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-{{ $movement->status_color }} mb-2">
                                                {{ $movement->status_label }}
                                            </span>
                                            <div class="dropdown
                                                <button class="btn btn-link text-muted p-0" type="button" 
                                                        id="movementActions{{ $movement->id }}" data-bs-toggle="dropdown" 
                                                        aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="movementActions{{ $movement->id }}">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('equipment-movements.show', $movement) }}">
                                                        <i class="fas fa-eye me-2"></i> Voir les détails
                                                    </a>
                                                </li>
                                                @if($movement->status == 'scheduled' || $movement->status == 'in_progress')
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('equipment-movements.edit', $movement) }}">
                                                            <i class="fas fa-edit me-2"></i> Modifier
                                                        </a>
                                                    </li>
                                                    @if($movement->status == 'scheduled')
                                                        <li>
                                                            <form action="{{ route('equipment-movements.start', $movement) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="fas fa-play me-2"></i> Démarrer
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @elseif($movement->status == 'in_progress')
                                                        <li>
                                                            <form action="{{ route('equipment-movements.complete', $movement) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="fas fa-check me-2"></i> Terminer
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="{{ route('equipment-movements.cancel', $movement) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="dropdown-item text-danger" 
                                                                    onclick="return confirm('Êtes-vous sûr de vouloir annuler ce mouvement ?')">
                                                                <i class="fas fa-times me-2"></i> Annuler
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                                @if($movement->status == 'completed' || $movement->status == 'cancelled')
                                                    <li>
                                                        <form action="{{ route('equipment-movements.destroy', $movement) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger" 
                                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce mouvement ? Cette action est irréversible.')">
                                                                <i class="fas fa-trash-alt me-2"></i> Supprimer
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted">
                                Affichage de {{ $movements->firstItem() }} à {{ $movements->lastItem() }} sur {{ $movements->total() }} entrées
                            </div>
                            <nav>
                                {{ $movements->withQueryString()->links() }}
                            </nav>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Calendrier des mouvements -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Calendrier</h6>
                </div>
                <div class="card-body">
                    <div id="movementCalendar"></div>
                </div>
            </div>
            
            <!-- Mouvements en cours -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">En cours</h6>
                    <span class="badge bg-primary">{{ $inProgressMovements->count() }}</span>
                </div>
                <div class="card-body">
                    @if($inProgressMovements->isEmpty())
                        <div class="text-center py-3">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <p class="text-muted mb-0">Aucun mouvement en cours</p>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($inProgressMovements as $movement)
                                <a href="{{ route('equipment-movements.show', $movement) }}" 
                                   class="list-group-item list-group-item-action px-0 py-2">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="fas {{ $movement->type_icon }} fa-2x text-{{ $movement->type_color }}"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $movement->reference }}</h6>
                                            <p class="mb-0 small text-muted">
                                                <i class="fas fa-{{ $movement->equipment->category->icon ?? 'box' }} me-1"></i>
                                                {{ $movement->equipment->name }}
                                                @if($movement->equipment->serial_number)
                                                    <span class="ms-1">({{ $movement->equipment->serial_number }})</span>
                                                @endif
                                            </p>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-{{ $movement->status_color }} mb-1">
                                                {{ $movement->status_label }}
                                            </span>
                                            <div class="small text-muted">
                                                {{ $movement->started_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Mouvements à venir -->
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">À venir</h6>
                    <span class="badge bg-warning text-dark">{{ $upcomingMovements->count() }}</span>
                </div>
                <div class="card-body">
                    @if($upcomingMovements->isEmpty())
                        <div class="text-center py-3">
                            <i class="far fa-calendar-check fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Aucun mouvement prévu</p>
                        </div>
                    @else
                        <div class="timeline">
                            @foreach($upcomingMovements as $movement)
                                <div class="timeline-item">
                                    <div class="timeline-date small text-muted mb-1">
                                        {{ $movement->scheduled_date->format('d M Y - H:i') }}
                                        @if($movement->is_today)
                                            <span class="badge bg-info ms-1">Aujourd'hui</span>
                                        @elseif($movement->is_tomorrow)
                                            <span class="badge bg-info ms-1">Demain</span>
                                        @endif
                                    </div>
                                    <div class="timeline-content">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <strong>
                                                <a href="{{ route('equipment-movements.show', $movement) }}" class="text-dark">
                                                    {{ $movement->reference }}
                                                </a>
                                            </strong>
                                            <span class="badge bg-{{ $movement->type_color }}">
                                                {{ $movement->type_label }}
                                            </span>
                                        </div>
                                        <p class="mb-0 small">
                                            <i class="fas fa-{{ $movement->equipment->category->icon ?? 'box' }} me-1"></i>
                                            {{ $movement->equipment->name }}
                                        </p>
                                        <div class="small text-muted">
                                            <i class="fas fa-user me-1"></i> {{ $movement->requester->name }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modale d'importation -->
<div class="modal fade" id="importMovementsModal" tabindex="-1" aria-labelledby="importMovementsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('equipment-movements.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="importMovementsModalLabel">Importer des mouvements</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Téléchargez notre modèle Excel pour importer des mouvements.
                        <a href="{{ asset('templates/movements_import_template.xlsx') }}" class="btn btn-sm btn-outline-primary ms-2" download>
                            <i class="fas fa-download me-1"></i> Télécharger le modèle
                        </a>
                    </div>
                    
                    <div class="mb-3">
                        <label for="import_file" class="form-label">Fichier Excel à importer <span class="text-danger">*</span></label>
                        <input type="file" class="form-control @error('import_file') is-invalid @enderror" 
                               id="import_file" name="import_file" accept=".xlsx, .xls, .csv" required>
                        @error('import_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Formats acceptés : .xlsx, .xls, .csv (max: 5MB)</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="has_headers" name="has_headers" checked>
                            <label class="form-check-label" for="has_headers">
                                La première ligne contient les en-têtes
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-file-import me-1"></i> Importer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<!-- FullCalendar -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/locales/fr.js'></script>
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css' rel='stylesheet' />

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialiser le calendrier
        var calendarEl = document.getElementById('movementCalendar');
        if (calendarEl) {
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'fr',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: [
                    @foreach($calendarMovements as $event)
                    {
                        title: '{{ $event['title'] }}',
                        start: '{{ $event['start'] }}',
                        end: '{{ $event['end'] ?? null }}',
                        url: '{{ $event['url'] }}',
                        backgroundColor: '{{ $event['backgroundColor'] }}',
                        borderColor: '{{ $event['borderColor'] }}',
                        textColor: '{{ $event['textColor'] }}',
                        extendedProps: {
                            status: '{{ $event['extendedProps']['status'] ?? '' }}',
                            type: '{{ $event['extendedProps']['type'] ?? '' }}'
                        }
                    },
                    @endforeach
                ],
                eventClick: function(info) {
                    info.jsEvent.preventDefault();
                    if (info.event.url) {
                        window.location.href = info.event.url;
                    }
                },
                eventDidMount: function(info) {
                    // Ajouter un tooltip avec plus d'informations
                    if (info.event.extendedProps.status) {
                        const tooltip = new bootstrap.Tooltip(info.el, {
                            title: `
                                <strong>${info.event.title}</strong><br>
                                Type: ${info.event.extendedProps.type}<br>
                                Statut: ${info.event.extendedProps.status}
                            `,
                            html: true,
                            placement: 'top',
                            trigger: 'hover',
                            container: 'body'
                        });
                    }
                }
            });
            calendar.render();
        }
        
        // Gestion de l'affichage des champs de date personnalisée
        const dateRangeSelect = document.getElementById('date_range');
        const dateRangeFields = document.querySelector('.date-range-fields');
        
        if (dateRangeSelect) {
            dateRangeSelect.addEventListener('change', function() {
                if (this.value === 'custom') {
                    dateRangeFields.style.display = 'block';
                } else {
                    dateRangeFields.style.display = 'none';
                    document.getElementById('date_from').value = '';
                    document.getElementById('date_to').value = '';
                }
            });
        }
        
        // Fonction d'exportation
        window.exportTo = function(format) {
            const url = new URL(window.location.href);
            url.searchParams.set('export', format);
            window.open(url.toString(), '_blank');
        };
    });
</script>

<style>
    /* Personnalisation du calendrier */
    #movementCalendar {
        font-size: 0.875rem;
    }
    
    .fc .fc-toolbar-title {
        font-size: 1.25rem;
    }
    
    .fc .fc-button {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    .fc-event {
        font-size: 0.7rem;
        padding: 0.1rem 0.25rem;
        margin-bottom: 0.1rem;
        cursor: pointer;
    }
    
    .fc-event:hover {
        opacity: 0.9;
    }
    
    .fc-daygrid-event-dot {
        margin-right: 0.25rem;
    }
    
    .fc-daygrid-day-number {
        padding: 0.25rem;
    }
    
    /* Ajustement pour les petits écrans */
    @media (max-width: 767.98px) {
        .fc-toolbar.fc-header-toolbar {
            display: block;
            text-align: center;
        }
        
        .fc-toolbar.fc-header-toolbar .fc-toolbar-chunk {
            display: block;
            margin-bottom: 0.5rem;
        }
    }
</style>
@endpush
@endsection
