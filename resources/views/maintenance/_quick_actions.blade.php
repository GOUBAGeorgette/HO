<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-bolt me-2"></i>Actions rapides
        </h6>
    </div>
    <div class="card-body">
        <div class="d-grid gap-2">
            @if($maintenance->status === 'scheduled')
                @can('update', $maintenance)
                    <a href="{{ route('maintenance.edit', $maintenance) }}" class="btn btn-primary mb-2">
                        <i class="fas fa-edit me-1"></i> Modifier
                    </a>
                    
                    <button type="button" class="btn btn-success mb-2" data-bs-toggle="modal" data-bs-target="#startMaintenanceModal">
                        <i class="fas fa-play me-1"></i> Démarrer la maintenance
                    </button>
                    
                    <button type="button" class="btn btn-warning text-white mb-2" data-bs-toggle="modal" data-bs-target="#cancelMaintenanceModal">
                        <i class="fas fa-times-circle me-1"></i> Annuler la maintenance
                    </button>
                @endcan
                
                @can('delete', $maintenance)
                    <button type="button" class="btn btn-danger mb-2" data-bs-toggle="modal" data-bs-target="#deleteMaintenanceModal">
                        <i class="fas fa-trash me-1"></i> Supprimer
                    </button>
                @endcan
                
            @elseif($maintenance->status === 'in_progress')
                @can('update', $maintenance)
                    <button type="button" class="btn btn-success mb-2" data-bs-toggle="modal" data-bs-target="#completeMaintenanceModal">
                        <i class="fas fa-check-circle me-1"></i> Terminer la maintenance
                    </button>
                    
                    <button type="button" class="btn btn-warning text-white mb-2" data-bs-toggle="modal" data-bs-target="#cancelMaintenanceModal">
                        <i class="fas fa-times-circle me-1"></i> Annuler la maintenance
                    </button>
                @endcan
            @endif
            
            @if($maintenance->status !== 'cancelled')
                <a href="{{ route('maintenance.report', $maintenance) }}" class="btn btn-info text-white mb-2" target="_blank">
                    <i class="fas fa-file-pdf me-1"></i> Générer un rapport
                </a>
                
                <a href="{{ route('maintenance.export', $maintenance) }}" class="btn btn-secondary mb-2">
                    <i class="fas fa-file-export me-1"></i> Exporter en Excel
                </a>
            @endif
            
            <a href="{{ route('equipment.show', $maintenance->equipment) }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-1"></i> Voir l'équipement
            </a>
        </div>
    </div>
</div>

<!-- Modals pour les actions -->
@if($maintenance->status === 'scheduled')
    @can('update', $maintenance)
        <!-- Modal de démarrage de maintenance -->
        <div class="modal fade" id="startMaintenanceModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('maintenance.start', $maintenance) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Démarrer la maintenance</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                        </div>
                        <div class="modal-body">
                            <p>Êtes-vous sûr de vouloir démarrer cette maintenance ?</p>
                            <div class="form-group">
                                <label for="start_notes" class="form-label">Notes (optionnel)</label>
                                <textarea class="form-control" id="start_notes" name="notes" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-success">Confirmer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Modal d'annulation de maintenance -->
        <div class="modal fade" id="cancelMaintenanceModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('maintenance.cancel', $maintenance) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Annuler la maintenance</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                        </div>
                        <div class="modal-body">
                            <p>Veuillez indiquer la raison de l'annulation :</p>
                            <div class="form-group">
                                <label for="cancellation_reason" class="form-label">Raison <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="cancellation_reason" name="cancellation_reason" rows="3" required></textarea>
                            </div>
                            <div class="form-group mt-3">
                                <label for="cancellation_notes" class="form-label">Notes supplémentaires (optionnel)</label>
                                <textarea class="form-control" id="cancellation_notes" name="notes" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-warning text-white">Confirmer l'annulation</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
    
    @can('delete', $maintenance)
        <!-- Modal de suppression de maintenance -->
        <div class="modal fade" id="deleteMaintenanceModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('maintenance.destroy', $maintenance) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="modal-header">
                            <h5 class="modal-title">Confirmer la suppression</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                        </div>
                        <div class="modal-body">
                            <p>Êtes-vous sûr de vouloir supprimer cette maintenance ? Cette action est irréversible.</p>
                            <div class="form-group">
                                <label for="delete_reason" class="form-label">Raison de la suppression <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="delete_reason" name="reason" rows="2" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-danger">Supprimer définitivement</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
    
@elseif($maintenance->status === 'in_progress')
    @can('update', $maintenance)
        <!-- Modal de fin de maintenance -->
        <div class="modal fade" id="completeMaintenanceModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('maintenance.complete', $maintenance) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Terminer la maintenance</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group mb-3">
                                <label for="completion_notes" class="form-label">Rapport de fin <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="completion_notes" name="completion_notes" rows="4" required></textarea>
                                <div class="form-text">Décrivez les travaux effectués et l'état de l'équipement.</div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="actual_duration" class="form-label">Durée réelle (minutes)</label>
                                        <input type="number" class="form-control" id="actual_duration" name="actual_duration" min="1" value="{{ $maintenance->estimated_duration }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="total_cost" class="form-label">Coût total (€)</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="total_cost" name="total_cost" min="0" step="0.01" value="{{ $maintenance->estimated_cost }}">
                                            <span class="input-group-text">€</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" id="equipment_operational" name="equipment_operational" value="1" checked>
                                <label class="form-check-label" for="equipment_operational">
                                    L'équipement est pleinement opérationnel
                                </label>
                            </div>
                            
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="requires_followup" name="requires_followup" value="1">
                                <label class="form-check-label" for="requires_followup">
                                    Un suivi est nécessaire
                                </label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-success">Terminer la maintenance</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
@endif
