@extends('layouts.maquette')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Modifier l'équipement : {{ $equipment->name }}</h5>
                    <a href="{{ route('equipment.show', $equipment) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('equipment.update', $equipment) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Informations de base -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6>Informations de base</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Nom *</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $equipment->name) }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="model" class="form-label">Modèle</label>
                                                    <input type="text" class="form-control @error('model') is-invalid @enderror" id="model" name="model" value="{{ old('model', $equipment->model) }}">
                                                    @error('model')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="brand" class="form-label">Marque</label>
                                                    <input type="text" class="form-control @error('brand') is-invalid @enderror" id="brand" name="brand" value="{{ old('brand', $equipment->brand) }}">
                                                    @error('brand')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="model" class="form-label">Modèle</label>
                                                    <input type="text" class="form-control @error('model') is-invalid @enderror" id="model" name="model" value="{{ old('model', $equipment->model) }}">
                                                    @error('model')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="manufacturer" class="form-label">Fabricant</label>
                                                    <input type="text" class="form-control @error('manufacturer') is-invalid @enderror" id="manufacturer" name="manufacturer" value="{{ old('manufacturer', $equipment->manufacturer) }}">
                                                    @error('manufacturer')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="type" class="form-label">Type</label>
                                            <input type="text" class="form-control @error('type') is-invalid @enderror" id="type" name="type" value="{{ old('type', $equipment->type) }}">
                                            @error('type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="quantity" class="form-label">Quantité</label>
                                            <input type="number" min="1" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity', $equipment->quantity) }}">
                                            @error('quantity')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Catégorie et emplacement -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6>Catégorie et emplacement</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="category_id" class="form-label">Catégorie *</label>
                                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                                <option value="">Sélectionner une catégorie</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" {{ old('category_id', $equipment->category_id) == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('category_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="location_id" class="form-label">Emplacement *</label>
                                            <select class="form-select @error('location_id') is-invalid @enderror" id="location_id" name="location_id" required>
                                                <option value="">Sélectionner un emplacement</option>
                                                @foreach($locations as $location)
                                                    <option value="{{ $location->id }}" {{ old('location_id', $equipment->location_id) == $location->id ? 'selected' : '' }}>
                                                        {{ $location->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('location_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="assigned_to" class="form-label">Assigné à</label>
                                            <select class="form-select @error('assigned_to') is-invalid @enderror" id="assigned_to" name="assigned_to">
                                                <option value="">Non assigné</option>
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}" {{ old('assigned_to', $equipment->assigned_to) == $user->id ? 'selected' : '' }}>
                                                        {{ $user->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('assigned_to')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Responsable et maintenance -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6>Responsable et maintenance</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="responsible_person" class="form-label">Personne responsable</label>
                                            <input type="text" class="form-control @error('responsible_person') is-invalid @enderror" id="responsible_person" name="responsible_person" value="{{ old('responsible_person', $equipment->responsible_person) }}">
                                            @error('responsible_person')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="maintenance_frequency" class="form-label">Fréquence de maintenance</label>
                                            <input type="text" class="form-control @error('maintenance_frequency') is-invalid @enderror" id="maintenance_frequency" name="maintenance_frequency" value="{{ old('maintenance_frequency', $equipment->maintenance_frequency) }}">
                                            @error('maintenance_frequency')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="maintenance_tasks" class="form-label">Tâches de maintenance</label>
                                            <textarea class="form-control @error('maintenance_tasks') is-invalid @enderror" id="maintenance_tasks" name="maintenance_tasks" rows="2">{{ old('maintenance_tasks', $equipment->maintenance_tasks) }}</textarea>
                                            @error('maintenance_tasks')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="maintenance_type" class="form-label">Type de maintenance</label>
                                            <input type="text" class="form-control @error('maintenance_type') is-invalid @enderror" id="maintenance_type" name="maintenance_type" value="{{ old('maintenance_type', $equipment->maintenance_type) }}">
                                            @error('maintenance_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input @error('is_usable') is-invalid @enderror" type="checkbox" id="is_usable" name="is_usable" value="1" {{ old('is_usable', $equipment->is_usable) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_usable">Utilisable</label>
                                            @error('is_usable')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                                    <input type="date" class="form-control @error('purchase_date') is-invalid @enderror" id="purchase_date" name="purchase_date" value="{{ old('purchase_date', $equipment->purchase_date ? $equipment->purchase_date->format('Y-m-d') : '') }}">
                                                    @error('purchase_date')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="purchase_cost" class="form-label">Coût d'achat (€)</label>
                                                    <input type="number" step="0.01" class="form-control @error('purchase_cost') is-invalid @enderror" id="purchase_cost" name="purchase_cost" value="{{ old('purchase_cost', $equipment->purchase_cost) }}">
                                                    @error('purchase_cost')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="warranty_months" class="form-label">Garantie (mois)</label>
                                                    <input type="number" class="form-control @error('warranty_months') is-invalid @enderror" id="warranty_months" name="warranty_months" value="{{ old('warranty_months', $equipment->warranty_months) }}">
                                                    @error('warranty_months')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="status" class="form-label">Statut *</label>
                                                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                                        @foreach(['available' => 'Disponible', 'in_use' => 'En utilisation', 'maintenance' => 'En maintenance', 'out_of_service' => 'Hors service'] as $value => $label)
                                                            <option value="{{ $value }}" {{ old('status', $equipment->status) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('status')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="status" class="form-label">État</label>
                                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                                <option value="">Sélectionner un état</option>
                                                @foreach(['excellent' => 'Excellent', 'bon' => 'Bon', 'moyen' => 'Moyen', 'mauvais' => 'Mauvais', 'hors_service' => 'Hors service'] as $value => $label)
                                                    <option value="{{ $value }}" {{ old('status', $equipment->status) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            @error('condition')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="notes" class="form-label">Notes</label>
                                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $equipment->notes) }}</textarea>
                                            @error('notes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="suggestions" class="form-label">Suggestions</label>
                                            <textarea class="form-control @error('suggestions') is-invalid @enderror" id="suggestions" name="suggestions" rows="2">{{ old('suggestions', $equipment->suggestions) }}</textarea>
                                            @error('suggestions')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Image -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6>Image</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="image" class="form-label">Image de l'équipement</label>
                                            <input class="form-control @error('image') is-invalid @enderror" type="file" id="image" name="image" accept="image/*">
                                            @error('image')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            @if($equipment->image_path)
                                                <div class="mt-2">
                                                    <img src="{{ asset('storage/' . $equipment->image_path) }}" alt="Image actuelle" class="img-thumbnail" style="max-width: 200px;">
                                                    <div class="form-check mt-2">
                                                        <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image" value="1">
                                                        <label class="form-check-label" for="remove_image">
                                                            Supprimer l'image actuelle
                                                        </label>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </n>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteEquipmentModal">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                            <div>
                                <a href="{{ route('equipment.show', $equipment) }}" class="btn btn-secondary me-2">
                                    <i class="fas fa-times"></i> Annuler
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Enregistrer les modifications
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteEquipmentModal" tabindex="-1" aria-labelledby="deleteEquipmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteEquipmentModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer définitivement cet équipement ? Cette action est irréversible.
                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" id="confirm_delete" name="confirm_delete">
                    <label class="form-check-label" for="confirm_delete">
                        Je confirme vouloir supprimer cet équipement
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('equipment.destroy', $equipment) }}" method="POST" id="deleteForm">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" id="deleteButton" disabled>
                        <i class="fas fa-trash"></i> Supprimer définitivement
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Activer le bouton de suppression uniquement si la case est cochée
    document.getElementById('confirm_delete').addEventListener('change', function() {
        document.getElementById('deleteButton').disabled = !this.checked;
    });

    // Gérer la soumission du formulaire de suppression
    document.getElementById('deleteForm').addEventListener('submit', function(e) {
        if (!confirm('Êtes-vous absolument sûr de vouloir supprimer cet équipement ? Cette action est irréversible.')) {
            e.preventDefault();
        }
    });
</script>
@endpush

@endsection
