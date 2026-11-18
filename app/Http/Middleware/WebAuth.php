<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Memastikan pengguna sudah login. Contoh pemakaian:
 *   WebAuth::class              -> admin atau pemilik
 *   WebAuth::class . ':admin'   -> hanya admin
 */
class WebAuth
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $role = $request->session()->get('role');

        if (!$role) {
            return response()->json(['message' => 'Sesi berakhir. Silakan login kembali.'], 401);
        }
        if ($roles && !in_array($role, $roles, true)) {
            return response()->json(['message' => 'Anda tidak memiliki akses untuk tindakan ini.'], 403);
        }

        return $next($request);
    }
}
