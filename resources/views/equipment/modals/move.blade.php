<div class="modal fade" id="moveEquipmentModal" tabindex="-1" aria-labelledby="moveEquipmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('equipment.move', $equipment) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="moveEquipmentModalLabel">Déplacer l'équipement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="location_id" class="form-label">Nouvel emplacement</label>
                        <select class="form-select" id="location_id" name="location_id" required>
                            <option value="">Sélectionner un emplacement</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}" {{ $equipment->location_id == $location->id ? 'selected' : '' }}>
                                    {{ $location->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="moved_at" class="form-label">Date de déplacement</label>
                        <input type="datetime-local" class="form-control" id="moved_at" name="moved_at" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes (facultatif)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer le déplacement</button>
                </div>
            </form>
        </div>
    </div>
</div>
