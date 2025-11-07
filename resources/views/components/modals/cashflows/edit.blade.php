<form wire:submit.prevent="editCashflow">

    <div class="modal fade" tabindex="-1" id="editCashflowModal" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Ubah Catatan Cashflow</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    
                    <input type="hidden" wire:model="editCashflowId">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editDate" class="form-label">Tanggal</label>
                            <input type="date"
                                   class="form-control @error('editDate') is-invalid @enderror"
                                   id="editDate"
                                   wire:model="editDate">
                            
                            @error('editDate')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="editType" class="form-label">Jenis</label>
                            <select class="form-select @error('editType') is-invalid @enderror"
                                    id="editType"
                                    wire:model="editType">
                                <option value="">Pilih Jenis</option>
                                <option value="income">Pemasukan (Income)</option>
                                <option value="expense">Pengeluaran (Expense)</option>
                            </select>

                            @error('editType')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="editAmount" class="form-label">Jumlah (Rp)</label>
                        <input type="number"
                               class="form-control @error('editAmount') is-invalid @enderror"
                               id="editAmount"
                               wire:model="editAmount"
                               placeholder="Contoh: 50000">

                        @error('editAmount')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <input id="edit_description" type="hidden" wire:model.defer="editDescription">

                    <div class="mb-3" wire:ignore>
                        <label for="edit_description" class="form-label">Keterangan</label>
                        <trix-editor input="edit_description"
                                     class="form-control @error('editDescription') is-invalid @enderror"
                                     style="min-height: 150px;"></trix-editor>
                    </div>

                    @error('editDescription')
                        <span class="text-danger d-block mt-n2 mb-3">{{ $message }}</span>
                    @enderror

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" 
                            wire:loading.attr="disabled" wire:target="editCashflow">
                        Batal
                    </button>
                    
                    <button type="submit" class="btn btn-primary" 
                            wire:loading.attr="disabled" wire:target="editCashflow">
                        
                        <span wire:loading.remove wire:target="editCashflow">
                            Simpan Perubahan
                        </span>
                        
                        <span wire:loading wire:target="editCashflow">
                            <i class="fas fa-spinner fa-spin me-1"></i> 
                            Menyimpan...
                        </span>
                    </button>
                </div>

            </div>
        </div>
    </div>
</form>