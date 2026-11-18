<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Pengelolaan data master dan akun (khusus admin).
 * Setiap fungsi memvalidasi data, lalu menyimpan ke database.
 */
class MasterController extends Controller
{
    // jenis => [tabel, kolom id, kolom nama, panjang maksimal]
    private const MASTER = [
        'warna' => ['warna', 'id_warna', 'nama_warna', 50],
        'ukuran' => ['ukuran', 'id_ukuran', 'nama_ukuran', 50],
        'kategori' => ['kategori_rusak', 'id_kategori_rusak', 'jenis_kerusakan', 100],
        'tindak' => ['tindak_lanjut', 'id_tindak_lanjut', 'nama_tindakan', 100],
    ];

    // jenis => [tabel pemakai, kolom foreign key]
    private const PEMAKAI = [
        'warna' => ['produk', 'id_warna'],
        'ukuran' => ['produk', 'id_ukuran'],
        'kategori' => ['barang_rusak', 'id_kategori_rusak'],
        'tindak' => ['barang_rusak', 'id_tindak_lanjut'],
    ];

    // peran => [tabel, kolom id]
    private const AKUN = [
        'admin' => ['admin', 'id_admin'],
        'pemilik' => ['pemilik', 'id_pemilik'],
        'gudang' => ['staff_gudang', 'id_staff_gudang'],
    ];

    private function gagal(string $pesan, int $kode = 422): JsonResponse
    {
        return response()->json(['message' => $pesan], $kode);
    }

    private function ok(): JsonResponse
    {
        return response()->json(['ok' => true]);
    }

    /** Apakah produk/rak sudah dipakai di stok (qty > 0) atau di riwayat transaksi? */
    private function dipakai(string $kolom, int $id): bool
    {
        foreach (['inbound', 'outbound', 'barang_rusak', 'stock_opname'] as $tabel) {
            if (DB::table($tabel)->where($kolom, $id)->exists()) {
                return true;
            }
        }

        return DB::table('stok_lokasi')->where($kolom, $id)->where('qty', '>', 0)->exists();
    }

    // ---------------------------------------------------------------- PRODUK

    public function saveProduk(Request $r): JsonResponse
    {
        $id = (int) $r->input('id');
        $sku = trim((string) $r->input('sku'));
        $nama = trim((string) $r->input('nama'));
        $w = (int) $r->input('w');
        $u = (int) $r->input('u');
        $desk = trim((string) $r->input('desk'));

        if ($sku === '' || $nama === '') {
            return $this->gagal('SKU dan model wajib diisi.');
        }
        if (mb_strlen($sku) > 50 || mb_strlen($nama) > 150) {
            return $this->gagal('SKU maksimal 50 karakter dan nama model maksimal 150 karakter.');
        }
        if (!DB::table('warna')->where('id_warna', $w)->exists() || !DB::table('ukuran')->where('id_ukuran', $u)->exists()) {
            return $this->gagal('Warna atau ukuran tidak valid.');
        }

        $kembar = DB::table('produk')
            ->where('id_produk', '<>', $id)
            ->where(function ($q) use ($sku, $nama, $w, $u) {
                $q->whereRaw('LOWER(sku) = ?', [mb_strtolower($sku)])
                    ->orWhere(function ($q2) use ($nama, $w, $u) {
                        $q2->whereRaw('LOWER(nama_model) = ?', [mb_strtolower($nama)])
                            ->where('id_warna', $w)
                            ->where('id_ukuran', $u);
                    });
            })->exists();
        if ($kembar) {
            return $this->gagal('SKU atau kombinasi model, warna, ukuran ini sudah ada.');
        }

        $data = [
            'sku' => $sku, 'nama_model' => $nama, 'id_warna' => $w, 'id_ukuran' => $u,
            'deskripsi' => $desk === '' ? null : $desk,
        ];

        if ($id > 0) {
            DB::table('produk')->where('id_produk', $id)->update($data);
        } else {
            DB::table('produk')->insert($data);
        }

        return $this->ok();
    }

    public function deleteProduk(string $id): JsonResponse
    {
        $id = (int) $id;
        if ($this->dipakai('id_produk', $id)) {
            return $this->gagal('Varian masih punya stok atau riwayat transaksi, jadi tidak bisa dihapus.');
        }

        DB::transaction(function () use ($id) {
            DB::table('stok_lokasi')->where('id_produk', $id)->delete(); // baris stok berisi 0
            DB::table('produk')->where('id_produk', $id)->delete();
        });

        return $this->ok();
    }

    // ----------------------------------------- WARNA, UKURAN, JENIS RUSAK, TINDAK LANJUT, RAK

