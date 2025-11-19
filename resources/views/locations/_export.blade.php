<!-- Modal d'exportation d'emplacements -->
<div class="modal fade" id="exportLocationsModal" tabindex="-1" aria-labelledby="exportLocationsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="exportLocationsModalLabel">Exporter les emplacements</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form action="{{ route('locations.export') }}" method="GET">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="exportFormat" class="form-label">Format d'exportation</label>
                        <select class="form-select" id="exportFormat" name="format" required>
                            <option value="xlsx">Excel (.xlsx)</option>
                            <option value="csv">CSV (.csv)</option>
                            <option value="pdf">PDF (.pdf)</option>
                        </select>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="exportOnlyActive" name="only_active" checked>
                        <label class="form-check-label" for="exportOnlyActive">
                            Exporter uniquement les emplacements actifs
                        </label>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        L'exportation peut prendre quelques instants en fonction du nombre d'emplacements.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-download me-1"></i> Exporter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
