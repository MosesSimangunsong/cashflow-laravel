<form wire:submit.prevent="deleteCashflow">
    <div class="modal fade" tabindex="-1" id="deleteCashflowModal" wire:ignore.self>
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
                                    wire:model="deleteConfirmText" 
                                    placeholder="HAPUS"
                                >
                        
                        @error('deleteConfirmText')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Lanjutkan, Hapus</button>
                </div>
            </div>
        </div>
    </div>
</form>