<div class="modal fade" id="uploadDocumentModal" tabindex="-1" aria-labelledby="uploadDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="equipment_id" value="{{ $equipment->id }}">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadDocumentModalLabel">Ajouter un document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom du document</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="document" class="form-label">Fichier</label>
                        <input class="form-control" type="file" id="document" name="document" required>
                        <div class="form-text">Formats acceptés : PDF, DOC, DOCX, XLS, XLSX, JPG, PNG (max: 5MB)</div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description (facultatif)</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Téléverser</button>
                </div>
            </form>
        </div>
    </div>
</div>
