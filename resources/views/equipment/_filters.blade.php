<form method="GET" action="{{ route('equipment.index') }}" class="mb-4">
    <div class="row g-3">
        <div class="col-md-3">
            <label for="search" class="form-label">Recherche</label>
            <input type="text" 
                   class="form-control form-control-sm" 
                   id="search" 
                   name="search" 
                   value="{{ request('search') }}" 
                   placeholder="Nom, n° série, modèle...">
        </div>
        
        <div class="col-md-2">
            <label for="status" class="form-label">Statut</label>
            <select class="form-select form-select-sm" id="status" name="status">
                <option value="">Tous</option>
                @foreach(['available' => 'Disponible', 'in_use' => 'En utilisation', 'maintenance' => 'En maintenance', 'out_of_service' => 'Hors service'] as $value => $label)
                    <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="col-md-2">
            <label for="category_id" class="form-label">Catégorie</label>
            <select class="form-select form-select-sm" id="category_id" name="category_id">
                <option value="">Toutes</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="col-md-2">
            <label for="location_id" class="form-label">Emplacement</label>
            <select class="form-select form-select-sm" id="location_id" name="location_id">
                <option value="">Tous</option>
                @foreach($locations as $location)
                    <option value="{{ $location->id }}" {{ request('location_id') == $location->id ? 'selected' : '' }}>
                        {{ $location->name }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="col-md-3 d-flex align-items-end">
            <div class="btn-group w-100">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-search me-1"></i> Filtrer
                </button>
                <a href="{{ route('equipment.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-undo me-1"></i> Réinitialiser
                </a>
            </div>
        </div>
    </div>
    
    <!-- Filtres avancés -->
    <div class="row mt-3" id="advancedFilters" style="display: none;">
        <div class="col-md-3">
            <label for="manufacturer" class="form-label">Fabricant</label>
            <input type="text" 
                   class="form-control form-control-sm" 
                   id="manufacturer" 
                   name="manufacturer" 
                   value="{{ request('manufacturer') }}" 
                   placeholder="Filtrer par fabricant">
        </div>
        
        <div class="col-md-3">
            <label for="condition" class="form-label">État</label>
            <select class="form-select form-select-sm" id="condition" name="condition">
                <option value="">Tous</option>
                @foreach(['excellent' => 'Excellent', 'good' => 'Bon', 'fair' => 'Moyen', 'poor' => 'Mauvais'] as $value => $label)
                    <option value="{{ $value }}" {{ request('condition') == $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="col-md-3">
            <label for="assigned_to" class="form-label">Assigné à</label>
            <select class="form-select form-select-sm" id="assigned_to" name="assigned_to">
                <option value="">Tous</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('assigned_to') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="col-md-3">
            <label for="warranty_expires" class="form-label">Garantie</label>
            <select class="form-select form-select-sm" id="warranty_expires" name="warranty_expires">
                <option value="">Tous</option>
                <option value="soon" {{ request('warranty_expires') == 'soon' ? 'selected' : '' }}>Expire bientôt (dans 3 mois)</option>
                <option value="expired" {{ request('warranty_expires') == 'expired' ? 'selected' : '' }}>Expirée</option>
            </select>
        </div>
    </div>
    
    <div class="row mt-2">
        <div class="col-12 text-end">
            <button type="button" 
                    class="btn btn-sm btn-link p-0 text-decoration-none" 
                    id="toggleAdvancedFilters">
                <i class="fas fa-caret-down me-1"></i> Filtres avancés
            </button>
        </div>
    </div>
</form>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleButton = document.getElementById('toggleAdvancedFilters');
        const advancedFilters = document.getElementById('advancedFilters');
        
        toggleButton.addEventListener('click', function() {
            if (advancedFilters.style.display === 'none') {
                advancedFilters.style.display = 'flex';
                toggleButton.innerHTML = '<i class="fas fa-caret-up me-1"></i> Masquer les filtres avancés';
            } else {
                advancedFilters.style.display = 'none';
                toggleButton.innerHTML = '<i class="fas fa-caret-down me-1"></i> Filtres avancés';
            }
        });
        
        // Afficher les filtres avancés s'il y a des valeurs
        const hasAdvancedFilters = [
            'manufacturer', 
            'condition', 
            'assigned_to', 
            'warranty_expires'
        ].some(param => new URLSearchParams(window.location.search).has(param));
        
        if (hasAdvancedFilters) {
            advancedFilters.style.display = 'flex';
            toggleButton.innerHTML = '<i class="fas fa-caret-up me-1"></i> Masquer les filtres avancés';
        }
    });
</script>
@endpush
