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
                            <label for="addDate" class="form-label">Tanggal</label>
                            <input type="date"
                                   class="form-control @error('addDate') is-invalid @enderror"
                                   id="addDate"
                                   wire:model="addDate">
                            
                            @error('addDate')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="addType" class="form-label">Jenis</label>
                            <select class="form-select @error('addType') is-invalid @enderror"
                                    id="addType"
                                    wire:model="addType">
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
                        <label for="addAmount" class="form-label">Jumlah (Rp)</label>
                        <input type="number"
                               class="form-control @error('addAmount') is-invalid @enderror"
                               id="addAmount"
                               wire:model="addAmount"
                               placeholder="Contoh: 50000">

                        @error('addAmount')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3" wire:ignore>
                        <label for="add_description" class="form-label">Keterangan</label>
                        <input id="add_description" type="hidden" wire:model.defer="addDescription">
                        <trix-editor input="add_description"
                                     class="form-control @error('addDescription') is-invalid @enderror"
                                     style="min-height: 150px;"></trix-editor>
                    </div>

                    @error('addDescription')
                        <span class="text-danger d-block mt-n2 mb-3">{{ $message }}</span>
                    @enderror

                    {{-- 
                      Input 'add_notes' yang tersembunyi dari kode asli telah dihapus
                      karena tidak terlihat terhubung ke input yang terlihat (berbeda dengan 'add_description'
                      yang terhubung ke Trix) dan tampaknya tidak digunakan. 
                      Ini membuat kode lebih bersih dan mudah dipelihara.
                    --}}

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>

            </div>
        </div>
    </div>
</form>