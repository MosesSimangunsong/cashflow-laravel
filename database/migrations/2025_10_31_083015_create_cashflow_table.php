<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Membuat tabel 'cashflows'
        Schema::create('cashflows', function (Blueprint $table) {
            $table->id();
            
            // Kebutuhan Autentikasi: Relasi ke user
            $table->bigInteger('user_id')->unsigned(); 

            // Kolom inti untuk cashflow
            $table->date('date'); // Tanggal pencatatan (Untuk Statistik & Filter)
            $table->enum('type', ['income', 'expense']); // Jenis: Pemasukan atau Pengeluaran (Untuk Statistik & Filter)
            $table->decimal('amount', 15, 2); // Jumlah nominal (Untuk Statistik)

            // Modifikasi dari 'title' -> 'description'
            $table->string('description'); // Keterangan singkat (Untuk Pencarian)

            // Kebutuhan Catatan (Trix Editor): Modifikasi dari 'description' -> 'notes'
            $table->text('notes')->nullable(); // Catatan detail menggunakan Trix Editor

            // Kebutuhan Olah Gambar: Modifikasi dari 'cover' -> 'attachment'
            $table->string('attachment')->nullable(); // Path untuk bukti/gambar
            
            $table->timestamps(); // created_at dan updated_at

            // Kebutuhan Autentikasi: Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Menghapus tabel 'cashflows'
        Schema::dropIfExists('cashflows');
    }
};