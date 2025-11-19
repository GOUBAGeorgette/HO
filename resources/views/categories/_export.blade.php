<div class="modal fade" id="exportCategoriesModal" tabindex="-1" aria-labelledby="exportCategoriesModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('categories.export') }}" method="POST" id="exportCategoriesForm">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="exportCategoriesModalLabel">
                        <i class="fas fa-file-export me-2"></i> Exporter des catégories
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <p class="mb-3">Sélectionnez les options d'exportation :</p>
                        
                        <div class="mb-3">
                            <label for="export_format" class="form-label">Format d'exportation <span class="text-danger">*</span></label>
                            <select class="form-select" id="export_format" name="format" required>
                                <option value="xlsx" selected>Excel (.xlsx)</option>
                                <option value="csv">CSV (.csv)</option>
                                <option value="pdf">PDF (.pdf)</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Étendue de l'exportation</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="export_scope" id="export_scope_all" value="all" checked>
                                <label class="form-check-label" for="export_scope_all">
                                    Toutes les catégories
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="export_scope" id="export_scope_selected" value="selected" disabled>
                                <label class="form-check-label" for="export_scope_selected">
                                    Catégories sélectionnées (0)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="export_scope" id="export_scope_filtered" value="filtered">
                                <label class="form-check-label" for="export_scope_filtered">
                                    Résultats actuels des filtres
                                </label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Options d'exportation</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="include_hierarchy" name="include_hierarchy" checked>
                                <label class="form-check-label" for="include_hierarchy">
                                    Inclure la hiérarchie complète
                                </label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="include_inactive" name="include_inactive">
                                <label class="form-check-label" for="include_inactive">
                                    Inclure les catégories inactives
                                </label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="include_stats" name="include_stats" checked>
                                <label class="form-check-label" for="include_stats">
                                    Inclure les statistiques (nombre d'équipements, etc.)
                                </label>
                            </div>
                        </div>
                        
                        <div class="mb-3" id="columnsContainer">
                            <label class="form-label">Colonnes à inclure <span class="text-muted">(Glissez pour réorganiser)</span></label>
                            <div id="sortableColumns" class="list-group">
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="col_id" name="columns[]" value="id" checked disabled>
                                        <label class="form-check-label" for="col_id">ID</label>
                                    </div>
                                    <i class="fas fa-grip-vertical text-muted"></i>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="col_name" name="columns[]" value="name" checked>
                                        <label class="form-check-label" for="col_name">Nom</label>
                                    </div>
                                    <i class="fas fa-grip-vertical text-muted"></i>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="col_code" name="columns[]" value="code" checked>
                                        <label class="form-check-label" for="col_code">Code</label>
                                    </div>
                                    <i class="fas fa-grip-vertical text-muted"></i>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="col_description" name="columns[]" value="description" checked>
                                        <label class="form-check-label" for="col_description">Description</label>
                                    </div>
                                    <i class="fas fa-grip-vertical text-muted"></i>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="col_parent" name="columns[]" value="parent" checked>
                                        <label class="form-check-label" for="col_parent">Catégorie parente</label>
                                    </div>
                                    <i class="fas fa-grip-vertical text-muted"></i>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="col_status" name="columns[]" value="status" checked>
                                        <label class="form-check-label" for="col_status">Statut</label>
                                    </div>
                                    <i class="fas fa-grip-vertical text-muted"></i>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="col_equipments_count" name="columns[]" value="equipments_count" checked>
                                        <label class="form-check-label" for="col_equipments_count">Nbre d'équipements</label>
                                    </div>
                                    <i class="fas fa-grip-vertical text-muted"></i>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="col_children_count" name="columns[]" value="children_count" checked>
                                        <label class="form-check-label" for="col_children_count">Nbre de sous-catégories</label>
                                    </div>
                                    <i class="fas fa-grip-vertical text-muted"></i>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="col_created_at" name="columns[]" value="created_at">
                                        <label class="form-check-label" for="col_created_at">Date de création</label>
                                    </div>
                                    <i class="fas fa-grip-vertical text-muted"></i>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="col_updated_at" name="columns[]" value="updated_at">
                                        <label class="form-check-label" for="col_updated_at">Dernière mise à jour</label>
                                    </div>
                                    <i class="fas fa-grip-vertical text-muted"></i>
                                </div>
                            </div>
                            <div class="mt-2 d-flex justify-content-between">
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="selectAllColumns">
                                    <i class="fas fa-check-square me-1"></i> Tout sélectionner
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllColumns">
                                    <i class="far fa-square me-1"></i> Tout désélectionner
                                </button>
                            </div>
                        </div>
                        
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle me-2"></i>
                            L'exportation peut prendre quelques instants si vous avez un grand nombre de catégories.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-success" id="submitExport">
                        <i class="fas fa-file-export me-1"></i> Exporter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialiser le tri des colonnes avec SortableJS si disponible
        if (typeof Sortable !== 'undefined') {
            new Sortable(document.getElementById('sortableColumns'), {
                animation: 150,
                ghostClass: 'bg-light',
                handle: '.fa-grip-vertical',
                onEnd: function() {
                    // Mettre à jour l'ordre des colonnes
                    updateColumnOrder();
                }
            });
        }
        
        // Fonction pour mettre à jour l'ordre des colonnes
        function updateColumnOrder() {
            const columns = [];
            document.querySelectorAll('#sortableColumns .list-group-item').forEach((item, index) => {
                const input = item.querySelector('input[type="checkbox"]');
                if (input) {
                    columns.push(input.value);
                }
            });
            console.log('Ordre des colonnes :', columns);
            // Ici, vous pourriez enregistrer l'ordre des colonnes dans un champ caché
            // ou l'utiliser pour réorganiser les données avant l'exportation
        }
        
        // Sélectionner/désélectionner toutes les colonnes
        document.getElementById('selectAllColumns')?.addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('#sortableColumns input[type="checkbox"]:not(:disabled)');
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
        });
        
        document.getElementById('deselectAllColumns')?.addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('#sortableColumns input[type="checkbox"]:not(:disabled)');
            checkboxes.forEach(checkbox => {
                if (!checkbox.hasAttribute('data-required')) {
                    checkbox.checked = false;
                }
            });
        });
        
        // Mettre à jour le compteur de sélection
        function updateSelectedCount() {
            const selectedCount = document.querySelectorAll('input[name="selected_categories[]"]:checked').length;
            const selectedLabel = document.querySelector('label[for="export_scope_selected"]');
            
            if (selectedLabel && selectedCount > 0) {
                selectedLabel.textContent = `Catégories sélectionnées (${selectedCount})`;
                document.getElementById('export_scope_selected').disabled = false;
            } else if (selectedLabel) {
                selectedLabel.textContent = 'Catégories sélectionnées (0)';
                document.getElementById('export_scope_selected').disabled = true;
                document.getElementById('export_scope_all').checked = true;
            }
        }
        
        // Écouter les changements de sélection dans le tableau principal
        document.addEventListener('click', function(e) {
            if (e.target.matches('input[name="selected_categories[]"]')) {
                updateSelectedCount();
            }
        });
        
        // Gérer la soumission du formulaire d'exportation
        const exportForm = document.getElementById('exportCategoriesForm');
        if (exportForm) {
            exportForm.addEventListener('submit', function(e) {
                const submitButton = document.getElementById('submitExport');
                
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Préparation de l\'export...';
                }
                
                // Ici, vous pourriez ajouter une logique pour collecter les ID des catégories sélectionnées
                // si l'option "Catégories sélectionnées" est choisie
                
                return true;
            });
        }
        
        // Initialiser le compteur de sélection au chargement de la page
        updateSelectedCount();
    });
    
    // Fonction pour afficher la modale d'exportation
    function showExportCategoriesModal() {
        const modal = new bootstrap.Modal(document.getElementById('exportCategoriesModal'));
        modal.show();
    }
</script>
@endpush
