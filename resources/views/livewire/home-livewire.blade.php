<div class="mt-3">
    <div class="card">
        <div class="card-header d-flex">
            <div class="flex-fill">
                <h3>Hay, {{ $auth->name }}</h3>
                <p class="mb-0">Selamat datang di dasbor cashflow Anda.</p>
            </div>
            <div>
                <a href="{{ route('auth.logout') }}" class="btn btn-warning">Keluar</a>
            </div>
        </div>
    </div>

    <!-- [NEW] Kebutuhan Lanjutan: Bagian Statistik & Chart -->
    <div class="card mt-3">
        <div class="card-header">
            <h5 class="mb-0">Statistik Ringkas</h5>
            <small>Berdasarkan filter tanggal yang Anda pilih.</small>
        </div>
        <div class="card-body">
            <!-- Menampilkan Total dari HomeLivewire.php -->
            <div class="row text-center mb-3">
                <div class="col-md-4">
                    <div class="card bg-success-subtle border-success">
                        <div class="card-body">
                            <h6 class="text-success">Total Pemasukan</h6>
                            <h4 class="text-success mb-0">Rp {{ number_format($totalIncome, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-danger-subtle border-danger">
                        <div class="card-body">
                            <h6 class="text-danger">Total Pengeluaran</h6>
                            <h4 class="text-danger mb-0">Rp {{ number_format($totalExpense, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-primary-subtle border-primary">
                        <div class="card-body">
                            <h6 class="text-primary">Saldo Akhir</h6>
                            <h4 class="text-primary mb-0">Rp {{ number_format($balance, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- [NEW] Kebutuhan Lanjutan: Placeholder untuk ApexCharts -->
            <!-- Anda perlu inisialisasi chart ini di file JavaScript/layout Anda -->
            <div id="cashflowChart" style="min-height: 350px;">
                <!-- Indikator loading selagi data chart diambil oleh Livewire -->
                <div wire:loading.flex class="justify-content-center align-items-center h-100">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading chart...</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- [MODIFIED] Card untuk Tabel Data -->
    <div class="card mt-3">
        <div class="card-body">
            <div class="d-flex mb-3">
                <!-- [MODIFIED] Mengganti judul "Daftar Todo" -->
                <div class="flex-fill">
                    <h3>Riwayat Cashflow</h3>
                </div>
                <div>
                    <!-- [MODIFIED] Mengganti modal target ke 'addCashflowModal' -->
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCashflowModal">
                        <i class="fas fa-plus"></i> Tambah Catatan
                    </button>
                </div>
            </div>

            <!-- [NEW] Kebutuhan Lanjutan: Bagian Filter & Pencarian -->
            <div class="card bg-light mb-3">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <label class="form-label">Cari Keterangan</label>
                            <!-- wire:model.live.debounce.300ms akan otomatis mencari 300ms setelah user berhenti mengetik -->
                            <input type="text" class="form-control" placeholder="Cari..." 
                                   wire:model.live.debounce.300ms="search">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Jenis</label>
                            <select class="form-select" wire:model.live="filterType">
                                <option value="">Semua Jenis</option>
                                <option value="income">Pemasukan</option>
                                <option value="expense">Pengeluaran</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Dari Tanggal</label>
                            <input type="date" class="form-control" wire:model.live="filterDateStart">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Sampai Tanggal</label>
                            <input type="date" class="form-control" wire:model.live="filterDateEnd">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Indikator loading saat memfilter/mencari -->
            <div wire:loading.flex class="justify-content-center my-2">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            
            <!-- [MODIFIED] Tabel untuk Cashflow -->
            <div class="table-responsive" wire:loading.remove>
                <table class="table table-striped table-hover">
                    <tr class="table-light">
                        <!-- [MODIFIED] Header tabel disesuaikan -->
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Jenis</th>
                        <th>Jumlah</th>
                        <th>Keterangan</th>
                        <th>Tindakan</th>
                    </tr>
                    
                    <!-- [MODIFIED] Looping $cashflows (bukan $todos) -->
                    <!-- Menggunakan @forelse untuk menangani data kosong -->
                    @forelse ($cashflows as $key => $cashflow)
                        <tr>
                            <!-- [NEW] Nomor urut yang benar untuk pagination -->
                            <td>{{ $cashflows->firstItem() + $key }}</td>
                            
                            <!-- [NEW] Menampilkan tanggal (sudah di-cast sebagai objek Date di Model) -->
                            <td>{{ $cashflow->date->format('d M Y') }}</td>
                            
                            <!-- [NEW] Menampilkan Jenis (Income/Expense) dengan badge -->
                            <td>
                                @if ($cashflow->type == 'income')
                                    <span class="badge bg-success-subtle text-success-emphasis rounded-pill">Pemasukan</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger-emphasis rounded-pill">Pengeluaran</span>
                                @endif
                            </td>
                            
                            <!-- [NEW] Menampilkan Jumlah (Amount) dengan format Rupiah dan warna -->
                            <td class="{{ $cashflow->type == 'income' ? 'text-success' : 'text-danger' }} fw-bold">
                                {{ $cashflow->type == 'income' ? '+' : '-' }}
                                Rp {{ number_format($cashflow->amount, 0, ',', '.') }}
                            </td>
                            
                            <!-- [MODIFIED] Menampilkan 'description', bukan 'title' -->
                            <td>{{ Str::limit($cashflow->description, 35) }}</td>
                            
                            <!-- [MODIFIED] Kolom Tindakan (Button) -->
                            <td>
                                <!-- [MODIFIED] Rute detail diubah ke 'cashflows' -->
                                <a href="{{ route('app.cashflows.detail', ['cashflow_id' => $cashflow->id]) }}"
                                    class="btn btn-sm btn-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <!-- [MODIFIED] Fungsi 'wire:click' diubah ke 'prepareEditCashflow' -->
                                <button wire:click="prepareEditCashflow({{ $cashflow->id }})" 
                                        class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <!-- [MODIFIED] Fungsi 'wire:click' diubah ke 'prepareDeleteCashflow' -->
                                <button wire:click="prepareDeleteCashflow({{ $cashflow->id }})" 
                                        class="btn btn-sm btn-danger" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <!-- [MODIFIED] Pesan untuk data kosong -->
                        <tr>
                            <td colspan="6" class="text-center">
                                <div class="my-3">
                                    <i class="fas fa-folder-open fa-3x text-muted"></i>
                                    <p class="mt-2 text-muted">Belum ada data cashflow yang tersedia.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </table>
            </div>

            <!-- [NEW] Kebutuhan Lanjutan: Link Pagination -->
            <div class="mt-3">
                {{ $cashflows->links() }}
            </div>
            
        </div>
    </div>

    {{-- [MODIFIED] Ganti path include modal dari 'todos' ke 'flowcharts' --}}
    @include('components.modals.flowcharts.add')
    @include('components.modals.flowcharts.edit')
    @include('components.modals.flowcharts.delete')
</div>