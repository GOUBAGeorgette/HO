<?php

namespace App\Http\Controllers;

use App\Models\EquipmentMovement;
use App\Models\Equipment;
use App\Models\Location;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Enums\MovementStatus;

class EquipmentMovementController extends Controller
{
    /**
     * Affiche l'historique des mouvements avec des filtres.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    /**
     * Affiche le formulaire de création d'un nouveau mouvement.
     *
     * @return \Illuminate\View\View
     */
    /**
     * Enregistre un nouveau mouvement d'équipement.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validation des données du formulaire
        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'origin_type' => 'required|in:location,department,external',
            'origin_location_id' => 'nullable|required_if:origin_type,location|exists:locations,id',
            'origin_department_id' => 'nullable|required_if:origin_type,department|exists:departments,id',
            'origin_external' => 'nullable|required_if:origin_type,external|string|max:255',
            'origin_contact' => 'required|string|max:255',
            'destination_type' => 'required|in:location,department,external',
            'destination_location_id' => 'nullable|required_if:destination_type,location|exists:locations,id',
            'destination_department_id' => 'nullable|required_if:destination_type,department|exists:departments,id',
            'destination_external' => 'nullable|required_if:destination_type,external|string|max:255',
            'destination_contact' => 'required|string|max:255',
            'movement_type' => 'required|in:maintenance,repair,transfer,loan,return,other',
            'priority' => 'required|in:low,medium,high',
            'scheduled_date' => 'required|date',
            'assigned_to' => 'required|exists:users,id',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'requires_approval' => 'nullable|boolean',
            'terms_accepted' => 'required|accepted'
        ]);

        try {
            // Création du mouvement
            $movement = new EquipmentMovement([
                'equipment_id' => $validated['equipment_id'],
                'origin_type' => $validated['origin_type'],
                'origin_location_id' => $validated['origin_location_id'] ?? null,
                'origin_department_id' => $validated['origin_department_id'] ?? null,
                'origin_external' => $validated['origin_external'] ?? null,
                'origin_contact' => $validated['origin_contact'],
                'destination_type' => $validated['destination_type'],
                'destination_location_id' => $validated['destination_location_id'] ?? null,
                'destination_department_id' => $validated['destination_department_id'] ?? null,
                'destination_external' => $validated['destination_external'] ?? null,
                'destination_contact' => $validated['destination_contact'],
                'type' => $validated['movement_type'],
                'priority' => $validated['priority'],
                'scheduled_date' => $validated['scheduled_date'],
                'assigned_to' => $validated['assigned_to'],
                'reason' => $validated['reason'],
                'notes' => $validated['notes'] ?? null,
                'status' => $validated['requires_approval'] ? 'pending_approval' : 'approved',
                'requested_by' => auth()->id(),
                'approved_by' => $validated['requires_approval'] ? null : auth()->id(),
                'approved_at' => $validated['requires_approval'] ? null : now(),
            ]);

            $movement->save();

            // Mise à jour de l'emplacement de l'équipement si le mouvement est approuvé
            if (!$validated['requires_approval']) {
                $equipment = Equipment::find($validated['equipment_id']);
                if ($validated['destination_type'] === 'location') {
                    $equipment->location_id = $validated['destination_location_id'];
                } elseif ($validated['destination_type'] === 'department') {
                    $department = Department::find($validated['destination_department_id']);
                    $equipment->location_id = $department->location_id;
                }
                $equipment->save();
            }

            // Redirection avec message de succès
            return redirect()->route('equipment-movements.index')
                ->with('success', 'Le mouvement d\'équipement a été créé avec succès.');

        } catch (\Exception $e) {
            // En cas d'erreur, on redirige avec un message d'erreur
            return redirect()->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création du mouvement : ' . $e->getMessage());
        }
    }

    /**
     * Affiche le formulaire de création d'un nouveau mouvement.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Vérifier s'il y a des équipements disponibles
        $equipmentList = Equipment::with('category')
            ->where('status', '!=', 'out_of_service')
            ->orderBy('name')
            ->get();
            
        // Si aucun équipement n'est disponible, en créer un pour le test
        if ($equipmentList->isEmpty()) {
            $category = \App\Models\Category::where('is_active', true)->first();
            if ($category) {
                $equipment = new Equipment([
                    'name' => 'Ordinateur Portable de Test',
                    'serial_number' => 'TEST-' . rand(1000, 9999),
                    'model' => 'Dell XPS 15',
                    'manufacturer' => 'Dell',
                    'status' => 'available',
                    'category_id' => $category->id,
                    'location_id' => 1,
                    'purchase_date' => now(),
                    'purchase_cost' => 1299.99,
                    'warranty_months' => 24,
                    'condition' => 'excellent',
                    'created_by' => auth()->id() ?? 1,
                    'updated_by' => auth()->id() ?? 1
                ]);
                
                $equipment->save();
                
                // Recharger la liste des équipements avec la relation de catégorie
                $equipmentList = Equipment::with('category')
                    ->where('status', '!=', 'out_of_service')
                    ->orderBy('name')
                    ->get();
                    
                // Afficher un message de débogage
                \Log::info('Équipement de test créé', ['id' => $equipment->id, 'name' => $equipment->name]);
            } else {
                \Log::error('Aucune catégorie active trouvée pour créer un équipement de test');
            }
        }
        
        // Log pour débogage
        \Log::info('Liste des équipements transmise à la vue', [
            'count' => $equipmentList->count(),
            'equipments' => $equipmentList->toArray()
        ]);
        
        // Vérifier la structure des données pour le premier équipement
        if ($equipmentList->isNotEmpty()) {
            $firstEquipment = $equipmentList->first();
            \Log::info('Premier équipement', [
                'id' => $firstEquipment->id,
                'name' => $firstEquipment->name,
                'category' => $firstEquipment->category,
                'has_category' => !is_null($firstEquipment->category)
            ]);
        }

        $locations = Location::where('is_active', true)
            ->orderBy('name')
            ->get();

        $users = User::orderBy('name')->get();
        
        $departments = Department::orderBy('name')->get();

        return view('equipment-movements.create', [
            'equipmentList' => $equipmentList,
            'locations' => $locations,
            'users' => $users,
            'departments' => $departments,
            'statuses' => MovementStatus::cases(),
        ]);
    }

    /**
     * Affiche l'historique des mouvements avec des filtres.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Requête de base avec les relations nécessaires
        $query = EquipmentMovement::with([
            'equipment', 
            'originLocation',
            'originDepartment',
            'destinationLocation',
            'destinationDepartment',
            'requester',
            'approver'
        ]);

        // Filtrage par type de mouvement
        if ($request->has('type') && in_array($request->type, ['checkin', 'checkout', 'transfer', 'maintenance'])) {
            $query->where('type', $request->type);
        }

        // Filtrage par date
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('scheduled_date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('scheduled_date', '<=', $request->date_to);
        }

        // Filtrage par équipement
        if ($request->has('equipment_id') && $request->equipment_id) {
            $query->where('equipment_id', $request->equipment_id);
        }

        // Filtrage par emplacement source
        if ($request->has('origin_location_id') && $request->origin_location_id) {
            $query->where('origin_location_id', $request->origin_location_id);
        }

        // Filtrage par emplacement de destination
        if ($request->has('destination_location_id') && $request->destination_location_id) {
            $query->where('destination_location_id', $request->destination_location_id);
        }

        // Récupération des mouvements paginés
        $movements = $query->orderBy('scheduled_date', 'desc')->paginate(10);
        
        // Récupération des mouvements à venir (pour le panneau latéral)
        $upcomingMovements = EquipmentMovement::where('scheduled_date', '>=', now())
            ->orderBy('scheduled_date')
            ->take(5)
            ->get();
            
        // Récupération des mouvements en cours
        $inProgressMovements = EquipmentMovement::where('status', 'in_progress')
            ->orderBy('scheduled_date')
            ->get();
            
        // Préparation des événements pour le calendrier
        $calendarMovements = EquipmentMovement::whereNotNull('scheduled_date')
            ->where('scheduled_date', '>=', now()->subMonths(3)) // 3 derniers mois
            ->get()
            ->map(function ($movement) {
                // Définir la couleur en fonction du type de mouvement
                $colors = [
                    'checkout' => '#3498db', // Bleu
                    'checkin' => '#2ecc71',  // Vert
                    'transfer' => '#9b59b6', // Violet
                    'maintenance' => '#f39c12', // Orange
                    'repair' => '#e74c3c',   // Rouge
                    'loan' => '#1abc9c',     // Turquoise
                    'return' => '#2ecc71',   // Vert (comme checkin)
                    'other' => '#95a5a6'     // Gris
                ];
                
                return [
                    'title' => $movement->equipment->name . ' - ' . $movement->type_label,
                    'start' => $movement->scheduled_date->toIso8601String(),
                    'end' => $movement->completed_at ? $movement->completed_at->toIso8601String() : null,
                    'url' => route('equipment-movements.show', $movement),
                    'backgroundColor' => $colors[$movement->type] ?? '#95a5a6',
                    'borderColor' => $colors[$movement->type] ?? '#95a5a6',
                    'textColor' => '#ffffff',
                    'extendedProps' => [
                        'status' => $movement->status,
                        'type' => $movement->type
                    ]
                ];
            })->toArray();

        // Statistiques pour les cartes
        $stats = [
            'total' => EquipmentMovement::count(),
            'pending' => EquipmentMovement::where('status', 'pending')->count(),
            'approved' => EquipmentMovement::where('status', 'approved')->count(),
            'in_progress' => EquipmentMovement::where('status', 'in_progress')->count(),
            'completed' => EquipmentMovement::where('status', 'completed')->count(),
            'cancelled' => EquipmentMovement::where('status', 'cancelled')->count(),
        ];

        // Données pour les filtres
        $equipmentList = Equipment::orderBy('name')->get();
        $locations = Location::where('is_active', true)->orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('equipment-movements.index', [
            'movements' => $movements,
            'upcomingMovements' => $upcomingMovements,
            'equipmentList' => $equipmentList,
            'locations' => $locations,
            'users' => $users,
            'filters' => $request->all(),
            'stats' => $stats,
            'calendarMovements' => $calendarMovements,
            'inProgressMovements' => $inProgressMovements,
        ]);
    }

    /**
     * Affiche le formulaire de sortie d'équipement.
     *
     * @param  int  $equipmentId
     * @return \Illuminate\View\View
     */
    public function createCheckout($equipmentId = null)
    {
        $equipment = $equipmentId 
            ? Equipment::findOrFail($equipmentId)
            : null;

        $locations = Location::where('is_active', true)->orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('movements.checkout', [
            'equipment' => $equipment,
            'locations' => $locations,
            'users' => $users,
        ]);
    }

