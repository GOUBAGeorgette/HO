<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Affiche la liste des catégories avec statistiques.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Category::withCount('equipment');

        // Recherche par nom
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filtre par catégorie parente
        if ($request->has('parent_id') && $request->parent_id !== '') {
            $query->where('parent_id', $request->parent_id);
        } else {
            // Par défaut, ne montrer que les catégories de premier niveau
            $query->whereNull('parent_id');
        }

        // Filtre par statut
        if ($request->has('status') && in_array($request->status, ['active', 'inactive'])) {
            $query->where('is_active', $request->status === 'active');
        }

        // Tri des résultats
        $sort = $request->input('sort', 'name');
        $direction = $request->input('direction', 'asc');
        
        if (in_array($sort, ['name', 'equipment_count', 'created_at'])) {
            $query->orderBy($sort, $direction);
        }

        $categories = $query->paginate(15)->withQueryString();
        
        // Récupération des catégories parentes pour le filtre
        $parentCategories = Category::whereNull('parent_id')->orderBy('name')->get();

        return view('categories.index', [
            'categories' => $categories,
            'parentCategories' => $parentCategories,
            'sort' => $sort,
            'direction' => $direction,
            'search' => $request->input('search', ''),
            'selectedParent' => $request->input('parent_id'),
            'selectedStatus' => $request->input('status')
        ]);
    }

    /**
     * Affiche le formulaire de création d'une catégorie.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $categories = Category::whereNull('parent_id')
            ->with('children')
            ->orderBy('name')
            ->get();
            
        return view('categories.create', [
            'categories' => $categories
        ]);
    }

    /**
     * Enregistre une nouvelle catégorie.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
        ]);

        Category::create($validated);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Catégorie créée avec succès.');
    }

    /**
     * Affiche les détails d'une catégorie.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\View\View
     */
    public function show(Category $category)
    {
        $equipment = $category->equipment()
            ->with(['location', 'assignedUser'])
            ->orderBy('name')
            ->paginate(15);
            
        // Récupérer les maintenances des équipements de cette catégorie
        $maintenances = \App\Models\Maintenance::whereIn('equipment_id', $category->equipment()->pluck('id'))
            ->with('equipment')
            ->latest()
            ->paginate(10, ['*'], 'maintenances')
            ->withQueryString();

        return view('categories.show', [
            'category' => $category->loadCount('equipment'),
            'equipment' => $equipment,
            'maintenances' => $maintenances
        ]);
    }

    /**
     * Affiche le formulaire de modification d'une catégorie.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\View\View
     */
    public function edit(Category $category)
    {
        // Charger explicitement les relations nécessaires
        $category->load(['parent', 'children']);
        
        // Si la catégorie a un parent, charger également ses ancêtres
        if ($category->parent) {
            $category->load('parent.ancestors');
        }
        
        // Récupérer toutes les catégories pour le sélecteur de parent
        $categories = Category::where('id', '!=', $category->id)
            ->where(function($query) use ($category) {
                $query->whereNull('parent_id')
                      ->orWhere('parent_id', '!=', $category->id);
            })
            ->get();
            
        return view('categories.edit', [
            'category' => $category,
            'categories' => $categories
        ]);
    }

    /**
     * Met à jour une catégorie dans la base de données.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
        ]);

        $category->update($validated);

        return redirect()
            ->route('categories.show', $category)
            ->with('success', 'Catégorie mise à jour avec succès.');
    }

    /**
     * Supprime une catégorie de la base de données.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Category $category)
    {
        // Vérifier s'il y a des équipements associés
        if ($category->equipment()->exists()) {
            return back()->with('error', 'Impossible de supprimer cette catégorie car elle contient des équipements.');
        }

        $category->delete();

        return redirect()
            ->route('categories.index')
            ->with('success', 'Catégorie supprimée avec succès.');
    }

    /**
     * Affiche les statistiques des catégories.
     *
     * @return \Illuminate\View\View
     */
    public function stats()
    {
        $stats = Category::select('categories.*')
            ->selectRaw('COUNT(equipment.id) as equipment_count')
            ->leftJoin('equipment', 'categories.id', '=', 'equipment.category_id')
            ->groupBy('categories.id')
            ->orderBy('equipment_count', 'desc')
            ->get();

        return view('categories.stats', compact('stats'));
    }

    /**
     * Exporte la liste des catégories au format CSV.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    /**
     * Exporte les catégories au format CSV.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function export()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=categories_' . date('Y-m-d') . '.csv',
        ];

        $callback = function() {
            $handle = fopen('php://output', 'w');
            
            // En-têtes
            fputcsv($handle, [
                'ID',
                'Nom',
                'Description',
                'Catégorie parente',
                'Statut',
                'Date de création',
                'Date de mise à jour',
                'Nombre d\'équipements'
            ]);

            // Données
            Category::with(['parent', 'equipment'])->withCount('equipment')->chunk(100, function($categories) use ($handle) {
                foreach ($categories as $category) {
                    fputcsv($handle, [
                        $category->id,
                        $category->name,
                        $category->description,
                        $category->parent ? $category->parent->name : '',
                        $category->is_active ? 'Actif' : 'Inactif',
                        $category->created_at->format('Y-m-d H:i:s'),
                        $category->updated_at->format('Y-m-d H:i:s'),
                        $category->equipment_count
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Importe des catégories à partir d'un fichier.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    /**
     * Supprime plusieurs catégories en même temps.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:categories,id',
        ]);

        try {
            $deleted = 0;
            $withEquipment = [];
            
            foreach ($request->ids as $id) {
                $category = Category::withCount('equipment')->findOrFail($id);
                
                // Vérifier si la catégorie a des équipements
                if ($category->equipment_count > 0) {
                    $withEquipment[] = $category->name;
                    continue;
                }
                
                // Supprimer les sous-catégories (sera géré par le SoftDeletes)
                $category->children()->delete();
                
                // Supprimer la catégorie
                $category->delete();
                $deleted++;
            }
            
            $message = "$deleted catégorie(s) supprimée(s) avec succès.";
            
            if (count($withEquipment) > 0) {
                $message .= " Les catégories suivantes n'ont pas pu être supprimées car elles contiennent des équipements : " . 
                           implode(', ', $withEquipment);
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted' => $deleted,
                'with_equipment' => $withEquipment
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la suppression en masse des catégories : ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la suppression des catégories : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Importe des catégories depuis un fichier.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240',
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        
        try {
            if (in_array($extension, ['xlsx', 'xls'])) {
                $data = \Excel::toArray([], $file)[0];
            } else {
                $data = array_map('str_getcsv', file($file->getPathname()));
                $headers = array_shift($data); // En-têtes de colonne
            }

            // Supprimer l'en-tête si présent
            if (isset($data[0]) && in_array(strtolower($data[0][0]), ['id', 'nom', 'name'])) {
                array_shift($data);
            }

            $imported = 0;
            $skipped = 0;
            
            foreach ($data as $row) {
                try {
                    // Format attendu : Nom, Description, Catégorie parente (optionnel), Statut (optionnel)
                    $name = $row[0] ?? null;
                    $description = $row[1] ?? null;
                    $parentName = $row[2] ?? null;
                    $isActive = isset($row[3]) ? (strtolower(trim($row[3])) === 'actif' || $row[3] === '1' || strtolower(trim($row[3])) === 'active') : true;

                    if (empty($name)) {
                        $skipped++;
                        continue;
                    }

                    $categoryData = [
                        'name' => $name,
                        'description' => $description,
                        'is_active' => $isActive,
                    ];

                    // Gestion de la catégorie parente
                    if (!empty($parentName)) {
                        $parentCategory = Category::where('name', $parentName)->first();
                        if ($parentCategory) {
                            $categoryData['parent_id'] = $parentCategory->id;
                        } else {
                            // Optionnel : créer la catégorie parente si elle n'existe pas
                            // $parentCategory = Category::create(['name' => $parentName]);
                            // $categoryData['parent_id'] = $parentCategory->id;
                        }
                    }

                    // Vérifier si la catégorie existe déjà
                    $existingCategory = Category::where('name', $name);
                    if (!empty($parentName) && $parentCategory) {
                        $existingCategory->where('parent_id', $parentCategory->id);
                    } else {
                        $existingCategory->whereNull('parent_id');
                    }

                    if ($existingCategory->exists()) {
                        $existingCategory->update($categoryData);
                    } else {
                        Category::create($categoryData);
                    }

                    $imported++;
                } catch (\Exception $e) {
                    $skipped++;
                    \Log::error('Erreur lors de l\'import d\'une catégorie : ' . $e->getMessage());
                    continue;
                }
            }

            $message = "Importation terminée : $imported catégories importées";
            if ($skipped > 0) {
                $message .= ", $skipped lignes ignorées";
            }

            return redirect()->route('categories.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'import du fichier : ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de l\'import du fichier : ' . $e->getMessage());
        }
    }
}
