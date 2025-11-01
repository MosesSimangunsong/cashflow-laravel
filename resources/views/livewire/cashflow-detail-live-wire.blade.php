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
            <h5>Bukti (Attachment)</h5>
            @if ($cashflow->attachment)
                <!-- Tampilkan gambar jika ada (klik untuk memperbesar) -->
                <a href="{{ asset('storage/' . $cashflow->attachment) }}" target="_blank" rel="noopener noreferrer" 
                   title="Lihat gambar penuh">
                    <img src="{{ asset('storage/' . $cashflow->attachment) }}" alt="Bukti Cashflow" 
                         class="img-fluid rounded border mb-3" 
                         style="max-height: 400px; max-width: 100%; object-fit: cover;">
                </a>
            @else
                <!-- Tampilkan placeholder jika tidak ada attachment -->
                <div class="text-center text-muted border rounded p-4 mb-3">
                    <i class="fas fa-image fa-3x"></i>
                    <p class="mt-2 mb-0">Tidak ada bukti (attachment) yang diunggah.</p>
                </div>
            @endif
            
            <hr>

            <!-- [MODIFIED] Menampilkan Catatan (Notes) dari Trix Editor -->
            <h5>Catatan Tambahan:</h5>
            @if ($cashflow->notes)
                <!-- [PENTING] Render HTML dari Trix Editor menggunakan {!! ... !!} -->
                <!-- Kita beri class 'trix-content' agar style-nya sama (jika Anda load CSS Trix) -->
                <div class="trix-content">
                    {!! $cashflow->notes !!}
                </div>
            @else
                <p class="text-muted"><em>Tidak ada catatan tambahan.</em></p>
            @endif
        </div>
    </div>

    {{-- [MODIFIED] Ganti path modal dari 'todos' ke 'flowcharts' --}}
    @include('components.modals.flowcharts.edit-cover')
</div>