    /**
     * Enregistre une sortie d'équipement.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeCheckout(Request $request)
    {
        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'to_location_id' => 'nullable|exists:locations,id',
            'assigned_to' => 'required|exists:users,id',
            'expected_return_date' => 'nullable|date|after:today',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $equipment = Equipment::findOrFail($validated['equipment_id']);
        
        // Démarrer une transaction
        DB::beginTransaction();

        try {
            // Créer le mouvement de sortie
            $movement = $equipment->movements()->create([
                'from_location_id' => $equipment->location_id,
                'to_location_id' => $validated['to_location_id'] ?? null,
                'moved_by' => auth()->id(),
                'assigned_to' => $validated['assigned_to'],
                'moved_at' => now(),
                'type' => 'checkout',
                'expected_return_date' => $validated['expected_return_date'] ?? null,
                'reason' => $validated['reason'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Mettre à jour l'emplacement de l'équipement
            $equipment->update([
                'status' => 'in_use',
                'assigned_to' => $validated['assigned_to'],
                'location_id' => $validated['to_location_id'] ?? null,
            ]);

            DB::commit();

            return redirect()
                ->route('equipment.show', $equipment)
                ->with('success', 'Sortie d\'équipement enregistrée avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    /**
     * Affiche le formulaire de retour d'équipement.
     *
     * @param  int  $equipmentId
     * @return \Illuminate\View\View
     */
    public function createCheckin($equipmentId)
    {
        $equipment = Equipment::with(['lastMovement'])->findOrFail($equipmentId);
        $locations = Location::where('is_active', true)->orderBy('name')->get();

        return view('movements.checkin', [
            'equipment' => $equipment,
            'locations' => $locations,
        ]);
    }

