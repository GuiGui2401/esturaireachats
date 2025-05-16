<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Gardez si vous utilisez encore les logs

class EnsureSystemKey
{
    public function handle(Request $request, Closure $next)
    {
        // ---- AJOUT : Laisser passer les requêtes OPTIONS ----
        if ($request->isMethod('OPTIONS')) {
            // Important : Laisser HandleCors gérer la réponse finale pour OPTIONS
            return $next($request); 
        }
        // ---- FIN DE L'AJOUT ----


        // --- Votre logique de vérification de clé pour les autres méthodes ---
        $headerKey = $request->header('System-Key');
        $configKey = config('app.system_key');
        // $envKey = env('SYSTEM_KEY'); // Si vous loggez encore

        // Log::info(...); // Gardez les logs si besoin pour déboguer

        if (!$headerKey || $headerKey !== $configKey) { 
            // Log::error(...); // Gardez les logs si besoin

            // Il est préférable de renvoyer un code 403 (Interdit) ici
            return response()->json([
                'result' => false,
                'message' => 'Request not found! (EnsureSystemKey Failed Check)' 
            ], 403); // Status 403
        }
        // --- Fin de la logique de vérification ---

        return $next($request);
    }
}