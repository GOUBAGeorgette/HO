@extends('layouts.maquette')

@section('title', 'Mouvements d\'équipements')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Mouvements d'équipements</h1>
        <a href="{{ route('equipment-movements.create') }}" class="d-none d-sm-inline-block btn btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Nouveau mouvement
        </a>
    </div>

    <!-- Filtres -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtres</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('equipment-movements.index') }}" method="GET" class="form-inline">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="equipment_id" class="form-label">Équipement</label>
                        <select name="equipment_id" id="equipment_id" class="form-select">
                            <option value="">Tous les équipements</option>
                            @foreach($equipmentList as $equipment)
                                <option value="{{ $equipment->id }}" {{ request('equipment_id') == $equipment->id ? 'selected' : '' }}>
                                    {{ $equipment->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Statut</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">Tous les statuts</option>
                            @foreach(\App\Enums\MovementStatus::cases() as $status)
                                <option value="{{ $status->value }}" {{ request('status') == $status->value ? 'selected' : '' }}>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">Du</label>
                        <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">Au</label>
                        <div class="input-group">
                            <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Filtrer
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des mouvements -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Liste des mouvements</h6>
            <div>
                <a href="{{ route('equipment-movements.export', request()->query()) }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-file-export fa-sm"></i> Exporter
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Équipement</th>
                            <th>Depuis</th>
                            <th>Vers</th>
                            <th>Date prévue</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movements as $movement)
                            <tr>
                                <td>{{ $movement->id }}</td>
                                <td>{{ $movement->equipment->name ?? 'N/A' }}</td>
                                <td>{{ $movement->fromLocation->name ?? 'N/A' }}</td>
                                <td>{{ $movement->toLocation->name ?? 'N/A' }}</td>
                                <td>{{ $movement->expected_at?->format('d/m/Y H:i') ?? 'Non défini' }}</td>
                                <td>
                                    <span class="badge bg-{{ $movement->status->color() }}">
                                        {{ $movement->status->label() }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('equipment-movements.show', $movement) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if($movement->canBeApproved())
                                            <form action="{{ route('equipment-movements.approve', $movement) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" title="Approuver">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if($movement->canBeStarted())
                                            <form action="{{ route('equipment-movements.start', $movement) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-warning" title="Démarrer">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if($movement->canBeCompleted())
                                            <form action="{{ route('equipment-movements.complete', $movement) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-primary" title="Terminer">
                                                    <i class="fas fa-flag-checkered"></i>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if($movement->canBeCancelled())
                                            <form action="{{ route('equipment-movements.cancel', $movement) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Annuler">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Aucun mouvement trouvé</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                
                {{ $movements->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialisation des sélecteurs avec Select2 si disponible
    $(document).ready(function() {
        if ($.fn.select2) {
            $('#equipment_id, #status').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        }
    });
</script>
@endpush
