<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use App\Models\Equipment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\MaintenanceScheduled;
use App\Notifications\MaintenanceCompleted;
use App\Notifications\MaintenanceOverdue;

class MaintenanceController extends Controller
{
    /**
     * Affiche la liste des maintenances avec filtres.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    

    /**
     * Affiche la liste des maintenances avec filtres.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Maintenance::with(['equipment', 'assignedTo', 'createdBy']);

        // Filtrage par statut
        if ($request->has('status') && in_array($request->status, ['scheduled', 'in_progress', 'completed', 'cancelled'])) {
            $query->where('status', $request->status);
        }

        // Filtrage par type de maintenance
        if ($request->has('type') && in_array($request->type, ['preventive', 'corrective', 'inspection', 'calibration'])) {
            $query->where('maintenance_type', $request->type);
        }

        // Filtrage par équipement
        if ($request->has('equipment_id') && $request->equipment_id) {
            $query->where('equipment_id', $request->equipment_id);
        }

        // Filtrage par date de planification
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('scheduled_date', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('scheduled_date', '<=', $request->end_date);
        }

        // Filtrage des maintenances en retard
        if ($request->has('overdue') && $request->overdue) {
            $query->where('scheduled_date', '<', now())
                ->whereIn('status', ['scheduled', 'in_progress']);
        }

        // Tri des résultats
        $sortField = $request->input('sort', 'scheduled_date');
        $sortDirection = $request->input('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $maintenances = $query->paginate(15)->withQueryString();

        // Récupérer la liste des équipements pour le filtre
        $equipmentList = Equipment::orderBy('name')->get();
        $technicians = User::role('technician')->orderBy('name')->get();

        return view('maintenance.index', [
            'maintenances' => $maintenances,
            'equipmentList' => $equipmentList,
            'technicians' => $technicians,
            'filters' => $request->all(),
        ]);
    }

    /**
     * Affiche le formulaire de création d'une maintenance.
     *
     * @param  int|null  $equipmentId
     * @return \Illuminate\View\View
     */
    public function create($equipmentId = null)
    {
        // Récupérer l'équipement si un ID est fourni
        $equipment = $equipmentId ? Equipment::findOrFail($equipmentId) : null;
        
        // Récupérer la liste des équipements actifs avec leurs relations
        $equipments = Equipment::active()
            ->with(['category', 'location'])
            ->orderBy('name')
            ->get();
            
        // Récupérer la liste des techniciens
        $technicians = User::role('technicien')
            ->orWhere('can_perform_maintenance', true)
            ->orderBy('name')
            ->get();
            
        // Récupérer les types de maintenance depuis l'énumération
        $maintenanceTypes = \App\Enums\MaintenanceType::cases();
        $priorities = \App\Enums\Priority::cases();
        
        // Si un équipement est spécifié, le sélectionner par défaut
        if ($equipment) {
            $equipments->prepend($equipment);
        }
        
        return view('maintenance.create', compact(
            'equipment',
            'equipments',
            'technicians',
            'maintenanceTypes',
            'priorities'
        ));
    }

    /**
     * Enregistre une nouvelle maintenance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'maintenance_type' => 'required|in:preventive,corrective,inspection,calibration',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'assigned_to' => 'required|exists:users,id',
            'estimated_hours' => 'nullable|numeric|min:0.5|max:24',
            'priority' => 'required|in:low,medium,high,critical',
            'notes' => 'nullable|string',
        ]);

        // Ajouter l'ID de l'utilisateur connecté comme créateur
        $validated['created_by'] = auth()->id();
        $validated['status'] = 'scheduled';

        // Créer la maintenance
        $maintenance = Maintenance::create($validated);

        // Notifier l'utilisateur assigné
        $assignedUser = User::find($validated['assigned_to']);
        $assignedUser->notify(new MaintenanceScheduled($maintenance));

        // Notifier les administrateurs
        $admins = User::role('admin')->get();
        Notification::send($admins, new MaintenanceScheduled($maintenance, 'Une nouvelle maintenance a été planifiée.'));

        return redirect()
            ->route('maintenances.show', $maintenance)
            ->with('success', 'Maintenance planifiée avec succès.');
    }

    /**
     * Affiche les détails d'une maintenance.
     *
     * @param  \App\Models\Maintenance  $maintenance
     * @return \Illuminate\View\View
     */
    public function show(Maintenance $maintenance)
    {
        $maintenance->load([
            'equipment', 
            'assignedTo', 
            'createdBy',
            'equipment.maintenances' => function($query) use ($maintenance) {
                $query->where('id', '!=', $maintenance->id)
                    ->orderBy('scheduled_date', 'desc')
                    ->limit(5);
            }
        ]);

        return view('maintenances.show', compact('maintenance'));
    }

