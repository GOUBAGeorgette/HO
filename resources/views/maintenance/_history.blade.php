@if($maintenance->history->isNotEmpty() || (isset($showEmpty) && $showEmpty))
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-history me-2"></i>Historique des modifications
                @if($maintenance->history->isNotEmpty())
                    <span class="badge bg-primary">{{ $maintenance->history->count() }}</span>
                @endif
            </h6>
        </div>
        
        @if($maintenance->history->isNotEmpty())
            <div class="card-body p-0">
                <div class="timeline">
                    @foreach($maintenance->history as $history)
                        <div class="timeline-item {{ $history->event === 'completed' ? 'completed' : ($history->event === 'cancelled' ? 'cancelled' : '') }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">
                                        {{ $history->getEventLabel() }}
                                    </h6>
                                    <p class="mb-1">
                                        <small class="text-muted">
                                            <i class="far fa-user me-1"></i> {{ $history->user->name }}
                                            <span class="mx-2">•</span>
                                            <i class="far fa-clock me-1"></i> {{ $history->created_at->format('d/m/Y H:i') }}
                                        </small>
                                    </p>
                                    @if($history->notes)
                                        <div class="alert alert-light p-2 small mt-2 mb-0">
                                            {!! nl2br(e($history->notes)) !!}
                                        </div>
                                    @endif
                                </div>
                                @if($history->properties->isNotEmpty())
                                    <button type="button" class="btn btn-sm btn-outline-secondary" 
                                            data-bs-toggle="collapse" 
                                            data-bs-target="#historyDetails{{ $history->id }}" 
                                            aria-expanded="false" 
                                            aria-controls="historyDetails{{ $history->id }}">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                @endif
                            </div>
                            
                            @if($history->properties->isNotEmpty())
                                <div class="collapse mt-2" id="historyDetails{{ $history->id }}">
                                    <div class="card card-body bg-light p-2 small">
                                        <table class="table table-sm table-borderless mb-0">
                                            <tbody>
                                                @foreach($history->properties->get('attributes') as $key => $value)
                                                    @if(!in_array($key, ['updated_at', 'created_at']))
                                                        <tr>
                                                            <td class="text-muted" style="width: 30%;">
                                                                {{ __("maintenance.fields.{$key}", ['default' => $key]) }}
                                                            </td>
                                                            <td class="text-end">
                                                                @if(is_bool($value))
                                                                    <span class="badge bg-{{ $value ? 'success' : 'secondary' }}">
                                                                        {{ $value ? 'Oui' : 'Non' }}
                                                                    </span>
                                                                @elseif($key === 'status')
                                                                    <span class="badge bg-{{ $value === 'completed' ? 'success' : ($value === 'cancelled' ? 'secondary' : 'primary') }}">
                                                                        {{ __("maintenance.status.{$value}") }}
                                                                    </span>
                                                                @elseif($key === 'priority')
                                                                    <span class="badge bg-{{ $value === 'high' ? 'danger' : ($value === 'medium' ? 'warning' : 'success') }}">
                                                                        {{ __("maintenance.priorities.{$value}") }}
                                                                    </span>
                                                                @elseif($key === 'maintenance_type')
                                                                    <span class="badge bg-info">
                                                                        {{ __("maintenance.types.{$value}") }}
                                                                    </span>
                                                                @elseif(is_numeric($value) && str_contains($key, 'cost') || str_contains($key, 'price'))
                                                                    {{ number_format($value, 2, ',', ' ') }} €
                                                                @elseif(is_string($value) && (str_contains($key, 'date') || str_contains($key, 'at')) && $value !== null)
                                                                    {{ \Carbon\Carbon::parse($value)->format('d/m/Y H:i') }}
                                                                @else
                                                                    {{ $value ?? '-' }}
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                    
                    <!-- Événement de création -->
                    <div class="timeline-item">
                        <h6 class="mb-1">
                            Demande de maintenance créée
                        </h6>
                        <p class="mb-0">
                            <small class="text-muted">
                                <i class="far fa-user me-1"></i> {{ $maintenance->createdBy->name ?? 'Système' }}
                                <span class="mx-2">•</span>
                                <i class="far fa-clock me-1"></i> {{ $maintenance->created_at->format('d/m/Y H:i') }}
                            </small>
                        </p>
                    </div>
                </div>
            </div>
        @else
            <div class="card-body text-center py-5">
                <i class="fas fa-history fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-0">Aucun historique disponible</p>
            </div>
        @endif
    </div>
@endif
