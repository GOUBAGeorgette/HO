<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EquipmentMovementController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DocumentController;

// Routes publiques
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Routes protégées par authentification
Route::middleware('auth', 'verified')->group(function () {
    // Profil utilisateur
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/import', [EquipmentController::class, 'importForm'])->name('import');
    Route::post('/import', [EquipmentController::class, 'import'])->name('import.submit');
    

    // Routes pour les équipements
    Route::prefix('equipment')->name('equipment.')->group(function() {
        // Routes de base
        Route::get('/', [EquipmentController::class, 'index'])->name('index');
        Route::get('/create', [EquipmentController::class, 'create'])->name('create');
        Route::post('/', [EquipmentController::class, 'store'])->name('store');
        Route::get('/{equipment}', [EquipmentController::class, 'show'])->name('show');
        Route::get('/{equipment}/edit', [EquipmentController::class, 'edit'])->name('edit');
        Route::put('/{equipment}', [EquipmentController::class, 'update'])->name('update');
        Route::delete('/{equipment}', [EquipmentController::class, 'destroy'])->name('destroy');
        
        // Import/Export
        Route::get('/export', [EquipmentController::class, 'export'])->name('export');
        // Autres actions
        Route::post('/{equipment}/maintenance', [EquipmentController::class, 'scheduleMaintenance'])->name('schedule-maintenance');
        Route::post('/{equipment}/move', [EquipmentController::class, 'move'])->name('move');
        Route::post('/{equipment}/assign', [EquipmentController::class, 'assign'])->name('assign');
        Route::delete('/{equipment}/unassign', [EquipmentController::class, 'unassign'])->name('unassign');
        Route::get('/{equipment}/history', [EquipmentController::class, 'history'])->name('history');
    });
    
    // Routes pour les mouvements d'équipements
    Route::prefix('equipment-movements')->name('equipment-movements.')->group(function () {
        // Routes de base
        Route::resource('/', EquipmentMovementController::class)->except(['show', 'create']);
        
        // Routes personnalisées
        Route::get('/create', [EquipmentMovementController::class, 'create'])->name('create');
        Route::get('/{equipment_movement}', [EquipmentMovementController::class, 'show'])->name('show');
        
        // Actions sur les mouvements
        Route::post('/{equipment_movement}/approve', [EquipmentMovementController::class, 'approve'])
            ->name('approve');
        Route::post('/{equipment_movement}/start', [EquipmentMovementController::class, 'start'])
            ->name('start');
        Route::post('/{equipment_movement}/complete', [EquipmentMovementController::class, 'complete'])
            ->name('complete');
        Route::post('/{equipment_movement}/cancel', [EquipmentMovementController::class, 'cancel'])
            ->name('cancel');
        
        // Rapports et exports
        Route::get('/{equipment_movement}/report', [EquipmentMovementController::class, 'report'])
            ->name('report');
        Route::get('/export', [EquipmentMovementController::class, 'export'])
            ->name('export');
            
        // Importation
        Route::post('/import', [EquipmentMovementController::class, 'import'])
            ->name('import');
    });

    // Routes pour la maintenance
    Route::prefix('maintenance')->name('maintenance.')->group(function () {
        // Liste des maintenances
        Route::get('/', [MaintenanceController::class, 'index'])->name('index');
        
        // Création d'une maintenance
        Route::get('/create', [MaintenanceController::class, 'create'])->name('create');
        Route::post('/', [MaintenanceController::class, 'store'])->name('store');
        
        // Visualisation et édition
        Route::get('/{maintenance}', [MaintenanceController::class, 'show'])->name('show');
        Route::get('/{maintenance}/edit', [MaintenanceController::class, 'edit'])->name('edit');
        Route::put('/{maintenance}', [MaintenanceController::class, 'update'])->name('update');
        
        // Actions sur les maintenances
        Route::post('/{maintenance}/start', [MaintenanceController::class, 'start'])
            ->name('start');
        Route::post('/{maintenance}/complete', [MaintenanceController::class, 'complete'])
            ->name('complete');
        Route::post('/{maintenance}/cancel', [MaintenanceController::class, 'cancel'])
            ->name('cancel');
        
        // Pièces jointes
        Route::post('/{maintenance}/attachments', [MaintenanceController::class, 'storeAttachment'])
            ->name('attachments.store');
        Route::delete('/attachments/{attachment}', [MaintenanceController::class, 'destroyAttachment'])
            ->name('attachments.destroy');
        
        // Rapports
        Route::get('/{maintenance}/report', [MaintenanceController::class, 'report'])
            ->name('report');
        Route::get('/export', [MaintenanceController::class, 'export'])
            ->name('export');
    });

    // Routes pour les emplacements (Locations)
    Route::prefix('locations')->name('locations.')->group(function () {
        Route::get('/', [LocationController::class, 'index'])->name('index');
        Route::get('/create', [LocationController::class, 'create'])->name('create');
        Route::post('/', [LocationController::class, 'store'])->name('store');
        Route::get('/{location}', [LocationController::class, 'show'])->name('show');
        Route::get('/{location}/edit', [LocationController::class, 'edit'])->name('edit');
        Route::put('/{location}', [LocationController::class, 'update'])->name('update');
        Route::delete('/{location}', [LocationController::class, 'destroy'])->name('destroy');
        
        // Export des données
        Route::get('/export', [LocationController::class, 'export'])->name('export');
        
        // Gestion des images des emplacements
        Route::post('/{location}/images', [LocationController::class, 'storeImage'])
            ->name('images.store');
        Route::delete('/{location}/images/{image}', [LocationController::class, 'destroyImage'])
            ->name('images.destroy');
            
        // Gestion des fichiers des emplacements
        Route::post('/{location}/files', [LocationController::class, 'storeFile'])
            ->name('files.store');
        Route::delete('/{location}/files/{file}', [LocationController::class, 'destroyFile'])
            ->name('files.destroy');
        
        // Import des données
        Route::post('/import', [LocationController::class, 'import'])->name('import');
        
        // Téléchargement du modèle d'importation
        Route::get('/template', [LocationController::class, 'downloadTemplate'])->name('template');
        
        // Suppression multiple
        Route::delete('/destroy-multiple', [LocationController::class, 'destroyMultiple'])->name('destroy.multiple');
    });

    // Routes pour les équipements - Configuration unique plus haut dans le fichier

    // Routes pour les catégories d'équipements
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::get('/{category}', [CategoryController::class, 'show'])->name('show');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
        
        // Gestion des sous-catégories
        Route::post('/{category}/subcategories', [CategoryController::class, 'storeSubcategory'])->name('subcategories.store');
        Route::put('/subcategories/{subcategory}', [CategoryController::class, 'updateSubcategory'])->name('subcategories.update');
        Route::delete('/subcategories/{subcategory}', [CategoryController::class, 'destroySubcategory'])->name('subcategories.destroy');
        
        // Import/Export
        Route::post('/import', [CategoryController::class, 'import'])->name('import');
        Route::get('/export', [CategoryController::class, 'export'])->name('export');
        
        // Actions groupées
        Route::post('/bulk-delete', [CategoryController::class, 'bulkDelete'])->name('bulk-delete');
    });

    // Routes pour les documents
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::post('/', [DocumentController::class, 'store'])->name('store');
        Route::get('/{document}', [DocumentController::class, 'show'])->name('show');
        Route::delete('/{document}', [DocumentController::class, 'destroy'])->name('destroy');
    });
});

require __DIR__.'/auth.php';