    /**
     * Affiche le formulaire de modification d'une maintenance.
     *
     * @param  \App\Models\Maintenance  $maintenance
     * @return \Illuminate\View\View
     */
    public function edit(Maintenance $maintenance)
    {
        if ($maintenance->status === 'completed') {
            return back()->with('error', 'Impossible de modifier une maintenance terminée.');
        }

        $equipmentList = Equipment::orderBy('name')->get();
        $technicians = User::role('technician')->orderBy('name')->get();

        return view('maintenances.edit', [
            'maintenance' => $maintenance,
            'equipmentList' => $equipmentList,
            'technicians' => $technicians,
        ]);
    }

    /**
     * Met à jour une maintenance existante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Maintenance  $maintenance
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Maintenance $maintenance)
    {
        if ($maintenance->status === 'completed') {
            return back()->with('error', 'Impossible de modifier une maintenance terminée.');
        }

        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'maintenance_type' => 'required|in:preventive,corrective,inspection,calibration',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'scheduled_date' => 'required|date',
            'assigned_to' => 'required|exists:users,id',
            'status' => 'required|in:scheduled,in_progress,on_hold,completed,cancelled',
            'completed_date' => 'nullable|date|after_or_equal:scheduled_date',
            'estimated_hours' => 'nullable|numeric|min:0.5|max:24',
            'actual_hours' => 'nullable|numeric|min:0.1',
            'cost' => 'nullable|numeric|min:0',
            'priority' => 'required|in:low,medium,high,critical',
            'notes' => 'nullable|string',
        ]);

        // Si la maintenance est marquée comme terminée, enregistrer la date de fin
        if ($validated['status'] === 'completed' && empty($validated['completed_date'])) {
            $validated['completed_date'] = now();
        }

        // Si la date de planification change, réinitialiser le statut
        if ($maintenance->scheduled_date->format('Y-m-d') !== $validated['scheduled_date'] 
            && $maintenance->status !== 'scheduled') {
            $validated['status'] = 'scheduled';
        }

        // Mettre à jour la maintenance
        $maintenance->update($validated);

        // Notifier des changements importants
        if ($maintenance->wasChanged('assigned_to')) {
            $assignedUser = User::find($validated['assigned_to']);
            $assignedUser->notify(new MaintenanceScheduled($maintenance, 'Vous avez été assigné à cette maintenance.'));
        }

        if ($maintenance->status === 'completed') {
            // Notifier les administrateurs de l'achèvement
            $admins = User::role('admin')->get();
            Notification::send($admins, new MaintenanceCompleted($maintenance));
            
            // Mettre à jour la date de dernière maintenance de l'équipement
            $maintenance->equipment->update(['last_maintenance_date' => now()]);
        }

        return redirect()
            ->route('maintenances.show', $maintenance)
            ->with('success', 'Maintenance mise à jour avec succès.');
    }

    /**
     * Affiche le formulaire d'ajout de pièces détachées à une maintenance.
     *
     * @param  \App\Models\Maintenance  $maintenance
     * @return \Illuminate\View\View
     */
    public function showPartsForm(Maintenance $maintenance)
    {
        $maintenance->load('parts');
        return view('maintenances.parts', compact('maintenance'));
    }