    public function store(Request $r, string $type): JsonResponse
    {
        if ($type === 'lok') {
            $rak = mb_strtoupper(trim((string) $r->input('rak')));
            $tg = trim((string) $r->input('tg'));
            $sd = trim((string) $r->input('sd'));
            $kap = (int) $r->input('kap');

            if ($rak === '' || $tg === '' || $sd === '' || $kap < 1) {
                return $this->gagal('Rak, tingkat, sisi, dan kapasitas wajib diisi.');
            }
            if (mb_strlen($rak) > 10 || mb_strlen($tg) > 10 || mb_strlen($sd) > 10) {
                return $this->gagal('Rak, tingkat, dan sisi maksimal 10 karakter.');
            }

            $kode = "$rak-$tg-$sd";
            if (DB::table('lokasi_rak')->where('kode_rak', $kode)->exists()) {
                return $this->gagal("Kode rak $kode sudah dipakai.");
            }

            DB::table('lokasi_rak')->insert([
                'rak' => $rak, 'tingkat_rak' => $tg, 'sisi_rak' => $sd, 'kode_rak' => $kode, 'kapasitas' => $kap,
            ]);

            return $this->ok();
        }

        if (!isset(self::MASTER[$type])) {
            return $this->gagal('Jenis data tidak dikenal.', 404);
        }

        [$tabel, , $kolom, $maks] = self::MASTER[$type];
        $nama = trim((string) $r->input('n'));

        if ($nama === '') {
            return $this->gagal('Nama wajib diisi.');
        }
        if (mb_strlen($nama) > $maks) {
            return $this->gagal("Nama maksimal $maks karakter.");
        }
        if (DB::table($tabel)->whereRaw("LOWER($kolom) = ?", [mb_strtolower($nama)])->exists()) {
            return $this->gagal("$nama sudah ada.");
        }

        DB::table($tabel)->insert([$kolom => $nama]);

        return $this->ok();
    }

    public function destroy(string $type, string $id): JsonResponse
    {
        $id = (int) $id;

        if ($type === 'lok') {
            if ($this->dipakai('id_lokasi', $id)) {
                return $this->gagal('Rak masih berisi stok atau sudah dipakai di transaksi, jadi tidak bisa dihapus.');
            }

            DB::transaction(function () use ($id) {
                DB::table('stok_lokasi')->where('id_lokasi', $id)->delete();
                DB::table('lokasi_rak')->where('id_lokasi', $id)->delete();
            });

            return $this->ok();
        }

        if (!isset(self::MASTER[$type])) {
            return $this->gagal('Jenis data tidak dikenal.', 404);
        }

        [$tabel, $pk] = self::MASTER[$type];
        [$tabelPakai, $kolomPakai] = self::PEMAKAI[$type];

        if (DB::table($tabelPakai)->where($kolomPakai, $id)->exists()) {
            return $this->gagal('Data ini dipakai di produk atau transaksi, jadi tidak bisa dihapus.');
        }

        DB::table($tabel)->where($pk, $id)->delete();

        return $this->ok();
    }

    // ------------------------------------------------------------------ AKUN

    public function storeAkun(Request $r): JsonResponse
    {
        $role = (string) $r->input('role');
        $nama = trim((string) $r->input('nama'));
        $username = trim((string) $r->input('username'));
        $password = (string) $r->input('password');

        if (!isset(self::AKUN[$role])) {
            return $this->gagal('Peran tidak valid.');
        }
        if ($nama === '' || $username === '' || mb_strlen($password) < 6) {
            return $this->gagal('Lengkapi nama, username, dan password minimal 6 karakter.');
        }
        if (mb_strlen($nama) > 100 || mb_strlen($username) > 50) {
            return $this->gagal('Nama maksimal 100 karakter dan username maksimal 50 karakter.');
        }

        // Username harus unik di ketiga tabel akun
        foreach (self::AKUN as [$tabel]) {
            if (DB::table($tabel)->whereRaw('LOWER(username) = ?', [mb_strtolower($username)])->exists()) {
                return $this->gagal('Username sudah dipakai. Pilih username lain.');
            }
        }

        DB::table(self::AKUN[$role][0])->insert([
            'username' => $username,
            'password' => Hash::make($password),
            'nama_lengkap' => $nama,
        ]);

        return $this->ok();
    }

    public function destroyAkun(Request $r, string $role, string $id): JsonResponse
    {
        if (!isset(self::AKUN[$role])) {
            return $this->gagal('Peran tidak valid.');
        }

        [$tabel, $pk] = self::AKUN[$role];
        $id = (int) $id;

        if ($role === $r->session()->get('role') && $id === (int) $r->session()->get('uid')) {
            return $this->gagal('Akun yang sedang dipakai tidak bisa dihapus.');
        }
        if (in_array($role, ['admin', 'pemilik'], true) && DB::table($tabel)->count() <= 1) {
            return $this->gagal('Minimal harus ada satu akun admin dan satu akun pemilik.');
        }

        try {
            DB::table($tabel)->where($pk, $id)->delete();
        } catch (QueryException $e) {
            return $this->gagal('Akun ini sudah punya riwayat transaksi, jadi tidak bisa dihapus.');
        }

        return $this->ok();
    }
}
