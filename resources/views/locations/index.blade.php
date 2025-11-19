@extends('layouts.maquette')

@section('title', 'Gestion des emplacements')

@push('styles')
<style>
    .location-badge {
        font-size: 0.8rem;
        padding: 0.35em 0.65em;
    }
    
    .location-tree {
        list-style: none;
        padding-left: 1.5rem;
    }
    
    .location-tree li {
        position: relative;
        padding: 0.25rem 0;
    }
    
    .location-tree li:before {
        content: '';
        position: absolute;
        left: -1rem;
        top: 1rem;
        width: 0.75rem;
        border-top: 1px solid #dee2e6;
    }
    
    .location-tree li:after {
        content: '';
        position: absolute;
        left: -1rem;
        top: 0;
        bottom: 0;
        border-left: 1px solid #dee2e6;
    }
    
    .location-tree li:last-child:after {
        height: 1rem;
    }
    
    .location-tree li:first-child:after {
        top: 1rem;
    }
    
    .location-tree > li:first-child:after {
        top: 1.5rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestion des emplacements</h1>
        <a href="{{ route('locations.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Nouvel emplacement
        </a>
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
                <form action="{{ route('locations.index') }}" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Recherche</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Nom, code ou description...">
                    </div>
                    
                    <div class="col-md-3">
                        <label for="type" class="form-label">Type</label>
                        <select class="form-select" id="type" name="type">
                            <option value="">Tous les types</option>
                            <option value="building" {{ request('type') == 'building' ? 'selected' : '' }}>Bâtiment</option>
                            <option value="room" {{ request('type') == 'room' ? 'selected' : '' }}>Salle</option>
                            <option value="shelf" {{ request('type') == 'shelf' ? 'selected' : '' }}>Étagère</option>
                            <option value="desk" {{ request('type') == 'desk' ? 'selected' : '' }}>Bureau</option>
                            <option value="warehouse" {{ request('type') == 'warehouse' ? 'selected' : '' }}>Entrepôt</option>
                            <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>Autre</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="status" class="form-label">Statut</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Tous les statuts</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                            <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>En maintenance</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-1"></i> Appliquer
                        </button>
                        <a href="{{ route('locations.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-undo me-1"></i>
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
                                Emplacements actifs</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_count'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-map-marker-alt fa-2x text-gray-300"></i>
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
                                Équipements enregistrés</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['equipment_count'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-laptop fa-2x text-gray-300"></i>
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
                                Types d'emplacements</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['type_count'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tags fa-2x text-gray-300"></i>
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
                                Emplacements vides</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['empty_count'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box-open fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Liste des emplacements -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Liste des emplacements</h6>
            <div>
                <button type="button" class="btn btn-sm btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#importLocationsModal">
                    <i class="fas fa-file-import me-1"></i> Importer
                </button>
                <a href="#" class="btn btn-sm btn-outline-success" onclick="showExportModal()">
                    <i class="fas fa-file-export me-1"></i> Exporter
                </a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                </div>
            @endif
            
            @if($locations->isEmpty())
                <div class="text-center py-4">
                    <i class="fas fa-map-marker-alt fa-4x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-800">Aucun emplacement trouvé</h5>
                    <p class="text-muted">Commencez par ajouter votre premier emplacement</p>
                    <a href="{{ route('locations.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Ajouter un emplacement
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="locationsTable" width="100%" cellspacing="0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                    </div>
                                </th>
                                <th>Nom</th>
                                <th>Code</th>
                                <th>Type</th>
                                <th>Parent</th>
                                <th>Équipements</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($locations as $location)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input location-checkbox" type="checkbox" name="selected_locations[]" value="{{ $location->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('locations.show', $location) }}" class="text-primary">
                                            <i class="fas {{ $location->getIcon() }} me-1"></i>
                                            {{ $location->name }}
                                        </a>
                                    </td>
                                    <td>{{ $location->code ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $location->getTypeColor() }}">
                                            {{ $location->getTypeLabel() }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($location->parent)
                                            <a href="{{ route('locations.show', $location->parent) }}" class="text-muted">
                                                <i class="fas {{ $location->parent->getIcon() }} me-1"></i>
                                                {{ $location->parent->name }}
                                            </a>
                                        @else
                                            <span class="text-muted">Aucun</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">
                                            {{ $location->equipments_count }} équipement(s)
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $location->is_active ? 'success' : 'secondary' }}">
                                            {{ $location->is_active ? 'Actif' : 'Inactif' }}
                                        </span>
                                        @if($location->is_under_maintenance)
                                            <span class="badge bg-warning text-dark">Maintenance</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('locations.show', $location) }}" class="btn btn-info" data-bs-toggle="tooltip" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('locations.edit', $location) }}" class="btn btn-primary" data-bs-toggle="tooltip" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" 
                                                    data-bs-target="#deleteLocationModal{{ $location->id }}" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        
                                        <!-- Modal de suppression -->
                                        <div class="modal fade" id="deleteLocationModal{{ $location->id }}" tabindex="-1" 
                                             aria-labelledby="deleteLocationModalLabel{{ $location->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title" id="deleteLocationModalLabel{{ $location->id }}">
                                                            Confirmer la suppression
                                                        </h5>
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
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Affichage de {{ $locations->firstItem() }} à {{ $locations->lastItem() }} sur {{ $locations->total() }} entrées
                    </div>
                    <nav>
                        {{ $locations->withQueryString()->links() }}
                    </nav>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Carte des emplacements -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Carte des emplacements</h6>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="mapViewOptions" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-cog"></i> Options
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="mapViewOptions">
                    <li><a class="dropdown-item" href="#" id="expandAll">Tout développer</a></li>
                    <li><a class="dropdown-item" href="#" id="collapseAll">Tout réduire</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#" id="showActiveOnly">Afficher uniquement les actifs</a></li>
                    <li><a class="dropdown-item" href="#" id="showAll">Tout afficher</a></li>
                </ul>
            </div>
        </div>
        <div class="card-body">
            <div id="locationTree">
                {!! $locationTree !!}
            </div>
        </div>
    </div>
