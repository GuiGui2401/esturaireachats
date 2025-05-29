<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\ProductImageHash;
use App\Models\Upload;
use Jenssegers\ImageHash\ImageHash;
use Jenssegers\ImageHash\Implementations\DifferenceHash;

class CalculateProductImageHashes extends Command
{
    protected $signature = 'products:calculate-image-hashes {--batch=100}';
    protected $description = 'Calculate image hashes for products';

    public function handle()
    {
        $hasher = new ImageHash(new DifferenceHash());
        $batch = $this->option('batch');

        // Récupérer les produits qui n'ont pas encore de hash, avec leur relation thumbnail
        $products = Product::with('thumbnail')
            ->whereNotIn('id', function ($query) {
                $query->select('product_id')->from('product_image_hashes');
            })
            ->limit($batch)
            ->get();

        $this->info("Nombre de produits à traiter : " . $products->count());

        $bar = $this->output->createProgressBar(count($products));
        $bar->start();

        $processedCount = 0;
        $errorCount = 0;
        $missingImageCount = 0;

        foreach ($products as $product) {
            // Vérifier si le produit a une miniature
            if ($product->thumbnail && $product->thumbnail->file_name) {
                if ($this->processImage($product, $product->thumbnail, $hasher)) {
                    $processedCount++;
                } else {
                    $errorCount++;
                }
            } else {
                $missingImageCount++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->info("\nTraitement terminé pour {$products->count()} produits.");
        $this->info("Statistiques:");
        $this->info("- Produits traités avec succès: $processedCount");
        $this->info("- Produits avec erreurs: $errorCount");
        $this->info("- Produits sans image: $missingImageCount");
    }

    private function processImage($product, $upload, $hasher)
    {
        try {
            // Chemin relatif de l'image (déjà au format 'uploads/all/filename.jpg')
            $imagePath = $upload->file_name;

            // Construction du chemin complet sans ajouter à nouveau 'uploads/all/'
            $fullPath = public_path($imagePath);

            if (!file_exists($fullPath)) {
                $this->warn("Image introuvable: $fullPath pour le produit ID {$product->id}");
                return false;
            }

            // Calculer le hash
            $hash = $hasher->hash($fullPath);

            // Enregistrer le hash
            ProductImageHash::create([
                'product_id' => $product->id,
                'image_path' => $imagePath,
                'image_hash' => (string)$hash
            ]);

            return true;
        } catch (\Exception $e) {
            $this->error("Erreur pour l'image du produit {$product->id}: {$e->getMessage()}");
            return false;
        }
    }
}
