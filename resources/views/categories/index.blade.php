@extends('layouts.maquette')

@section('title', 'Gestion des catégories')

@section('content')
<div class="container-fluid">
    <!-- En-tête de page -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestion des catégories</h1>
        <div>
            <a href="{{ route('categories.export') }}" class="btn btn-sm btn-outline-secondary mr-2">
                <i class="fas fa-file-export"></i> Exporter
            </a>
            <a href="{{ route('categories.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> Nouvelle catégorie
            </a>
        </div>
    </div>

    <!-- Carte principale -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Liste des catégories</h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="fas fa-file-import fa-sm fa-fw mr-2 text-gray-400"></i> Importer
                    </a></li>
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#exportModal">
                        <i class="fas fa-file-export fa-sm fa-fw mr-2 text-gray-400"></i> Exporter la sélection
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulkDeleteModal">
                        <i class="fas fa-trash-alt fa-sm fa-fw mr-2 text-danger"></i> Supprimer la sélection
                    </a></li>
                </ul>
            </div>
        </div>
        
        <div class="card-body">
            <!-- Filtres -->
            <form method="GET" action="{{ route('categories.index') }}" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text" 
                               class="form-control form-control-sm" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Rechercher une catégorie...">
                    </div>
                    
                    <div class="col-md-3">
                        <select class="form-select form-select-sm" name="parent_id">
                            <option value="">Toutes les catégories</option>
                            @foreach($parentCategories as $category)
                                <option value="{{ $category->id }}" {{ request('parent_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <select class="form-select form-select-sm" name="status">
                            <option value="">Tous les statuts</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-search fa-sm"></i> Filtrer
                        </button>
                    </div>
                </div>
                
                <div class="row mt-2">
                    <div class="col-12 text-end">
                        <a href="{{ route('categories.index') }}" class="btn btn-sm btn-link p-0 text-decoration-none">
                            <i class="fas fa-sync-alt fa-sm"></i> Réinitialiser les filtres
                        </a>
                    </div>
                </div>
            </form>
            
            <!-- Tableau des catégories -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="categoriesTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th width="40">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                </div>
                            </th>
                            <th>@sortablelink('name', 'Nom')</th>
                            <th>Description</th>
                            <th>Parent</th>
                            <th>Équipements</th>
                            <th>Statut</th>
                            <th>Dernière mise à jour</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input row-checkbox" type="checkbox" value="{{ $category->id }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($category->image_path)
                                            <img src="{{ asset('storage/' . $category->image_path) }}" 
                                                 alt="{{ $category->name }}" 
                                                 class="img-thumbnail me-2" 
                                                 style="width: 30px; height: 30px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center me-2" 
                                                 style="width: 30px; height: 30px;">
                                                <i class="fas fa-folder text-muted"></i>
                                            </div>
                                        @endif
                                        <a href="{{ route('categories.show', $category) }}" class="text-decoration-none">
                                            {{ $category->name }}
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    {{ $category->description ? Str::limit($category->description, 50) : '-' }}
                                </td>
                                <td>
                                    @if($category->parent)
                                        <a href="{{ route('categories.show', $category->parent) }}" class="text-decoration-none">
                                            {{ $category->parent->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary">
                                        {{ $category->equipments_count }} équipement(s)
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $category->is_active ? 'success' : 'secondary' }}">
                                        {{ $category->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                                <td>
                                    <span data-bs-toggle="tooltip" data-bs-placement="top" 
                                          title="{{ $category->updated_at->format('d/m/Y H:i') }}">
                                        {{ $category->updated_at->diffForHumans() }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('categories.show', $category) }}" 
                                           class="btn btn-info" 
                                           data-bs-toggle="tooltip" 
                                           title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('categories.edit', $category) }}" 
                                           class="btn btn-warning" 
                                           data-bs-toggle="tooltip" 
                                           title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-danger" 
                                                data-bs-toggle="tooltip" 
                                                title="Supprimer"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal{{ $category->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Modal de suppression -->
                                    <div class="modal fade" id="deleteModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Confirmer la suppression</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Êtes-vous sûr de vouloir supprimer la catégorie <strong>{{ $category->name }}</strong> ?</p>
                                                    
                                                    @if($category->children->count() > 0)
                                                        <div class="alert alert-warning">
                                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                                            Cette catégorie contient {{ $category->children->count() }} sous-catégorie(s). 
                                                            La suppression les affectera également.
                                                        </div>
                                                    @endif
                                                    
                                                    @if($category->equipments_count > 0)
                                                        <div class="alert alert-warning">
                                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                                            Cette catégorie est utilisée par {{ $category->equipments_count }} équipement(s).
                                                            Vous devez d'abord déplacer ou supprimer ces équipements.
                                                        </div>
                                                    @endif
                                                    
                                                    <p class="text-danger">Cette action est irréversible.</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger" {{ $category->equipments_count > 0 ? 'disabled' : '' }}>
                                                            <i class="fas fa-trash"></i> Confirmer la suppression
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-folder-open fa-3x text-gray-400 mb-3"></i>
                                    <p class="text-muted mb-0">Aucune catégorie trouvée</p>
                                    <a href="{{ route('categories.create') }}" class="btn btn-primary mt-3">
                                        <i class="fas fa-plus"></i> Créer une catégorie
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($categories->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Affichage de {{ $categories->firstItem() }} à {{ $categories->lastItem() }} sur {{ $categories->total() }} entrées
                    </div>
                    <div>
                        {{ $categories->withQueryString()->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal d'import -->
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('categories.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Importer des catégories</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file" class="form-label">Fichier CSV/Excel</label>
                        <input class="form-control" type="file" id="file" name="file" required>
                        <div class="form-text">
                            Format attendu : Nom, Description, Catégorie parente (optionnel), Statut (actif/inactif)
                        </div>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="updateExisting" name="update_existing">
                        <label class="form-check-label" for="updateExisting">
                            Mettre à jour les catégories existantes
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ asset('templates/categories_import_template.csv') }}" class="btn btn-outline-secondary me-auto">
                        <i class="fas fa-file-download"></i> Télécharger le modèle
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-file-import"></i> Importer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal d'export -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('categories.export') }}" method="POST" id="exportForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Exporter les catégories</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="exportFormat" class="form-label">Format</label>
                        <select class="form-select" id="exportFormat" name="format" required>
                            <option value="csv">CSV</option>
                            <option value="xlsx">Excel (XLSX)</option>
                            <option value="pdf">PDF</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Colonnes à exporter</label>
                        <div class="border rounded p-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="colName" name="columns[]" value="name" checked>
                                <label class="form-check-label" for="colName">
                                    Nom
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="colDescription" name="columns[]" value="description" checked>
                                <label class="form-check-label" for="colDescription">
                                    Description
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="colParent" name="columns[]" value="parent" checked>
                                <label class="form-check-label" for="colParent">
                                    Catégorie parente
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="colStatus" name="columns[]" value="status" checked>
                                <label class="form-check-label" for="colStatus">
                                    Statut
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="colEquipments" name="columns[]" value="equipments_count" checked>
                                <label class="form-check-label" for="colEquipments">
                                    Nombre d'équipements
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="colCreatedAt" name="columns[]" value="created_at">
                                <label class="form-check-label" for="colCreatedAt">
                                    Date de création
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="colUpdatedAt" name="columns[]" value="updated_at">
                                <label class="form-check-label" for="colUpdatedAt">
                                    Dernière mise à jour
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="exportSelected" name="export_selected">
                        <label class="form-check-label" for="exportSelected">
                            Exporter uniquement les éléments sélectionnés
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-file-export"></i> Exporter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de suppression en masse -->
<div class="modal fade" id="bulkDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('categories.bulk-delete') }}" method="POST" id="bulkDeleteForm">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title">Confirmer la suppression multiple</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer les catégories sélectionnées ?</p>
                    <p class="text-danger">Cette action est irréversible et supprimera également toutes les sous-catégories associées.</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Les catégories contenant des équipements ne pourront pas être supprimées.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Supprimer la sélection
                    </button>
                </div>
            </form>
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
        
        // Gestion de la sélection/désélection de toutes les lignes
        const selectAllCheckbox = document.getElementById('selectAll');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');
        
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                rowCheckboxes.forEach(checkbox => {
                    checkbox.checked = selectAllCheckbox.checked;
                });
            });
            
            // Désélectionner "Tout sélectionner" si une case est décochée
            rowCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    if (!this.checked) {
                        selectAllCheckbox.checked = false;
                    } else {
                        // Vérifier si toutes les cases sont cochées
                        const allChecked = Array.from(rowCheckboxes).every(cb => cb.checked);
                        selectAllCheckbox.checked = allChecked;
                    }
                });
            });
        }
        
        // Gestion du formulaire d'export
        const exportForm = document.getElementById('exportForm');
        const exportSelectedCheckbox = document.getElementById('exportSelected');
        
        if (exportForm && exportSelectedCheckbox) {
            exportForm.addEventListener('submit', function(e) {
                if (exportSelectedCheckbox.checked) {
                    const selectedIds = Array.from(document.querySelectorAll('.row-checkbox:checked'))
                        .map(checkbox => checkbox.value);
                    
                    if (selectedIds.length === 0) {
                        e.preventDefault();
                        alert('Veuillez sélectionner au moins une catégorie à exporter.');
                        return false;
                    }
                    
                    // Ajouter les IDs sélectionnés au formulaire
                    selectedIds.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'selected_ids[]';
                        input.value = id;
                        exportForm.appendChild(input);
                    });
                }
            });
        }
        
        // Gestion du formulaire de suppression en masse
        const bulkDeleteForm = document.getElementById('bulkDeleteForm');
        
        if (bulkDeleteForm) {
            bulkDeleteForm.addEventListener('submit', function(e) {
                const selectedIds = Array.from(document.querySelectorAll('.row-checkbox:checked'))
                    .map(checkbox => checkbox.value);
                
                if (selectedIds.length === 0) {
                    e.preventDefault();
                    alert('Veuillez sélectionner au moins une catégorie à supprimer.');
                    return false;
                }
                
                // Ajouter les IDs sélectionnés au formulaire
                selectedIds.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = id;
                    bulkDeleteForm.appendChild(input);
                });
                
                return confirm('Êtes-vous sûr de vouloir supprimer les catégories sélectionnées ? Cette action est irréversible.');
            });
        }
    });
</script>
@endpush

@push('styles')
<style>
    .table th {
        white-space: nowrap;
        vertical-align: middle;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .btn-group-sm > .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    .form-check-input:checked {
        background-color: #4e73df;
        border-color: #4e73df;
    }
    
    .badge {
        font-size: 0.8rem;
        font-weight: 500;
        padding: 0.35em 0.65em;
    }
    
    .dropdown-menu {
        font-size: 0.85rem;
    }
    
    .dropdown-item {
        padding: 0.35rem 1.25rem;
    }
    
    .dropdown-item i {
        width: 1.2em;
        text-align: center;
    }
</style>
@endpush
@endsection
