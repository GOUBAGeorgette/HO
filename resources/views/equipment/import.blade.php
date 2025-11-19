@extends('layouts.maquette')

@section('title', 'Importer des équipements')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-import me-2"></i> Importer des équipements
                    </h3>
                </div>
                
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Téléchargez le modèle d'importation et suivez les instructions pour importer vos équipements.
                    </div>
                    
                    <div class="d-flex justify-content-between mb-4">
                        <a href="{{ route('equipment.export') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-file-export me-1"></i> Télécharger le modèle
                        </a>
                        
                        
                    </div>
                    
                    <form action="{{ route('import.submit') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="file" class="form-label">
                                Fichier d'importation <span class="text-danger">*</span>
                            </label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" required>
                            <div class="form-text">
                                Formats acceptés : .xlsx, .xls, .csv (max: 10 Mo)
                            </div>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('equipment.index') }}" class="btn btn-secondary me-md-2">
                                <i class="fas fa-times me-1"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload me-1"></i> Importer
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="card-footer">
                    <h5 class="mb-3">Instructions d'importation :</h5>
                    <ol class="mb-0">
                        <li>Téléchargez le modèle d'importation</li>
                        <li>Remplissez les colonnes avec les données des équipements</li>
                        <li>Enregistrez le fichier au format .xlsx, .xls ou .csv</li>
                        <li>Utilisez le formulaire ci-dessus pour importer le fichier</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
