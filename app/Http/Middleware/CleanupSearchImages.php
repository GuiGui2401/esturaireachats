<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;

class CleanupSearchImages
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // Après envoi de la réponse, vérifiez s'il y a une image à supprimer
        if (session()->has('delete_search_image_after_view')) {
            $searchImage = session('delete_search_image_after_view');
            
            // Supprimer l'image si elle existe
            if ($searchImage && Storage::disk('public')->exists($searchImage)) {
                Storage::disk('public')->delete($searchImage);
            }
            
            // Supprimer la variable de session
            session()->forget('delete_search_image_after_view');
        }
        
        return $response;
    }
}
