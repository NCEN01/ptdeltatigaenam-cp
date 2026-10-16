<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * midtrans_order_id dijadikan unik.
 *
 * Midtrans mengirim ulang webhook-nya ketika balasan pertama tidak diterima
 * atau dianggap gagal, dan kiriman ulang itu bisa tiba bersamaan dengan yang
 * pertama. MidtransWebhookController memakai Transaction::updateOrCreate yang
 * berkunci pada kolom ini — dan updateOrCreate bukan operasi atomik: ia
 * melakukan SELECT lebih dulu, lalu INSERT bila tidak ketemu.
 *
 * Dua permintaan yang berjalan berbarengan sama-sama mendapat "tidak ketemu"
 * pada SELECT-nya, lalu keduanya INSERT. Tanpa indeks unik, basis data
 * menerima keduanya dan satu pembayaran tercatat dua kali. Dengan indeks ini,
 * yang kalah ditolak basis data — yang tepat, karena memang hanya boleh ada
 * satu baris per pesanan Midtrans.
 *
 * Indeks biasa yang lama dilepas: indeks unik melayani pencarian yang sama,
 * jadi menyimpan keduanya hanya menambah pekerjaan tulis tanpa manfaat.
 *
 * Diperiksa sebelum dijalankan: tabelnya masih kosong dan tidak ada nilai
 * ganda maupun null, jadi migrasi ini tidak bisa gagal karena data lama.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('transactions_midtrans_order_idx');
            $table->unique('midtrans_order_id', 'transactions_midtrans_order_unique');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropUnique('transactions_midtrans_order_unique');
            $table->index('midtrans_order_id', 'transactions_midtrans_order_idx');
        });
    }
};
