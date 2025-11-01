<form wire:submit.prevent="addCashflow">
    <div class="modal fade" tabindex="-1" id="addCashflowModal" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Catatan Cashflow</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" class="form-control" wire:model="addDate">
                            @error('addDate')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis</label>
                            <select class="form-select" wire:model="addType">
                                <option value="">Pilih Jenis</option>
                                <option value="income">Pemasukan (Income)</option>
                                <option value="expense">Pengeluaran (Expense)</option>
                            </select>
                            @error('addType')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jumlah (Rp)</label>
                        <input type="number" class="form-control" wire:model="addAmount" placeholder="Contoh: 50000">
                        @error('addAmount')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <input type="text" class="form-control" wire:model="addDescription" placeholder="Contoh: Beli Kopi">
                        @error('addDescription')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3" wire:ignore> <label class="form-label">Catatan (Opsional)</label>
                        <input id="add_notes" type="hidden" name="addNotes" wire:model.defer="addNotes">
                        <trix-editor input="add_notes" class="form-control" 
                            style="min-height: 150px;"></trix-editor>
                        
                        @error('addNotes')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Bukti (Opsional)</label>
                        <input type="file" class="form-control" wire:model="addAttachment">
                        <div wire:loading wire:target="addAttachment" class="text-muted mt-1">
                            Uploading...
                        </div>
                        @if ($addAttachment)
                            <img src="{{ $addAttachment->temporaryUrl() }}" class="img-fluid rounded mt-2" style="max-height: 150px;">
                        @endif
                        @error('addAttachment')
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