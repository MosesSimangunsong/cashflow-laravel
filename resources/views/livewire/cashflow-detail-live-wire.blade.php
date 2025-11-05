<div class="mt-3">
    <div class="card">
        <div class="card-header d-flex">
            <div class="flex-fill">
                <!-- Tombol Kembali -->
                <a href="{{ route('app.home') }}" class="text-decoration-none">
                    <small class="text-muted">
                        <i class="fas fa-arrow-left"></i> Kembali ke Home
                    </small>
                </a>
                
                <!-- [MODIFIED] Menampilkan Keterangan (Description) -->
                <h3 class="mt-2">
                    {{ $cashflow->description }}
                </h3>

                <!-- [NEW] Menampilkan Tanggal, Jenis, dan Jumlah -->
                <div class="d-flex align-items-center">
                    <!-- Jenis (Badge) -->
                    @if ($cashflow->type == 'income')
                        <span class="badge bg-success-subtle text-success-emphasis rounded-pill fs-6 me-2">
                            <i class="fas fa-arrow-up"></i> Pemasukan
                        </span>
                    @else
                        <span class="badge bg-danger-subtle text-danger-emphasis rounded-pill fs-6 me-2">
                            <i class="fas fa-arrow-down"></i> Pengeluaran
                        </span>
                    @endif

                    <!-- Jumlah (Amount) -->
                    <h4 class="mb-0 {{ $cashflow->type == 'income' ? 'text-success' : 'text-danger' }}">
                        Rp {{ number_format($cashflow->amount, 0, ',', '.') }}
                    </h4>
                </div>
                <!-- Tanggal -->
                <small class="text-muted">
                    Dicatat pada: {{ $cashflow->date->format('d F Y') }}
                </small>
            </div>
            <div>
                <!-- [MODIFIED] Mengganti 'Cover' -> 'Attachment' dan target modal -->
                <button class="btn btn-warning" data-bs-target="#editAttachmentModal" data-bs-toggle="modal">
                    <i class="fas fa-image"></i> Ubah Bukti
                </button>
            </div>
        </div>
        <div class="card-body">
            
            <!-- [MODIFIED] Menampilkan Bukti (Attachment) -->
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h5 class="mb-0">Bukti (Attachment)</h5>
                <div>
                    @if ($cashflow->attachment)
                        <button class="btn btn-sm btn-outline-danger" wire:click="deleteAttachment" 
                                onclick="return confirm('Apakah Anda yakin ingin menghapus bukti ini?')">
                            <i class="fas fa-trash-alt"></i> Hapus Bukti
                        </button>
                    @endif
                </div>
            </div>

            @if ($cashflow->attachment)
                <!-- Tampilkan gambar jika ada (klik untuk memperbesar) -->
                <div class="position-relative">
                    <a href="{{ asset('storage/' . $cashflow->attachment) }}" target="_blank" rel="noopener noreferrer" 
                       title="Klik untuk melihat gambar penuh">
                        <img src="{{ asset('storage/' . $cashflow->attachment) }}" alt="Bukti Cashflow" 
                             class="img-fluid rounded border" 
                             style="max-height: 400px; max-width: 100%; object-fit: contain;">
                        <div class="position-absolute top-0 end-0 p-2">
                            <span class="badge bg-dark bg-opacity-75">
                                <i class="fas fa-search-plus"></i> Klik untuk memperbesar
                            </span>
                        </div>
                    </a>
                </div>
            @else
                <!-- Tampilkan placeholder jika tidak ada attachment -->
                <div class="text-center text-muted border rounded p-4">
                    <i class="fas fa-file-image fa-3x mb-2"></i>
                    <p class="mb-1">Tidak ada bukti (attachment) yang diunggah.</p>
                    <small>Klik tombol "Ubah Bukti" untuk mengunggah bukti baru.</small>
                </div>
            @endif
            
            
        </div>
    </div>

    @include('components.modals.cashflows.edit-attachment')

</div>