    /**
     * Ajoute des pièces détachées à une maintenance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Maintenance  $maintenance
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addParts(Request $request, Maintenance $maintenance)
    {
        $validated = $request->validate([
            'parts' => 'required|array',
            'parts.*.name' => 'required|string|max:255',
            'parts.*.part_number' => 'nullable|string|max:100',
            'parts.*.quantity' => 'required|integer|min:1',
            'parts.*.unit_cost' => 'required|numeric|min:0',
            'parts.*.supplier' => 'nullable|string|max:255',
        ]);

        // Ajouter les pièces détachées
        $maintenance->parts()->createMany($validated['parts']);

        // Mettre à jour le coût total de la maintenance
        $totalPartsCost = $maintenance->parts->sum(function($part) {
            return $part->quantity * $part->unit_cost;
        });

        $maintenance->update([
            'cost' => $totalPartsCost + ($maintenance->actual_hours * $maintenance->hourly_rate)
        ]);

        return back()->with('success', 'Pièces détachées ajoutées avec succès.');
    }

    /**
     * Affiche le formulaire de rapport de maintenance.
     *
     * @param  \App\Models\Maintenance  $maintenance
     * @return \Illuminate\View\View
     */
    public function showReportForm(Maintenance $maintenance)
    {
        if ($maintenance->status !== 'completed') {
            return back()->with('error', 'Le rapport ne peut être complété que pour une maintenance terminée.');
        }

        return view('maintenances.report', compact('maintenance'));
    }

