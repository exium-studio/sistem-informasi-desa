<?php

namespace App\Http\Middleware;

use App\Http\Resources\Templates\Response\WithoutDataResource;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerifiedRole
{
    public function handle(Request $request, Closure $next, $guard = null)
    {
        $user = Auth::user();

        // Pastikan sudah login
        if (!$user) {
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_UNAUTHORIZED,
                    'Logout Gagal',
                    'Anda tidak memiliki sesi login yang aktif.',
                    'NO_ACTIVE_SESSION'
                ),
                Response::HTTP_UNAUTHORIZED
            );
        }

        // Cek apakah request berasal dari web atau mobile
        $isWeb = $request->is('web/*');
        $isMobile = $request->is('mobile/*');

        // Web roles
        $webRoles = ['Super Admin', 'Lurah', 'Kepala RW', 'Kepala RT'];

        // Mobile roles
        $mobileRoles = ['Super Admin', 'Warga Sipil'];

        // Mengecek akses berdasarkan role dan jenis request
        if ($isWeb && !$user->hasRole($webRoles)) {
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_UNAUTHORIZED,
                    'Logout Gagal',
                    'Anda tidak memiliki sesi login yang aktif.',
                    'NO_ACTIVE_SESSION'
                ),
                Response::HTTP_UNAUTHORIZED
            );
        }

        if ($isMobile && !$user->hasRole($mobileRoles)) {
            return response()->json(
                new WithoutDataResource(
                    Response::HTTP_UNAUTHORIZED,
                    'Logout Gagal',
                    'Anda tidak memiliki sesi login yang aktif.',
                    'NO_ACTIVE_SESSION'
                ),
                Response::HTTP_UNAUTHORIZED
            );
        }

        return $next($request);
    }
}
