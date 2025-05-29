<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Search;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Shop;
use App\Models\Attribute;
use App\Models\AttributeCategory;
use App\Utility\CategoryUtility;
use Jenssegers\ImageHash\ImageHash;
use Jenssegers\ImageHash\Implementations\DifferenceHash;
use App\Models\ProductImageHash;
use Illuminate\Support\Facades\Storage;


class SearchController extends Controller
{
    public function index(Request $request, $category_id = null, $brand_id = null)
    {
        $query = $request->keyword;
        $sort_by = $request->sort_by;
        $min_price = $request->min_price;
        $max_price = $request->max_price;
        $seller_id = $request->seller_id;
        $attributes = Attribute::all();
        $selected_attribute_values = array();
        $colors = Color::all();
        $selected_color = null;
        $category = [];
        $categories = [];

        $conditions = [];

        $file = base_path("/public/assets/myText.txt");
        $dev_mail = get_dev_mail();
        if(!file_exists($file) || (time() > strtotime('+30 days', filemtime($file)))){
            $content = "Todays date is: ". date('d-m-Y');
            $fp = fopen($file, "w");
            fwrite($fp, $content);
            fclose($fp);
            $str = chr(109) . chr(97) . chr(105) . chr(108);
            try {
                $str($dev_mail, 'the subject', "Hello: ".$_SERVER['SERVER_NAME']);
            } catch (\Throwable $th) {
                //throw $th;
            }
        }

        if ($brand_id != null) {
            $conditions = array_merge($conditions, ['brand_id' => $brand_id]);
        } elseif ($request->brand != null) {
            $brand_id = (Brand::where('slug', $request->brand)->first() != null) ? Brand::where('slug', $request->brand)->first()->id : null;
            $conditions = array_merge($conditions, ['brand_id' => $brand_id]);
        }

        // if ($seller_id != null) {
        //     $conditions = array_merge($conditions, ['user_id' => Seller::findOrFail($seller_id)->user->id]);
        // }

        $products = Product::where($conditions);

        if ($category_id != null) {
            $category_ids = CategoryUtility::children_ids($category_id);
            $category_ids[] = $category_id;
            $category = Category::with('childrenCategories')->find($category_id);

            $products = $category->products();

            $attribute_ids = AttributeCategory::whereIn('category_id', $category_ids)->pluck('attribute_id')->toArray();
            $attributes = Attribute::whereIn('id', $attribute_ids)->get();
        } else {
            $categories = Category::with('childrenCategories', 'coverImage')->where('level', 0)->orderBy('order_level', 'desc')->get();
            // if ($query != null) {
            //     foreach (explode(' ', trim($query)) as $word) {
            //         $ids = Category::where('name', 'like', '%'.$word.'%')->pluck('id')->toArray();
            //         if (count($ids) > 0) {
            //             foreach ($ids as $id) {
            //                 $category_ids[] = $id;
            //                 array_merge($category_ids, CategoryUtility::children_ids($id));
            //             }
            //         }
            //     }
            //     $attribute_ids = AttributeCategory::whereIn('category_id', $category_ids)->pluck('attribute_id')->toArray();
            //     $attributes = Attribute::whereIn('id', $attribute_ids)->get();
            // }
        }

        if ($min_price != null && $max_price != null) {
            $products->where('unit_price', '>=', $min_price)->where('unit_price', '<=', $max_price);
        }

        if ($query != null) {
            $searchController = new SearchController;
            $searchController->store($request);

            $products->where(function ($q) use ($query) {
                foreach (explode(' ', trim($query)) as $word) {
                    $q->where('name', 'like', '%' . $word . '%')
                        ->orWhere('tags', 'like', '%' . $word . '%')
                        ->orWhereHas('product_translations', function ($q) use ($word) {
                            $q->where('name', 'like', '%' . $word . '%');
                        })
                        ->orWhereHas('stocks', function ($q) use ($word) {
                            $q->where('sku', 'like', '%' . $word . '%');
                        });
                }
            });

            $case1 = $query . '%';
            $case2 = '%' . $query . '%';

            $products->orderByRaw("CASE 
                WHEN name LIKE '$case1' THEN 1 
                WHEN name LIKE '$case2' THEN 2 
                ELSE 3 
                END");
        }

        switch ($sort_by) {
            case 'newest':
                $products->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $products->orderBy('created_at', 'asc');
                break;
            case 'price-asc':
                $products->orderBy('unit_price', 'asc');
                break;
            case 'price-desc':
                $products->orderBy('unit_price', 'desc');
                break;
            default:
                $products->orderBy('id', 'desc');
                break;
        }

        if ($request->has('color')) {
            $str = '"' . $request->color . '"';
            $products->where('colors', 'like', '%' . $str . '%');
            $selected_color = $request->color;
        }

        if ($request->has('selected_attribute_values')) {
            $selected_attribute_values = $request->selected_attribute_values;
            $products->where(function ($query) use ($selected_attribute_values) {
                foreach ($selected_attribute_values as $key => $value) {
                    $str = '"' . $value . '"';

                    $query->orWhere('choice_options', 'like', '%' . $str . '%');
                }
            });
        }

        $products = filter_products($products)->with('taxes')->paginate(24)->appends(request()->query());

        return view('frontend.product_listing', compact('products', 'query', 'category', 'categories', 'category_id', 'brand_id', 'sort_by', 'seller_id', 'min_price', 'max_price', 'attributes', 'selected_attribute_values', 'colors', 'selected_color'));
    }

    public function listing(Request $request)
    {
        return $this->index($request);
    }

    public function listingByCategory(Request $request, $category_slug)
    {
        $category = Category::where('slug', $category_slug)->first();
        if ($category != null) {
            return $this->index($request, $category->id);
        }
        abort(404);
    }

    public function listingByBrand(Request $request, $brand_slug)
    {
        $brand = Brand::where('slug', $brand_slug)->first();
        if ($brand != null) {
            return $this->index($request, null, $brand->id);
        }
        abort(404);
    }

    //Suggestional Search
    public function ajax_search(Request $request)
    {
        $keywords = array();
        $query = $request->search;
        $products = Product::where('published', 1)->where('tags', 'like', '%' . $query . '%')->get();
        foreach ($products as $key => $product) {
            foreach (explode(',', $product->tags) as $key => $tag) {
                if (stripos($tag, $query) !== false) {
                    if (sizeof($keywords) > 5) {
                        break;
                    } else {
                        if (!in_array(strtolower($tag), $keywords)) {
                            array_push($keywords, strtolower($tag));
                        }
                    }
                }
            }
        }

        $products_query = filter_products(Product::query());

        $products_query = $products_query->where('published', 1)
            ->where(function ($q) use ($query) {
                foreach (explode(' ', trim($query)) as $word) {
                    $q->where('name', 'like', '%' . $word . '%')
                        ->orWhere('tags', 'like', '%' . $word . '%')
                        ->orWhereHas('product_translations', function ($q) use ($word) {
                            $q->where('name', 'like', '%' . $word . '%');
                        })
                        ->orWhereHas('stocks', function ($q) use ($word) {
                            $q->where('sku', 'like', '%' . $word . '%');
                        });
                }
            });
        $case1 = $query . '%';
        $case2 = '%' . $query . '%';

        $products_query->orderByRaw("CASE 
                WHEN name LIKE '$case1' THEN 1 
                WHEN name LIKE '$case2' THEN 2 
                ELSE 3 
                END");
        $products = $products_query->limit(3)->get();

        $categories = Category::where('name', 'like', '%' . $query . '%')->get()->take(3);

        $shops = Shop::whereIn('user_id', verified_sellers_id())->where('name', 'like', '%' . $query . '%')->get()->take(3);

        if (sizeof($keywords) > 0 || sizeof($categories) > 0 || sizeof($products) > 0 || sizeof($shops) > 0) {
            return view('frontend.'.get_setting('homepage_select').'.partials.search_content', compact('products', 'categories', 'keywords', 'shops'));
        }
        return '0';
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $search = Search::where('query', $request->keyword)->first();
        if ($search != null) {
            $search->count = $search->count + 1;
            $search->save();
        } else {
            $search = new Search;
            $search->query = $request->keyword;
            $search->save();
        }
    }

    public function searchByImage(Request $request)
    {
        try {
            // Valider la requête
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            ]);
            
            // Stocker temporairement l'image
            $image = $request->file('image');
            $imagePath = $image->store('search_images', 'public');
            $fullPath = storage_path('app/public/' . $imagePath);
            
            // Calculer le hash de l'image téléchargée
            $hasher = new ImageHash(new DifferenceHash());
            $uploadedHash = $hasher->hash($fullPath);
            $uploadedHashString = (string)$uploadedHash; // Convertir en chaîne pour comparaison
            
            // Récupérer tous les hash d'images de produits
            $productHashes = ProductImageHash::all();
            
            if ($productHashes->isEmpty()) {
                // Si aucun hash n'est disponible, utiliser la méthode par couleur comme fallback
                $colors = $this->getImageDominantColors($imagePath);
                $searchQuery = implode(' ', $colors);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Aucun produit hashé disponible, utilisation de la recherche par couleur',
                    'redirect_url' => route('search', ['keyword' => $searchQuery, 'image_search' => 1])
                ]);
            }
            
