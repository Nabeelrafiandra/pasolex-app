<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * API untuk aplikasi Flutter staff gudang.
 * Memakai logika transaksi yang sama dengan GudangController (barang masuk, keluar, rusak, opname, cari),
 * tetapi login memakai token (Authorization: Bearer ...) sebagai pengganti cookie sesi.
 * Token disimpan di cache (CACHE_STORE=file), sehingga tidak perlu tabel baru.
 */
class MobileController extends GudangController
{
    public const KUNCI = 'gudang_token:';

    /** Identitas staf diambil dari token (diisi oleh middleware ApiToken). */
    protected function uid(Request $r): int
    {
        return (int) $r->attributes->get('uid');
    }

    public function login(Request $r): JsonResponse
    {
        $username = trim((string) $r->input('username'));
        $password = (string) $r->input('password');
        $row = DB::table('staff_gudang')->where('username', $username)->first();

        if (!$row || !Hash::check($password, $row->password)) {
            return $this->gagal('Username atau password tidak cocok. Periksa lagi lalu coba masuk kembali.');
        }

        $token = Str::random(64);
        Cache::put(self::KUNCI . hash('sha256', $token), [
            'uid' => (int) $row->id_staff_gudang,
            'nama' => $row->nama_lengkap,
        ], now()->addDays(7));

        return response()->json(['token' => $token, 'id' => (int) $row->id_staff_gudang, 'nama' => $row->nama_lengkap]);
    }

    public function me(Request $r): JsonResponse
    {
        return response()->json(['role' => 'gudang', 'id' => $this->uid($r), 'nama' => $r->attributes->get('nama')]);
    }

    public function logout(Request $r): JsonResponse
    {
        Cache::forget(self::KUNCI . hash('sha256', (string) $r->bearerToken()));

        return response()->json(['ok' => true]);
    }
}