</div>

<!-- Modale d'importation -->
@include('locations._import')

<!-- Modale d'exportation -->
@include('locations._export')

<!-- Modale de suppression en masse -->
<div class="modal fade" id="deleteSelectedModal" tabindex="-1" aria-labelledby="deleteSelectedModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteSelectedModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer les emplacements sélectionnés ?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Cette action est irréversible. Tous les équipements seront déplacés vers l'emplacement parent.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Annuler
                </button>
                <form action="{{ route('locations.destroy.multiple') }}" method="POST" id="deleteSelectedForm">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="selected_ids" id="selectedIds" value="">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-1"></i> Supprimer la sélection
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Activer les tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Gestion de la sélection multiple
        const selectAllCheckbox = document.getElementById('selectAll');
        const locationCheckboxes = document.querySelectorAll('.location-checkbox');
        const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
        
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                const isChecked = this.checked;
                locationCheckboxes.forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
                updateSelectedCount();
            });
        }
        
        locationCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateSelectedCount();
            });
        });
        
        function updateSelectedCount() {
            const selectedCount = document.querySelectorAll('.location-checkbox:checked').length;
            const selectedIds = [];
            
            document.querySelectorAll('.location-checkbox:checked').forEach(checkbox => {
                selectedIds.push(checkbox.value);
            });
            
            // Mettre à jour le champ caché avec les IDs sélectionnés
            document.getElementById('selectedIds').value = selectedIds.join(',');
            
            // Activer/désactiver le bouton de suppression multiple
            if (deleteSelectedBtn) {
                deleteSelectedBtn.disabled = selectedCount === 0;
                
                if (selectedCount > 0) {
                    deleteSelectedBtn.innerHTML = `<i class="fas fa-trash-alt me-1"></i> Supprimer (${selectedCount})`;
                } else {
                    deleteSelectedBtn.innerHTML = '<i class="fas fa-trash-alt me-1"></i> Supprimer la sélection';
                }
            }
            
            // Mettre à jour la case à cocher "Tout sélectionner"
            if (selectAllCheckbox && locationCheckboxes.length > 0) {
                const allChecked = selectedCount === locationCheckboxes.length;
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = selectedCount > 0 && !allChecked;
            }
        }
        
        // Gestion de l'arborescence
        const expandAllBtn = document.getElementById('expandAll');
        const collapseAllBtn = document.getElementById('collapseAll');
        const showActiveOnlyBtn = document.getElementById('showActiveOnly');
        const showAllBtn = document.getElementById('showAll');
        
        if (expandAllBtn) {
            expandAllBtn.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.location-tree .collapse').forEach(el => {
                    new bootstrap.Collapse(el, { toggle: true });
                });
            });
        }
        
        if (collapseAllBtn) {
            collapseAllBtn.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.location-tree .collapse.show').forEach(el => {
                    new bootstrap.Collapse(el, { hide: true });
                });
            });
        }
        
        if (showActiveOnlyBtn) {
            showActiveOnlyBtn.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.location-item').forEach(item => {
                    if (!item.classList.contains('active')) {
                        item.style.display = 'none';
                    } else {
                        item.style.display = '';
                        // S'assurer que les parents sont visibles
                        let parent = item.closest('.collapse').closest('.location-item');
                        while (parent) {
                            parent.style.display = '';
                            const collapse = parent.querySelector('.collapse');
                            if (collapse && !collapse.classList.contains('show')) {
                                new bootstrap.Collapse(collapse, { toggle: true });
                            }
                            parent = parent.closest('.collapse')?.closest('.location-item');
                        }
                    }
                });
            });
        }
        
        if (showAllBtn) {
            showAllBtn.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.location-item').forEach(item => {
                    item.style.display = '';
                });
            });
        }
    });
    
    // Fonction pour afficher la modale d'exportation
    function showExportModal() {
        const modal = new bootstrap.Modal(document.getElementById('exportLocationsModal'));
        modal.show();
    }
    
    // Fonction pour confirmer la suppression de plusieurs emplacements
    function confirmDeleteSelected() {
        const selectedCount = document.querySelectorAll('.location-checkbox:checked').length;
        if (selectedCount === 0) {
            alert('Veuillez sélectionner au moins un emplacement à supprimer.');
            return false;
        }
        
        const modal = new bootstrap.Modal(document.getElementById('deleteSelectedModal'));
        modal.show();
        return false;
    }
</script>
@endpush
