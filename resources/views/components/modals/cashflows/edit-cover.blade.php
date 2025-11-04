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
                        <label class="form-label">Upload Bukti Baru</label>
                        <input type="file" class="form-control @error('editAttachmentFile') is-invalid @enderror"
                            wire:model="editAttachmentFile">

                        <div wire:loading wire:target="editAttachmentFile" class="text-muted mt-1">
                            Uploading...
                        </div>

                        @if ($editAttachmentFile)
                            <img src="{{ $editAttachmentFile->temporaryUrl() }}" class="img-fluid rounded mt-2"
                                style="max-height: 200px;">
                        @endif

                        @error('editAttachmentFile')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" @if (!$editAttachmentFile) disabled @endif
                        wire:loading.attr="disabled" wire:target="editAttachmentFile">
                        <span wire:loading.remove wire:target="editAttachmentFile">
                            Simpan
                        </span>
                        <span wire:loading wire:target="editAttachmentFile">
                            Uploading...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>