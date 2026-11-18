<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Aplikasi Staff Gudang: login, barang masuk, barang keluar, barang rusak,
 * stock opname, dan pencarian lokasi rak.
 * Setiap perubahan stok dilakukan di dalam transaksi database (semua berhasil atau semua dibatalkan).
 */
class GudangController extends Controller
{
    protected function gagal(string $pesan, int $kode = 422): JsonResponse
    {
        return response()->json(['message' => $pesan], $kode);
    }

    private function sekarang(): string
    {
        return Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');
    }

    protected function uid(Request $r): int
    {
        return (int) $r->session()->get('uid');
    }

    // ---------------------------------------------------------------- AKUN

    public function login(Request $r): JsonResponse
    {
        $username = trim((string) $r->input('username'));
        $password = (string) $r->input('password');
        $row = DB::table('staff_gudang')->where('username', $username)->first();

        if ($row && Hash::check($password, $row->password)) {
            $r->session()->regenerate();
            $r->session()->put(['role' => 'gudang', 'uid' => (int) $row->id_staff_gudang, 'nama' => $row->nama_lengkap]);

            return response()->json(['role' => 'gudang', 'id' => (int) $row->id_staff_gudang, 'nama' => $row->nama_lengkap]);
        }

        return $this->gagal('Username atau password tidak cocok. Periksa lagi lalu coba masuk kembali.');
    }

    public function me(Request $r): JsonResponse
    {
        if ($r->session()->get('role') !== 'gudang') {
            return $this->gagal('Belum login.', 401);
        }

        return response()->json([
            'role' => 'gudang',
            'id' => $this->uid($r),
            'nama' => $r->session()->get('nama'),
        ]);
    }

    // ---------------------------------------------------------------- DATA

    public function data(Request $r): JsonResponse
    {
        $uid = $this->uid($r);

        $produk = DB::table('produk')
            ->join('warna', 'warna.id_warna', '=', 'produk.id_warna')
            ->join('ukuran', 'ukuran.id_ukuran', '=', 'produk.id_ukuran')
            ->select('produk.id_produk as id', 'produk.sku', 'produk.nama_model as nama', 'warna.nama_warna as warna', 'ukuran.nama_ukuran as ukuran')
            ->orderBy('produk.nama_model')->get()
            ->map(fn ($x) => ['id' => (int) $x->id, 'sku' => $x->sku, 'nama' => $x->nama, 'warna' => $x->warna, 'ukuran' => $x->ukuran])
            ->all();

        $terisi = DB::table('stok_lokasi')->select('id_lokasi', DB::raw('SUM(qty) as total'))->groupBy('id_lokasi')->pluck('total', 'id_lokasi');
        $lok = DB::table('lokasi_rak')->orderBy('kode_rak')->get()
            ->map(fn ($x) => ['id' => (int) $x->id_lokasi, 'kode' => $x->kode_rak, 'kap' => (int) $x->kapasitas, 'terisi' => (int) $terisi->get($x->id_lokasi, 0)])
            ->all();

        $stok = DB::table('stok_lokasi')->select('id_produk as p', 'id_lokasi as l', 'qty as q')->get()
            ->map(fn ($x) => ['p' => (int) $x->p, 'l' => (int) $x->l, 'q' => (int) $x->q])->all();

        $kategori = DB::table('kategori_rusak')->orderBy('id_kategori_rusak')->get()
            ->map(fn ($x) => ['id' => (int) $x->id_kategori_rusak, 'n' => $x->jenis_kerusakan])->all();
        $tindak = DB::table('tindak_lanjut')->orderBy('id_tindak_lanjut')->get()
            ->map(fn ($x) => ['id' => (int) $x->id_tindak_lanjut, 'n' => $x->nama_tindakan])->all();

        // Aktivitas terakhir milik staf yang sedang login
        $ambil = function (string $tabel, string $jenis, string $kolomQty) use ($uid) {
            return DB::table($tabel)
                ->join('produk', 'produk.id_produk', '=', "$tabel.id_produk")
                ->join('lokasi_rak', 'lokasi_rak.id_lokasi', '=', "$tabel.id_lokasi")
                ->where("$tabel.id_staff_gudang", $uid)
                ->orderByDesc("$tabel.tanggal")->limit(15)
                ->select("$tabel.tanggal as t", 'produk.nama_model as nama', 'produk.sku', 'lokasi_rak.kode_rak as rak', "$tabel.$kolomQty as q", DB::raw($tabel === 'stock_opname'
                    ? "CASE WHEN stock_opname.alasan LIKE 'Adjustment:%' THEN 'Adjustment' ELSE 'Opname' END as jenis"
                    : "'$jenis' as jenis"))
                ->get();
        };
        $riwayat = $ambil('inbound', 'Masuk', 'qty')
            ->concat($ambil('outbound', 'Keluar', 'qty'))
            ->concat($ambil('barang_rusak', 'Rusak', 'qty'))
            ->concat($ambil('stock_opname', 'Opname', 'selisih'))
            ->sortByDesc('t')->take(15)->values()
            ->map(fn ($x) => ['t' => $x->t, 'jenis' => $x->jenis, 'nama' => $x->nama, 'sku' => $x->sku, 'rak' => $x->rak, 'q' => (int) $x->q])
            ->all();

        return response()->json(compact('produk', 'lok', 'stok', 'kategori', 'tindak', 'riwayat'));
    }

