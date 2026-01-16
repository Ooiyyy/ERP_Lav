<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Jika Laravel mengirim sebagai satu string "001,002" maka pecah lagi
        if (count($roles) === 1 && is_string($roles[0]) && strpos($roles[0], ',') !== false) {
            $roles = array_map('trim', explode(',', $roles[0]));
        } else {
            $roles = array_map('trim', $roles);
        }

        // Normalisasi semua role jadi string untuk perbandingan yang konsisten
        $roles = array_map('strval', $roles);

        // Ambil role user dengan aman (sesuaikan field yang Anda pakai)
        // contoh: $user->jabatan atau $user->karyawan->id_jabatan
        $userRole = null;
        if (isset($user->jabatan)) {
            $userRole = $user->jabatan;
        } elseif (isset($user->karyawan) && isset($user->karyawan->id_jabatan)) {
            $userRole = $user->karyawan->id_jabatan;
        }

        // Jika masih null, tolak
        if ($userRole === null) {
            return response()->json(['message' => 'Forbidden - role not found'], 403);
        }

        $userRole = (string) $userRole;

        // Cek membership
        if (in_array($userRole, $roles, true)) {
            return $next($request);
        }

        // Optional: log untuk debugging
        Log::info('Role middleware failed', [
            'user_id' => $user->id ?? null,
            'user_role' => $userRole,
            'allowed_roles' => $roles,
        ]);

        return response()->json(['message' => 'Forbidden'], 403);
    }
}
