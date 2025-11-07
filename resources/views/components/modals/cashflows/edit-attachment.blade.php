<form wire:submit.prevent="editAttachment">

    <div class="modal fade" tabindex="-1" id="editAttachmentModal" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Ubah Bukti (Attachment)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label for="editAttachmentFile" class="form-label">Upload Bukti Baru</label>
                        <input type="file"
                               class="form-control @error('editAttachmentFile') is-invalid @enderror"
                               id="editAttachmentFile"
                               wire:model="editAttachmentFile"
                               accept="image/*">

                        <div wire:loading wire:target="editAttachmentFile" class="text-muted mt-1">
                            <i class="fas fa-spinner fa-spin me-1"></i> Uploading...
                        </div>

                        @error('editAttachmentFile')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <div class="mt-3">
                            @if ($editAttachmentFile)
                                <p class="mb-1">Preview:</p>
                                <img src="{{ $editAttachmentFile->temporaryUrl() }}" class="img-fluid rounded"
                                     style="max-height: 200px;" alt="Preview Attachment Baru">
                            @elseif($currentAttachment)
                                <p class="mb-1">Bukti saat ini:</p>
                                <img src="{{ asset('storage/' . $currentAttachment) }}" class="img-fluid rounded"
                                     style="max-height: 200px;" alt="Attachment Saat Ini">
                            @endif
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            wire:loading.attr="disabled" wire:target="editAttachmentFile">
                        Batal
                    </button>

                    <button type="submit" class="btn btn-primary" 
                            wire:loading.attr="disabled" wire:target="editAttachmentFile, editAttachment">
                        
                        <span wire:loading.remove wire:target="editAttachmentFile, editAttachment">
                            Simpan
                        </span>
                        
                        <span wire:loading wire:target="editAttachmentFile, editAttachment">
                            <i class="fas fa-spinner fa-spin me-1"></i> 
                            Menyimpan...
                        </span>
                    </button>
                </div>

            </div>
        </div>
    </div>
</form>