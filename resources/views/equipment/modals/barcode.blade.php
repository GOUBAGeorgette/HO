<div class="modal fade" id="barcodeModal" tabindex="-1" aria-labelledby="barcodeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="barcodeModalLabel">Code-barres de l'équipement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body text-center">
                @if($equipment->barcode)
                    <div class="mb-3">
                        {!! DNS1D::getBarcodeHTML($equipment->barcode, 'C128', 2, 80) !!}
                        <div class="mt-2">{{ $equipment->barcode }}</div>
                    </div>
                    <a href="{{ route('equipment.barcode.download', $equipment) }}" class="btn btn-primary">
                        <i class="fas fa-download me-2"></i>Télécharger
                    </a>
                @else
                    <p class="text-muted">Aucun code-barres disponible pour cet équipement.</p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
