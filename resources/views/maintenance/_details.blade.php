<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-info-circle me-2"></i>Détails de la maintenance
        </h6>
        <span class="badge bg-{{ $maintenance->status === 'completed' ? 'success' : ($maintenance->status === 'cancelled' ? 'secondary' : 'primary') }}">
            {{ __("maintenance.status.{$maintenance->status}") }}
        </span>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-bold">Type de maintenance</label>
                    <p class="mb-0">{{ __("maintenance.types.{$maintenance->maintenance_type}") }}</p>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Priorité</label>
                    <p class="mb-0">
                        @if($maintenance->priority === 'high')
                            <span class="badge bg-danger">Haute priorité</span>
                        @elseif($maintenance->priority === 'medium')
                            <span class="badge bg-warning text-dark">Priorité moyenne</span>
                        @else
                            <span class="badge bg-success">Basse priorité</span>
                        @endif
                    </p>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Date de planification</label>
                    <p class="mb-0">{{ $maintenance->scheduled_date->format('d/m/Y H:i') }}</p>
                </div>
                
                @if($maintenance->started_at)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Date de début</label>
                        <p class="mb-0">{{ $maintenance->started_at->format('d/m/Y H:i') }}</p>
                    </div>
                @endif
                
                @if($maintenance->completed_at)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Date de fin</label>
                        <p class="mb-0">{{ $maintenance->completed_at->format('d/m/Y H:i') }}</p>
                    </div>
                @endif
                
                @if($maintenance->cancelled_at)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Date d'annulation</label>
                        <p class="mb-0">{{ $maintenance->cancelled_at->format('d/m/Y H:i') }}</p>
                    </div>
                @endif
            </div>
            
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-bold">Créée par</label>
                    <p class="mb-0">
                        {{ $maintenance->createdBy->name ?? 'Système' }}
                        <small class="text-muted d-block">
                            {{ $maintenance->created_at->format('d/m/Y H:i') }}
                        </small>
                    </p>
                </div>
                
                @if($maintenance->assignedTo)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Assignée à</label>
                        <p class="mb-0">
                            {{ $maintenance->assignedTo->name }}
                            @if($maintenance->assignedTo->email)
                                <small class="text-muted d-block">
                                    <i class="fas fa-envelope me-1"></i> {{ $maintenance->assignedTo->email }}
                                </small>
                            @endif
                            @if($maintenance->assignedTo->phone)
                                <small class="text-muted d-block">
                                    <i class="fas fa-phone me-1"></i> {{ $maintenance->assignedTo->phone }}
                                </small>
                            @endif
                        </p>
                    </div>
                @endif
                
                @if($maintenance->estimated_duration)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Durée estimée</label>
                        <p class="mb-0">{{ $maintenance->estimated_duration }} minutes</p>
                    </div>
                @endif
                
                @if($maintenance->actual_duration)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Durée réelle</label>
                        <p class="mb-0">{{ $maintenance->actual_duration }} minutes</p>
                    </div>
                @endif
                
                @if($maintenance->estimated_cost)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Coût estimé</label>
                        <p class="mb-0">{{ number_format($maintenance->estimated_cost, 2, ',', ' ') }} €</p>
                    </div>
                @endif
                
                @if($maintenance->total_cost)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Coût total</label>
                        <p class="mb-0">{{ number_format($maintenance->total_cost, 2, ',', ' ') }} €</p>
                    </div>
                @endif
            </div>
        </div>
        
        <div class="mt-4">
            <label class="form-label fw-bold">Description</label>
            <div class="border rounded p-3 bg-light">
                {!! nl2br(e($maintenance->description)) !!}
            </div>
        </div>
        
        @if($maintenance->notes)
            <div class="mt-3">
                <label class="form-label fw-bold">Notes supplémentaires</label>
                <div class="border rounded p-3 bg-light">
                    {!! nl2br(e($maintenance->notes)) !!}
                </div>
            </div>
        @endif
        
        @if($maintenance->completion_notes)
            <div class="mt-3">
                <label class="form-label fw-bold">Rapport de fin</label>
                <div class="border rounded p-3 bg-light">
                    {!! nl2br(e($maintenance->completion_notes)) !!}
                </div>
            </div>
        @endif
        
        @if($maintenance->cancellation_reason)
            <div class="mt-3">
                <label class="form-label fw-bold">Raison de l'annulation</label>
                <div class="border rounded p-3 bg-light">
                    {!! nl2br(e($maintenance->cancellation_reason)) !!}
                </div>
            </div>
        @endif
    </div>
</div>
