@if($maintenance->parts->isNotEmpty() || (isset($showEmpty) && $showEmpty))
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-cogs me-2"></i>Pièces utilisées
                @if($maintenance->parts->isNotEmpty())
                    <span class="badge bg-primary">{{ $maintenance->parts->sum('quantity') }}</span>
                @endif
            </h6>
            @if(isset($showActions) && $showActions && $maintenance->status !== 'completed' && $maintenance->status !== 'cancelled')
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addPartModal">
                    <i class="fas fa-plus me-1"></i> Ajouter
                </button>
            @endif
        </div>
        
        @if($maintenance->parts->isNotEmpty())
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Référence</th>
                                <th class="text-end">Quantité</th>
                                <th class="text-end">Prix unitaire</th>
                                <th class="text-end">Total</th>
                                @if(isset($showActions) && $showActions && $maintenance->status !== 'completed' && $maintenance->status !== 'cancelled')
                                    <th class="text-end">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($maintenance->parts as $part)
                                <tr>
                                    <td>{{ $part->name }}</td>
                                    <td>{{ $part->reference ?? '-' }}</td>
                                    <td class="text-end">{{ $part->quantity }}</td>
                                    <td class="text-end">{{ number_format($part->unit_price, 2, ',', ' ') }} €</td>
                                    <td class="text-end">{{ number_format($part->quantity * $part->unit_price, 2, ',', ' ') }} €</td>
                                    @if(isset($showActions) && $showActions && $maintenance->status !== 'completed' && $maintenance->status !== 'cancelled')
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-primary" 
                                                        data-bs-toggle="modal" data-bs-target="#editPartModal{{ $part->id }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-danger" 
                                                        data-bs-toggle="modal" data-bs-target="#deletePartModal{{ $part->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                                
                                <!-- Modal d'édition de pièce -->
                                @if(isset($showActions) && $showActions && $maintenance->status !== 'completed' && $maintenance->status !== 'cancelled')
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
                                                            <label for="name" class="form-label">Nom <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" id="name" name="name" value="{{ $part->name }}" required>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <label for="quantity" class="form-label">Quantité <span class="text-danger">*</span></label>
                                                                <input type="number" class="form-control" id="quantity" name="quantity" min="1" value="{{ $part->quantity }}" required>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="unit_price" class="form-label">Prix unitaire (€) <span class="text-danger">*</span></label>
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
                                @endif
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3"></th>
                                <th class="text-end">Total :</th>
                                <th class="text-end">
                                    {{ number_format($maintenance->parts->sum(function($part) { 
                                        return $part->quantity * $part->unit_price; 
                                    }), 2, ',', ' ') }} €
                                </th>
                                @if(isset($showActions) && $showActions && $maintenance->status !== 'completed' && $maintenance->status !== 'cancelled')
                                    <th></th>
                                @endif
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @else
            <div class="card-body text-center py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-0">Aucune pièce utilisée pour le moment</p>
                @if(isset($showActions) && $showActions && $maintenance->status !== 'completed' && $maintenance->status !== 'cancelled')
                    <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addPartModal">
                        <i class="fas fa-plus me-1"></i> Ajouter une pièce
                    </button>
                @endif
            </div>
        @endif
    </div>
    
    @if(isset($showActions) && $showActions && $maintenance->status !== 'completed' && $maintenance->status !== 'cancelled')
        <!-- Modal d'ajout de pièce -->
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
    @endif
@endif
