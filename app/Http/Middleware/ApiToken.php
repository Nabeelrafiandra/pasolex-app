<?php

namespace App\Http\Middleware;

use App\Http\Controllers\MobileController;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/** Memeriksa token Bearer dari aplikasi Flutter. */
class ApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        $data = $token ? Cache::get(MobileController::KUNCI . hash('sha256', $token)) : null;

        if (!is_array($data)) {
            return response()->json(['message' => 'Sesi berakhir. Silakan login kembali.'], 401);
        }

        $request->attributes->set('uid', $data['uid']);
        $request->attributes->set('nama', $data['nama']);

        return $next($request);
    }
}
