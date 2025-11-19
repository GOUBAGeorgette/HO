@extends('layouts.maquette')

@section('title', 'Créer une catégorie')

@push('styles')
<style>
    .category-preview {
        max-width: 100%;
        height: auto;
        max-height: 200px;
        border-radius: 0.5rem;
        display: none;
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
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Créer une catégorie</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">Catégories</a></li>
                @if(isset($parent) && $parent)
                    @foreach($parent->ancestors->reverse() as $ancestor)
                        <li class="breadcrumb-item"><a href="{{ route('categories.show', $ancestor) }}">{{ $ancestor->name }}</a></li>
                    @endforeach
                    @if(!$parent->isRoot())
                        <li class="breadcrumb-item"><a href="{{ route('categories.show', $parent) }}">{{ $parent->name }}</a></li>
                    @endif
                @endif
                <li class="breadcrumb-item active" aria-current="page">Nouvelle</li>
            </ol>
        </nav>
    </div>
    
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informations de la catégorie</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data" id="categoryForm">
                        @csrf
                        
                        @if(isset($parent) && $parent)
                            <input type="hidden" name="parent_id" value="{{ $parent->id }}">
                        @endif
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nom de la catégorie <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="code" class="form-label">Code</label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                           id="code" name="code" value="{{ old('code') }}">
                                    <div class="form-text">Code court pour identifier la catégorie (ex: IT, FURN, etc.)</div>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                @if(!isset($parent) || !$parent)
                                    <div class="mb-3">
                                        <label for="parent_id" class="form-label">Catégorie parente</label>
                                        <select class="form-select @error('parent_id') is-invalid @enderror" 
                                                id="parent_id" name="parent_id">
                                            <option value="">Aucune (catégorie racine)</option>
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat->id }}" {{ old('parent_id') == $cat->id ? 'selected' : '' }}>
                                                    {{ $cat->name }}
                                                </option>
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
                                        <option value="laptop" {{ old('icon') == 'laptop' ? 'selected' : '' }} data-icon="laptop">
                                            <i class="fas fa-laptop"></i> Ordinateur portable
                                        </option>
                                        <option value="desktop" {{ old('icon') == 'desktop' ? 'selected' : '' }} data-icon="desktop">
                                            <i class="fas fa-desktop"></i> Ordinateur de bureau
                                        </option>
                                        <option value="mobile-alt" {{ old('icon') == 'mobile-alt' ? 'selected' : '' }} data-icon="mobile-alt">
                                            <i class="fas fa-mobile-alt"></i> Téléphone portable
                                        </option>
                                        <option value="tablet-alt" {{ old('icon') == 'tablet-alt' ? 'selected' : '' }} data-icon="tablet-alt">
                                            <i class="fas fa-tablet-alt"></i> Tablette
                                        </option>
                                        <option value="print" {{ old('icon') == 'print' ? 'selected' : '' }} data-icon="print">
                                            <i class="fas fa-print"></i> Imprimante
                                        </option>
                                        <option value="tv" {{ old('icon') == 'tv' ? 'selected' : '' }} data-icon="tv">
                                            <i class="fas fa-tv"></i> Écran
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
                                               {{ old('is_active', true) ? 'checked' : '' }}>
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
                                             src="{{ asset('img/default-category.png') }}" 
                                             alt="Aperçu de l'image" 
                                             class="img-fluid mt-2 category-preview">
                                    </div>
                                </div>
                                
                                <div class="card border-left-primary shadow h-100 py-2 mb-4">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                    Statut
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    <span id="statusBadge" class="badge bg-success">Active</span>
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ isset($parent) ? route('categories.show', $parent) : route('categories.index') }}" 
                               class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion de l'aperçu de l'image
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('imagePreview');
        
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
                } else {
                    imagePreview.src = '{{ asset('img/default-category.png') }}';
                    imagePreview.style.display = 'block';
                }
            });
            
            // Afficher l'aperçu par défaut
            imagePreview.style.display = 'block';
        }
        
        // Gestion du statut actif/inactif
        const isActiveCheckbox = document.getElementById('is_active');
        const statusBadge = document.getElementById('statusBadge');
        
        if (isActiveCheckbox && statusBadge) {
            isActiveCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    statusBadge.textContent = 'Active';
                    statusBadge.className = 'badge bg-success';
                } else {
                    statusBadge.textContent = 'Inactive';
                    statusBadge.className = 'badge bg-secondary';
                }
            });
            
            // Déclencher l'événement change au chargement
            isActiveCheckbox.dispatchEvent(new Event('change'));
        }
        
        // Validation du formulaire
        const form = document.getElementById('categoryForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                const nameInput = document.getElementById('name');
                if (!nameInput.value.trim()) {
                    e.preventDefault();
                    nameInput.classList.add('is-invalid');
                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.textContent = 'Le nom de la catégorie est requis';
                    nameInput.parentNode.appendChild(feedback);
                    nameInput.focus();
                }
            });
        }
    });
</script>
@endpush
@endsection
