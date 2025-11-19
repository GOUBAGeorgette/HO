@extends('layouts.maquette')

@section('title', 'Liste des équipements')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Liste des équipements</h3>
                    <div>
                        <a href="{{ route('equipment.export') }}" class="btn btn-sm btn-outline-secondary mr-2">
                            <i class="fas fa-file-export"></i> Exporter
                        </a>
                        <a href="{{ route('import') }}" class="btn btn-sm btn-success mr-2">
                            <i class="fas fa-file-import"></i> Importer
                        </a>
                        <a href="{{ route('equipment.create') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> Ajouter
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    @include('equipment._filters')
                    
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>Catégorie</th>
                                    <th>Nom</th>
                                    <th>Modèle</th>
                                    <th>Marque</th>
                                    <th>Type</th>
                                    <th>Quantité</th>
                                    <th>État</th>
                                    <th>Emplacement</th>
                                    <th>Utilisabilité</th>
                                    <th>Responsable</th>
                                    <th>Remarque</th>
                                    <th>Suggestions</th>
                                    <th>Fréquence de maintenance</th>
                                    <th>Tâches de maintenance</th>
                                    <th>Type de maintenance</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($equipment as $item)
                                    <tr>
                                        <td>{{ $item->category->name ?? '-' }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->model }}</td>
                                        <td>{{ $item->brand }}</td>
                                        <td>{{ $item->type ?? '-' }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>
                                            @php
                                                $statusClasses = [
                                                    'excellent' => 'success',
                                                    'bon' => 'info',
                                                    'moyen' => 'warning',
                                                    'mauvais' => 'warning',
                                                    'hors_service' => 'danger'
                                                ];
                                                $statusLabels = [
                                                    'excellent' => 'Excellent',
                                                    'bon' => 'Bon',
                                                    'moyen' => 'Moyen',
                                                    'mauvais' => 'Mauvais',
                                                    'hors_service' => 'Hors service'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $statusClasses[$item->status] ?? 'secondary' }}">
                                                {{ $statusLabels[$item->status] ?? $item->status }}
                                            </span>
                                        </td>
                                        <td>{{ $item->location ?? '-' }}</td>
                                        <td>
                                            @if($item->is_usable)
                                                <span class="badge bg-success">Utilisable</span>
                                            @else
                                                <span class="badge bg-danger">Non utilisable</span>
                                            @endif
                                        </td>
                                        <td>{{ $item->responsible_person ?? '-' }}</td>
                                        <td>{{ $item->notes ?? '-' }}</td>
                                        <td>{{ $item->suggestions ?? '-' }}</td>
                                        <td>{{ $item->maintenance_frequency ?? '-' }}</td>
                                        <td>{{ $item->maintenance_tasks ?? '-' }}</td>
                                        <td>{{ $item->maintenance_type ?? '-' }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('equipment.show', $item) }}" 
                                                   class="btn btn-info" 
                                                   title="Voir">
                                                    <i class="fe fe-eye"></i>
                                                </a>
                                                <a href="{{ route('equipment.edit', $item) }}" 
                                                   class="btn btn-warning" 
                                                   title="Modifier">
                                                    <i class="fe fe-edit"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-danger" 
                                                        title="Supprimer"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#deleteModal{{ $item->id }}">
                                                    <i class="fe fe-trash"></i>
                                                </button>
                                            </div>
                                            
                                            <!-- Modal de suppression -->
                                            <div class="modal fade" id="deleteModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Confirmer la suppression</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Êtes-vous sûr de vouloir supprimer l'équipement <strong>{{ $item->name }}</strong> ?</p>
                                                            <p class="text-danger">Cette action est irréversible.</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                            <form action="{{ route('equipment.destroy', $item) }}" method="POST">
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
                                @empty
                                    <tr>
                                        <td colspan="16" class="text-center">Aucun équipement trouvé</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center mt-3">
                        {{ $equipment->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .badge {
        font-size: 0.8rem;
        font-weight: 500;
        padding: 0.35em 0.65em;
    }
    
    .btn-group-sm > .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    .table th {
        white-space: nowrap;
    }
</style>
@endpush
