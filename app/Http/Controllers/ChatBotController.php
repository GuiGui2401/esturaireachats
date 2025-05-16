<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product; // Assurez-vous d'avoir un modèle Product
use OpenAI\Laravel\Facades\OpenAI;
use GuzzleHttp\Client;

class ChatBotController extends Controller
{
    public function handleMessage(Request $request)
    {
        $message = $request->input('message');
        $sessionId = $request->session()->getId();
        $history = session("chatbot_history_{$sessionId}", []);

        $products = [];

        // Recherche de produits si le message contient des mots-clés
        if (str_contains(strtolower($message), 'produit') || str_contains(strtolower($message), 'cherche')) {
            $keywords = explode(' ', $message);
            $products = Product::where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->orWhere('name', 'LIKE', "%{$keyword}%")
                        ->orWhere('description', 'LIKE', "%{$keyword}%");
                }
            })->take(3)->get()->map(function ($product) {
                return [
                    'name' => $product->name,
                    'image' => asset('storage/' . $product->image), // Assurez-vous que les images sont stockées correctement
                    'url' => route('product.show', $product->id), // Route vers la page du produit
                    'price' => $product->price
                ];
            });
        }

        // Ajouter le message utilisateur à l'historique
        $history[] = ['role' => 'user', 'content' => $message];

        // Créer un client Guzzle avec vérification SSL désactivée
            $client = new Client([
                'verify' => false, // Désactiver la vérification SSL (TEMPORAIRE)
            ]);

            // Appel à l'API OpenAI
            $response = $client->post('https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-4o',
                    'messages' => [
                        ['role' => 'system', 'content' => 'Tu es un assistant virtuel pour un site e-commerce. Réponds en français de manière naturelle et concise. Si l\'utilisateur mentionne un produit, utilise les informations fournies pour suggérer des produits avec leurs noms, prix et liens. Évite de proposer de contacter le service client. Si le contexte n\'est pas clair, demande des précisions. Utilise un ton amical et professionnel.'],
                        ['role' => 'user', 'content' => $message]
                    ],
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            $botResponse = $data['choices'][0]['message']['content'];

        // Ajouter la réponse du bot à l'historique
        $history[] = ['role' => 'assistant', 'content' => $botResponse];
        session(["chatbot_history_{$sessionId}" => $history]);

        // Si des produits sont trouvés, ajoutez-les à la réponse
        if ($products->isNotEmpty()) {
            $botResponse .= "\n\nVoici quelques produits que j'ai trouvés :\n";
            foreach ($products as $product) {
                $botResponse .= "- [{$product['name']}]({$product['url']}) - {$product['price']} €\n";
            }
        }

        return response()->json([
            'message' => $botResponse,
            'products' => $products
        ]);
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $path = $request->file('image')->store('chatbot_images', 'public');

        return response()->json([
            'imageUrl' => asset('storage/' . $path)
        ]);
    }
}
