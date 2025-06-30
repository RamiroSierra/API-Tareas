<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http;

class ValidacionToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Token no proporcionado'], 401);
        }

        $authServiceUrl = env('AUTH_SERVICE_URL', 'http://servicio-autenticacion.test/api/validate');
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->get($authServiceUrl);

        if ($response->successful()) {
            $userData = $response->json();
            $request->merge(['user' => $userData]);
            return $next($request);
        }

        return response()->json([
            'error' => 'Token inválido o expirado',
            'details' => $response->json()
        ], 401);
    }
}