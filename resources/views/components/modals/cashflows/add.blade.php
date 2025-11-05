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
                        <label class="form-label">Keterangan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" wire:model="addDescription" placeholder="Contoh: Beli Kopi" required>
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

                    <!-- Foto/bukti tidak diminta saat tambah; tersedia di halaman detail jika diperlukan -->

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</form>