    /**
     * Enregistre un retour d'équipement.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $equipmentId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeCheckin(Request $request, $equipmentId)
    {
        $validated = $request->validate([
            'to_location_id' => 'required|exists:locations,id',
            'condition' => 'required|in:excellent,good,fair,poor',
            'notes' => 'nullable|string',
        ]);

        $equipment = Equipment::findOrFail($equipmentId);
        $lastMovement = $equipment->movements()->latest()->first();
        
        if (!$lastMovement || $lastMovement->type !== 'checkout') {
            return back()->with('error', 'Aucune sortie en cours trouvée pour cet équipement.');
        }

        // Démarrer une transaction
        DB::beginTransaction();

        try {
            // Créer le mouvement de retour
            $movement = $equipment->movements()->create([
                'from_location_id' => $lastMovement->to_location_id ?? $equipment->location_id,
                'to_location_id' => $validated['to_location_id'],
                'moved_by' => auth()->id(),
                'assigned_to' => null, // L'équipement n'est plus assigné à un utilisateur
                'moved_at' => now(),
                'type' => 'checkin',
                'reason' => 'Retour d\'équipement',
                'notes' => $validated['notes'] . "\nÉtat au retour: " . $validated['condition'],
            ]);

            // Mettre à jour l'équipement
            $equipment->update([
                'status' => 'available',
                'assigned_to' => null,
                'location_id' => $validated['to_location_id'],
            ]);

            // Marquer le mouvement de sortie comme complété
            $lastMovement->update(['is_completed' => true]);

            DB::commit();

            return redirect()
                ->route('equipment.show', $equipment)
                ->with('success', 'Retour d\'équipement enregistré avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    /**
     * Affiche les détails d'un mouvement.
     *
     * @param  \App\Models\EquipmentMovement  $movement
     * @return \Illuminate\View\View
     */
    public function show(EquipmentMovement $movement)
    {
        $movement->load([
            'equipment', 
            'fromLocation', 
            'toLocation', 
            'movedBy', 
            'assignedTo'
        ]);

        return view('movements.show', compact('movement'));
    }

