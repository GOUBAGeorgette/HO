@extends('layouts.maquette')

@section('title', 'Modifier la catégorie : ' . $category->name)

@push('styles')
<style>
    .category-preview {
        max-width: 100%;
        height: auto;
        max-height: 200px;
        border-radius: 0.5rem;
    }
    
    .icon-option {
        width: 30px;
        height: 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 5px;
        border-radius: 4px;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
    }
    
    .icon-option i {
        font-size: 1rem;
    }
    
    .select2-container--default .select2-selection--single {
        height: 38px;
        padding: 5px;
        border: 1px solid #d1d3e2;
        border-radius: 0.35rem;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 26px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Modifier la catégorie</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">Catégories</a></li>
                @php
                    $ancestors = collect();
                    $parent = $category->parent;
                    
                    while ($parent) {
                        $ancestors->push($parent);
                        $parent = $parent->parent;
                    }
                    $ancestors = $ancestors->reverse();
                @endphp
                
                @foreach($ancestors as $ancestor)
                    <li class="breadcrumb-item">
                        <a href="{{ route('categories.show', $ancestor) }}">{{ $ancestor->name }}</a>
                    </li>
                @endforeach
                <li class="breadcrumb-item active" aria-current="page">Modifier</li>
            </ol>
        </nav>
    </div>
    
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Modifier les informations</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('categories.update', $category) }}" method="POST" enctype="multipart/form-data" id="categoryForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nom de la catégorie <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $category->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="code" class="form-label">Code</label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                           id="code" name="code" value="{{ old('code', $category->code) }}">
                                    <div class="form-text">Code court pour identifier la catégorie (ex: IT, FURN, etc.)</div>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3">{{ old('description', $category->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                @if(!$category->isRoot())
                                    <div class="mb-3">
                                        <label for="parent_id" class="form-label">Catégorie parente</label>
                                        <select class="form-select @error('parent_id') is-invalid @enderror" 
                                                id="parent_id" name="parent_id">
                                            <option value="">Aucune (catégorie racine)</option>
                                            @foreach($categories as $cat)
                                                @if($cat->id !== $category->id && !$category->isDescendantOf($cat))
                                                    <option value="{{ $cat->id }}" {{ old('parent_id', $category->parent_id) == $cat->id ? 'selected' : '' }}>
                                                        {{ $cat->name }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                        @error('parent_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endif
                                
                                <div class="mb-3">
                                    <label for="icon" class="form-label">Icône</label>
                                    <select class="form-select @error('icon') is-invalid @enderror" 
                                            id="icon" name="icon">
                                        <option value="">Sélectionner une icône</option>
                                        <option value="laptop" {{ old('icon', $category->icon) == 'laptop' ? 'selected' : '' }} data-icon="laptop">
                                            <i class="fas fa-laptop"></i> Ordinateur portable
                                        </option>
                                        <option value="desktop" {{ old('icon', $category->icon) == 'desktop' ? 'selected' : '' }} data-icon="desktop">
                                            <i class="fas fa-desktop"></i> Ordinateur de bureau
                                        </option>
                                        <option value="mobile-alt" {{ old('icon', $category->icon) == 'mobile-alt' ? 'selected' : '' }} data-icon="mobile-alt">
                                            <i class="fas fa-mobile-alt"></i> Téléphone portable
                                        </option>
                                        <option value="tablet-alt" {{ old('icon', $category->icon) == 'tablet-alt' ? 'selected' : '' }} data-icon="tablet-alt">
                                            <i class="fas fa-tablet-alt"></i> Tablette
                                        </option>
                                        <option value="print" {{ old('icon', $category->icon) == 'print' ? 'selected' : '' }} data-icon="print">
                                            <i class="fas fa-print"></i> Imprimante
                                        </option>
                                        <option value="tv" {{ old('icon', $category->icon) == 'tv' ? 'selected' : '' }} data-icon="tv">
                                            <i class="fas fa-tv"></i> Écran
                                        </option>
                                        <option value="keyboard" {{ old('icon', $category->icon) == 'keyboard' ? 'selected' : '' }} data-icon="keyboard">
                                            <i class="fas fa-keyboard"></i> Clavier
                                        </option>
                                        <option value="mouse" {{ old('icon', $category->icon) == 'mouse' ? 'selected' : '' }} data-icon="mouse">
                                            <i class="fas fa-mouse"></i> Souris
                                        </option>
                                        <option value="server" {{ old('icon', $category->icon) == 'server' ? 'selected' : '' }} data-icon="server">
                                            <i class="fas fa-server"></i> Serveur
                                        </option>
                                        <option value="network-wired" {{ old('icon', $category->icon) == 'network-wired' ? 'selected' : '' }} data-icon="network-wired">
                                            <i class="fas fa-network-wired"></i> Réseau
                                        </option>
                                        <option value="hdd" {{ old('icon', $category->icon) == 'hdd' ? 'selected' : '' }} data-icon="hdd">
                                            <i class="fas fa-hdd"></i> Disque dur
                                        </option>
                                        <option value="usb" {{ old('icon', $category->icon) == 'usb' ? 'selected' : '' }} data-icon="usb">
                                            <i class="fas fa-usb"></i> Périphérique USB
                                        </option>
                                        <option value="camera" {{ old('icon', $category->icon) == 'camera' ? 'selected' : '' }} data-icon="camera">
                                            <i class="fas fa-camera"></i> Appareil photo
                                        </option>
                                        <option value="video" {{ old('icon', $category->icon) == 'video' ? 'selected' : '' }} data-icon="video">
                                            <i class="fas fa-video"></i> Caméra
                                        </option>
                                        <option value="headphones" {{ old('icon', $category->icon) == 'headphones' ? 'selected' : '' }} data-icon="headphones">
                                            <i class="fas fa-headphones"></i> Casque audio
                                        </option>
                                        <option value="microphone" {{ old('icon', $category->icon) == 'microphone' ? 'selected' : '' }} data-icon="microphone">
                                            <i class="fas fa-microphone"></i> Microphone
                                        </option>
                                        <option value="projector" {{ old('icon', $category->icon) == 'projector' ? 'selected' : '' }} data-icon="projector">
                                            <i class="fas fa-tv"></i> Vidéoprojecteur
                                        </option>
                                        <option value="calculator" {{ old('icon', $category->icon) == 'calculator' ? 'selected' : '' }} data-icon="calculator">
                                            <i class="fas fa-calculator"></i> Calculatrice
                                        </option>
                                        <option value="fax" {{ old('icon', $category->icon) == 'fax' ? 'selected' : '' }} data-icon="fax">
                                            <i class="fas fa-fax"></i> Télécopieur
                                        </option>
                                        <option value="toolbox" {{ old('icon', $category->icon) == 'toolbox' ? 'selected' : '' }} data-icon="toolbox">
                                            <i class="fas fa-toolbox"></i> Outillage
                                        </option>
                                        <option value="wrench" {{ old('icon', $category->icon) == 'wrench' ? 'selected' : '' }} data-icon="wrench">
                                            <i class="fas fa-wrench"></i> Outil
                                        </option>
                                        <option value="screwdriver" {{ old('icon', $category->icon) == 'screwdriver' ? 'selected' : '' }} data-icon="screwdriver">
                                            <i class="fas fa-screwdriver"></i> Tournevis
                                        </option>
                                        <option value="ruler" {{ old('icon', $category->icon) == 'ruler' ? 'selected' : '' }} data-icon="ruler">
                                            <i class="fas fa-ruler"></i> Règle
                                        </option>
                                        <option value="ruler-combined" {{ old('icon', $category->icon) == 'ruler-combined' ? 'selected' : '' }} data-icon="ruler-combined">
                                            <i class="fas fa-ruler-combined"></i> Double décimètre
                                        </option>
                                        <option value="balance-scale" {{ old('icon', $category->icon) == 'balance-scale' ? 'selected' : '' }} data-icon="balance-scale">
                                            <i class="fas fa-balance-scale"></i> Balance
                                        </option>
                                        <option value="flask" {{ old('icon', $category->icon) == 'flask' ? 'selected' : '' }} data-icon="flask">
                                            <i class="fas fa-flask"></i> Éprouvette
                                        </option>
                                        <option value="microscope" {{ old('icon', $category->icon) == 'microscope' ? 'selected' : '' }} data-icon="microscope">
                                            <i class="fas fa-microscope"></i> Microscope
                                        </option>
                                        <option value="vial" {{ old('icon', $category->icon) == 'vial' ? 'selected' : '' }} data-icon="vial">
                                            <i class="fas fa-vial"></i> Tube à essai
                                        </option>
                                        <option value="vials" {{ old('icon', $category->icon) == 'vials' ? 'selected' : '' }} data-icon="vials">
                                            <i class="fas fa-vials"></i> Tubes à essai
                                        </option>
                                        <option value="thermometer-half" {{ old('icon', $category->icon) == 'thermometer-half' ? 'selected' : '' }} data-icon="thermometer-half">
                                            <i class="fas fa-thermometer-half"></i> Thermomètre
                                        </option>
                                        <option value="tint" {{ old('icon', $category->icon) == 'tint' ? 'selected' : '' }} data-icon="tint">
                                            <i class="fas fa-tint"></i> Goutte
                                        </option>
                                    </select>
                                    @error('icon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               id="is_active" name="is_active" value="1" 
                                               {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Catégorie active
                                        </label>
                                    </div>
                                    <div class="form-text">Décochez pour désactiver cette catégorie</div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="image" class="form-label">Image de la catégorie</label>
                                    <input type="file" 
                                           class="form-control @error('image') is-invalid @enderror" 
                                           id="image" 
                                           name="image" 
                                           accept="image/*">
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    
                                    <div class="mt-2 text-center">
                                        <img id="imagePreview" 
                                             src="{{ $category->image_path ? asset('storage/' . $category->image_path) : asset('img/default-category.png') }}" 
                                             alt="Aperçu de l'image" 
                                             class="img-fluid mt-2 category-preview">
                                    </div>
                                    
                                    @if($category->image_path)
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="remove_image" name="remove_image" value="1">
                                            <label class="form-check-label text-danger" for="remove_image">
                                                Supprimer l'image actuelle
                                            </label>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="card border-left-primary shadow h-100 py-2 mb-4">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                    Statut
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    <span id="statusBadge" class="badge {{ $category->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                        {{ $category->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas {{ $category->is_active ? 'fa-check-circle' : 'fa-times-circle' }} fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card border-left-info shadow h-100 py-2 mb-4">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                    Équipements
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    {{ $category->equipments_count }}
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-boxes fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                @if($category->children_count > 0)
                                    <div class="card border-left-warning shadow h-100 py-2 mb-4">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                        Sous-catégories
                                                    </div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                        {{ $category->children_count }}
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-sitemap fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('categories.show', $category) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Annuler
                            </a>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Enregistrer les modifications
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Section de suppression -->
            @if($category->canBeDeleted())
                <div class="card shadow mb-4 border-left-danger">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-danger">Zone dangereuse</h6>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title text-danger">Supprimer cette catégorie</h5>
                        <p class="card-text">
                            La suppression de cette catégorie est irréversible. 
                            @if($category->children_count > 0)
                                <strong>Toutes les sous-catégories et leurs équipements seront également supprimés.</strong>
                            @endif
                            @if($category->equipments_count > 0)
                                <strong>Tous les équipements de cette catégorie seront également supprimés.</strong>
                            @endif
                        </p>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteCategoryModal">
                            <i class="fas fa-trash-alt me-1"></i> Supprimer la catégorie
                        </button>
                    </div>
                </div>
            @else
                <div class="card shadow mb-4 border-left-warning">
                    <div class="card-body">
                        <h5 class="card-title text-warning">Suppression non autorisée</h5>
                        <p class="card-text">
                            Cette catégorie ne peut pas être supprimée car elle contient des équipements ou des sous-catégories.
                            Veuillez d'abord supprimer ou déplacer tous les équipements et sous-catégories avant de pouvoir supprimer cette catégorie.
                        </p>
                        <div class="mt-3">
                            <a href="{{ route('categories.show', $category) }}" class="btn btn-primary">
                                <i class="fas fa-eye me-1"></i> Voir les détails
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de suppression -->
@if($category->canBeDeleted())
    <div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-labelledby="deleteCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteCategoryModalLabel">Confirmer la suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer définitivement cette catégorie ?</p>
                    <ul class="mb-0">
                        <li>Nom : <strong>{{ $category->name }}</strong></li>
                        @if($category->code)
                            <li>Code : <strong>{{ $category->code }}</strong></li>
                        @endif
                        @if($category->children_count > 0)
                            <li class="text-danger">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                {{ $category->children_count }} sous-catégorie(s) seront également supprimée(s)
                            </li>
                        @endif
                        @if($category->equipments_count > 0)
                            <li class="text-danger">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                {{ $category->equipments_count }} équipement(s) seront également supprimé(s)
                            </li>
                        @endif
                    </ul>
                    <div class="alert alert-danger mt-3">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>Attention :</strong> Cette action est irréversible.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Annuler
                    </button>
                    <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash-alt me-1"></i> Confirmer la suppression
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion de l'aperçu de l'image
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('imagePreview');
        const removeImageCheckbox = document.getElementById('remove_image');
        
        if (imageInput && imagePreview) {
            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        imagePreview.style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                    
                    // Décocher la case à cocher de suppression si une nouvelle image est sélectionnée
                    if (removeImageCheckbox) {
                        removeImageCheckbox.checked = false;
                    }
                }
            });
            
            // Gestion de la case à cocher de suppression d'image
            if (removeImageCheckbox) {
                removeImageCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        imagePreview.src = '{{ asset('img/default-category.png') }}';
                        imagePreview.style.display = 'block';
                    } else if (!imageInput.files.length) {
                        // Si l'utilisateur décoche et qu'aucune nouvelle image n'est sélectionnée
                        imagePreview.src = '{{ $category->image_path ? asset('storage/' . $category->image_path) : asset('img/default-category.png') }}';
                    }
                });
            }
        }
        
        // Gestion du statut actif/inactif
        const isActiveCheckbox = document.getElementById('is_active');
        const statusBadge = document.getElementById('statusBadge');
        
        if (isActiveCheckbox && statusBadge) {
            isActiveCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    statusBadge.textContent = 'Active';
                    statusBadge.className = 'badge bg-success';
                    document.querySelector('.fa-2x').className = 'fas fa-check-circle fa-2x text-gray-300';
                } else {
                    statusBadge.textContent = 'Inactive';
                    statusBadge.className = 'badge bg-secondary';
                    document.querySelector('.fa-2x').className = 'fas fa-times-circle fa-2x text-gray-300';
                }
            });
        }
        
        // Validation du formulaire
        const form = document.getElementById('categoryForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                const nameInput = document.getElementById('name');
                if (!nameInput.value.trim()) {
                    e.preventDefault();
                    nameInput.classList.add('is-invalid');
                    
                    // Supprimer les messages d'erreur existants
                    const existingFeedback = nameInput.nextElementSibling;
                    if (!existingFeedback || !existingFeedback.classList.contains('invalid-feedback')) {
                        const feedback = document.createElement('div');
                        feedback.className = 'invalid-feedback';
                        feedback.textContent = 'Le nom de la catégorie est requis';
                        nameInput.parentNode.insertBefore(feedback, nameInput.nextSibling);
                    }
                    
                    nameInput.focus();
                }
            });
        }
        
        // Initialisation de Select2 si présent
        if (jQuery && jQuery.fn.select2) {
            jQuery('#parent_id').select2({
                placeholder: 'Sélectionnez une catégorie parente',
                allowClear: true,
                width: '100%'
            });
            
            jQuery('#icon').select2({
                templateResult: formatIcon,
                templateSelection: formatIcon,
                escapeMarkup: function(m) { return m; },
                width: '100%'
            });
        }
        
        // Fonction de formatage des icônes pour Select2
        function formatIcon(icon) {
            if (!icon.id) { return icon.text; }
            var $icon = jQuery(
                '<span><i class="fas fa-' + jQuery(icon.element).data('icon') + ' me-2"></i>' + icon.text + '</span>'
            );
            return $icon;
        }
    });
</script>
@endpush
@endsection
