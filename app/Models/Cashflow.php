<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cashflow extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model.
     *
     * @var string
     */
    // [FIX] Memperbaiki typo 'casflows' menjadi 'cashflows'
    protected $table = 'cashflows';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     * Disesuaikan dengan migrasi 'create_cashflows_table'.
     *
     * @var array<int, string>
     */
    // [MODIFIED] Mengganti $fillable lama agar sesuai migrasi
    protected $fillable = [
        'user_id',
        'date',
        'type',
        'amount',
        'description', // Keterangan singkat
        'notes',       // Catatan detail (untuk Trix Editor)
        'attachment',  // Bukti gambar (untuk Olah Gambar)
    ];

    /**
     * Atribut yang harus di-cast ke tipe data aslinya.
     * Ini penting untuk 'amount' (angka) dan 'date' (tanggal).
     *
     * @var array<string, string>
     */
    // [NEW] Menambahkan casts untuk mempermudah manipulasi data
    protected $casts = [
        'date' => 'date',        // Otomatis mengubah string jadi objek Carbon Date
        'amount' => 'decimal:2', // Memastikan 'amount' adalah angka desimal
    ];

    // [REMOVED] Menghapus 'public $timestamps = true;' karena ini sudah default.

    /**
     * [Kebutuhan Autentikasi]
     * Mendefinisikan relasi bahwa setiap data Cashflow "milik" (BelongsTo) satu User.
     */
    public function user(): BelongsTo
    {
        // Ini menghubungkan kolom 'user_id' di tabel 'cashflows'
        // dengan 'id' di tabel 'users'.
        return $this->belongsTo(User::class);
    }
}