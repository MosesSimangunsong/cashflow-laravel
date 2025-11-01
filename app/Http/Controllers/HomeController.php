<?php

namespace App\Http\Controllers;

// [TAMBAHAN] Mengimpor 'Request' tidak wajib di sini 
// karena parameter di-inject oleh rute, tapi ini adalah
// controller Anda.
class HomeController extends Controller
{
    /**
     * Menampilkan halaman dasbor utama (Home).
     * Halaman ini akan memuat komponen 'HomeLivewire'.
     */
    public function index()
    {
        return view('pages.app.home');
    }

    /**
     * [MODIFIED]
     * Menampilkan halaman detail untuk satu item cashflow.
     * Halaman ini akan memuat komponen 'CashflowDetailLivewire'.
     *
     * @param int $cashflow_id ID dari item cashflow (didapat dari rute)
     * @return \Illuminate\Contracts\View\View
     */
    // [MODIFIED] Ganti nama 'todoDetail' menjadi 'cashflowDetail'
    // [FIX] Menambahkan parameter '$cashflow_id' yang akan diterima dari rute.
    // Ini PENTING karena CashflowDetailLivewire::mount() Anda memanggil request()->route('cashflow_id').
    public function cashflowDetail($cashflow_id)
    {
        // [MODIFIED] Ganti path view 'pages.app.todos.detail'
        // Anda harus membuat file view ini: 
        // 'resources/views/pages/app/cashflow/detail.blade.php'
        return view('pages.app.cashflow.detail', [
            'cashflow_id' => $cashflow_id // Mengirim ID ke view (best practice)
        ]);
    }
}