    /**
     * Affiche le rapport des mouvements.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function report(Request $request)
    {
        $query = EquipmentMovement::with(['equipment', 'fromLocation', 'toLocation', 'movedBy']);

        // Filtres
        if ($request->has('type') && in_array($request->type, ['checkin', 'checkout', 'transfer', 'maintenance'])) {
            $query->where('type', $request->type);
        }

        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('moved_at', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('moved_at', '<=', $request->end_date);
        }

        // Grouper les résultats par date
        $movementsByDate = $query->orderBy('moved_at', 'desc')
            ->get()
            ->groupBy(function($date) {
                return Carbon::parse($date->moved_at)->format('Y-m-d');
            });

        // Statistiques
        $stats = [
            'total' => $query->count(),
            'checkouts' => $query->clone()->where('type', 'checkout')->count(),
            'checkins' => $query->clone()->where('type', 'checkin')->count(),
            'transfers' => $query->clone()->where('type', 'transfer')->count(),
        ];

        return view('movements.report', [
            'movementsByDate' => $movementsByDate,
            'stats' => $stats,
            'filters' => $request->all(),
        ]);
    }

    /**
     * Exporte les mouvements au format CSV.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function export(Request $request)
    {
        $query = EquipmentMovement::with(['equipment', 'fromLocation', 'toLocation', 'movedBy']);

        // Appliquer les mêmes filtres que pour l'index
        if ($request->has('type') && in_array($request->type, ['checkin', 'checkout', 'transfer', 'maintenance'])) {
            $query->where('type', $request->type);
        }

        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('moved_at', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('moved_at', '<=', $request->end_date);
        }

        $movements = $query->orderBy('moved_at', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="mouvements_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($movements) {
            $file = fopen('php://output', 'w');
            
            // En-têtes
            fputcsv($file, [
                'ID',
                'Date',
                'Type',
                'Équipement',
                'N° de série',
                'Depuis l\'emplacement',
                'Vers l\'emplacement',
                'Effectué par',
                'Assigné à',
                'Raison',
                'Notes',
            ]);

            // Données
            foreach ($movements as $movement) {
                fputcsv($file, [
                    $movement->id,
                    $movement->moved_at->format('d/m/Y H:i'),
                    $movement->type_label,
                    $movement->equipment->name,
                    $movement->equipment->serial_number,
                    $movement->fromLocation ? $movement->fromLocation->name : 'N/A',
                    $movement->toLocation ? $movement->toLocation->name : 'N/A',
                    $movement->movedBy->name,
                    $movement->assignedTo ? $movement->assignedTo->name : 'N/A',
                    $movement->reason,
                    $movement->notes,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