    // ------------------------------------------------------- CARI LOKASI RAK

    public function cari(Request $r): JsonResponse
    {
        $kata = mb_strtolower(trim((string) $r->query('q')));
        if ($kata === '') {
            return $this->gagal('Isi SKU terlebih dahulu.');
        }

        $produk = DB::table('produk')
            ->join('warna', 'warna.id_warna', '=', 'produk.id_warna')
            ->join('ukuran', 'ukuran.id_ukuran', '=', 'produk.id_ukuran')
            ->where(function ($q) use ($kata) {
                $q->whereRaw('LOWER(produk.sku) LIKE ?', ["%$kata%"])
                    ->orWhereRaw('LOWER(produk.nama_model) LIKE ?', ["%$kata%"]);
            })
            ->select('produk.id_produk', 'produk.sku', 'produk.nama_model', 'warna.nama_warna', 'ukuran.nama_ukuran')
            ->limit(10)->get();

        $hasil = [];
        foreach ($produk as $p) {
            $rak = DB::table('stok_lokasi')
                ->join('lokasi_rak', 'lokasi_rak.id_lokasi', '=', 'stok_lokasi.id_lokasi')
                ->where('stok_lokasi.id_produk', $p->id_produk)->where('stok_lokasi.qty', '>', 0)
                ->orderByDesc('stok_lokasi.qty')
                ->select('lokasi_rak.kode_rak as kode', 'stok_lokasi.qty as qty')->get();

            if ($rak->isNotEmpty()) {
                $hasil[] = [
                    'sku' => $p->sku, 'nama' => $p->nama_model, 'warna' => $p->nama_warna, 'ukuran' => $p->nama_ukuran,
                    'total' => (int) $rak->sum('qty'),
                    'rak' => $rak->map(fn ($x) => ['kode' => $x->kode, 'qty' => (int) $x->qty])->all(),
                ];
            }
        }

        return response()->json(['hasil' => $hasil]);
    }

    // --------------------------------------------------- FUNGSI BANTU STOK

    private function stokSaatIni(int $p, int $l): int
    {
        return (int) (DB::table('stok_lokasi')->where('id_produk', $p)->where('id_lokasi', $l)->lockForUpdate()->value('qty') ?? 0);
    }

    private function terisi(int $l): int
    {
        return (int) DB::table('stok_lokasi')->where('id_lokasi', $l)->sum('qty');
    }

    private function setStok(int $p, int $l, int $qty): void
    {
        $q = DB::table('stok_lokasi')->where('id_produk', $p)->where('id_lokasi', $l);
        if ($q->exists()) {
            $q->update(['qty' => $qty]);
        } else {
            DB::table('stok_lokasi')->insert(['id_produk' => $p, 'id_lokasi' => $l, 'qty' => $qty]);
        }
    }

    /** Memastikan produk dan rak yang dipilih ada. Mengembalikan pesan error atau null. */
    private function cekPilihan(int $p, int $l): ?string
    {
        if (!DB::table('produk')->where('id_produk', $p)->exists()) {
            return 'Pilih produk terlebih dahulu.';
        }
        if (!DB::table('lokasi_rak')->where('id_lokasi', $l)->exists()) {
            return 'Pilih lokasi rak terlebih dahulu.';
        }

        return null;
    }

