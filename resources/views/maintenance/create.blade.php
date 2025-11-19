@extends('layouts.app')

@section('title', 'Créer une maintenance')

@section('content')
<div class="container-fluid">
    <!-- En-tête de la page -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Nouvelle maintenance</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ route('maintenance.index') }}">Maintenances</a></li>
                <li class="breadcrumb-item active" aria-current="page">Nouvelle</li>
            </ol>
        </nav>
    </div>

    <!-- Contenu principal -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-tools me-2"></i>Nouvelle maintenance
                    </h6>
                </div>
                <div class="card-body">
                    <form id="maintenanceForm" action="{{ route('maintenance.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Onglets de navigation -->
                        <ul class="nav nav-tabs mb-4" id="maintenanceTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="equipment-tab" data-bs-toggle="tab" data-bs-target="#equipment" type="button" role="tab" aria-controls="equipment" aria-selected="true">
                                    <i class="fas fa-laptop-code me-1"></i> Équipement
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab" aria-controls="details" aria-selected="false" disabled>
                                    <i class="fas fa-info-circle me-1"></i> Détails
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="parts-tab" data-bs-toggle="tab" data-bs-target="#parts" type="button" role="tab" aria-controls="parts" aria-selected="false" disabled>
                                    <i class="fas fa-cogs me-1"></i> Pièces
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="attachments-tab" data-bs-toggle="tab" data-bs-target="#attachments" type="button" role="tab" aria-controls="attachments" aria-selected="false" disabled>
                                    <i class="fas fa-paperclip me-1"></i> Pièces jointes
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="confirmation-tab" data-bs-toggle="tab" data-bs-target="#confirmation" type="button" role="tab" aria-controls="confirmation" aria-selected="false" disabled>
                                    <i class="fas fa-check-circle me-1"></i> Confirmation
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="maintenanceTabsContent">
                            <!-- Étape 1: Sélection de l'équipement -->
                            <div class="tab-pane fade show active" id="equipment" role="tabpanel" aria-labelledby="equipment-tab">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="equipment_id" class="form-label">Équipement <span class="text-danger">*</span></label>
                                            <select class="form-control select2" id="equipment_id" name="equipment_id" required>
                                                <option value="">Sélectionnez un équipement</option>
                                                @foreach($equipments as $equipment)
                                                    <option value="{{ $equipment->id }}" 
                                                            data-category="{{ $equipment->category->name }}"
                                                            data-serial="{{ $equipment->serial_number }}"
                                                            data-location="{{ $equipment->location ? $equipment->location->name : 'Non spécifiée' }}"
                                                            data-status="{{ $equipment->status }}">
                                                        {{ $equipment->name }} ({{ $equipment->model }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div id="equipment-details" class="card mb-4 d-none">
                                            <div class="card-body">
                                                <h6 class="card-title">Détails de l'équipement</h6>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <p class="mb-1"><strong>Catégorie:</strong></p>
                                                        <p class="mb-1" id="equipment-category">-</p>
                                                    </div>
                                                    <div class="col-6">
                                                        <p class="mb-1"><strong>N° de série:</strong></p>
                                                        <p class="mb-1" id="equipment-serial">-</p>
                                                    </div>
                                                    <div class="col-6">
                                                        <p class="mb-1"><strong>Localisation:</strong></p>
                                                        <p class="mb-1" id="equipment-location">-</p>
                                                    </div>
                                                    <div class="col-6">
                                                        <p class="mb-1"><strong>Statut:</strong></p>
                                                        <p class="mb-1"><span id="equipment-status" class="badge">-</span></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="alert alert-info">
                                            <h6><i class="fas fa-info-circle me-2"></i>Instructions</h6>
                                            <p class="mb-0">Sélectionnez l'équipement nécessitant une maintenance dans la liste déroulante ci-contre. Les détails de l'équipement s'afficheront automatiquement.</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between mt-4">
                                    <div>
                                        <a href="{{ route('maintenance.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left me-1"></i> Annuler
                                        </a>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-primary next-step" data-next="details">
                                            Suivant <i class="fas fa-arrow-right ms-1"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Étape 2: Détails de la maintenance -->
                            <div class="tab-pane fade" id="details" role="tabpanel" aria-labelledby="details-tab">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="maintenance_type" class="form-label">Type de maintenance <span class="text-danger">*</span></label>
                                            <select class="form-select" id="maintenance_type" name="maintenance_type" required>
                                                <option value="">Sélectionnez un type</option>
                                                @foreach(\App\Enums\MaintenanceType::cases() as $type)
                                                    <option value="{{ $type->value }}" {{ old('maintenance_type') == $type->value ? 'selected' : '' }}>
                                                        {{ $type->label() }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="form-group mb-3">
                                            <label for="priority" class="form-label">Priorité <span class="text-danger">*</span></label>
                                            <select class="form-select" id="priority" name="priority" required>
                                                @foreach(\App\Enums\Priority::cases() as $priority)
                                                    <option value="{{ $priority->value }}" {{ old('priority', 'medium') == $priority->value ? 'selected' : '' }}>
                                                        {{ $priority->label() }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="form-group mb-3">
                                            <label for="scheduled_date" class="form-label">Date planifiée <span class="text-danger">*</span></label>
                                            <input type="datetime-local" class="form-control" id="scheduled_date" name="scheduled_date" 
                                                   value="{{ old('scheduled_date', now()->format('Y-m-d\TH:i')) }}" required>
                                        </div>
                                        
                                        <div class="form-group mb-3">
                                            <label for="estimated_duration" class="form-label">Durée estimée (minutes)</label>
                                            <input type="number" class="form-control" id="estimated_duration" name="estimated_duration" 
                                                   min="1" value="{{ old('estimated_duration', 60) }}">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="assigned_to" class="form-label">Assigner à</label>
                                            <select class="form-select select2" id="assigned_to" name="assigned_to">
                                                <option value="">Non assigné</option>
                                                @foreach($technicians as $technician)
                                                    <option value="{{ $technician->id }}" {{ old('assigned_to') == $technician->id ? 'selected' : '' }}>
                                                        {{ $technician->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="form-group mb-3">
                                            <label for="estimated_cost" class="form-label">Coût estimé (€)</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="estimated_cost" name="estimated_cost" 
                                                       min="0" step="0.01" value="{{ old('estimated_cost', 0) }}">
                                                <span class="input-group-text">€</span>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group mb-3">
                                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                            <textarea class="form-control" id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                                        </div>
                                        
                                        <div class="form-group mb-3">
                                            <label for="notes" class="form-label">Notes supplémentaires</label>
                                            <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between mt-4">
                                    <div>
                                        <button type="button" class="btn btn-outline-secondary prev-step" data-prev="equipment">
                                            <i class="fas fa-arrow-left me-1"></i> Précédent
                                        </button>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-primary next-step" data-next="parts">
                                            Suivant <i class="fas fa-arrow-right ms-1"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Étape 3: Pièces utilisées -->
                            <div class="tab-pane fade" id="parts" role="tabpanel" aria-labelledby="parts-tab">
                                <div id="parts-container">
                                    <!-- Les pièces seront ajoutées ici dynamiquement -->
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Aucune pièce n'a encore été ajoutée. Utilisez le bouton ci-dessous pour en ajouter.
                                    </div>
                                </div>
                                
                                <button type="button" class="btn btn-outline-primary mt-3" id="add-part-btn">
                                    <i class="fas fa-plus me-1"></i> Ajouter une pièce
                                </button>
                                
                                <!-- Template pour une nouvelle ligne de pièce -->
                                <template id="part-template">
                                    <div class="card mb-3 part-item">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">Nom <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control part-name" name="parts[0][name]" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="form-label">Référence</label>
                                                        <input type="text" class="form-control part-reference" name="parts[0][reference]">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="form-label">Quantité <span class="text-danger">*</span></label>
                                                        <input type="number" class="form-control part-quantity" name="parts[0][quantity]" min="1" value="1" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="form-label">Prix unitaire (€) <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <input type="number" class="form-control part-unit-price" name="parts[0][unit_price]" min="0" step="0.01" value="0" required>
                                                            <span class="input-group-text">€</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-1 d-flex align-items-end">
                                                    <button type="button" class="btn btn-outline-danger btn-sm remove-part" title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                
                                <div class="d-flex justify-content-between mt-4">
                                    <div>
                                        <button type="button" class="btn btn-outline-secondary prev-step" data-prev="details">
                                            <i class="fas fa-arrow-left me-1"></i> Précédent
                                        </button>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-primary next-step" data-next="attachments">
                                            Suivant <i class="fas fa-arrow-right ms-1"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Étape 4: Pièces jointes -->
                            <div class="tab-pane fade" id="attachments" role="tabpanel" aria-labelledby="attachments-tab">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Vous pouvez ajouter des pièces jointes à cette maintenance (photos, documents, etc.).
                                    Les formats acceptés sont : JPG, PNG, PDF, DOC, XLS. Taille maximale : 5 Mo par fichier.
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="attachments" class="form-label">Pièces jointes</label>
                                    <input class="form-control" type="file" id="attachments" name="attachments[]" multiple>
                                    <div id="attachments-preview" class="mt-3"></div>
                                </div>
                                
                                <div class="d-flex justify-content-between mt-4">
                                    <div>
                                        <button type="button" class="btn btn-outline-secondary prev-step" data-prev="parts">
                                            <i class="fas fa-arrow-left me-1"></i> Précédent
                                        </button>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-primary next-step" data-next="confirmation">
                                            Suivant <i class="fas fa-arrow-right ms-1"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Étape 5: Confirmation -->
                            <div class="tab-pane fade" id="confirmation" role="tabpanel" aria-labelledby="confirmation-tab">
                                <div class="alert alert-success">
                                    <h5><i class="fas fa-check-circle me-2"></i>Vérifiez les informations avant de soumettre</h5>
                                    <p class="mb-0">Veuillez vérifier que toutes les informations sont correctes avant de créer cette maintenance.</p>
                                </div>
                                
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">Récapitulatif</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6>Équipement</h6>
                                                <p id="summary-equipment">-</p>
                                                
                                                <h6 class="mt-4">Type de maintenance</h6>
                                                <p id="summary-type">-</p>
                                                
                                                <h6 class="mt-4">Priorité</h6>
                                                <p id="summary-priority">-</p>
                                                
                                                <h6 class="mt-4">Date planifiée</h6>
                                                <p id="summary-scheduled-date">-</p>
                                            </div>
                                            <div class="col-md-6">
                                                <h6>Description</h6>
                                                <p id="summary-description">-</p>
                                                
                                                <h6 class="mt-4">Notes</h6>
                                                <p id="summary-notes">-</p>
                                                
                                                <h6 class="mt-4">Pièces utilisées</h6>
                                                <div id="summary-parts">
                                                    <p class="text-muted">Aucune pièce ajoutée</p>
                                                </div>
                                                
                                                <h6 class="mt-4">Pièces jointes</h6>
                                                <div id="summary-attachments">
                                                    <p class="text-muted">Aucune pièce jointe</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-check mb-4">
                                    <input class="form-check-input" type="checkbox" id="confirm_terms" required>
                                    <label class="form-check-label" for="confirm_terms">
                                        Je confirme que les informations fournies sont exactes et complètes.
                                    </label>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <button type="button" class="btn btn-outline-secondary prev-step" data-prev="attachments">
                                            <i class="fas fa-arrow-left me-1"></i> Précédent
                                        </button>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-save me-1"></i> Enregistrer la maintenance
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Styles pour les onglets */
    .nav-tabs .nav-link {
        color: #6c757d;
        font-weight: 500;
    }
    
    .nav-tabs .nav-link.active {
        font-weight: 600;
    }
    
    /* Style pour les aperçus de pièces jointes */
    .attachment-preview {
        max-height: 150px;
        object-fit: cover;
    }
    
    /* Style pour les cartes de pièces */
    .part-item {
        border-left: 3px solid #4e73df;
    }
    
    /* Style pour le récapitulatif */
    #summary-parts ul, #summary-attachments ul {
        padding-left: 20px;
        margin-bottom: 0;
    }
    
    /* Style pour les étapes désactivées */
    .nav-link.disabled {
        color: #d1d3e2;
        cursor: not-allowed;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialisation de Select2
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });
        
        // Gestion des onglets
        let currentStep = 'equipment';
        const steps = ['equipment', 'details', 'parts', 'attachments', 'confirmation'];
        
        // Activer/désactiver les onglets
        function updateTabs() {
            const currentIndex = steps.indexOf(currentStep);
            
            steps.forEach((step, index) => {
                const tab = $(`#${step}-tab`);
                if (index <= currentIndex) {
                    tab.removeClass('disabled');
                } else {
                    tab.addClass('disabled');
                }
            });
        }
        
        // Navigation entre les étapes
        $('.next-step').on('click', function() {
            const nextStep = $(this).data('next');
            const currentTab = $(`#${currentStep}`);
            const nextTab = $(`#${nextStep}`);
            
            // Validation du formulaire avant de passer à l'étape suivante
            if (currentStep === 'equipment' && !validateEquipmentStep()) {
                return false;
            } else if (currentStep === 'details' && !validateDetailsStep()) {
                return false;
            }
            
            // Mettre à jour l'étape courante
            currentStep = nextStep;
            
            // Mettre à jour les onglets
            updateTabs();
            
            // Mettre à jour l'interface
            currentTab.removeClass('show active');
            nextTab.addClass('show active');
            
            // Mettre à jour la navigation
            $(`#${currentStep}-tab`).tab('show');
            
            // Mettre à jour le récapitulatif si on est sur la dernière étape
            if (currentStep === 'confirmation') {
                updateSummary();
            }
        });
        
        $('.prev-step').on('click', function() {
            const prevStep = $(this).data('prev');
            const currentTab = $(`#${currentStep}`);
            const prevTab = $(`#${prevStep}`);
            
            // Mettre à jour l'étape courante
            currentStep = prevStep;
            
            // Mettre à jour l'interface
            currentTab.removeClass('show active');
            prevTab.addClass('show active');
            
            // Mettre à jour la navigation
            $(`#${currentStep}-tab`).tab('show');
        });
        
        // Validation de l'étape équipement
        function validateEquipmentStep() {
            const equipmentId = $('#equipment_id').val();
            
            if (!equipmentId) {
                toastr.error('Veuillez sélectionner un équipement.');
                return false;
            }
            
            return true;
        }
        
        // Validation de l'étape détails
        function validateDetailsStep() {
            const maintenanceType = $('#maintenance_type').val();
            const scheduledDate = $('#scheduled_date').val();
            const description = $('#description').val().trim();
            
            if (!maintenanceType) {
                toastr.error('Veuillez sélectionner un type de maintenance.');
                return false;
            }
            
            if (!scheduledDate) {
                toastr.error('Veuillez spécifier une date planifiée.');
                return false;
            }
            
            if (!description) {
                toastr.error('Veuillez saisir une description.');
                return false;
            }
            
            return true;
        }
        
        // Affichage des détails de l'équipement sélectionné
        $('#equipment_id').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const equipmentDetails = $('#equipment-details');
            
            if (selectedOption.val()) {
                $('#equipment-category').text(selectedOption.data('category') || '-');
                $('#equipment-serial').text(selectedOption.data('serial') || '-');
                $('#equipment-location').text(selectedOption.data('location') || '-');
                
                // Mise à jour du statut avec un badge coloré
                const status = selectedOption.data('status');
                let statusBadge = '';
                
                switch(status) {
                    case 'available':
                        statusBadge = '<span class="badge bg-success">Disponible</span>';
                        break;
                    case 'in_use':
                        statusBadge = '<span class="badge bg-primary">En utilisation</span>';
                        break;
                    case 'maintenance':
                        statusBadge = '<span class="badge bg-warning">En maintenance</span>';
                        break;
                    case 'out_of_service':
                        statusBadge = '<span class="badge bg-danger">Hors service</span>';
                        break;
                    default:
                        statusBadge = '<span class="badge bg-secondary">Inconnu</span>';
                }
                
                $('#equipment-status').html(statusBadge);
                equipmentDetails.removeClass('d-none');
            } else {
                equipmentDetails.addClass('d-none');
            }
        });
        
        // Gestion des pièces utilisées
        let partCounter = 0;
        
        // Ajouter une nouvelle pièce
        $('#add-part-btn').on('click', function() {
            const template = $('#part-template').html();
            const newPart = $(template.replace(/\[0\]/g, `[${partCounter}]`));
            
            $('#parts-container .alert').remove();
            $('#parts-container').append(newPart);
            
            // Initialiser les tooltips
            newPart.find('[data-bs-toggle="tooltip"]').tooltip();
            
            partCounter++;
        });
        
        // Supprimer une pièce
        $(document).on('click', '.remove-part', function() {
            $(this).closest('.part-item').fadeOut(300, function() {
                $(this).remove();
                
                // Afficher un message si plus de pièces
                if ($('#parts-container .part-item').length === 0) {
                    $('#parts-container').html(`
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Aucune pièce n'a encore été ajoutée. Utilisez le bouton ci-dessous pour en ajouter.
                        </div>
                    `);
                }
            });
        });
        
        // Aperçu des pièces jointes
        $('#attachments').on('change', function() {
            const files = this.files;
            const preview = $('#attachments-preview');
            preview.empty();
            
            if (files.length > 0) {
                const list = $('<div class="row g-2"></div>');
                
                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    const fileType = file.type.split('/')[0];
                    let previewContent = '';
                    
                    if (fileType === 'image') {
                        previewContent = `
                            <div class="col-md-2 col-4">
                                <div class="card">
                                    <img src="${URL.createObjectURL(file)}" class="card-img-top" style="height: 80px; object-fit: cover;">
                                    <div class="card-body p-2">
                                        <p class="small text-truncate mb-0" title="${file.name}">${file.name}</p>
                                        <small class="text-muted">${formatFileSize(file.size)}</small>
                                    </div>
                                </div>
                            </div>
                        `;
                    } else {
                        const iconClass = getFileIconClass(file.name);
                        previewContent = `
                            <div class="col-md-3 col-6">
                                <div class="card h-100">
                                    <div class="card-body text-center py-3">
                                        <i class="${iconClass} fa-3x text-muted mb-2"></i>
                                        <p class="small text-truncate mb-1" title="${file.name}">${file.name}</p>
                                        <small class="text-muted">${formatFileSize(file.size)}</small>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                    
                    list.append(previewContent);
                }
                
                preview.html(list);
            } else {
                preview.html('<p class="text-muted">Aucun fichier sélectionné</p>');
            }
        });
        
        // Mise à jour du récapitulatif
        function updateSummary() {
            // Équipement
            const equipmentText = $('#equipment_id option:selected').text();
            $('#summary-equipment').text(equipmentText || '-');
            
            // Type de maintenance
            const typeText = $('#maintenance_type option:selected').text();
            $('#summary-type').text(typeText || '-');
            
            // Priorité
            const priorityText = $('#priority option:selected').text();
            $('#summary-priority').text(priorityText || '-');
            
            // Date planifiée
            const scheduledDate = new Date($('#scheduled_date').val());
            const formattedDate = scheduledDate.toLocaleDateString('fr-FR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            $('#summary-scheduled-date').text(formattedDate || '-');
            
            // Description et notes
            $('#summary-description').text($('#description').val() || '-');
            $('#summary-notes').text($('#notes').val() || '-');
            
            // Pièces utilisées
            const partsList = [];
            $('.part-item').each(function() {
                const name = $(this).find('.part-name').val();
                const quantity = $(this).find('.part-quantity').val();
                const unitPrice = parseFloat($(this).find('.part-unit-price').val()).toFixed(2);
                const total = (quantity * unitPrice).toFixed(2);
                
                partsList.push(`
                    <div class="d-flex justify-content-between">
                        <span>${name} (x${quantity})</span>
                        <span>${total} €</span>
                    </div>
                `);
            });
            
            if (partsList.length > 0) {
                $('#summary-parts').html(partsList.join(''));
            } else {
                $('#summary-parts').html('<p class="text-muted">Aucune pièce ajoutée</p>');
            }
            
            // Pièces jointes
            const files = $('#attachments')[0].files;
            const attachmentsList = [];
            
            if (files.length > 0) {
                for (let i = 0; i < files.length; i++) {
                    attachmentsList.push(`<li>${files[i].name} (${formatFileSize(files[i].size)})</li>`);
                }
                $('#summary-attachments').html(`<ul class="mb-0">${attachmentsList.join('')}</ul>`);
            } else {
                $('#summary-attachments').html('<p class="text-muted">Aucune pièce jointe</p>');
            }
        }
        
        // Fonction utilitaire pour formater la taille des fichiers
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
        
        // Fonction utilitaire pour obtenir l'icône d'un fichier en fonction de son extension
        function getFileIconClass(filename) {
            const extension = filename.split('.').pop().toLowerCase();
            
            switch(extension) {
                case 'pdf':
                    return 'fas fa-file-pdf';
                case 'doc':
                case 'docx':
                    return 'fas fa-file-word';
                case 'xls':
                case 'xlsx':
                    return 'fas fa-file-excel';
                case 'jpg':
                case 'jpeg':
                case 'png':
                case 'gif':
                    return 'fas fa-file-image';
                case 'zip':
                case 'rar':
                case '7z':
                    return 'fas fa-file-archive';
                default:
                    return 'fas fa-file';
            }
        }
        
        // Ajouter une première pièce par défaut
        if ($('#parts-container .part-item').length === 0) {
            $('#add-part-btn').trigger('click');
        }
        
        // Initialisation des tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>
@endpush
