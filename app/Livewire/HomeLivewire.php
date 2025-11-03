<?php

namespace App\Livewire;

use App\Models\Cashflow;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class HomeLivewire extends Component
{
    // [NEW] Menggunakan trait yang di-import
    use WithFileUploads, WithPagination;

    public function getMonthlyCashflowStats()
    {
        $startDate = now()->subMonths(11)->startOfMonth();
        $endDate = now()->endOfMonth();

        $monthlyStats = Cashflow::where('user_id', $this->auth->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('EXTRACT(YEAR FROM date) as year, EXTRACT(MONTH FROM date) as month, type, SUM(amount) as total')
            ->groupBy('year', 'month', 'type')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $monthlyData = [];
        $currentDate = $startDate->copy();

        $cumulativeBalance = 0;
        while ($currentDate <= $endDate) {
            $year = $currentDate->year;
            $month = $currentDate->month;

            $income = $monthlyStats
                ->where('year', $year)
                ->where('month', $month)
                ->where('type', 'income')
                ->first();

            $expense = $monthlyStats
                ->where('year', $year)
                ->where('month', $month)
                ->where('type', 'expense')
                ->first();

            $incomeVal = $income ? $income->total : 0;
            $expenseVal = $expense ? $expense->total : 0;
            $cumulativeBalance += $incomeVal - $expenseVal;

            $monthlyData[] = [
                'month' => $currentDate->isoFormat('MMMM YYYY'),
                'income' => $incomeVal,
                'expense' => $expenseVal,
                'balance' => $cumulativeBalance
            ];

            $currentDate->addMonth();
        }

        return $monthlyData;
    }

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


    // Reset pagination to show new data
    $this->resetPage();
    $this->dispatch('closeModal', id: 'addCashflowModal');

    // [NEW] Trigger chart update
    $this->dispatch('updateChart');

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
        

    // Reset pagination to show updated data
    $this->resetPage();
    $this->dispatch('closeModal', id: 'editCashflowModal');

    // [NEW] Trigger chart update
    $this->dispatch('updateChart');

    // [NEW] Kebutuhan SweetAlert: Kirim notifikasi sukses
    $this->dispatch('showSweetAlert', ['icon' => 'success', 'title' => 'Berhasil', 'text' => 'Catatan cashflow berhasil diperbarui!']);
    }

    // --- (3) Delete Cashflow (Hapus Data) ---

    // Properties for delete operation
    public $deleteCashflowDescription;
    public $deleteCashflowAmount;
    public $deleteConfirmText = '';

    public $deleteId;

    public function prepareDeleteCashflow($id)
    {
        $this->deleteId = $id;
        $cashflow = Cashflow::find($id);
        
        if (!$cashflow || $cashflow->user_id !== $this->auth->id) {
            $this->dispatch('showSweetAlert', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Data tidak ditemukan'
            ]);
            return;
        }

        // Simpan informasi untuk ditampilkan di modal
        $this->deleteCashflowDescription = $cashflow->description;
        $this->deleteCashflowAmount = $cashflow->amount;
        
        // Reset konfirmasi
        $this->deleteConfirmText = '';
        
        // Tampilkan modal via Livewire event listener di layout (showModal)
        $this->dispatch('showModal', id: 'deleteCashflowModal');
    }

    // [MODIFIED] Nama fungsi dari deleteTodo()
    public function deleteCashflow()
    {
        if (strtolower($this->deleteConfirmText) !== 'hapus') {
            $this->dispatch('showSweetAlert', [
                'icon' => 'error', 
                'title' => 'Konfirmasi Gagal', 
                'text' => 'Ketik "HAPUS" untuk konfirmasi'
            ]);
            return;
        }

        try {
            $cashflow = Cashflow::where('id', $this->deleteId)
                               ->where('user_id', $this->auth->id)
                               ->first();

            if (!$cashflow) {
                $this->dispatch('showSweetAlert', [
                    'icon' => 'error',
                    'title' => 'Error',
                    'text' => 'Data tidak ditemukan'
                ]);
                return;
            }

            // Hapus file attachment jika ada
            if ($cashflow->attachment) {
                Storage::disk('public')->delete($cashflow->attachment);
            }

            $cashflow->delete();


            // Reset pagination to show updated data
            $this->resetPage();
            $this->dispatch('closeModal', id: 'deleteCashflowModal');

            // [NEW] Trigger chart update
            $this->dispatch('updateChart');

            // Reset form dan state
            $this->reset(['deleteId', 'deleteCashflowDescription', 'deleteCashflowAmount', 'deleteConfirmText']);

            $this->dispatch('showSweetAlert', [
                'icon' => 'success',
                'title' => 'Berhasil',
                'text' => 'Data berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('showSweetAlert', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Gagal menghapus data: ' . $e->getMessage()
            ]);
        }
    }
}