    private function selesai(array $h, string $pesanOk = ''): JsonResponse
    {
        return isset($h['gagal']) ? $this->gagal($h['gagal']) : response()->json($h + ['ok' => true]);
    }

    // ------------------------------------------------------- BARANG MASUK

    public function inbound(Request $r): JsonResponse
    {
        $p = (int) $r->input('p');
        $l = (int) $r->input('l');
        $q = (int) $r->input('q');
        $uid = $this->uid($r);

        if ($pesan = $this->cekPilihan($p, $l)) {
            return $this->gagal($pesan);
        }
        if ($q < 1) {
            return $this->gagal('Jumlah harus lebih dari 0.');
        }

        $h = DB::transaction(function () use ($p, $l, $q, $uid) {
            $rak = DB::table('lokasi_rak')->where('id_lokasi', $l)->lockForUpdate()->first();
            $sisa = max(0, (int) $rak->kapasitas - $this->terisi($l));
            if ($q > $sisa) {
                return ['gagal' => "Rak {$rak->kode_rak} hanya muat {$sisa} pcs lagi."];
            }

            DB::table('inbound')->insert([
                'id_produk' => $p, 'id_lokasi' => $l, 'id_staff_gudang' => $uid, 'qty' => $q, 'tanggal' => $this->sekarang(),
            ]);
            $this->setStok($p, $l, $this->stokSaatIni($p, $l) + $q);

            return [];
        });

        return $this->selesai($h);
    }

    // ------------------------------------------------------ BARANG KELUAR

    public function outbound(Request $r): JsonResponse
    {
        $p = (int) $r->input('p');
        $l = (int) $r->input('l');
        $q = (int) $r->input('q');
        $uid = $this->uid($r);

        if ($pesan = $this->cekPilihan($p, $l)) {
            return $this->gagal($pesan);
        }
        if ($q < 1) {
            return $this->gagal('Jumlah harus lebih dari 0.');
        }

        $h = DB::transaction(function () use ($p, $l, $q, $uid) {
            $rak = DB::table('lokasi_rak')->where('id_lokasi', $l)->lockForUpdate()->first();
            $ada = $this->stokSaatIni($p, $l);
            if ($q > $ada) {
                return ['gagal' => "Stok tidak cukup. Tersedia {$ada} pcs di rak {$rak->kode_rak}."];
            }

            DB::table('outbound')->insert([
                'id_produk' => $p, 'id_lokasi' => $l, 'id_staff_gudang' => $uid, 'qty' => $q, 'tanggal' => $this->sekarang(),
            ]);
            $this->setStok($p, $l, $ada - $q);

            return [];
        });

        return $this->selesai($h);
    }

    // ------------------------------------------------------- BARANG RUSAK

    public function rusak(Request $r): JsonResponse
    {
        $p = (int) $r->input('p');
        $l = (int) $r->input('l');
        $q = (int) $r->input('q');
        $k = (int) $r->input('k');
        $f = (int) $r->input('f');
        $ket = trim((string) $r->input('ket'));
        $uid = $this->uid($r);

        if ($pesan = $this->cekPilihan($p, $l)) {
            return $this->gagal($pesan);
        }
        if ($q < 1) {
            return $this->gagal('Jumlah harus lebih dari 0.');
        }
        if (!DB::table('kategori_rusak')->where('id_kategori_rusak', $k)->exists() || !DB::table('tindak_lanjut')->where('id_tindak_lanjut', $f)->exists()) {
            return $this->gagal('Pilih jenis kerusakan dan tindak lanjut.');
        }

        $h = DB::transaction(function () use ($p, $l, $q, $k, $f, $ket, $uid) {
            $rak = DB::table('lokasi_rak')->where('id_lokasi', $l)->lockForUpdate()->first();
            $ada = $this->stokSaatIni($p, $l);
            if ($q > $ada) {
                return ['gagal' => "Stok tidak cukup. Tersedia {$ada} pcs di rak {$rak->kode_rak}."];
            }

            DB::table('barang_rusak')->insert([
                'id_produk' => $p, 'id_lokasi' => $l, 'id_kategori_rusak' => $k, 'id_tindak_lanjut' => $f,
                'id_inbound' => null, 'id_outbound' => null, 'id_staff_gudang' => $uid,
                'qty' => $q, 'keterangan' => $ket === '' ? null : $ket, 'tanggal' => $this->sekarang(),
            ]);
            $this->setStok($p, $l, $ada - $q);

            return [];
        });

        return $this->selesai($h);
    }

