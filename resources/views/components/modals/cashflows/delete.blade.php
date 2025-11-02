<form wire:submit.prevent="deleteCashflow">
    <div class="modal fade" 
         tabindex="-1" 
         id="deleteCashflowModal" 
         wire:ignore.self
         x-data
    x-on:hidden.bs.modal="$wire.reset(['deleteId', 'deleteCashflowDescription', 'deleteCashflowAmount', 'deleteConfirmText'])">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hapus Catatan Cashflow</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        Apakah Anda yakin ingin menghapus catatan cashflow ini?
                        
                        <ul class="my-2">
                            <li>
                                <strong>Keterangan:</strong> 
                                {{ $deleteCashflowDescription ?? 'Catatan tidak ditemukan' }}
                            </li>
                            <li>
                                <strong>Jumlah:</strong> 
                                Rp {{ number_format($deleteCashflowAmount ?? 0, 0, ',', '.') }}
                            </li>
                        </ul>
                        Tindakan ini tidak dapat dibatalkan.
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Untuk konfirmasi, ketik <strong>HAPUS</strong> di bawah ini:</label>
                        
                                <input 
                            type="text" 
                            class="form-control @error('deleteConfirmText') is-invalid @enderror" 
                            wire:model.live="deleteConfirmText" 
                            placeholder="HAPUS"
                            autocomplete="off"
                        >                        @error('deleteConfirmText')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" 
                            data-bs-dismiss="modal"
                            wire:loading.attr="disabled"
                            wire:target="deleteCashflow">Batal</button>
                    <button type="submit" 
                            class="btn btn-danger" 
                            wire:loading.attr="disabled"
                            wire:target="deleteCashflow">
                        <span wire:loading.remove wire:target="deleteCashflow">
                            Lanjutkan, Hapus
                        </span>
                        <span wire:loading wire:target="deleteCashflow">
                            <i class="fas fa-spinner fa-spin me-1"></i>
                            Menghapus...
                        </span>
                    </button>
                </div>
                
                <div wire:loading wire:target="deleteCashflow" class="position-absolute w-100 h-100 top-0 left-0 bg-white bg-opacity-75 d-flex align-items-center justify-content-center">
                    <div class="text-center">
                        <div class="spinner-border text-primary mb-2" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div>Sedang menghapus data...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
 