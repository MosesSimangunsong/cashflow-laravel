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
                            <label class="form-label">Tanggal</label>
                            <input type="date" class="form-control" wire:model="editDate">
                            @error('editDate')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis</label>
                            <select class="form-select" wire:model="editType">
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
                        <label class="form-label">Jumlah (Rp)</label>
                        <input type="number" class="form-control" wire:model="editAmount" placeholder="Contoh: 50000">
                        @error('editAmount')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea class="form-control" rows="3" wire:model.defer="editDescription"></textarea>
                        @error('editDescription')
                    s     <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div> --}}
                    
                    <input id="edit_description" type="hidden" wire:model.defer="editDescription">
                    <div class="mb-3" wire:ignore>
                        <label class="form-label">Keterangan</label>
                        <trix-editor input="edit_description" class="form-control" style="min-height: 150px;"></trix-editor>
                        @error('editDescription')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    
                    

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</form>
