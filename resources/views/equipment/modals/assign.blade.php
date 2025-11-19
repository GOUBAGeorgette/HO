<div class="modal fade" id="assignEquipmentModal" tabindex="-1" aria-labelledby="assignEquipmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('equipment.assign', $equipment) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="assignEquipmentModalLabel">Assigner l'équipement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="assigned_to" class="form-label">Utilisateur</label>
                        <select class="form-select" id="assigned_to" name="assigned_to" required>
                            <option value="">Sélectionner un utilisateur</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $equipment->assigned_to == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="assigned_at" class="form-label">Date d'assignation</label>
                        <input type="datetime-local" class="form-control" id="assigned_at" name="assigned_at" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="assignment_notes" class="form-label">Notes (facultatif)</label>
                        <textarea class="form-control" id="assignment_notes" name="assignment_notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer l'assignation</button>
                </div>
            </form>
        </div>
    </div>
</div>