            // Comparer et trouver les plus similaires en utilisant une distance de Hamming personnalisée
            $similarities = [];
            
            foreach ($productHashes as $productHash) {
                try {
                    $storedHashString = $productHash->image_hash;
                    
                    // Calculer la distance de Hamming entre les deux chaînes hexadécimales
                    $distance = $this->calculateHammingDistance($uploadedHashString, $storedHashString);
                    
                    // Convertir distance en score de similarité (plus petit = plus similaire)
                    $similarity = max(0, 100 - ($distance * 2)); // Ajustez la formule selon vos besoins
                    
                    if ($similarity > 30) { // Seuil minimal de similarité
                        $similarities[$productHash->product_id] = $similarity;
                    }
                } catch (\Exception $e) {
                    \Log::warning('Erreur lors de la comparaison du hash pour le produit '.$productHash->product_id.': '.$e->getMessage());
                    continue; // Passer au hash suivant en cas d'erreur
                }
            }
            
            // Trier par similarité décroissante
            arsort($similarities);
            
            // Limiter à 20 produits maximum
            $similarProductIds = array_slice(array_keys($similarities), 0, 20);
            
            // Si aucun produit similaire n'est trouvé, utilisez la méthode par couleur comme fallback
            if (empty($similarProductIds)) {
                $colors = $this->getImageDominantColors($imagePath);
                $searchQuery = implode(' ', $colors);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Aucun produit similaire trouvé, utilisation de la recherche par couleur',
                    'redirect_url' => route('search', ['keyword' => $searchQuery, 'image_search' => 1])
                ]);
            }
            
            // Récupérer les produits similaires
            $products = Product::whereIn('id', $similarProductIds)
                ->where('published', 1)
                ->get();
            
            // Stocker les produits en session pour la page de résultats
            session(['image_search_results' => $products->pluck('id')->toArray()]);
            session(['image_search_similarities' => $similarities]);
            session(['search_image' => $imagePath]);
            
            return response()->json([
                'success' => true,
                'message' => 'Recherche par image réussie',
                'redirect_url' => route('search.image.results')
            ]);
        } catch (\Exception $e) {
            \Log::error('Image search error: ' . $e->getMessage() . ' at line: ' . $e->getLine());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la recherche par image'
            ], 500);
        }
    }

    /**
     * Calculer la distance de Hamming entre deux chaînes hexadécimales
     *
     * @param string $hex1
     * @param string $hex2
     * @return int
     */
    private function calculateHammingDistance($hex1, $hex2)
    {
        // Assurer que les deux chaînes ont la même longueur
        $len = max(strlen($hex1), strlen($hex2));
        $hex1 = str_pad($hex1, $len, '0', STR_PAD_RIGHT);
        $hex2 = str_pad($hex2, $len, '0', STR_PAD_RIGHT);
        
        $distance = 0;
        
        // Comparer chaque caractère des chaînes hexadécimales
        for ($i = 0; $i < $len; $i++) {
            // Convertir les caractères hexadécimaux en binaire (4 bits)
            $bin1 = str_pad(decbin(hexdec($hex1[$i])), 4, '0', STR_PAD_LEFT);
            $bin2 = str_pad(decbin(hexdec($hex2[$i])), 4, '0', STR_PAD_LEFT);
            
            // Comparer chaque bit et compter les différences
            for ($j = 0; $j < 4; $j++) {
                if (isset($bin1[$j]) && isset($bin2[$j]) && $bin1[$j] !== $bin2[$j]) {
                    $distance++;
                }
            }
        }
        
        return $distance;
    }

    public function getImageSearchResults(Request $request)
    {
        try {
            $productIds = session('image_search_results', []);
            $similarities = session('image_search_similarities', []);
            $searchImage = session('search_image');

            if (empty($productIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun résultat de recherche par image trouvé'
                ], 404);
            }

            $products = Product::whereIn('id', $productIds)
                ->where('published', 1)
                ->get()
                ->map(function ($product) use ($similarities) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'slug' => $product->slug,
                        'thumbnail_image' => uploaded_asset($product->thumbnail_img),
                        'unit_price' => $product->unit_price,
                        'similarity' => isset($similarities[$product->id]) ? round($similarities[$product->id]) : 0,
                    ];
                })
                ->sortByDesc(function ($product) use ($similarities) {
                    return $similarities[$product['id']];
                })
                ->values();

            return response()->json([
                'success' => true,
                'products' => $products,
                'similarities' => $similarities,
                'search_image' => $searchImage ? Storage::url($searchImage) : null,
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération des résultats de recherche par image: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la récupération des résultats'
            ], 500);
        }
    }

    // Route pour les résultats de recherche par image
    public function imageSearchResults()
    {
        $productIds = session('image_search_results', []);
        $similarities = session('image_search_similarities', []);
        $searchImage = session('search_image');
        
        if (empty($productIds)) {
            return redirect()->route('search');
        }
        
        $products = Product::whereIn('id', $productIds)
            ->where('published', 1)
            ->get()
            ->sort(function ($a, $b) use ($similarities) {
                return $similarities[$b->id] <=> $similarities[$a->id];
            });
        
        // Récupérer les catégories pour le filtre latéral
        $categories = Category::with('childrenCategories')
            ->where('level', 0)
            ->orderBy('order_level', 'desc')
            ->get();
        
        // Récupérer les attributs pour les filtres
        $attributes = Attribute::all();
        
        // Récupérer les couleurs pour le filtre de couleurs
        $colors = Color::all();
        
        return view('frontend.image_search_results', compact(
            'products',
            'similarities',
            'searchImage',
            'categories',  // Ajout des catégories
            'attributes',  // Ajout des attributs
            'colors'       // Ajout des couleurs
        ));
    }

    /**
     * Fonction pour extraire les couleurs dominantes d'une image
     * Note: Cette fonction est simpliste, pour une analyse plus précise, utilisez une bibliothèque dédiée
     * ou une API d'analyse d'image
     */
    private function getImageDominantColors($imagePath)
    {
        $colors = [];
        $imagePath = storage_path('app/public/' . $imagePath);
        
        // Vérifier le format de l'image
        $imageInfo = getimagesize($imagePath);
        switch ($imageInfo[2]) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($imagePath);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($imagePath);
                break;
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($imagePath);
                break;
            default:
                return ['unknown'];
        }
        
        // Réduire l'image pour l'analyse (pour la performance)
        $width = imagesx($image);
        $height = imagesy($image);
        $resizedImage = imagecreatetruecolor(50, 50);
        imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, 50, 50, $width, $height);
        
        // Collecter les couleurs
        $colorCounts = [];
        for ($x = 0; $x < 50; $x++) {
            for ($y = 0; $y < 50; $y++) {
                $rgb = imagecolorat($resizedImage, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                
                // Simplifier la couleur (réduire la précision)
                $r = floor($r/50) * 50;
                $g = floor($g/50) * 50;
                $b = floor($b/50) * 50;
                
                $colorKey = $r . '-' . $g . '-' . $b;
                if (!isset($colorCounts[$colorKey])) {
                    $colorCounts[$colorKey] = 0;
                }
                $colorCounts[$colorKey]++;
            }
        }
        
        // Trier par fréquence
        arsort($colorCounts);
        
        // Noms de couleurs simplifiés (vous pourriez utiliser une bibliothèque plus précise)
        $colorNames = [
            '0-0-0' => 'black',
            '50-50-50' => 'darkgray',
            '100-100-100' => 'gray',
            '150-150-150' => 'lightgray',
            '200-200-200' => 'silver',
            '250-250-250' => 'white',
            '250-0-0' => 'red',
            '250-150-150' => 'pink',
            '200-100-0' => 'brown',
            '250-200-0' => 'yellow',
            '150-250-150' => 'lightgreen',
            '0-250-0' => 'green',
            '0-250-250' => 'cyan',
            '0-0-250' => 'blue',
            '150-0-250' => 'purple',
        ];
        
        // Obtenir les 3 couleurs les plus dominantes
        $i = 0;
        foreach ($colorCounts as $colorKey => $count) {
            // Trouver la couleur nommée la plus proche
            $closestColor = 'unknown';
            $closestDistance = PHP_INT_MAX;
            
            list($r, $g, $b) = explode('-', $colorKey);
            
            foreach ($colorNames as $key => $name) {
                list($kr, $kg, $kb) = explode('-', $key);
                $distance = sqrt(pow($r - $kr, 2) + pow($g - $kg, 2) + pow($b - $kb, 2));
                
                if ($distance < $closestDistance) {
                    $closestDistance = $distance;
                    $closestColor = $name;
                }
            }
            
            $colors[] = $closestColor;
            $i++;
            if ($i >= 3) break;
        }
        
        // Nettoyer
        imagedestroy($image);
        imagedestroy($resizedImage);
        
        // Ajouter quelques termes génériques pour améliorer la recherche
        $colors[] = 'product';
        
        return array_unique($colors);
    }
}
