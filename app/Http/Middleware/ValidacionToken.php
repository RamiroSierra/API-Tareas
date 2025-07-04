<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class ValidacionToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        
        if (!$token) {
            return response()->json(['error' => 'Token no enviado'], 401);
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->get('http://localhost:8000/api/validate');

        if ($response->successful()) {
            $request->merge(['user' => $response->json()]);
            return $next($request);
        }

        return response()->json(['error' => 'Token inválido'], 401);
    }
}