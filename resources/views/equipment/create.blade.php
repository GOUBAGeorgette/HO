@extends('layouts.maquette')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white mb-6">Ajouter un nouvel équipement</h2>
        
        <form action="{{ route('equipment.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Informations de base -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300">Informations de base</h3>
                    
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nom *</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catégorie *</label>
                        <select name="category_id" id="category_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Sélectionnez une catégorie</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="model" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Modèle</label>
                        <input type="text" name="model" id="model" value="{{ old('model') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <div>
                        <label for="brand" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Marque</label>
                        <input type="text" name="brand" id="brand" value="{{ old('brand') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
                        <input type="text" name="type" id="type" value="{{ old('type') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantité</label>
                        <input type="number" name="quantity" id="quantity" min="1" value="{{ old('quantity', 1) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                </div>

                <!-- État et localisation -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300">État et localisation</h3>
                    
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">État</label>
                        <select name="status" id="status" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="excellent" {{ old('status', 'bon') == 'excellent' ? 'selected' : '' }}>Excellent</option>
                            <option value="bon" {{ old('status', 'bon') == 'bon' ? 'selected' : '' }}>Bon</option>
                            <option value="moyen" {{ old('status') == 'moyen' ? 'selected' : '' }}>Moyen</option>
                            <option value="mauvais" {{ old('status') == 'mauvais' ? 'selected' : '' }}>Mauvais</option>
                            <option value="hors_service" {{ old('status') == 'hors_service' ? 'selected' : '' }}>Hors service</option>
                        </select>
                    </div>

                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Localisation</label>
                        <input type="text" name="location" id="location" value="{{ old('location') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="is_usable" id="is_usable" value="1" 
                            {{ old('is_usable', true) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                        <label for="is_usable" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">Utilisable</label>
                    </div>

                    <div>
                        <label for="assigned_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Responsable</label>
                        <select name="assigned_to" id="assigned_to"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Non assigné</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="responsible_person" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Personne responsable (si non dans la liste)</label>
                        <input type="text" name="responsible_person" id="responsible_person" value="{{ old('responsible_person') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                </div>
            </div>

            <!-- Notes et suggestions -->
            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Informations complémentaires</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                        <textarea name="notes" id="notes" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('notes') }}</textarea>
                    </div>

                    <div>
                        <label for="suggestions" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Suggestions d'amélioration</label>
                        <textarea name="suggestions" id="suggestions" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('suggestions') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Maintenance -->
            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Maintenance</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="maintenance_frequency" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fréquence de maintenance</label>
                        <input type="text" name="maintenance_frequency" id="maintenance_frequency" value="{{ old('maintenance_frequency') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            placeholder="Ex: Tous les 6 mois">
                    </div>

                    <div>
                        <label for="maintenance_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type de maintenance</label>
                        <input type="text" name="maintenance_type" id="maintenance_type" value="{{ old('maintenance_type') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            placeholder="Ex: Vérification technique">
                    </div>

                    <div>
                        <label for="maintenance_tasks" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tâches de maintenance</label>
                        <input type="text" name="maintenance_tasks" id="maintenance_tasks" value="{{ old('maintenance_tasks') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            placeholder="Ex: Nettoyage, Vérification">
                    </div>
                </div>
            </div>

            <!-- Documents -->
            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Documents associés</h3>
                
                <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 text-center">
                    <div class="flex justify-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                    </div>
                    <div class="mt-2">
                        <label for="documents" class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                            <span>Télécharger des fichiers</span>
                            <input id="documents" name="documents[]" type="file" multiple class="sr-only">
                        </label>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            ou glissez-déposez des fichiers ici
                        </p>
                    </div>
                </div>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Taille maximale : 10MB. Formats acceptés : PDF, JPG, PNG, DOC, XLS
                </p>
            </div>

            <!-- Boutons d'action -->
            <div class="flex justify-end mt-8 space-x-3">
                <a href="{{ route('equipment.index') }}" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600">
                    Annuler
                </a>
                <button type="submit" 
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Enregistrer l'équipement
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Gestion du glisser-déposer de fichiers
    const dropArea = document.querySelector('.border-dashed');
    const fileInput = document.getElementById('documents');
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, highlight, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, unhighlight, false);
    });
    
    function highlight() {
        dropArea.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
    }
    
    function unhighlight() {
        dropArea.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
    }
    
    dropArea.addEventListener('drop', handleDrop, false);
    
    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles(files);
    }
    
    fileInput.addEventListener('change', function() {
        handleFiles(this.files);
    });
    
    function handleFiles(files) {
        // Ici, vous pouvez ajouter la logique pour gérer les fichiers téléchargés
        console.log(files);
        // Par exemple, afficher un aperçu des fichiers ou les ajouter à un tableau
    }
</script>
@endpush
@endsection
