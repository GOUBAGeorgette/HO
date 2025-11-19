<div class="modal fade" id="qrcodeModal" tabindex="-1" aria-labelledby="qrcodeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qrcodeModalLabel">QR Code de l'équipement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body text-center">
                @if($equipment->qr_code)
                    <div class="mb-3">
                        {!! DNS2D::getBarcodeHTML(route('equipment.show', $equipment), 'QRCODE', 10, 10) !!}
                        <div class="mt-2">{{ $equipment->qr_code }}</div>
                    </div>
                    <a href="{{ route('equipment.qrcode.download', $equipment) }}" class="btn btn-primary">
                        <i class="fas fa-download me-2"></i>Télécharger
                    </a>
                @else
                    <p class="text-muted">Aucun QR code disponible pour cet équipement.</p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
