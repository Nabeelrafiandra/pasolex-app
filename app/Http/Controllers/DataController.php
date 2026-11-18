<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DataController extends Controller
{
    // Kolom yang harus berupa angka di JSON
    private const INT_KEYS = ['id', 'p', 'l', 'q', 'k', 'f', 'sis', 'fis', 'sel'];

    /**
     * Mengirim seluruh data yang dibutuhkan dashboard dalam satu permintaan.
     * Nama kunci dipendekkan agar cocok dengan kode tampilan (JavaScript).
     */
    public function index(): JsonResponse
    {
        $data = [
            'warna' => DB::table('warna')->orderBy('id_warna')->get()
                ->map(fn ($r) => ['id' => (int) $r->id_warna, 'n' => $r->nama_warna])->all(),

            'ukuran' => DB::table('ukuran')->orderBy('id_ukuran')->get()
                ->map(fn ($r) => ['id' => (int) $r->id_ukuran, 'n' => $r->nama_ukuran])->all(),

            'kategori' => DB::table('kategori_rusak')->orderBy('id_kategori_rusak')->get()
                ->map(fn ($r) => ['id' => (int) $r->id_kategori_rusak, 'n' => $r->jenis_kerusakan])->all(),

            'tindak' => DB::table('tindak_lanjut')->orderBy('id_tindak_lanjut')->get()
                ->map(fn ($r) => ['id' => (int) $r->id_tindak_lanjut, 'n' => $r->nama_tindakan])->all(),

            'produk' => DB::table('produk')->orderBy('id_produk')->get()
                ->map(fn ($r) => [
                    'id' => (int) $r->id_produk, 'sku' => $r->sku, 'nama' => $r->nama_model,
                    'w' => (int) $r->id_warna, 'u' => (int) $r->id_ukuran, 'desk' => $r->deskripsi,
                ])->all(),

            'lok' => DB::table('lokasi_rak')->orderBy('id_lokasi')->get()
                ->map(fn ($r) => [
                    'id' => (int) $r->id_lokasi, 'rak' => $r->rak, 'tg' => $r->tingkat_rak,
                    'sd' => $r->sisi_rak, 'kode' => $r->kode_rak, 'kap' => (int) $r->kapasitas,
                ])->all(),

            'stok' => DB::table('stok_lokasi')
                ->select('id_produk as p', 'id_lokasi as l', 'qty as q')->get()
                ->map(fn ($r) => $this->baris($r))->all(),

            'inbound' => $this->mutasi('inbound', 'id_inbound'),
            'outbound' => $this->mutasi('outbound', 'id_outbound'),
            'rusak' => $this->mutasi('barang_rusak', 'id_rusak', [
                'barang_rusak.id_kategori_rusak as k',
                'barang_rusak.id_tindak_lanjut as f',
                'barang_rusak.keterangan as ket',
            ]),

            'adj' => DB::table('stock_opname')
                ->join('staff_gudang as s', 's.id_staff_gudang', '=', 'stock_opname.id_staff_gudang')
                ->select(
                    'stock_opname.id_opname as id', 'stock_opname.id_produk as p', 'stock_opname.id_lokasi as l',
                    'stock_opname.stok_sistem as sis', 'stock_opname.stok_fisik as fis', 'stock_opname.selisih as sel',
                    'stock_opname.alasan', 'stock_opname.tanggal as t', 's.nama_lengkap as petugas'
                )
                ->orderBy('stock_opname.tanggal')->orderBy('stock_opname.id_opname')->get()
                ->map(fn ($r) => $this->baris($r))->all(),

            // Daftar akun (tanpa password)
            'u' => [
                'pemilik' => $this->akun('pemilik', 'id_pemilik'),
                'admin' => $this->akun('admin', 'id_admin'),
                'gudang' => $this->akun('staff_gudang', 'id_staff_gudang'),
            ],
        ];

        return response()->json($data);
    }

    private function mutasi(string $tabel, string $pk, array $tambahan = []): array
    {
        $kolom = [
            "$tabel.$pk as id", "$tabel.id_produk as p", "$tabel.id_lokasi as l",
            "$tabel.qty as q", "$tabel.tanggal as t", 's.nama_lengkap as petugas',
        ];

        return DB::table($tabel)
            ->join('staff_gudang as s', 's.id_staff_gudang', '=', "$tabel.id_staff_gudang")
            ->select(array_merge($kolom, $tambahan))
            ->orderBy("$tabel.tanggal")->orderBy("$tabel.$pk")
            ->get()
            ->map(fn ($r) => $this->baris($r))
            ->all();
    }

    private function akun(string $tabel, string $pk): array
    {
        return DB::table($tabel)->orderBy($pk)->get()
            ->map(fn ($r) => ['id' => (int) $r->$pk, 'username' => $r->username, 'nama' => $r->nama_lengkap])
            ->all();
    }

    private function baris($r): array
    {
        $a = (array) $r;
        foreach (self::INT_KEYS as $k) {
            if (isset($a[$k])) {
                $a[$k] = (int) $a[$k];
            }
        }
        if (array_key_exists('petugas', $a)) {
            $a['by'] = $a['petugas'];
            unset($a['petugas']);
        }

        return $a;
    }
}
