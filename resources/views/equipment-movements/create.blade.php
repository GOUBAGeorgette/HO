@extends('layouts.maquette')

@section('title', 'Nouveau mouvement d\'équipement')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    .equipment-image {
        height: 120px;
        object-fit: cover;
        border-radius: 0.25rem;
    }
    
    .step-indicator {
        display: flex;
        justify-content: space-between;
        margin-bottom: 1.5rem;
    }
    
    .step {
        flex: 1;
        text-align: center;
        position: relative;
    }
    
    .step-number {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background-color: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 0.5rem;
    }
    
    .step.active .step-number {
        background-color: #0d6efd;
        color: white;
    }
    
    .section {
        display: none;
    }
    
    .section.active {
        display: block;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-exchange-alt me-2"></i> Nouveau mouvement d'équipement
        </h1>
        <div>
            <a href="{{ route('equipment-movements.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Annuler
            </a>
        </div>
    </div>
    
    <!-- Barre de progression -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="step-indicator">
                <div class="step active" id="step-1">
                    <div class="step-number">1</div>
                    <div class="step-label">Équipement</div>
                </div>
                <div class="step" id="step-2">
                    <div class="step-number">2</div>
                    <div class="step-label">Origine & Destination</div>
                </div>
                <div class="step" id="step-3">
                    <div class="step-number">3</div>
                    <div class="step-label">Détails</div>
                </div>
                <div class="step" id="step-4">
                    <div class="step-number">4</div>
                    <div class="step-label">Confirmation</div>
                </div>
            </div>
        </div>
    </div>
    
    <form id="movementForm" action="{{ route('equipment-movements.store') }}" method="POST">
        @csrf
        
        <!-- Section 1: Équipement -->
        <div class="section active" id="section-1">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-box me-2"></i> Sélection des équipements
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="equipment_id" class="form-label">Équipement <span class="text-danger">*</span></label>
                        <select class="form-select" id="equipment_id" name="equipment_id" required>
                            <option value="">Sélectionnez un équipement</option>
                            @foreach($equipmentList as $equipment)
                                <option value="{{ $equipment->id }}" 
                                    data-category="{{ $equipment->category->name ?? '' }}"
                                    data-model="{{ $equipment->model ?? '' }}"
                                    data-serial="{{ $equipment->serial_number ?? '' }}">
                                    {{ $equipment->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div id="equipmentDetails" class="mt-3" style="display: none;">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div id="equipmentImage" class="text-center">
                                            <div class="bg-light d-flex align-items-center justify-content-center" 
                                                 style="width: 120px; height: 120px; margin: 0 auto; border-radius: 0.25rem;">
                                                <i class="fas fa-box fa-3x text-muted"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <h5 id="equipmentName"></h5>
                                        <p class="mb-1" id="equipmentCategory"></p>
                                        <p class="mb-1" id="equipmentModel"></p>
                                        <p class="mb-0" id="equipmentSerial"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-primary" id="nextToStep2">
                    Suivant <i class="fas fa-arrow-right ms-1"></i>
                </button>
            </div>
        </div>
        
        <!-- Section 2: Origine & Destination -->
        <div class="section" id="section-2">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-map-marker-alt me-2"></i> Origine et destination
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3">Origine</h5>
                            <div class="mb-3">
                                <label class="form-label">Type d'origine <span class="text-danger">*</span></label>
                                <select class="form-select" id="origin_type" name="origin_type" required>
                                    <option value="">Sélectionnez un type</option>
                                    <option value="location">Emplacement</option>
                                    <option value="department">Département</option>
                                    <option value="external">Externe</option>
                                </select>
                            </div>
                            <div id="origin_location_field" class="mb-3" style="display: none;">
                                <label for="origin_location_id" class="form-label">Emplacement <span class="text-danger">*</span></label>
                                <select class="form-select" id="origin_location_id" name="origin_location_id">
                                    <option value="">Sélectionnez un emplacement</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="origin_department_field" class="mb-3" style="display: none;">
                                <label for="origin_department_id" class="form-label">Département <span class="text-danger">*</span></label>
                                <select class="form-select" id="origin_department_id" name="origin_department_id">
                                    <option value="">Sélectionnez un département</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="origin_external_field" class="mb-3" style="display: none;">
                                <label for="origin_external" class="form-label">Détails de l'origine externe</label>
                                <input type="text" class="form-control" id="origin_external" name="origin_external" placeholder="Ex: Fournisseur, Adresse...">
                            </div>
                            <div class="mb-3">
                                <label for="origin_contact" class="form-label">Contact</label>
                                <input type="text" class="form-control" id="origin_contact" name="origin_contact" placeholder="Nom du contact">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h5 class="mb-3">Destination</h5>
                            <div class="mb-3">
                                <label class="form-label">Type de destination <span class="text-danger">*</span></label>
                                <select class="form-select" id="destination_type" name="destination_type" required>
                                    <option value="">Sélectionnez un type</option>
                                    <option value="location">Emplacement</option>
                                    <option value="department">Département</option>
                                    <option value="external">Externe</option>
                                </select>
                            </div>
                            <div id="destination_location_field" class="mb-3" style="display: none;">
                                <label for="destination_location_id" class="form-label">Emplacement <span class="text-danger">*</span></label>
                                <select class="form-select" id="destination_location_id" name="destination_location_id">
                                    <option value="">Sélectionnez un emplacement</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="destination_department_field" class="mb-3" style="display: none;">
                                <label for="destination_department_id" class="form-label">Département <span class="text-danger">*</span></label>
                                <select class="form-select" id="destination_department_id" name="destination_department_id">
                                    <option value="">Sélectionnez un département</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="destination_external_field" class="mb-3" style="display: none;">
                                <label for="destination_external" class="form-label">Détails de la destination externe</label>
                                <input type="text" class="form-control" id="destination_external" name="destination_external" placeholder="Ex: Client, Adresse...">
                            </div>
                            <div class="mb-3">
                                <label for="destination_contact" class="form-label">Contact</label>
                                <input type="text" class="form-control" id="destination_contact" name="destination_contact" placeholder="Nom du contact">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" id="backToStep1">
                    <i class="fas fa-arrow-left me-1"></i> Précédent
                </button>
                <button type="button" class="btn btn-primary" id="nextToStep3">
                    Suivant <i class="fas fa-arrow-right ms-1"></i>
                </button>
            </div>
        </div>
        
        <!-- Section 3: Détails du mouvement -->
        <div class="section" id="section-3">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i> Détails du mouvement
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="movement_type" class="form-label">Type de mouvement <span class="text-danger">*</span></label>
                                <select class="form-select" id="movement_type" name="movement_type" required>
                                    <option value="">Sélectionnez un type</option>
                                    <option value="transfer">Transfert</option>
                                    <option value="loan">Prêt</option>
                                    <option value="maintenance">Maintenance</option>
                                    <option value="repair">Réparation</option>
                                    <option value="other">Autre</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="priority" class="form-label">Priorité <span class="text-danger">*</span></label>
                                <select class="form-select" id="priority" name="priority" required>
                                    <option value="low">Basse</option>
                                    <option value="medium" selected>Moyenne</option>
                                    <option value="high">Haute</option>
                                    <option value="urgent">Urgente</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="scheduled_date" class="form-label">Date prévue <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="scheduled_date" name="scheduled_date" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="assigned_to" class="form-label">Assigné à</label>
                                <select class="form-select" id="assigned_to" name="assigned_to">
                                    <option value="">Non assigné</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="reason" class="form-label">Raison du mouvement</label>
                                <textarea class="form-control" id="reason" name="reason" rows="3" placeholder="Décrivez la raison de ce mouvement"></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes supplémentaires</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Ajoutez des notes ou des instructions supplémentaires"></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="attachments" class="form-label">Pièces jointes</label>
                                <input class="form-control" type="file" id="attachments" name="attachments[]" multiple>
                                <div class="form-text">Vous pouvez sélectionner plusieurs fichiers</div>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="requires_approval" name="requires_approval" checked>
                                <label class="form-check-label" for="requires_approval">
                                    Nécessite une approbation
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" id="backToStep2">
                    <i class="fas fa-arrow-left me-1"></i> Précédent
                </button>
                <button type="button" class="btn btn-primary" id="nextToStep4">
                    Suivant <i class="fas fa-arrow-right ms-1"></i>
                </button>
            </div>
        </div>
        
        <!-- Section 4: Confirmation -->
        <div class="section" id="section-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-check-circle me-2"></i> Confirmation
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Veuillez vérifier les informations avant de soumettre la demande de mouvement.
                    </div>
                    
                    <h5 class="mb-3">Récapitulatif du mouvement</h5>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="mb-0">Équipement</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-1" id="summary_equipment"></p>
                                    <p class="mb-1" id="summary_category"></p>
                                    <p class="mb-1" id="summary_model"></p>
                                    <p class="mb-0" id="summary_serial"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="mb-0">Détails du mouvement</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-1"><strong>Type :</strong> <span id="summary_movement_type"></span></p>
                                    <p class="mb-1"><strong>Priorité :</strong> <span id="summary_priority"></span></p>
                                    <p class="mb-1"><strong>Date prévue :</strong> <span id="summary_scheduled_date"></span></p>
                                    <p class="mb-1"><strong>Assigné à :</strong> <span id="summary_assigned_to"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="mb-0">Origine</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-1" id="summary_origin"></p>
                                    <p class="mb-0" id="summary_origin_contact"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="mb-0">Destination</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-1" id="summary_destination"></p>
                                    <p class="mb-0" id="summary_destination_contact"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h6>Notes et commentaires</h6>
                        <div class="card">
                            <div class="card-body">
                                <p id="summary_notes" class="mb-0"></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="terms_accepted" name="terms_accepted" required>
                        <label class="form-check-label" for="terms_accepted">
                            Je confirme que les informations fournies sont exactes et j'accepte les conditions d'utilisation.
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" id="backToStep3">
                    <i class="fas fa-arrow-left me-1"></i> Précédent
                </button>
                <button type="submit" class="btn btn-success" id="submitForm">
                    <i class="fas fa-check me-1"></i> Soumettre la demande
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialiser Select2 pour tous les selects
        $('.form-select').select2({
            theme: 'bootstrap-5'
        });
        
        // Configurer les champs de date et heure
        const now = new Date();
        const formattedDate = now.toISOString().slice(0, 16);
        $('#scheduled_date').val(formattedDate);
        
        // Afficher les détails de l'équipement sélectionné
        $('#equipment_id').on('change', function() {
            const selected = $(this).find('option:selected');
            if (selected.val()) {
                $('#equipmentName').text(selected.text());
                $('#equipmentCategory').html(`<strong>Catégorie :</strong> ${selected.data('category') || 'Non spécifiée'}`);
                $('#equipmentModel').html(`<strong>Modèle :</strong> ${selected.data('model') || 'Non spécifié'}`);
                $('#equipmentSerial').html(`<strong>N° de série :</strong> ${selected.data('serial') || 'Non spécifié'}`);
                $('#equipmentDetails').show();
            } else {
                $('#equipmentDetails').hide();
            }
        });
        
        // Gestion des champs dynamiques pour l'origine
        $('#origin_type').on('change', function() {
            const type = $(this).val();
            $('#origin_location_field, #origin_department_field, #origin_external_field').hide();
            
            if (type === 'location') {
                $('#origin_location_field').show();
            } else if (type === 'department') {
                $('#origin_department_field').show();
            } else if (type === 'external') {
                $('#origin_external_field').show();
            }
            
            // Valider le champ requis
            validateOriginDestinationFields();
        });
        
        // Gestion des champs dynamiques pour la destination
        $('#destination_type').on('change', function() {
            const type = $(this).val();
            $('#destination_location_field, #destination_department_field, #destination_external_field').hide();
            
            if (type === 'location') {
                $('#destination_location_field').show();
            } else if (type === 'department') {
                $('#destination_department_field').show();
            } else if (type === 'external') {
                $('#destination_external_field').show();
            }
            
            // Valider le champ requis
            validateOriginDestinationFields();
        });
        
        // Fonction pour valider les champs d'origine et de destination
        function validateOriginDestinationFields() {
            const originType = $('#origin_type').val();
            const destType = $('#destination_type').val();
            
            let originValid = true;
            if (originType === 'location') {
                originValid = $('#origin_location_id').val() !== '';
            } else if (originType === 'department') {
                originValid = $('#origin_department_id').val() !== '';
            } else if (originType === 'external') {
                originValid = $('#origin_external').val().trim() !== '';
            }
            
            let destValid = true;
            if (destType === 'location') {
                destValid = $('#destination_location_id').val() !== '';
            } else if (destType === 'department') {
                destValid = $('#destination_department_id').val() !== '';
            } else if (destType === 'external') {
                destValid = $('#destination_external').val().trim() !== '';
            }
            
            return originValid && destValid;
        }
        
        // Mettre à jour le récapitulatif
        function updateSummary() {
            // Équipement
            const equipment = $('#equipment_id option:selected').text();
            const category = $('#equipment_id option:selected').data('category') || 'Non spécifiée';
            const model = $('#equipment_id option:selected').data('model') || 'Non spécifié';
            const serial = $('#equipment_id option:selected').data('serial') || 'Non spécifié';
            
            $('#summary_equipment').html(`<strong>Équipement :</strong> ${equipment}`);
            $('#summary_category').html(`<strong>Catégorie :</strong> ${category}`);
            $('#summary_model').html(`<strong>Modèle :</strong> ${model}`);
            $('#summary_serial').html(`<strong>N° de série :</strong> ${serial}`);
            
            // Origine
            const originType = $('#origin_type').val();
            let originText = '';
            
            if (originType === 'location') {
                originText = $('#origin_location_id option:selected').text();
            } else if (originType === 'department') {
                originText = $('#origin_department_id option:selected').text();
            } else if (originType === 'external') {
                originText = $('#origin_external').val();
            }
            
            $('#summary_origin').html(`<strong>${$('#origin_type option:selected').text()} :</strong> ${originText}`);
            $('#summary_origin_contact').html(`<strong>Contact :</strong> ${$('#origin_contact').val() || 'Non spécifié'}`);
            
            // Destination
            const destType = $('#destination_type').val();
            let destText = '';
            
            if (destType === 'location') {
                destText = $('#destination_location_id option:selected').text();
            } else if (destType === 'department') {
                destText = $('#destination_department_id option:selected').text();
            } else if (destType === 'external') {
                destText = $('#destination_external').val();
            }
            
            $('#summary_destination').html(`<strong>${$('#destination_type option:selected').text()} :</strong> ${destText}`);
            $('#summary_destination_contact').html(`<strong>Contact :</strong> ${$('#destination_contact').val() || 'Non spécifié'}`);
            
            // Détails du mouvement
            $('#summary_movement_type').text($('#movement_type option:selected').text());
            $('#summary_priority').text($('#priority option:selected').text());
            
            const scheduledDate = new Date($('#scheduled_date').val());
            $('#summary_scheduled_date').text(scheduledDate.toLocaleString('fr-FR'));
            
            const assignedTo = $('#assigned_to option:selected').text();
            $('#summary_assigned_to').text(assignedTo || 'Non assigné');
            
            // Notes
            const notes = $('#notes').val() || 'Aucune note';
            $('#summary_notes').text(notes);
        }
        
        // Navigation entre les étapes
        let currentStep = 1;
        
        function goToStep(step) {
            // Validation avant de passer à l'étape suivante
            if (step > currentStep) {
                let isValid = true;
                
                if (step === 2 && !$('#equipment_id').val()) {
                    alert('Veuillez sélectionner un équipement');
                    return false;
                }
                
                if (step === 3) {
                    if (!validateOriginDestinationFields()) {
                        alert('Veuillez remplir tous les champs obligatoires pour l\'origine et la destination');
                        return false;
                    }
                }
                
                if (step === 4) {
                    if (!$('#movement_type').val() || !$('#scheduled_date').val()) {
                        alert('Veuillez remplir tous les champs obligatoires');
                        return false;
                    }
                }
            }
            
            $('.section').removeClass('active');
            $(`#section-${step}`).addClass('active');
            
            $('.step').removeClass('active');
            $(`#step-${step}`).addClass('active');
            
            currentStep = step;
            
            // Mettre à jour le récapitulatif avant d'afficher l'étape 4
            if (step === 4) {
                updateSummary();
            }
            
            return true;
        }
        
        // Gestion des boutons de navigation
        $('#nextToStep2').on('click', function() {
            goToStep(2);
        });
        
        $('#backToStep1').on('click', function() {
            goToStep(1);
        });
        
        $('#nextToStep3').on('click', function() {
            goToStep(3);
        });
        
        $('#backToStep2').on('click', function() {
            goToStep(2);
        });
        
        $('#nextToStep4').on('click', function() {
            goToStep(4);
        });
        
        $('#backToStep3').on('click', function() {
            goToStep(3);
        });
        
        // Gestion de la soumission du formulaire
        $('#movementForm').on('submit', function(e) {
            if (!$('#terms_accepted').is(':checked')) {
                e.preventDefault();
                alert('Veuvez accepter les conditions d\'utilisation pour soumettre le formulaire');
                return false;
            }
            
            // Afficher un indicateur de chargement
            const submitBtn = $('#submitForm');
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Soumission en cours...');
        });
        
        // Initialiser les champs dynamiques
        $('#origin_type, #destination_type').trigger('change');
    });
</script>
@endpush