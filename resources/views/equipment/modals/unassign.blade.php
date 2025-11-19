<div class="modal fade" id="unassignEquipmentModal" tabindex="-1" aria-labelledby="unassignEquipmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('equipment.unassign', $equipment) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title" id="unassignEquipmentModalLabel">Désassigner l'équipement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir désassigner cet équipement de {{ $equipment->assignedUser->name ?? 'l\'utilisateur actuel' }} ?</p>
                    <div class="mb-3">
                        <label for="unassigned_at" class="form-label">Date de désassignation</label>
                        <input type="datetime-local" class="form-control" id="unassigned_at" name="unassigned_at" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="unassignment_notes" class="form-label">Raison (facultatif)</label>
                        <textarea class="form-control" id="unassignment_notes" name="unassignment_notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Confirmer la désassignation</button>
                </div>
            </form>
        </div>
    </div>
</div>
