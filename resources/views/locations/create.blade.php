@extends('layouts.maquette')

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('locationForm');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('Form submission started');
        
        // Afficher les données du formulaire
        const formData = new FormData(form);
        for (let [key, value] of formData.entries()) {
            console.log(key + ': ' + value);
        }
        
        // Soumettre le formulaire manuellement
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.text().then(text => {
                console.log('Response text:', text);
                if (response.redirected) {
                    window.location.href = response.url;
                } else if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            });
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Une erreur est survenue lors de la création de l\'emplacement.');
        });
    });
});
</script>
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>{{ __('Créer un nouvel emplacement') }}</span>
                        <a href="{{ route('locations.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <form id="locationForm" method="POST" action="{{ route('locations.store') }}" onsubmit="console.log('Form submitted');">
                        @csrf

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">
                                {{ __('Nom') }} <span class="text-danger">*</span>
                            </label>

                            <div class="col-md-6">
                                <input id="name" type="text" 
                                    class="form-control @error('name') is-invalid @enderror" 
                                    name="name" 
                                    value="{{ old('name') }}" 
                                    required 
                                    autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>


                        <div class="row mb-3">
                            <label for="building" class="col-md-4 col-form-label text-md-end">
                                {{ __('Bâtiment') }}
                            </label>

                            <div class="col-md-6">
                                <input id="building" type="text" 
                                    class="form-control @error('building') is-invalid @enderror" 
                                    name="building" 
                                    value="{{ old('building') }}">

                                @error('building')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="room" class="col-md-4 col-form-label text-md-end">
                                Salle / Local
                            </label>

                            <div class="col-md-6">
                                <input id="room" type="text" 
                                    class="form-control @error('room') is-invalid @enderror" 
                                    name="room" 
                                    value="{{ old('room') }}">

                                @error('room')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="description" class="col-md-4 col-form-label text-md-end">
                                {{ __('Description') }}
                            </label>

                            <div class="col-md-6">
                                <textarea id="description" 
                                    class="form-control @error('description') is-invalid @enderror" 
                                    name="description" 
                                    rows="3">{{ old('description') }}</textarea>

                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                        type="checkbox" 
                                        name="is_active" 
                                        id="is_active" 
                                        {{ old('is_active', true) ? 'checked' : '' }}>

                                    <label class="form-check-label" for="is_active">
                                        {{ __('Emplacement actif') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" id="submitBtn" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> {{ __('Enregistrer') }}
                                </button>
                                <script>
                                    document.getElementById('locationForm').addEventListener('submit', function(e) {
                                        console.log('Form submission intercepted');
                                        // Ne pas empêcher la soumission du formulaire
                                    });
                                </script>
                                <a href="{{ route('locations.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i> {{ __('Annuler') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
