@extends('layouts.maquette')

@section('title', 'Gestion des maintenances')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    .status-badge {
        font-size: 0.8rem;
        padding: 0.35em 0.65em;
    }
    
    .priority-high {
        color: #dc3545;
        font-weight: bold;
    }
    
    .priority-medium {
        color: #ffc107;
        font-weight: bold;
    }
    
    .priority-low {
        color: #28a745;
    }
    
    .filters-card {
        margin-bottom: 1.5rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-tools me-2"></i> Gestion des maintenances
        </h1>
        <div>
            <a href="{{ route('maintenance.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Nouvelle maintenance
            </a>
        </div>
    </div>
    
    <!-- Filtres -->
    <div class="card shadow mb-4 filters-card">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter me-2"></i>Filtres
            </h6>
            <button class="btn btn-sm btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#filtersCollapse">
                <i class="fas fa-chevron-down"></i>
            </button>
        </div>
        <div class="collapse show" id="filtersCollapse">
            <div class="card-body">
                <form method="GET" action="{{ route('maintenance.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label for="status" class="form-label">Statut</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Tous les statuts</option>
                            <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Planifiée</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>En cours</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Terminée</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulée</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="type" class="form-label">Type de maintenance</label>
                        <select class="form-select" id="type" name="type">
                            <option value="">Tous les types</option>
                            <option value="preventive" {{ request('type') == 'preventive' ? 'selected' : '' }}>Préventive</option>
                            <option value="corrective" {{ request('type') == 'corrective' ? 'selected' : '' }}>Corrective</option>
                            <option value="inspection" {{ request('type') == 'inspection' ? 'selected' : '' }}>Inspection</option>
                            <option value="calibration" {{ request('type') == 'calibration' ? 'selected' : '' }}>Étalonnage</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="equipment_id" class="form-label">Équipement</label>
                        <select class="form-select" id="equipment_id" name="equipment_id">
                            <option value="">Tous les équipements</option>
                            @foreach($equipments as $equipment)
                                <option value="{{ $equipment->id }}" {{ request('equipment_id') == $equipment->id ? 'selected' : '' }}>
                                    {{ $equipment->name }} ({{ $equipment->serial_number }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="assigned_to" class="form-label">Assigné à</label>
                        <select class="form-select" id="assigned_to" name="assigned_to">
                            <option value="">Tous les techniciens</option>
                            @foreach($technicians as $technician)
                                <option value="{{ $technician->id }}" {{ request('assigned_to') == $technician->id ? 'selected' : '' }}>
                                    {{ $technician->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="start_date" class="form-label">Date de début</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                    </div>
                    
                    <div class="col-md-3">
                        <label for="end_date" class="form-label">Date de fin</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                    </div>
                    
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-filter me-1"></i> Filtrer
                        </button>
                        <a href="{{ route('maintenance.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-undo me-1"></i> Réinitialiser
                        </a>
                        @if(request()->hasAny(['status', 'type', 'equipment_id', 'assigned_to', 'start_date', 'end_date']))
                            <a href="{{ route('maintenance.export', request()->query()) }}" class="btn btn-success float-end">
                                <i class="fas fa-file-export me-1"></i> Exporter
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Liste des maintenances -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>Liste des maintenances
            </h6>
            <span class="badge bg-primary">{{ $maintenances->total() }} {{ Str::plural('maintenance', $maintenances->total()) }}</span>
        </div>
        <div class="card-body">
            @if($maintenances->isEmpty())
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-2"></i> Aucune maintenance trouvée avec les critères sélectionnés.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Référence</th>
                                <th>Équipement</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Date prévue</th>
                                <th>Statut</th>
                                <th>Priorité</th>
                                <th>Assigné à</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($maintenances as $maintenance)
                                <tr>
                                    <td>{{ $maintenance->reference }}</td>
                                    <td>
                                        <a href="{{ route('equipment.show', $maintenance->equipment) }}">
                                            {{ $maintenance->equipment->name }}
                                        </a>
                                        <div class="text-muted small">{{ $maintenance->equipment->serial_number }}</div>
                                    </td>
                                    <td>
                                        @php
                                            $typeBadges = [
                                                'preventive' => 'info',
                                                'corrective' => 'warning',
                                                'inspection' => 'primary',
                                                'calibration' => 'success'
                                            ];
                                            $badgeClass = $typeBadges[$maintenance->maintenance_type] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $badgeClass }}">
                                            {{ __("maintenance.types.{$maintenance->maintenance_type}") }}
                                        </span>
                                    </td>
                                    <td>{{ Str::limit($maintenance->description, 50) }}</td>
                                    <td>{{ $maintenance->scheduled_date->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @php
                                            $statusBadges = [
                                                'scheduled' => 'info',
                                                'in_progress' => 'primary',
                                                'completed' => 'success',
                                                'cancelled' => 'secondary'
                                            ];
                                            $badgeClass = $statusBadges[$maintenance->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $badgeClass }}">
                                            {{ __("maintenance.status.{$maintenance->status}") }}
                                        </span>
                                    </td>
                                    <td class="priority-{{ $maintenance->priority }}">
                                        {{ __("maintenance.priorities.{$maintenance->priority}") }}
                                    </td>
                                    <td>
                                        @if($maintenance->assignedTo)
                                            {{ $maintenance->assignedTo->name }}
                                        @else
                                            <span class="text-muted">Non assigné</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('maintenance.show', $maintenance) }}" class="btn btn-info" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @can('update', $maintenance)
                                                <a href="{{ route('maintenance.edit', $maintenance) }}" class="btn btn-primary" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan
                                            @can('delete', $maintenance)
                                                <button type="button" class="btn btn-danger" title="Supprimer" 
                                                    data-bs-toggle="modal" data-bs-target="#deleteModal{{ $maintenance->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endcan
                                        </div>
                                        
                                        <!-- Modal de suppression -->
                                        @can('delete', $maintenance)
                                            <div class="modal fade" id="deleteModal{{ $maintenance->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Confirmer la suppression</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Êtes-vous sûr de vouloir supprimer la maintenance <strong>{{ $maintenance->reference }}</strong> ?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                            <form action="{{ route('maintenance.destroy', $maintenance) }}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger">Supprimer</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Affichage de {{ $maintenances->firstItem() }} à {{ $maintenances->lastItem() }} sur {{ $maintenances->total() }} maintenances
                    </div>
                    {{ $maintenances->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialiser Select2
        $('select').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
        
        // Gérer l'affichage/masquage des filtres
        $('[data-bs-toggle="collapse"]').on('click', function() {
            const icon = $(this).find('i');
            icon.toggleClass('fa-chevron-down fa-chevron-up');
        });
    });
</script>
@endpush
