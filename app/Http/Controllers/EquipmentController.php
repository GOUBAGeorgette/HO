<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Category;
use App\Models\Location;
use App\Models\User;
use App\Models\Document;
use App\Models\EquipmentHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;
use App\Imports\EquipmentImport;
use App\Exports\EquipmentExport;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EquipmentController extends Controller
{
    /**
     * Affiche la liste des équipements avec filtres avancés.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    /**
     * Affiche le formulaire d'importation d'équipements.
     *
     * @return \Illuminate\View\View
     */
    public function importForm()
    {
        return view('equipment.import');
    }

    /**
     * Traite l'importation du fichier Excel d'équipements.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import(Request $request)
    {
        // Log des informations de débogage
        $debugInfo = [
            'has_file' => $request->hasFile('file'),
            'file_valid' => $request->file('file') ? $request->file('file')->isValid() : false,
            'file_extension' => $request->file('file') ? $request->file('file')->getClientOriginalExtension() : null,
            'file_mime' => $request->file('file') ? $request->file('file')->getMimeType() : null,
            'file_size' => $request->file('file') ? $request->file('file')->getSize() : null,
        ];
        \Log::info('Tentative d\'importation de fichier', $debugInfo);

        if (!$request->hasFile('file') || !$request->file('file')->isValid()) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['file' => 'Le fichier est invalide ou n\'a pas pu être téléchargé.']);
        }

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());
        
        // Vérification de l'extension
        $allowedExtensions = ['xlsx', 'xls', 'csv'];
        if (!in_array($extension, $allowedExtensions)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['file' => 'Le fichier doit être de type : ' . implode(', ', $allowedExtensions) . '. Extension détectée: ' . $extension]);
        }
        
        // Vérification de la taille (20MB max)
        $maxSize = 20 * 1024 * 1024;
        if ($file->getSize() > $maxSize) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['file' => 'Le fichier ne doit pas dépasser ' . number_format($maxSize / (1024 * 1024), 2) . ' Mo.']);
        }

        try {
            // Forcer le type MIME si nécessaire
            $mimeType = $file->getMimeType();
            if ($mimeType === 'application/octet-stream' && in_array($extension, ['xlsx', 'xls', 'csv'])) {
                $mimeType = $extension === 'csv' ? 'text/csv' : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                \Log::info('Forçage du type MIME', ['ancien' => 'application/octet-stream', 'nouveau' => $mimeType]);
            }
            
            // Vérification supplémentaire pour s'assurer que le fichier est un Excel/CSV valide
            $filePath = $file->getRealPath();
            $fileContent = file_get_contents($filePath);
            
            \Log::info('Vérification du fichier', [
                'file_size' => filesize($filePath),
                'file_exists' => file_exists($filePath),
                'is_readable' => is_readable($filePath),
                'first_bytes' => bin2hex(substr($fileContent, 0, 8))
            ]);
            
            if ($extension === 'csv') {
                // Vérification basique pour les fichiers CSV
                $lines = explode("\n", $fileContent);
                if (count($lines) < 2) {
                    throw new \Exception('Le fichier CSV semble vide ou invalide.');
                }
            } else {
                // Vérification pour les fichiers Excel
                // Les fichiers Excel commencent par un en-tête spécifique
                $excelHeader = substr($fileContent, 0, 4);
                
                // Définition des en-têtes valides en hexadécimal
                $validExcelHeaders = [
                    hex2bin('504B0304'), // .xlsx
                    hex2bin('D0CF11E0')  // .xls
                ];
                
                $hexHeader = bin2hex($excelHeader);
                $validHexHeaders = array_map('bin2hex', $validExcelHeaders);
                
                $isValid = false;
                foreach ($validExcelHeaders as $validHeader) {
                    if (strncmp($excelHeader, $validHeader, 4) === 0) {
                        $isValid = true;
                        break;
                    }
                }
                
                \Log::info('En-tête du fichier', [
                    'header_hex' => $hexHeader,
                    'valid_headers' => $validHexHeaders,
                    'is_valid' => $isValid
                ]);
                
                if (!$isValid) {
                    throw new \Exception('Le fichier ne semble pas être un fichier Excel valide. En-tête détecté: ' . $hexHeader);
                }
            }
            try {
                $import = new EquipmentImport();
                Excel::import($import, $file);
                
                if ($import->getRowCount() > 0) {
                    return redirect()
                        ->route('equipment.index')
                        ->with('success', 'Importation des équipements terminée avec succès ! ' . $import->getRowCount() . ' lignes importées.');
                } else {
                    return redirect()
                        ->back()
                        ->with('warning', 'Le fichier a été importé mais aucune donnée valide n\'a été trouvée.');
                }
            } catch (\Exception $e) {
                \Log::error('Erreur lors de l\'importation du fichier', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors(['file' => 'Erreur lors de l\'importation du fichier : ' . $e->getMessage()]);
            }
                
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            
            $errorMessages = [];
            foreach ($failures as $failure) {
                $attribute = $failure->attribute();
                $values = $failure->values();
                $value = isset($values[$attribute]) ? $values[$attribute] : 'N/A';
                $errorMessages[] = "Ligne {$failure->row()}: {$failure->errors()[0]} (Valeur: {$value})";
            }
            
            return back()
                ->withErrors(['import' => $errorMessages])
                ->withInput();
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Une erreur est survenue lors de l\'importation : ' . $e->getMessage());
        }
    }

    public function index(Request $request)
    {
        $query = Equipment::with(['category', 'assignedUser']);

        // Filtrage par recherche
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('responsible_person', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('category', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filtrage par statut
        if ($request->has('status') && in_array($request->status, ['excellent', 'bon', 'moyen', 'mauvais', 'hors_service'])) {
            $query->where('status', $request->status);
        }

        // Filtrage par catégorie
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Filtrage par utilisabilité
        if ($request->has('is_usable') && $request->is_usable !== 'all') {
            $query->where('is_usable', $request->boolean('is_usable'));
        }

        // Tri des résultats
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');
        
        if (in_array($sort, ['name', 'model', 'brand', 'type', 'quantity', 'status', 'location', 'created_at'])) {
            $query->orderBy($sort, $direction);
        } elseif ($sort === 'category') {
            $query->join('categories', 'equipment.category_id', '=', 'categories.id')
                  ->orderBy('categories.name', $direction)
                  ->select('equipment.*');
        } elseif ($sort === 'assigned_user') {
            $query->leftJoin('users', 'equipment.assigned_to', '=', 'users.id')
                  ->orderBy('users.name', $direction)
                  ->select('equipment.*');
        }

        $equipment = $query->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        return view('equipment.index', [
            'equipment' => $equipment,
            'categories' => $categories,
            'users' => $users,
            'locations' => $locations,
            'filters' => $request->all(),
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }

    /**
     * Affiche le formulaire de création d'un équipement.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        
        return view('equipment.create', [
            'categories' => $categories,
            'users' => $users,
            'locations' => $locations,
            'statuses' => [
                'excellent' => 'Excellent',
                'bon' => 'Bon',
                'moyen' => 'Moyen',
                'mauvais' => 'Mauvais',
                'hors_service' => 'Hors service'
            ]
        ]);
    }

    /**
     * Enregistre un nouvel équipement dans la base de données.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'model' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'quantity' => 'required|integer|min:1',
            'status' => 'required|in:excellent,bon,moyen,mauvais,hors_service',
            'location' => 'nullable|string|max:255',
            'is_usable' => 'boolean',
            'responsible_person' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'suggestions' => 'nullable|string',
            'maintenance_frequency' => 'nullable|string|max:255',
            'maintenance_tasks' => 'nullable|string',
            'maintenance_type' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        // S'assurer que is_usable est bien un booléen
        $validated['is_usable'] = $request->has('is_usable');

        // Journalisation pour le débogage
        \Log::info('Création d\'un nouvel équipement', ['data' => $validated]);
        
        // Démarrer une transaction
        DB::beginTransaction();

        try {
            // Créer l'équipement
            $equipment = Equipment::create($validated);
            
            // Gestion des documents joints
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $document) {
                    $path = $document->store('equipment/documents', 'public');
                    $equipment->documents()->create([
                        'name' => $document->getClientOriginalName(),
                        'file_path' => $path,
                        'file_size' => $document->getSize(),
                        'mime_type' => $document->getMimeType(),
                        'uploaded_by' => auth()->id(),
                    ]);
                }
            }
            
            // Enregistrer l'historique
            $equipment->history()->create([
                'action' => 'created',
                'user_id' => auth()->id(),
                'details' => 'Création de l\'équipement',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            // Valider la transaction
            DB::commit();
            \Log::info('Transaction commitée avec succès', ['equipment_id' => $equipment->id]);

            return redirect()
                ->route('equipment.show', $equipment)
                ->with('success', 'Équipement créé avec succès.');
                
        } catch (\Exception $e) {
            // En cas d'erreur, annuler la transaction
            DB::rollBack();
            
            // Journaliser l'erreur
            \Log::error('Erreur lors de la création de l\'équipement', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Supprimer les fichiers uploadés en cas d'erreur
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }
            
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création de l\'équipement : ' . $e->getMessage());
        }
    }

    /**
     * Affiche les détails d'un équipement avec toutes les informations associées.
     *
     * @param  \App\Models\Equipment  $equipment
     * @return \Illuminate\View\View
     */
    public function show(Equipment $equipment)
    {
        // Charger les relations principales
        $equipment->load([
            'location',
            'category',
            'assignedUser',
            'documents',
            'createdBy',
            'updatedBy'
        ]);
        
        // Charger la liste des emplacements actifs pour le formulaire de déplacement
        $locations = Location::where('is_active', true)
            ->orderBy('name')
            ->get();
            
        // Charger la liste des utilisateurs actifs pour le formulaire d'assignation
        $users = User::where('is_active', true)
            ->orderBy('name')
            ->get();
        
        // Derniers mouvements
        $recentMovements = $equipment->movements()
            ->with(['originLocation', 'destinationLocation', 'requester', 'assignedTo'])
            ->orderBy('scheduled_date', 'desc')
            ->take(5)
            ->get();
            
        // Dernières maintenances
        $recentMaintenances = $equipment->maintenances()
            ->with(['assignedTo', 'createdBy'])
            ->orderBy('scheduled_date', 'desc')
            ->take(5)
            ->get();
            
        // Historique des modifications
        $auditLogs = $equipment->audits()
            ->with('user')
            ->latest()
            ->take(10)
            ->get();
            
        // Équipements similaires (même catégorie et marque)
        $similarEquipment = Equipment::where('id', '!=', $equipment->id)
            ->where('category_id', $equipment->category_id)
            ->where('brand', $equipment->brand)
            ->with(['location', 'assignedUser'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        // Calcul de l'âge de l'équipement
        $age = $equipment->purchase_date ? Carbon::parse($equipment->purchase_date)->diffForHumans() : 'Inconnu';
        
        // Vérifier si la garantie est expirée
        $warrantyStatus = null;
        if ($equipment->warranty_expires) {
            $warrantyExpires = Carbon::parse($equipment->warranty_expires);
            $warrantyStatus = [
                'expired' => $warrantyExpires->isPast(),
                'expiry_date' => $warrantyExpires->format('d/m/Y'),
                'remaining_days' => $warrantyExpires->diffInDays(now(), false) * -1,
                'remaining_months' => $warrantyExpires->diffInMonths(now(), false) * -1,
            ];
        }
        
        // Statistiques d'utilisation
        $usageStats = [
            'total_maintenances' => $equipment->maintenances()->count(),
            'total_movements' => $equipment->movements()->count(),
            'days_since_purchase' => $equipment->purchase_date ? now()->diffInDays(Carbon::parse($equipment->purchase_date)) : null,
            'avg_maintenance_interval' => $this->calculateAverageMaintenanceInterval($equipment),
        ];

        return view('equipment.show', [
            'equipment' => $equipment,
            'recentMovements' => $recentMovements,
            'recentMaintenances' => $recentMaintenances,
            'auditLogs' => $auditLogs,
            'similarEquipment' => $similarEquipment,
            'age' => $age,
            'warrantyStatus' => $warrantyStatus,
            'usageStats' => $usageStats,
            'locations' => $locations, // Liste des emplacements pour le formulaire de déplacement
            'users' => $users, // Liste des utilisateurs pour le formulaire d'assignation
        ]);
    }
    
    /**
     * Calcule l'intervalle moyen entre les maintenances pour un équipement.
     *
     * @param  \App\Models\Equipment  $equipment
     * @return int|null Nombre de jours ou null si pas assez de données
     */
    private function calculateAverageMaintenanceInterval($equipment)
    {
        $maintenances = $equipment->maintenances()
            ->whereNotNull('completed_date')
            ->orderBy('completed_date')
            ->pluck('completed_date');
            
        if ($maintenances->count() < 2) {
            return null;
        }
        
        $totalDays = 0;
        $previousDate = null;
        $count = 0;
        
        foreach ($maintenances as $date) {
            if ($previousDate) {
                $totalDays += $previousDate->diffInDays($date);
                $count++;
            }
            $previousDate = $date;
        }
        
        return $count > 0 ? round($totalDays / $count) : null;
    }

    /**
     * Affiche le formulaire de modification d'un équipement.
     *
     * @param  \App\Models\Equipment  $equipment
     * @return \Illuminate\View\View
     */
    public function edit(Equipment $equipment)
    {
        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $locations = Location::where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $users = User::where('is_active', true)
            ->orderBy('name')
            ->get();
            
        // Récupérer l'historique des modifications
        $auditLogs = $equipment->audits()
            ->with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('equipment.edit', [
            'equipment' => $equipment->load(['documents', 'category', 'location', 'assignedUser']),
            'categories' => $categories,
            'locations' => $locations,
            'users' => $users,
            'auditLogs' => $auditLogs,
        ]);
    }

    /**
     * Met à jour un équipement dans la base de données.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Equipment  $equipment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'model' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'quantity' => 'required|integer|min:1',
            'status' => 'required|in:excellent,bon,moyen,mauvais,hors_service',
            'location' => 'nullable|string|max:255',
            'is_usable' => 'boolean',
            'responsible_person' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'suggestions' => 'nullable|string',
            'maintenance_frequency' => 'nullable|string|max:255',
            'maintenance_tasks' => 'nullable|string',
            'maintenance_type' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'model' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'purchase_cost' => 'nullable|numeric|min:0',
            'warranty_months' => 'nullable|integer|min:0',
            'warranty_notes' => 'nullable|string|max:255',
            'status' => 'required|in:available,in_use,maintenance,out_of_service',
            'condition' => 'required|in:excellent,good,fair,poor',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'nullable|exists:locations,id',
            'assigned_to' => 'nullable|exists:users,id',
            'supplier' => 'nullable|string|max:255',
            'supplier_contact' => 'nullable|string|max:255',
            'order_number' => 'nullable|string|max:100',
            'barcode' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('equipment')->ignore($equipment->id)
            ],
            'qr_code' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('equipment')->ignore($equipment->id)
            ],
            'depreciation_years' => 'nullable|integer|min:1|max:50',
            'residual_value' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
            'documents.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,txt|max:10240', // 10MB max per file
            'delete_documents' => 'nullable|array',
            'delete_documents.*' => 'exists:equipment_documents,id',
        ]);

        // Démarrer une transaction pour s'assurer que tout est mis à jour correctement
        DB::beginTransaction();

        try {
            // Gestion de la suppression des documents
            if (!empty($validated['delete_documents'])) {
                $documentsToDelete = $equipment->documents()->whereIn('id', $validated['delete_documents'])->get();
                
                foreach ($documentsToDelete as $document) {
                    Storage::disk('public')->delete($document->file_path);
                    $document->delete();
                }
                
                unset($validated['delete_documents']);
            }

            // Gestion de l'upload de la nouvelle image
            if ($request->hasFile('image')) {
                // Supprimer l'ancienne image si elle existe
                if ($equipment->image_path) {
                    Storage::disk('public')->delete($equipment->image_path);
                }
                
                $path = $request->file('image')->store('equipment/images', 'public');
                $validated['image_path'] = $path;
            }

            // Gestion des nouveaux documents
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $document) {
                    $path = $document->store('equipment/documents', 'public');
                    $equipment->documents()->create([
                        'name' => $document->getClientOriginalName(),
                        'file_path' => $path,
                        'file_size' => $document->getSize(),
                        'mime_type' => $document->getMimeType(),
                        'uploaded_by' => Auth::id(),
                    ]);
                }
            }

            // Mettre à jour la date de fin de garantie si nécessaire
            if (!empty($validated['purchase_date']) && !empty($validated['warranty_months'])) {
                $purchaseDate = Carbon::parse($validated['purchase_date']);
                $warrantyMonths = (int) $validated['warranty_months']; // Conversion en entier
                $validated['warranty_expires'] = $purchaseDate->addMonths($warrantyMonths)->toDateString();
            } else {
                $validated['warranty_expires'] = null;
            }

            // Calculer la valeur actuelle si nécessaire
            if (!empty($validated['purchase_date']) && !empty($validated['purchase_cost']) && !empty($validated['depreciation_years'])) {
                $purchaseDate = Carbon::parse($validated['purchase_date']);
                $monthsInService = $purchaseDate->diffInMonths(now());
                $totalMonths = $validated['depreciation_years'] * 12;
                
                if ($monthsInService < $totalMonths) {
                    $monthlyDepreciation = ($validated['purchase_cost'] - ($validated['residual_value'] ?? 0)) / $totalMonths;
                    $depreciatedValue = $monthlyDepreciation * $monthsInService;
                    $validated['current_value'] = max($validated['purchase_cost'] - $depreciatedValue, $validated['residual_value'] ?? 0);
                } else {
                    $validated['current_value'] = $validated['residual_value'] ?? 0;
                }
            } else {
                $validated['current_value'] = null;
            }

            // Enregistrer les modifications
            $equipment->update($validated);

            // Enregistrer l'historique des modifications
            $changes = $equipment->getChanges();
            
            if (!empty($changes)) {
                $equipment->history()->create([
                    'action' => 'updated',
                    'user_id' => Auth::id(),
                    'details' => 'Mise à jour des informations',
                    'changed_fields' => $changes,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }

            DB::commit();

            return redirect()->route('equipment.show', $equipment)
                ->with('success', 'Équipement mis à jour avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour de l\'équipement : ' . $e->getMessage());
        }
    }

    /**
     * Supprime un équipement de la base de données.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Equipment  $equipment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, Equipment $equipment)
    {
        // Vérifier s'il y a des mouvements ou des maintenances associés
        if ($equipment->movements()->exists() || $equipment->maintenances()->exists()) {
            return back()->with('error', 'Impossible de supprimer cet équipement car il a des mouvements ou des maintenances associés.');
        }

        // Vérifier si la suppression est confirmée
        if (!$request->has('confirm_delete')) {
            return back()->with('warning', 'Veuillez cocher la case pour confirmer la suppression de l\'équipement.');
        }

        DB::beginTransaction();

        try {
            // Enregistrer l'historique avant suppression
            $equipment->history()->create([
                'action' => 'deleted',
                'user_id' => Auth::id(),
                'details' => 'Équipement supprimé',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Supprimer les documents associés
            foreach ($equipment->documents as $document) {
                Storage::disk('public')->delete($document->file_path);
                $document->delete();
            }

            // Supprimer l'image associée si elle existe
            if ($equipment->image_path) {
                Storage::disk('public')->delete($equipment->image_path);
            }

            // Supprimer l'équipement
            $equipment->delete();

            DB::commit();

            return redirect()
                ->route('equipment.index')
                ->with('success', 'Équipement supprimé avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->with('error', 'Une erreur est survenue lors de la suppression de l\'équipement : ' . $e->getMessage());
        }
    }

    /**
     * Affiche les résultats de la recherche avancée d'équipements.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function search(Request $request)
    {
        $query = Equipment::query()->with(['category', 'location', 'assignedUser']);
        
        // Recherche par texte
        if ($request->has('q') && !empty($request->q)) {
            $searchTerm = $request->q;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('serial_number', 'like', "%{$searchTerm}%")
                  ->orWhere('model', 'like', "%{$searchTerm}%")
                  ->orWhere('manufacturer', 'like', "%{$searchTerm}%")
                  ->orWhere('barcode', 'like', "%{$searchTerm}%")
                  ->orWhere('qr_code', 'like', "%{$searchTerm}%")
                  ->orWhere('order_number', 'like', "%{$searchTerm}%")
                  ->orWhere('notes', 'like', "%{$searchTerm}%")
                  ->orWhereHas('category', function($q) use ($searchTerm) {
                      $q->where('name', 'like', "%{$searchTerm}%");
                  });
            });
        }
        
        // Filtres avancés
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('category_id') && !empty($request->category_id)) {
            $query->where('category_id', $request->category_id);
        }
        
        if ($request->has('location_id') && !empty($request->location_id)) {
            $query->where('location_id', $request->location_id);
        }
        
        if ($request->has('assigned_to') && !empty($request->assigned_to)) {
            $query->where('assigned_to', $request->assigned_to);
        }
        
        if ($request->has('condition') && !empty($request->condition)) {
            $query->where('condition', $request->condition);
        }
        
        // Filtres de date
        if ($request->has('purchased_from') && !empty($request->purchased_from)) {
            $query->whereDate('purchase_date', '>=', $request->purchased_from);
        }
        
        if ($request->has('purchased_to') && !empty($request->purchased_to)) {
            $query->whereDate('purchase_date', '<=', $request->purchased_to);
        }
        
        if ($request->has('warranty_expires') && $request->warranty_expires === 'soon') {
            $query->whereNotNull('warranty_expires')
                  ->where('warranty_expires', '<=', now()->addMonths(3));
        } elseif ($request->has('warranty_expires') && $request->warranty_expires === 'expired') {
            $query->whereNotNull('warranty_expires')
                  ->where('warranty_expires', '<', now());
        }
        
        // Tri des résultats
        $sort = $request->input('sort', 'name');
        $direction = $request->input('direction', 'asc');
        
        if (in_array($sort, ['name', 'serial_number', 'model', 'manufacturer', 'purchase_date', 'status', 'created_at'])) {
            $query->orderBy($sort, $direction);
        }
        
        // Exporter les résultats si demandé
        if ($request->has('export') && $request->export === 'csv') {
            return $this->exportSearchResults($query->get());
        }
        
        $equipment = $query->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();
        $locations = Location::where('is_active', true)->orderBy('name')->get();
        $users = User::where('is_active', true)->orderBy('name')->get();

        return view('equipment.search', [
            'equipment' => $equipment,
            'categories' => $categories,
            'locations' => $locations,
            'users' => $users,
            'filters' => $request->all(),
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }
    
    /**
     * Exporte les résultats de recherche au format CSV.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $equipment
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    private function exportSearchResults($equipment)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="recherche_equipements_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($equipment) {
            $file = fopen('php://output', 'w');
            
            // En-têtes
            fputcsv($file, [
                'ID',
                'Nom',
                'N° de série',
                'Modèle',
                'Fabricant',
                'Catégorie',
                'Statut',
                'État',
                'Emplacement',
                'Assigné à',
                'Date d\'achat',
                'Coût d\'achat',
                'Valeur actuelle',
                'Date d\'expiration de la garantie',
                'Notes',
            ]);

            // Données
            foreach ($equipment as $item) {
                fputcsv($file, [
                    $item->id,
                    $item->name,
                    $item->serial_number,
                    $item->model,
                    $item->manufacturer,
                    $item->category ? $item->category->name : '',
                    $this->getStatusLabel($item->status),
                    $this->getConditionLabel($item->condition),
                    $item->location ? $item->location->name : '',
                    $item->assignedTo ? $item->assignedTo->name : '',
                    $item->purchase_date ? $item->purchase_date->format('d/m/Y') : '',
                    $item->purchase_cost ? number_format($item->purchase_cost, 2, ',', ' ') . ' €' : '',
                    $item->current_value ? number_format($item->current_value, 2, ',', ' ') . ' €' : '',
                    $item->warranty_expires ? $item->warranty_expires->format('d/m/Y') : '',
                    $item->notes,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Retourne le libellé du statut.
     *
     * @param  string  $status
     * @return string
     */
    private function getStatusLabel($status)
    {
        $labels = [
            'available' => 'Disponible',
            'in_use' => 'En utilisation',
            'maintenance' => 'En maintenance',
            'out_of_service' => 'Hors service',
        ];
        
        return $labels[$status] ?? $status;
    }
    
    /**
     * Retourne le libellé de l'état de l'équipement.
     *
     * @param  string  $condition
     * @return string
     */
    private function getConditionLabel($condition)
    {
        $labels = [
            'excellent' => 'Excellent',
            'good' => 'Bon',
            'fair' => 'Moyen',
            'poor' => 'Mauvais',
        ];
        
        return $labels[$condition] ?? $condition;
    }
    
    /**
     * Télécharge le code-barres d'un équipement au format PDF.
     *
     * @param  \App\Models\Equipment  $equipment
     * @return \Illuminate\Http\Response
     */
    public function downloadBarcode(Equipment $equipment)
    {
        if (!$equipment->barcode) {
            return back()->with('error', 'Aucun code-barres disponible pour cet équipement.');
        }
        
        $barcode = DNS1D::getBarcodePNG($equipment->barcode, 'C128');
        $barcodeImage = 'data:image/png;base64,' . $barcode;
        
        $pdf = PDF::loadView('equipment.barcode-pdf', [
            'equipment' => $equipment,
            'barcodeImage' => $barcodeImage
        ]);
        
        return $pdf->download('barcode-' . $equipment->id . '.pdf');
    }
    
    /**
     * Télécharge le QR code d'un équipement au format PDF.
     *
     * @param  \App\Models\Equipment  $equipment
     * @return \Illuminate\Http\Response
     */
    public function downloadQrCode(Equipment $equipment)
    {
        if (!$equipment->qr_code) {
            return back()->with('error', 'Aucun QR code disponible pour cet équipement.');
        }
        
        $qrCode = DNS2D::getBarcodePNG(route('equipment.show', $equipment), 'QRCODE');
        $qrCodeImage = 'data:image/png;base64,' . $qrCode;
        
        $pdf = PDF::loadView('equipment.qrcode-pdf', [
            'equipment' => $equipment,
            'qrCodeImage' => $qrCodeImage
        ]);
        
        return $pdf->download('qrcode-' . $equipment->id . '.pdf');
    }

    /**
     * Déplace un équipement vers un nouvel emplacement.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Equipment  $equipment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function move(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'location_id' => 'required|exists:locations,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Vérifier si l'emplacement a changé
        if ($equipment->location_id == $validated['location_id']) {
            return back()->with('warning', 'L\'équipement est déjà à cet emplacement.');
        }

        DB::beginTransaction();
        
        try {
            // Enregistrer l'ancien emplacement pour l'historique
            $previousLocationId = $equipment->location_id;
            
            // Mettre à jour l'emplacement de l'équipement
            $equipment->update([
                'location_id' => $validated['location_id'],
                'updated_by' => auth()->id(),
            ]);

            // Créer une entrée dans l'historique des mouvements
            EquipmentMovement::create([
                'equipment_id' => $equipment->id,
                'from_location_id' => $previousLocationId,
                'to_location_id' => $validated['location_id'],
                'moved_by' => auth()->id(),
                'moved_at' => now(),
                'notes' => $validated['notes'] ?? null,
                'type' => 'transfer',
            ]);

            DB::commit();
            
            return redirect()
                ->route('equipment.show', $equipment)
                ->with('success', 'L\'équipement a été déplacé avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors du déplacement de l\'équipement: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors du déplacement de l\'équipement.');
        }
    }
    
    /**
     * Assigner un équipement à un utilisateur.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Equipment  $equipment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function assign(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Vérifier si l'utilisateur a changé
        if ($equipment->assigned_to == $validated['assigned_to']) {
            return back()->with('warning', 'L\'équipement est déjà assigné à cet utilisateur.');
        }

        DB::beginTransaction();
        
        try {
            // Enregistrer l'ancien utilisateur pour l'historique
            $previousAssignedTo = $equipment->assigned_to;
            
            // Mettre à jour l'utilisateur assigné
            $equipment->update([
                'assigned_to' => $validated['assigned_to'],
                'updated_by' => auth()->id(),
            ]);

            // Créer une entrée dans l'historique des mouvements
            EquipmentMovement::create([
                'equipment_id' => $equipment->id,
                'assigned_to' => $validated['assigned_to'],
                'assigned_by' => auth()->id(),
                'assigned_at' => now(),
                'notes' => $validated['notes'] ?? 'Assignation de l\'équipement',
                'type' => 'assignment',
            ]);

            DB::commit();
            
            return redirect()
                ->route('equipment.show', $equipment)
                ->with('success', 'L\'équipement a été assigné avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de l\'assignation de l\'équipement: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de l\'assignation de l\'équipement.');
        }
    }
    
    /**
     * Désassigner un équipement d'un utilisateur.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Equipment  $equipment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unassign(Request $request, Equipment $equipment)
    {
        // Vérifier si l'équipement est déjà désassigné
        if (!$equipment->assigned_to) {
            return back()->with('warning', 'Cet équipement n\'est pas actuellement assigné.');
        }

        $validated = $request->validate([
            'unassigned_at' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        
        try {
            // Enregistrer l'utilisateur précédent pour l'historique
            $previousAssignedTo = $equipment->assigned_to;
            
            // Mettre à jour l'équipement pour le désassigner
            $equipment->update([
                'assigned_to' => null,
                'updated_by' => auth()->id(),
            ]);

            // Créer une entrée dans l'historique des mouvements
            EquipmentMovement::create([
                'equipment_id' => $equipment->id,
                'assigned_to' => null,
                'unassigned_by' => auth()->id(),
                'unassigned_at' => $validated['unassigned_at'],
                'notes' => $validated['notes'] ?? 'Désassignation de l\'équipement',
                'type' => 'unassignment',
            ]);

            DB::commit();
            
            return redirect()
                ->route('equipment.show', $equipment)
                ->with('success', 'L\'équipement a été désassigné avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de la désassignation de l\'équipement: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la désassignation de l\'équipement.');
        }
    }
}