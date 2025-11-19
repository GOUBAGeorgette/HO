<!-- Modal d'importation d'emplacements -->
<div class="modal fade" id="importLocationsModal" tabindex="-1" aria-labelledby="importLocationsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="importLocationsModalLabel">Importer des emplacements</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form action="{{ route('locations.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="importFile" class="form-label">Fichier à importer</label>
                        <input class="form-control" type="file" id="importFile" name="file" accept=".xlsx,.xls,.csv" required>
                        <div class="form-text">Formats acceptés : .xlsx, .xls, .csv</div>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="updateExisting" name="update_existing">
                        <label class="form-check-label" for="updateExisting">
                            Mettre à jour les emplacements existants
                        </label>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Téléchargez le <a href="{{ route('locations.template') }}">modèle d'importation</a> pour assurer un format correct.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-1"></i> Importer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