    // ------------------------------------------------------- STOCK OPNAME

    public function opname(Request $r): JsonResponse
    {
        $p = (int) $r->input('p');
        $l = (int) $r->input('l');
        $mentah = $r->input('fisik');
        $alasan = trim((string) $r->input('alasan'));
        $uid = $this->uid($r);

        if ($pesan = $this->cekPilihan($p, $l)) {
            return $this->gagal($pesan);
        }
        if (!is_numeric($mentah) || (int) $mentah < 0 || (string) (int) $mentah !== trim((string) $mentah)) {
            return $this->gagal('Isi hasil hitung fisik dengan angka 0 atau lebih.');
        }
        $fisik = (int) $mentah;

        $h = DB::transaction(function () use ($p, $l, $fisik, $alasan, $uid) {
            $rak = DB::table('lokasi_rak')->where('id_lokasi', $l)->lockForUpdate()->first();
            $sistem = $this->stokSaatIni($p, $l);
            $selisih = $fisik - $sistem;

            if ($this->terisi($l) + $selisih > (int) $rak->kapasitas) {
                return ['gagal' => "Hasil hitung melebihi kapasitas rak {$rak->kode_rak} ({$rak->kapasitas} pcs)."];
            }

            DB::table('stock_opname')->insert([
                'id_produk' => $p, 'id_lokasi' => $l, 'id_staff_gudang' => $uid,
                'stok_sistem' => $sistem, 'stok_fisik' => $fisik, 'selisih' => $selisih,
                'alasan' => $alasan === '' ? null : $alasan, 'tanggal' => $this->sekarang(),
            ]);
            $this->setStok($p, $l, $fisik);

            return ['selisih' => $selisih];
        });

        return $this->selesai($h);
    }

    // ---------------------------------------------------------- ADJUSTMENT

    /**
     * Koreksi stok manual (tambah atau kurangi) pada satu produk di satu rak.
     * Dicatat di tabel stock_opname (stok sistem, stok sesudah, selisih, alasan),
     * dengan awalan "Adjustment: " pada kolom alasan agar bisa dibedakan dari opname.
     */
    public function adjustment(Request $r): JsonResponse
    {
        $p = (int) $r->input('p');
        $l = (int) $r->input('l');
        $delta = (int) $r->input('delta');
        $alasan = trim((string) $r->input('alasan'));
        $uid = $this->uid($r);

        if ($pesan = $this->cekPilihan($p, $l)) {
            return $this->gagal($pesan);
        }
        if ($delta === 0) {
            return $this->gagal('Jumlah penyesuaian tidak boleh 0.');
        }
        if ($alasan === '') {
            return $this->gagal('Alasan penyesuaian wajib diisi.');
        }

        $h = DB::transaction(function () use ($p, $l, $delta, $alasan, $uid) {
            $rak = DB::table('lokasi_rak')->where('id_lokasi', $l)->lockForUpdate()->first();
            $sistem = $this->stokSaatIni($p, $l);
            $sesudah = $sistem + $delta;

            if ($sesudah < 0) {
                return ['gagal' => "Stok tidak boleh minus. Stok saat ini {$sistem} pcs di rak {$rak->kode_rak}."];
            }
            if ($delta > 0) {
                $sisa = max(0, (int) $rak->kapasitas - $this->terisi($l));
                if ($delta > $sisa) {
                    return ['gagal' => "Rak {$rak->kode_rak} hanya muat {$sisa} pcs lagi."];
                }
            }

            DB::table('stock_opname')->insert([
                'id_produk' => $p, 'id_lokasi' => $l, 'id_staff_gudang' => $uid,
                'stok_sistem' => $sistem, 'stok_fisik' => $sesudah, 'selisih' => $delta,
                'alasan' => 'Adjustment: ' . $alasan, 'tanggal' => $this->sekarang(),
            ]);
            $this->setStok($p, $l, $sesudah);

            return ['selisih' => $delta];
        });

        return $this->selesai($h);
    }
}
