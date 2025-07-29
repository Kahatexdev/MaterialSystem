<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class KategoriReturSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['nama_kategori' => 'TITIP BAHAN BAKU, REVISI DELIVERY', 'tipe_kategori' => 'BAHAN BAKU TITIP', 'role' => 'admin'],
            ['nama_kategori' => 'TITIP BAHAN BAKU', 'tipe_kategori' => 'BAHAN BAKU TITIP', 'role' => 'admin'],
            ['nama_kategori' => 'BELUM TAMBAHAN PK', 'tipe_kategori' => 'BAHAN BAKU TITIP', 'role' => 'admin'],
            ['nama_kategori' => 'KELOS ULANG', 'tipe_kategori' => 'PENGEMBALIAN', 'role' => 'admin'],
            ['nama_kategori' => 'LILITAN SPANDEX RUSAK', 'tipe_kategori' => 'PENGEMBALIAN', 'role' => 'admin'],
            ['nama_kategori' => 'REVISI KODE', 'tipe_kategori' => 'PENGEMBALIAN', 'role' => 'admin'],
            ['nama_kategori' => 'SPANDEX CARANG', 'tipe_kategori' => 'PENGEMBALIAN', 'role' => 'admin'],
            ['nama_kategori' => 'SPANDEX TIMBUL', 'tipe_kategori' => 'PENGEMBALIAN', 'role' => 'admin'],
            ['nama_kategori' => 'SPANDEX TIPIS', 'tipe_kategori' => 'PENGEMBALIAN', 'role' => 'admin'],
            ['nama_kategori' => 'KARET KERITING', 'tipe_kategori' => 'PENGEMBALIAN', 'role' => 'admin'],
            ['nama_kategori' => 'BENANG PUTUSAN DI MC', 'tipe_kategori' => 'PENGEMBALIAN', 'role' => 'admin'],
            ['nama_kategori' => 'BENANG SALAH UKURAN', 'tipe_kategori' => 'PENGEMBALIAN', 'role' => 'admin'],
            ['nama_kategori' => 'BENANG BELANG', 'tipe_kategori' => 'PENGEMBALIAN', 'role' => 'admin'],
            ['nama_kategori' => 'BENANG KASAR', 'tipe_kategori' => 'PENGEMBALIAN', 'role' => 'admin'],
            ['nama_kategori' => 'BENANG REJECT', 'tipe_kategori' => 'PENGEMBALIAN', 'role' => 'admin'],
            ['nama_kategori' => 'BENANG KOTOR', 'tipe_kategori' => 'PENGEMBALIAN', 'role' => 'admin'],
            ['nama_kategori' => 'BB SISA UNLOCK', 'tipe_kategori' => 'SIMPAN ULANG', 'role' => 'admin'],
            ['nama_kategori' => 'SALAH BUKA PO', 'tipe_kategori' => 'SIMPAN ULANG', 'role' => 'admin'],
            ['nama_kategori' => 'REVISI LOT', 'tipe_kategori' => 'SIMPAN ULANG', 'role' => 'admin'],
            ['nama_kategori' => 'BENANG TIDAK LULUS QC MC', 'tipe_kategori' => 'SIMPAN ULANG', 'role' => 'admin'],
            ['nama_kategori' => 'REVISI JENIS', 'tipe_kategori' => 'SIMPAN ULANG', 'role' => 'admin'],
            ['nama_kategori' => 'ORDER PINDAH AREA', 'tipe_kategori' => 'SIMPAN ULANG', 'role' => 'admin'],
            ['nama_kategori' => 'KIRIM LEBIH GBN SEBELUM EXPORT', 'tipe_kategori' => 'SIMPAN ULANG', 'role' => 'admin'],
            ['nama_kategori' => 'BENANG REJECT STOCK', 'tipe_kategori' => 'SIMPAN ULANG', 'role' => 'admin'],
            ['nama_kategori' => 'CANCEL BUYER', 'tipe_kategori' => 'SIMPAN ULANG', 'role' => 'admin'],
            ['nama_kategori' => 'BB SISA EXPORT', 'tipe_kategori' => 'SIMPAN ULANG', 'role' => 'admin'],
            ['nama_kategori' => 'REVISI BON', 'tipe_kategori' => 'PENGEMBALIAN', 'role' => 'admin'],
        ];

        $this->db->table('kategori_retur')->insertBatch($data);
    }
}
