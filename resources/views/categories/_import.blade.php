<div class="modal fade" id="importCategoriesModal" tabindex="-1" aria-labelledby="importCategoriesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('categories.import') }}" method="POST" enctype="multipart/form-data" id="importCategoriesForm">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="importCategoriesModalLabel">
                        <i class="fas fa-file-import me-2"></i> Importer des catégories
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Téléchargez notre modèle Excel pour importer des catégories. Assurez-vous de suivre le format indiqué.
                        <a href="{{ asset('templates/categories_import_template.xlsx') }}" class="btn btn-sm btn-outline-primary ms-2" download>
                            <i class="fas fa-download me-1"></i> Télécharger le modèle
                        </a>
                    </div>
                    
                    <div class="mb-4">
                        <label for="import_file" class="form-label">Fichier Excel à importer <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="file" class="form-control @error('import_file') is-invalid @enderror" 
                                   id="import_file" name="import_file" accept=".xlsx, .xls, .csv" required>
                            <button class="btn btn-outline-secondary" type="button" id="clearImportFile">
                                <i class="fas fa-times"></i>
                            </button>
                            @error('import_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-text">Formats acceptés : .xlsx, .xls, .csv (max: 5MB)</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="has_headers" name="has_headers" checked>
                            <label class="form-check-label" for="has_headers">
                                La première ligne contient les en-têtes
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Options d'importation</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="import_behavior" id="import_behavior_new" value="new" checked>
                            <label class="form-check-label" for="import_behavior_new">
                                Ajouter uniquement les nouvelles catégories
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="import_behavior" id="import_behavior_update" value="update">
                            <label class="form-check-label" for="import_behavior_update">
                                Mettre à jour les catégories existantes
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="import_behavior" id="import_behavior_replace" value="replace">
                            <label class="form-check-label" for="import_behavior_replace">
                                Remplacer toutes les catégories existantes (supprime les catégories non présentes dans le fichier)
                            </label>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning mt-4">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i> Instructions d'importation</h6>
                        <ul class="mb-0">
                            <li>Assurez-vous que le fichier suit exactement le format du modèle fourni.</li>
                            <li>Les champs marqués d'un astérisque (*) sont obligatoires.</li>
                            <li>Les codes de catégorie doivent être uniques.</li>
                            <li>Pour les hiérarchies, utilisez le champ "parent_code" pour référencer le code de la catégorie parente.</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitImport">
                        <i class="fas fa-file-import me-1"></i> Importer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Effacer la sélection du fichier
        document.getElementById('clearImportFile')?.addEventListener('click', function() {
            const fileInput = document.getElementById('import_file');
            fileInput.value = '';
        });
        
        // Validation du formulaire d'importation
        const importForm = document.getElementById('importCategoriesForm');
        if (importForm) {
            importForm.addEventListener('submit', function(e) {
                const fileInput = document.getElementById('import_file');
                const submitButton = document.getElementById('submitImport');
                
                if (!fileInput.files.length) {
                    e.preventDefault();
                    fileInput.classList.add('is-invalid');
                    
                    if (!fileInput.nextElementSibling?.classList?.contains('invalid-feedback')) {
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'invalid-feedback';
                        errorDiv.textContent = 'Veuillez sélectionner un fichier à importer';
                        fileInput.parentNode.insertBefore(errorDiv, fileInput.nextSibling);
                    }
                    
                    return false;
                }
                
                // Désactiver le bouton de soumission pour éviter les doubles clics
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Import en cours...';
                }
                
                return true;
            });
        }
        
        // Afficher un aperçu du fichier avant l'importation
        const fileInput = document.getElementById('import_file');
        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Vérifier la taille du fichier (max 5MB)
                    const maxSize = 5 * 1024 * 1024; // 5MB
                    
                    if (file.size > maxSize) {
                        alert('Le fichier est trop volumineux. La taille maximale autorisée est de 5 Mo.');
                        this.value = '';
                        return false;
                    }
                    
                    // Vérifier l'extension du fichier
                    const allowedExtensions = ['.xlsx', '.xls', '.csv'];
                    const fileName = file.name;
                    const fileExt = fileName.substring(fileName.lastIndexOf('.')).toLowerCase();
                    
                    if (!allowedExtensions.includes(fileExt)) {
                        alert('Format de fichier non pris en charge. Veuillez télécharger un fichier Excel (.xlsx, .xls) ou CSV.');
                        this.value = '';
                        return false;
                    }
                    
                    // Si tout est valide, supprimer les messages d'erreur
                    this.classList.remove('is-invalid');
                    const errorDiv = this.nextElementSibling;
                    if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
                        errorDiv.remove();
                    }
                }
            });
        }
    });
    
    // Fonction pour afficher la modale d'importation
    function showImportCategoriesModal() {
        const modal = new bootstrap.Modal(document.getElementById('importCategoriesModal'));
        modal.show();
    }
</script>
@endpush
