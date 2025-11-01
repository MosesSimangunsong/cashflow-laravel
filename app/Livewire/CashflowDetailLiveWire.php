<?php

namespace App\Livewire;

// [MODIFIED] Menggunakan Model Cashflow, bukan Todo
use App\Models\Cashflow;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

// [MODIFIED] Mengganti nama class dari TodoDetailLivewire menjadi CashflowDetailLivewire
class CashflowDetailLivewire extends Component
{
    use WithFileUploads;

    // [MODIFIED] Mengganti $todo menjadi $cashflow
    public $cashflow;
    public $auth;

    public function mount()
    {
        $this->auth = Auth::user();

        // [MODIFIED] Mengambil 'cashflow_id' dari rute.
        // Pastikan Anda menggunakan nama parameter ini di file routes/web.php Anda.
        $cashflow_id = request()->route('cashflow_id'); // <-- Ganti 'todo_id'

        // [MODIFIED] Mencari Cashflow, bukan Todo
        // [SECURITY FIX] Menambahkan 'where('user_id', ...)' 
        // agar user tidak bisa melihat/mengedit data milik user lain.
        $targetCashflow = Cashflow::where('id', $cashflow_id)
                                  ->where('user_id', $this->auth->id) 
                                  ->first();

        // Jika tidak ditemukan (atau bukan milik user ini), redirect ke home
        if (!$targetCashflow) {
            // Anda mungkin ingin ganti 'app.home' jika nama rute Anda berbeda
            return redirect()->route('app.home');
        }

        // [MODIFIED] Assign ke properti $cashflow
        $this->cashflow = $targetCashflow;
    }

    public function render()
    {
        // [MODIFIED] Mengarahkan ke view 'cashflow-detail-livewire'
        // Anda perlu membuat file view: 
        // resources/views/livewire/cashflow-detail-livewire.blade.php
        return view('livewire.cashflow-detail-livewire');
    }

    // [MODIFIED] Mengganti "Cover" (Todo) menjadi "Attachment" (Cashflow)
    // Properti ini untuk menampung file yang di-upload
    public $editAttachmentFile;

    // [MODIFIED] Mengganti nama fungsi editCoverTodo menjadi editAttachment
    public function editAttachment()
    {
        $this->validate([
            // [MODIFIED] Mengganti nama properti divalidasi
            'editAttachmentFile' => 'required|image|max:2048', // 2MB Max
        ]);

        if ($this->editAttachmentFile) {
            // [MODIFIED] Cek dan hapus attachment lama (jika ada)
            if ($this->cashflow->attachment && Storage::disk('public')->exists($this->cashflow->attachment)) {
                Storage::disk('public')->delete($this->cashflow->attachment);
            }

            $userId = $this->auth->id;
            $dateNumber = now()->format('YmdHis');
            $extension = $this->editAttachmentFile->getClientOriginalExtension();
            $filename = $userId . '_' . $dateNumber . '.' . $extension;

            // [MODIFIED] Menyimpan di folder 'attachments', bukan 'covers'
            $path = $this->editAttachmentFile->storeAs('attachments', $filename, 'public');
            
            // [MODIFIED] Menyimpan path ke properti 'attachment' di model
            $this->cashflow->attachment = $path;
            $this->cashflow->save();
        }

        // [MODIFIED] Reset properti file setelah selesai
        $this->reset(['editAttachmentFile']);

        // [MODIFIED] Menutup modal yang sesuai (yang akan kita modifikasi selanjutnya)
        $this->dispatch('closeModal', id: 'editAttachmentModal'); // <-- Ganti 'editCoverTodoModal'
        
        // [Kebutuhan SweetAlert] Kirim event untuk notifikasi sukses
        $this->dispatch('showSweetAlert', [
            'icon' => 'success', 
            'title' => 'Berhasil', 
            'text' => 'Bukti (Attachment) berhasil diperbarui!'
        ]);
    }
}