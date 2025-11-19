@if($maintenance->attachments->isNotEmpty())
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-paperclip me-2"></i>Pièces jointes ({{ $maintenance->attachments->count() }})
            </h6>
            @if(isset($showActions) && $showActions && $maintenance->status !== 'completed' && $maintenance->status !== 'cancelled')
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addAttachmentModal">
                    <i class="fas fa-plus me-1"></i> Ajouter
                </button>
            @endif
        </div>
        <div class="card-body">
            <div class="row g-3">
                @foreach($maintenance->attachments as $attachment)
                    <div class="col-md-4 col-6">
                        <div class="card h-100 position-relative">
                            @if(in_array($attachment->extension, ['jpg', 'jpeg', 'png', 'gif']))
                                <a href="{{ Storage::url($attachment->path) }}" data-fancybox="gallery" data-caption="{{ $attachment->original_name }}">
                                    <img src="{{ Storage::url($attachment->path) }}" class="card-img-top attachment-preview" alt="{{ $attachment->original_name }}">
                                </a>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas {{ getFileIconByExtension($attachment->extension) }} fa-3x text-muted mb-2"></i>
                                    <p class="small text-truncate px-2 mb-0" title="{{ $attachment->original_name }}">
                                        {{ $attachment->original_name }}
                                    </p>
                                </div>
                            @endif
                            <div class="card-footer bg-transparent pt-0 border-top-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">{{ formatFileSize($attachment->size) }}</small>
                                    <div class="btn-group">
                                        <a href="{{ route('maintenance.attachments.download', $attachment) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Télécharger">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        @if(isset($showActions) && $showActions && $maintenance->status !== 'completed' && $maintenance->status !== 'cancelled')
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger delete-attachment-btn" 
                                                    data-attachment-id="{{ $attachment->id }}" 
                                                    data-attachment-name="{{ $attachment->original_name }}"
                                                    title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                @if($attachment->notes)
                                    <div class="mt-2 small text-muted">
                                        <i class="fas fa-sticky-note me-1"></i> {{ Str::limit($attachment->notes, 50) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@elseif(isset($showEmpty) && $showEmpty)
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-paperclip me-2"></i>Pièces jointes
            </h6>
            @if(isset($showActions) && $showActions && $maintenance->status !== 'completed' && $maintenance->status !== 'cancelled')
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addAttachmentModal">
                    <i class="fas fa-plus me-1"></i> Ajouter
                </button>
            @endif
        </div>
        <div class="card-body text-center py-5">
            <i class="fas fa-paperclip fa-3x text-muted mb-3"></i>
            <p class="text-muted mb-0">Aucune pièce jointe pour le moment</p>
            @if(isset($showActions) && $showActions && $maintenance->status !== 'completed' && $maintenance->status !== 'cancelled')
                <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addAttachmentModal">
                    <i class="fas fa-plus me-1"></i> Ajouter une pièce jointe
                </button>
            @endif
        </div>
    </div>
@endif

@if(isset($showActions) && $showActions && $maintenance->status !== 'completed' && $maintenance->status !== 'cancelled')
    <!-- Modal d'ajout de pièce jointe -->
    <div class="modal fade" id="addAttachmentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('maintenance.attachments.store', $maintenance) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Ajouter des pièces jointes</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="attachments" class="form-label">Sélectionner des fichiers <span class="text-danger">*</span></label>
                            <input class="form-control" type="file" id="attachments" name="attachments[]" multiple required>
                            <div class="form-text">Formats acceptés : JPG, PNG, PDF, DOC, XLS. Taille maximale : 5 Mo par fichier.</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="attachment_notes" class="form-label">Notes (optionnel)</label>
                            <textarea class="form-control" id="attachment_notes" name="notes" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Téléverser</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
