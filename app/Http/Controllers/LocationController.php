<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class LocationController extends Controller
{
    /**
     * Affiche la liste des emplacements.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $locations = Location::withCount([
                'equipment', 
                'originMovements as movements_from_count', 
                'destinationMovements as movements_to_count'
            ])
            ->orderBy('name')
            ->paginate(15);
            
        // Récupérer tous les emplacements pour l'arborescence
        $allLocations = Location::with('children')
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();
            
        // Générer l'arborescence HTML
        $locationTree = $this->buildLocationTree($allLocations);

        return view('locations.index', compact('locations', 'locationTree'));
    }
    
    /**
     * Construit l'arborescence HTML des emplacements.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $locations
     * @param  int  $level
     * @return string
     */
    private function buildLocationTree($locations, $level = 0)
    {
        $html = '<ul class="location-tree' . ($level === 0 ? ' tree' : '') . '">';
        
        foreach ($locations as $location) {
            $hasChildren = $location->children->isNotEmpty();
            $isActive = $location->is_active ? '' : 'text-muted';
            
            $html .= '<li class="location-item ' . $isActive . '">';
            $html .= '<div class="d-flex align-items-center">';
            
            if ($hasChildren) {
                $html .= '<span class="toggle-tree me-2" data-bs-toggle="collapse" href="#location-' . $location->id . '" role="button">';
                $html .= '<i class="fas fa-chevron-right"></i>';
                $html .= '</span>';
            } else {
                $html .= '<span class="me-4"></span>';
            }
            
            $html .= '<a href="' . route('locations.show', $location) . '" class="text-decoration-none ' . $isActive . '">';
            $html .= '<i class="fas fa-map-marker-alt me-2"></i>' . e($location->name);
            $html .= '</a>';
            $html .= '<span class="badge bg-primary ms-2">' . $location->equipment_count . '</span>';
            $html .= '</div>';
            
            if ($hasChildren) {
                $html .= '<div class="collapse" id="location-' . $location->id . '">';
                $html .= $this->buildLocationTree($location->children, $level + 1);
                $html .= '</div>';
            }
            
            $html .= '</li>';
        }
        
        $html .= '</ul>';
        return $html;
    }

    /**
     * Affiche le formulaire de création d'un emplacement.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('locations.create');
    }

    /**
     * Enregistre un nouvel emplacement dans la base de données.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:locations',
            'building' => 'nullable|string|max:255',
            'room' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        try {
            Location::create($validated);
            return redirect()
                ->route('locations.index')
                ->with('success', 'Emplacement créé avec succès.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création de l\'emplacement : ' . $e->getMessage());
        }
    }

    /**
     * Affiche les détails d'un emplacement.
     *
     * @param  \App\Models\Location  $location
     * @return \Illuminate\View\View
     */
    public function show(Location $location)
    {
        $equipment = $location->equipment()
            ->with(['category', 'assignedUser'])
            ->orderBy('name')
            ->paginate(15);

        $recentMovements = $location->movementsFrom()
            ->with(['equipment', 'toLocation', 'assignedTo'])
            ->orderBy('moved_at', 'desc')
            ->take(10)
            ->get();

        return view('locations.show', [
            'location' => $location->loadCount(['equipment', 'movementsFrom', 'movementsTo']),
            'equipment' => $equipment,
            'recentMovements' => $recentMovements,
        ]);
    }

    /**
     * Affiche le formulaire de modification d'un emplacement.
     *
     * @param  \App\Models\Location  $location
     * @return \Illuminate\View\View
     */
    public function edit(Location $location)
    {
        return view('locations.edit', compact('location'));
    }

    /**
     * Met à jour un emplacement dans la base de données.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Location  $location
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:locations,name,' . $location->id,
            'building' => 'nullable|string|max:255',
            'room' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $location->update($validated);

        return redirect()
            ->route('locations.show', $location)
            ->with('success', 'Emplacement mis à jour avec succès.');
    }

    /**
     * Désactive un emplacement.
     *
     * @param  \App\Models\Location  $location
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deactivate(Location $location)
    {
        if ($location->equipment()->exists()) {
            return back()->with('error', 'Impossible de désactiver cet emplacement car il contient des équipements.');
        }

        $location->update(['is_active' => false]);

        return back()->with('success', 'Emplacement désactivé avec succès.');
    }

    /**
     * Active un emplacement.
     *
     * @param  \App\Models\Location  $location
     * @return \Illuminate\Http\RedirectResponse
     */
    public function activate(Location $location)
    {
        $location->update(['is_active' => true]);

        return back()->with('success', 'Emplacement activé avec succès.');
    }

    /**
     * Affiche le formulaire de transfert d'équipements entre emplacements.
     *
     * @param  \App\Models\Location  $location
     * @return \Illuminate\View\View
     */
    public function showTransferForm(Location $location)
    {
        $equipment = $location->equipment()->orderBy('name')->get();
        $destinations = Location::where('id', '!=', $location->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('locations.transfer', [
            'sourceLocation' => $location,
            'equipment' => $equipment,
            'destinations' => $destinations,
        ]);
    }

    /**
     * Traite le transfert d'équipements entre emplacements.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Location  $location
     * @return \Illuminate\Http\RedirectResponse
     */
    public function transfer(Request $request, Location $location)
    {
        $validated = $request->validate([
            'equipment' => 'required|array',
            'equipment.*' => 'exists:equipment,id',
            'destination_id' => 'required|exists:locations,id',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $destination = Location::findOrFail($validated['destination_id']);
        
        // Démarrer une transaction pour assurer l'intégrité des données
        DB::beginTransaction();

        try {
            // Mettre à jour l'emplacement des équipements
            Equipment::whereIn('id', $validated['equipment'])
                ->update(['location_id' => $destination->id]);

            // Enregistrer les mouvements
            foreach ($validated['equipment'] as $equipmentId) {
                $equipment = Equipment::find($equipmentId);
                
                // Créer un enregistrement de mouvement
                $equipment->movements()->create([
                    'from_location_id' => $location->id,
                    'to_location_id' => $destination->id,
                    'moved_by' => auth()->id(),
                    'moved_at' => now(),
                    'type' => 'transfer',
                    'reason' => $validated['reason'],
                    'notes' => $validated['notes'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('locations.show', $location)
                ->with('success', 'Équipements transférés avec succès vers ' . $destination->name . '.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors du transfert : ' . $e->getMessage());
        }
    }

    /**
     * Affiche le rapport d'inventaire pour un emplacement.
     *
     * @param  \App\Models\Location  $location
     * @return \Illuminate\View\View
     */
    public function inventoryReport(Location $location)
    {
        $equipment = $location->equipment()
            ->with(['category', 'assignedUser'])
            ->orderBy('name')
            ->get()
            ->groupBy('category.name');

        return view('locations.inventory-report', [
            'location' => $location,
            'equipmentByCategory' => $equipment,
        ]);
    }
    
    /**
     * Exporte les emplacements dans le format demandé.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        $format = $request->input('format', 'xlsx');
        $onlyActive = $request->has('only_active');
        
        $query = Location::query();
        
        if ($onlyActive) {
            $query->where('is_active', true);
        }
        
        $locations = $query->get();
        
        if ($format === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.locations-pdf', [
                'locations' => $locations,
                'title' => 'Liste des emplacements',
                'date' => now()->format('d/m/Y')
            ]);
            
            return $pdf->download('emplacements-' . now()->format('Y-m-d') . '.pdf');
        }
        
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\LocationsExport($locations),
            'emplacements-' . now()->format('Y-m-d') . '.' . $format,
            \Maatwebsite\Excel\Excel::XLSX
        );
    }
    
    /**
     * Importe des emplacements depuis un fichier.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
            'update_existing' => 'boolean'
        ]);
        
        $updateExisting = $request->boolean('update_existing');
        
        try {
            \Maatwebsite\Excel\Facades\Excel::import(
                new \App\Imports\LocationsImport($updateExisting),
                $request->file('file')
            );
            
            return redirect()->back()->with('success', 'Importation des emplacements terminée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de l\'importation : ' . $e->getMessage());
        }
    }
    
    /**
     * Télécharge le modèle d'importation.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadTemplate()
    {
        $headers = [
            'name' => 'Nom',
            'building' => 'Bâtiment',
            'room' => 'Salle',
            'description' => 'Description',
            'parent_name' => 'Emplacement parent (nom)',
            'is_active' => 'Actif (1/0)'
        ];
        
        $export = new \App\Exports\TemplateExport([$headers]);
        
        return \Maatwebsite\Excel\Facades\Excel::download(
            $export,
            'modele-import-emplacements.xlsx',
            \Maatwebsite\Excel\Excel::XLSX
        );
    }
    
    /**
     * Supprime plusieurs emplacements en une seule fois.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyMultiple(Request $request)
    {
        $request->validate([
            'selected_ids' => 'required|string'
        ]);
        
        $ids = explode(',', $request->selected_ids);
        
        // Vérifier les emplacements avec des équipements
        $locationsWithEquipment = Location::whereIn('id', $ids)
            ->whereHas('equipment')
            ->pluck('name')
            ->toArray();
            
        if (!empty($locationsWithEquipment)) {
            $message = 'Impossible de supprimer les emplacements suivants car ils contiennent des équipements : ';
            $message .= implode(', ', $locationsWithEquipment);
            return redirect()->back()->with('error', $message);
        }
        
        // Supprimer les emplacements
        $deletedCount = Location::whereIn('id', $ids)->delete();
        
        if ($deletedCount > 0) {
            return redirect()->back()->with('success', $deletedCount . ' emplacement(s) supprimé(s) avec succès.');
        }
        
        return redirect()->back()->with('warning', 'Aucun emplacement supprimé.');
    }

    /**
     * Stocke une nouvelle image pour l'emplacement.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Location  $location
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeImage(Request $request, Location $location)
    {
        $request->validate([
            'image' => 'required|image|max:2048', // 2MB max
        ]);

        try {
            // Créer un répertoire pour les images de l'emplacement s'il n'existe pas
            $path = $request->file('image')->store('public/locations/' . $location->id);
            
            // Enregistrer le chemin de l'image dans la base de données
            $location->images()->create([
                'path' => str_replace('public/', '', $path),
                'original_name' => $request->file('image')->getClientOriginalName(),
                'mime_type' => $request->file('image')->getMimeType(),
                'size' => $request->file('image')->getSize(),
            ]);

            return back()->with('success', 'Image ajoutée avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors du téléchargement de l\'image: ' . $e->getMessage());
        }
    }

    /**
     * Supprime une image de l'emplacement.
     *
     * @param  \App\Models\Location  $location
     * @param  int  $imageId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyImage(Location $location, $imageId)
    {
        try {
            $image = $location->images()->findOrFail($imageId);
            
            // Supprimer le fichier physique
            Storage::delete('public/' . $image->path);
            
            // Supprimer l'entrée dans la base de données
            $image->delete();

            return back()->with('success', 'Image supprimée avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression de l\'image: ' . $e->getMessage());
        }
    }

    /**
     * Stocke un nouveau fichier pour l'emplacement.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Location  $location
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeFile(Request $request, Location $location)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        try {
            // Créer un répertoire pour les fichiers de l'emplacement s'il n'existe pas
            $path = $request->file('file')->store('public/locations/' . $location->id . '/files');
            
            // Enregistrer le chemin du fichier dans la base de données
            $location->files()->create([
                'path' => str_replace('public/', '', $path),
                'original_name' => $request->file('file')->getClientOriginalName(),
                'mime_type' => $request->file('file')->getMimeType(),
                'size' => $request->file('file')->getSize(),
                'extension' => $request->file('file')->getClientOriginalExtension(),
            ]);

            return back()->with('success', 'Fichier ajouté avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors du téléchargement du fichier: ' . $e->getMessage());
        }
    }

    /**
     * Supprime un fichier de l'emplacement.
     *
     * @param  \App\Models\Location  $location
     * @param  int  $fileId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyFile(Location $location, $fileId)
    {
        try {
            $file = $location->files()->findOrFail($fileId);
            
            // Supprimer le fichier physique
            Storage::delete('public/' . $file->path);
            
            // Supprimer l'entrée dans la base de données
            $file->delete();

            return back()->with('success', 'Fichier supprimé avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression du fichier: ' . $e->getMessage());
        }
    }
}
