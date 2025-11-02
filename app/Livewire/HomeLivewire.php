<?php

namespace App\Livewire;

// [MODIFIED] Menggunakan Model Cashflow, bukan Todo
use App\Models\Cashflow;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
// [NEW] Menambahkan trait untuk Upload File (Attachment) dan Pagination
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class HomeLivewire extends Component
{
    // [NEW] Menggunakan trait yang di-import
    use WithFileUploads, WithPagination;

    public $auth;

    // [NEW] Kebutuhan Pencarian & Filter
    public $search = ''; // Untuk filter berdasarkan 'description'
    public $filterType = ''; // Untuk filter 'income' atau 'expense'
    public $filterDateStart;
    public $filterDateEnd;

    public function mount()
    {
        $this->auth = Auth::user();

        // [NEW] Inisialisasi filter tanggal (opsional, contoh: bulan ini)
        $this->filterDateStart = now()->startOfMonth()->format('Y-m-d');
        $this->filterDateEnd = now()->endOfMonth()->format('Y-m-d');
    }

    public function render()
    {
        // [MODIFIED] Query dasar: hanya ambil data milik user yang login
        $query = Cashflow::where('user_id', $this->auth->id);

        // [NEW] Terapkan Filter Tipe
        if ($this->filterType) {
            $query->where('type', $this->filterType);
        }

        // [NEW] Terapkan Filter Pencarian (Description)
        if ($this->search) {
            $query->where('description', 'like', '%' . $this->search . '%');
        }
        
        // [NEW] Terapkan Filter Tanggal
        if ($this->filterDateStart && $this->filterDateEnd) {
            $query->whereBetween('date', [$this->filterDateStart, $this->filterDateEnd]);
        }

        // [NEW] Kebutuhan Statistik (ApexCharts)
        // Kita hitung total *sebelum* pagination, tapi *setelah* filter
        $filteredCashflows = $query->get(); // Ambil semua data yang terfilter

        $totalIncome = $filteredCashflows->where('type', 'income')->sum('amount');
        $totalExpense = $filteredCashflows->where('type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;

        // [NEW] Kebutuhan Pagination
        // Ambil data untuk tabel, urutkan berdasarkan tanggal terbaru, dan paginasi (20 per halaman)
        $cashflows = $query->orderBy('date', 'desc')
                           ->orderBy('created_at', 'desc')
                           ->paginate(20);

        // [MODIFIED] Kirim data cashflows (hasil paginasi) dan data statistik ke view
        $data = [
            'cashflows' => $cashflows, // Data untuk tabel (sudah dipaginasi)
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'balance' => $balance,
        ];

        return view('livewire.home-livewire', $data);
    }

    // --- (1) Add Cashflow (Tambah Data) ---

    // [MODIFIED] Properti disesuaikan dengan modal 'add.blade.php'
    public $addDate;
    public $addType;
    public $addAmount;
    public $addDescription;
    public $addNotes; // Untuk Trix Editor
    public $addAttachment; // Untuk Upload Gambar

    // [MODIFIED] Nama fungsi dari addTodo() menjadi addCashflow()
    public function addCashflow()
    {
        // [MODIFIED] Validasi disesuaikan dengan migrasi
        $this->validate([
            'addDate' => 'required|date',
            'addType' => 'required|in:income,expense',
            'addAmount' => 'required|numeric|min:0',
            'addDescription' => 'required|string|max:255',
            'addNotes' => 'nullable|string', // Kebutuhan Trix Editor
            'addAttachment' => 'nullable|image|max:2048', // Kebutuhan Olah Gambar (2MB Max)
        ]);

        $path = null;
        // [NEW] Kebutuhan Olah Gambar: Logika untuk menyimpan file
        if ($this->addAttachment) {
            $userId = $this->auth->id;
            $dateNumber = now()->format('YmdHis');
            $extension = $this->addAttachment->getClientOriginalExtension();
            $filename = $userId . '_' . $dateNumber . '.' . $extension;
            // Simpan di 'storage/app/public/attachments'
            $path = $this->addAttachment->storeAs('attachments', $filename, 'public');
        }

        // [MODIFIED] Simpan cashflow ke database
        Cashflow::create([
            'user_id' => $this->auth->id,
            'date' => $this->addDate,
            'type' => $this->addType,
            'amount' => $this->addAmount,
            'description' => $this->addDescription,
            'notes' => $this->addNotes,
            'attachment' => $path, // Simpan path gambar (atau null)
        ]);

        // [MODIFIED] Reset semua properti form 'add'
        $this->reset(['addDate', 'addType', 'addAmount', 'addDescription', 'addNotes', 'addAttachment']);

        // [MODIFIED] Tutup modal yang sesuai
        $this->dispatch('closeModal', id: 'addCashflowModal');

        // [NEW] Kebutuhan SweetAlert: Kirim notifikasi sukses
        $this->dispatch('showSweetAlert', ['icon' => 'success', 'title' => 'Berhasil', 'text' => 'Catatan cashflow berhasil ditambahkan!']);
    }

    // --- (2) Edit Cashflow (Ubah Data) ---

    // [MODIFIED] Properti disesuaikan dengan modal 'edit.blade.php'
    public $editCashflowId;
    public $editDate;
    public $editType;
    public $editAmount;
    public $editDescription;
    public $editNotes; // Untuk Trix Editor

    // [MODIFIED] Nama fungsi dari prepareEditTodo()
    public function prepareEditCashflow($id)
    {
        // [MODIFIED] Ambil data cashflow
        // [SECURITY FIX] Pastikan data yang diambil adalah milik user yang login
        $cashflow = Cashflow::where('id', $id)->where('user_id', $this->auth->id)->first();
        if (!$cashflow) {
            return; // Gagal (data tidak ditemukan atau bukan milik user)
        }

        // [MODIFIED] Isi semua properti 'edit'
        $this->editCashflowId = $cashflow->id;
        $this->editDate = $cashflow->date; // Tidak perlu format karena sudah otomatis dihandle oleh date cast
        $this->editType = $cashflow->type;
        $this->editAmount = $cashflow->amount;
        $this->editDescription = $cashflow->description;
        $this->editNotes = $cashflow->notes;

        // [NEW] Kebutuhan Trix Editor: Kirim event untuk mengisi Trix Editor (karena di-wire:ignore)
        $this->dispatch('setTrixContent', ['id' => 'edit_notes', 'content' => $cashflow->notes]);

        // [MODIFIED] Tampilkan modal 'edit'
        $this->dispatch('showModal', id: 'editCashflowModal');
    }

    // [MODIFIED] Nama fungsi dari editTodo()
    public function editCashflow()
    {
        // [MODIFIED] Validasi data 'edit'
        $this->validate([
            'editDate' => 'required|date',
            'editType' => 'required|in:income,expense',
            'editAmount' => 'required|numeric|min:0',
            'editDescription' => 'required|string|max:255',
            'editNotes' => 'nullable|string',
        ]);

        // [SECURITY FIX] Ambil data dan pastikan milik user yang login
        $cashflow = Cashflow::where('id', $this->editCashflowId)
                            ->where('user_id', $this->auth->id)
                            ->first();
        if (!$cashflow) {
            $this->dispatch('showSweetAlert', ['icon' => 'error', 'title' => 'Gagal', 'text' => 'Data tidak ditemukan!']);
            return;
        }

        // [MODIFIED] Update data
        $cashflow->date = $this->editDate;
        $cashflow->type = $this->editType;
        $cashflow->amount = $this->editAmount;
        $cashflow->description = $this->editDescription;
        $cashflow->notes = $this->editNotes;
        $cashflow->save();

        // [MODIFIED] Reset properti 'edit'
        $this->reset(['editCashflowId', 'editDate', 'editType', 'editAmount', 'editDescription', 'editNotes']);
        
        // [MODIFIED] Tutup modal 'edit'
        $this->dispatch('closeModal', id: 'editCashflowModal');
        
        // [NEW] Kebutuhan SweetAlert: Kirim notifikasi sukses
        $this->dispatch('showSweetAlert', ['icon' => 'success', 'title' => 'Berhasil', 'text' => 'Catatan cashflow berhasil diperbarui!']);
    }

    // --- (3) Delete Cashflow (Hapus Data) ---

    // [MODIFIED] Properti disesuaikan dengan modal 'delete.blade.php'
    public $deleteCashflowId;
    public $deleteCashflowDescription; // Untuk ditampilkan di modal
    public $deleteCashflowAmount; // Untuk ditampilkan di modal
    public $deleteConfirmText; // Untuk konfirmasi 'HAPUS'

    // [MODIFIED] Nama fungsi dari prepareDeleteTodo()
    public function prepareDeleteCashflow($id)
    {
        // [SECURITY FIX] Pastikan data milik user yang login
        $cashflow = Cashflow::where('id', $id)->where('user_id', $this->auth->id)->first();
        if (!$cashflow) {
            return;
        }

        // [MODIFIED] Isi properti 'delete'
        $this->deleteCashflowId = $cashflow->id;
        $this->deleteCashflowDescription = $cashflow->description;
        $this->deleteCashflowAmount = $cashflow->amount;
        $this->deleteConfirmText = ''; // Kosongkan input konfirmasi

        // [MODIFIED] Tampilkan modal 'delete'
        $this->dispatch('showModal', id: 'deleteCashflowModal');
    }

    // [MODIFIED] Nama fungsi dari deleteTodo()
    public function deleteCashflow(){
    // Cek konfirmasi, buat jadi case-insensitive (memperbaiki UX)
    if (strtolower($this->deleteConfirmText) !== 'hapus') {
        $this->addError('deleteConfirmText', 'Ketik "HAPUS" untuk mengonfirmasi penghapusan.');
        return;
    }

        // [SECURITY FIX] Ambil data dan pastikan milik user
        $cashflow = Cashflow::where('id', $this->deleteCashflowId)
                            ->where('user_id', $this->auth->id)
                            ->first();

        if (!$cashflow) {
            $this->dispatch('showSweetAlert', ['icon' => 'error', 'title' => 'Gagal', 'text' => 'Data tidak ditemukan!']);
            return;
        }

        // [NEW] Kebutuhan Olah Gambar: Hapus file attachment (jika ada) sebelum hapus data
        if ($cashflow->attachment && Storage::disk('public')->exists($cashflow->attachment)) {
            Storage::disk('public')->delete($cashflow->attachment);
        }

        // [MODIFIED] Hapus data dari database
        $cashflow->delete();

        // [MODIFIED] Reset properti 'delete'
        $this->reset(['deleteCashflowId', 'deleteCashflowDescription', 'deleteCashflowAmount', 'deleteConfirmText']);
        
        // [MODIFIED] Tutup modal 'delete'
        $this->dispatch('closeModal', id: 'deleteCashflowModal');
        
        // [NEW] Kebutuhan SweetAlert: Kirim notifikasi sukses
        $this->dispatch('showSweetAlert', ['icon' => 'success', 'title' => 'Berhasil', 'text' => 'Catatan cashflow telah dihapus!']);
    }
}