    /**
     * Enregistre le rapport de maintenance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Maintenance  $maintenance
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeReport(Request $request, Maintenance $maintenance)
    {
        if ($maintenance->status !== 'completed') {
            return back()->with('error', 'Le rapport ne peut être complété que pour une maintenance terminée.');
        }

        $validated = $request->validate([
            'findings' => 'required|string',
            'actions_taken' => 'required|string',
            'recommendations' => 'nullable|string',
            'next_maintenance_date' => 'nullable|date|after:today',
            'technician_notes' => 'nullable|string',
            'signature' => 'nullable|string',
        ]);

        // Mettre à jour le rapport de maintenance
        $maintenance->update([
            'findings' => $validated['findings'],
            'actions_taken' => $validated['actions_taken'],
            'recommendations' => $validated['recommendations'] ?? null,
            'technician_notes' => $validated['technician_notes'] ?? null,
            'signature' => $validated['signature'] ?? null,
            'report_completed_at' => now(),
        ]);

        // Planifier la prochaine maintenance préventive si nécessaire
        if ($maintenance->maintenance_type === 'preventive' && !empty($validated['next_maintenance_date'])) {
            $nextMaintenance = $maintenance->replicate();
            $nextMaintenance->scheduled_date = $validated['next_maintenance_date'];
            $nextMaintenance->status = 'scheduled';
            $nextMaintenance->completed_date = null;
            $nextMaintenance->save();
        }

        return redirect()
            ->route('maintenances.show', $maintenance)
            ->with('success', 'Rapport de maintenance enregistré avec succès.');
    }

    /**
     * Affiche le tableau de bord des maintenances.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        // Maintenances à venir (7 prochains jours)
        $upcoming = Maintenance::with('equipment', 'assignedTo')
            ->where('scheduled_date', '>=', now())
            ->where('scheduled_date', '<=', now()->addDays(7))
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->orderBy('scheduled_date')
            ->get();

        // Maintenances en retard
        $overdue = Maintenance::with('equipment', 'assignedTo')
            ->where('scheduled_date', '<', now())
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->orderBy('scheduled_date')
            ->get();

        // Statistiques
        $stats = [
            'total' => Maintenance::count(),
            'scheduled' => Maintenance::where('status', 'scheduled')->count(),
            'in_progress' => Maintenance::where('status', 'in_progress')->count(),
            'completed' => Maintenance::where('status', 'completed')->count(),
            'overdue' => $overdue->count(),
        ];

        // Répartition par type de maintenance
        $maintenanceTypes = Maintenance::select('maintenance_type', DB::raw('count(*) as total'))
            ->groupBy('maintenance_type')
            ->pluck('total', 'maintenance_type');

        return view('maintenances.dashboard', [
            'upcoming' => $upcoming,
            'overdue' => $overdue,
            'stats' => $stats,
            'maintenanceTypes' => $maintenanceTypes,
        ]);
    }

    /**
     * Génère un rapport des coûts de maintenance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function costReport(Request $request)
    {
        $query = Maintenance::where('status', 'completed')
            ->whereNotNull('completed_date');

        // Filtrage par période
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        $query->whereBetween('completed_date', [$startDate, $endDate]);

        // Grouper par équipement et calculer les coûts
        $equipmentCosts = $query->select(
                'equipment_id',
                DB::raw('count(*) as maintenance_count'),
                DB::raw('sum(cost) as total_cost'),
                DB::raw('avg(cost) as avg_cost')
            )
            ->with('equipment')
            ->groupBy('equipment_id')
            ->orderByDesc('total_cost')
            ->get();

        // Grouper par type de maintenance
        $typeCosts = $query->select(
                'maintenance_type',
                DB::raw('count(*) as count'),
                DB::raw('sum(cost) as total_cost')
            )
            ->groupBy('maintenance_type')
            ->get();

        // Coût total
        $totalCost = $equipmentCosts->sum('total_cost');

        // Export CSV si demandé
        if ($request->has('export')) {
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="rapport_couts_maintenance_' . date('Y-m-d') . '.csv"',
            ];

            $callback = function() use ($equipmentCosts, $typeCosts, $totalCost, $startDate, $endDate) {
                $file = fopen('php://output', 'w');
                
                // En-tête
                fputcsv($file, ['Rapport des coûts de maintenance']);
                fputcsv($file, ['Période du ' . $startDate . ' au ' . $endDate]);
                fputcsv($file, ['']);
                
                // Coût total
                fputcsv($file, ['Coût total des maintenances', number_format($totalCost, 2) . ' €']);
                fputcsv($file, ['']);
                
                // Coûts par équipement
                fputcsv($file, ['Coûts par équipement']);
                fputcsv($file, ['Équipement', 'Nombre de maintenances', 'Coût total', 'Coût moyen']);
                
                foreach ($equipmentCosts as $cost) {
                    fputcsv($file, [
                        $cost->equipment->name,
                        $cost->maintenance_count,
                        number_format($cost->total_cost, 2) . ' €',
                        number_format($cost->avg_cost, 2) . ' €',
                    ]);
                }
                
                fputcsv($file, ['']);
                
                // Coûts par type de maintenance
                fputcsv($file, ['Coûts par type de maintenance']);
                fputcsv($file, ['Type', 'Nombre', 'Coût total']);
                
                foreach ($typeCosts as $type) {
                    fputcsv($file, [
                        $type->maintenance_type,
                        $type->count,
                        number_format($type->total_cost, 2) . ' €',
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return view('maintenances.cost-report', [
            'equipmentCosts' => $equipmentCosts,
            'typeCosts' => $typeCosts,
            'totalCost' => $totalCost,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    /**
     * Vérifie les maintenances préventives à planifier.
     *
     * @return void
     */
    public function checkPreventiveMaintenance()
    {
        $equipments = Equipment::where('requires_maintenance', true)
            ->where('last_maintenance_date', '<=', now()->subMonths(6)) // Exemple: maintenance tous les 6 mois
            ->whereDoesntHave('maintenances', function($query) {
                $query->where('scheduled_date', '>=', now())
                    ->where('status', 'scheduled');
            })
            ->get();

        foreach ($equipments as $equipment) {
            // Créer une maintenance préventive planifiée
            $maintenance = $equipment->maintenances()->create([
                'maintenance_type' => 'preventive',
                'title' => 'Maintenance préventive - ' . $equipment->name,
                'description' => 'Maintenance préventive planifiée pour ' . $equipment->name,
                'scheduled_date' => now()->addWeek(), // Planifier pour la semaine prochaine
                'status' => 'scheduled',
                'priority' => 'medium',
                'created_by' => 1, // ID de l'administrateur système
            ]);

            // Notifier les administrateurs
            $admins = User::role('admin')->get();
            Notification::send($admins, new MaintenanceScheduled($maintenance, 'Nouvelle maintenance préventive planifiée.'));
        }
    }

    /**
     * Envoie des notifications pour les maintenances en retard.
     *
     * @return void
     */
    public function notifyOverdueMaintenance()
    {
        $overdueMaintenances = Maintenance::where('scheduled_date', '<', now())
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->with(['equipment', 'assignedTo'])
            ->get();

        foreach ($overdueMaintenances as $maintenance) {
            // Notifier la personne assignée
            if ($maintenance->assignedTo) {
                $maintenance->assignedTo->notify(new MaintenanceOverdue($maintenance));
            }

            // Notifier les administrateurs
            $admins = User::role('admin')->get();
            Notification::send($admins, new MaintenanceOverdue($maintenance, 'Maintenance en retard pour ' . $maintenance->equipment->name));
        }
    }
}
