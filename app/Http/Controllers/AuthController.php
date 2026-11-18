<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Peran yang boleh memakai web ini (staff gudang memakai aplikasi sendiri)
    private const WEB_ROLES = ['admin' => 'id_admin', 'pemilik' => 'id_pemilik'];

    public function login(Request $request): JsonResponse
    {
        $username = trim((string) $request->input('username'));
        $password = (string) $request->input('password');

        foreach (self::WEB_ROLES as $role => $pk) {
            $row = DB::table($role)->where('username', $username)->first();

            if ($row && Hash::check($password, $row->password)) {
                $request->session()->regenerate();
                $request->session()->put([
                    'role' => $role,
                    'uid' => (int) $row->$pk,
                    'nama' => $row->nama_lengkap,
                ]);

                return response()->json(['role' => $role, 'id' => (int) $row->$pk, 'nama' => $row->nama_lengkap]);
            }
        }

        $staff = DB::table('staff_gudang')->where('username', $username)->first();
        if ($staff && Hash::check($password, $staff->password)) {
            return response()->json(['message' => 'Akun staff gudang tidak dapat masuk ke web ini. Gunakan aplikasi gudang.'], 403);
        }

        return response()->json(['message' => 'Username atau password tidak cocok. Periksa lagi lalu coba masuk kembali.'], 422);
    }

    public function me(Request $request): JsonResponse
    {
        $role = $request->session()->get('role');
        if (!$role) {
            return response()->json(['message' => 'Belum login.'], 401);
        }

        return response()->json([
            'role' => $role,
            'id' => $request->session()->get('uid'),
            'nama' => $request->session()->get('nama'),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['ok' => true]);
    }
}
