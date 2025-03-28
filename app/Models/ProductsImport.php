<?php

namespace App\Models;

use App\Models\Product;
use App\Models\ProductStock;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Str;
use Auth;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Storage;

//class ProductsImport implements ToModel, WithHeadingRow, WithValidation
class ProductsImport implements ToCollection, WithHeadingRow, WithValidation, ToModel
{
    use SoftDeletes;
    private $rows = 0;
    private $files = [];
    private $total = 0;
    private $user_id;

    public function __construct()
    {
        $this->user_id = Auth::user()->id;
    }

    public function collection(Collection $rows)
    {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', -1);
        $canImport = true;
        $samePrices = [];
        $user = User::find($this->user_id);
        if ($user->user_type == 'seller' && addon_is_activated('seller_subscription')) {
            if ((count($rows) + $user->products()->count()) > $user->shop->product_upload_limit
                || $user->shop->package_invalid_at == null
                || Carbon::now()->diffInDays(Carbon::parse($user->shop->package_invalid_at), false) < 0
            ) {
                $canImport = false;
                flash(translate('Please upgrade your package.'))->warning();
            }
        }

        if ($canImport) {

            foreach ($rows as $count => $row) {


                //skip duplicates

                $user_id = $user->id;
                $name = $row['name'];
                $p_link = $row['product_link_french'];
                $check = \DB::table('products')->select("id")
                    ->where(['name' => $name,  'user_id' => $user_id])
                    ->orWhere(['external_link_1' => $p_link])
                    ->count();

                //$check = Product::where(['name'  => $row['name'], 'user_id'=>auth()->user()->id])->orWhere(['external_link_1' => $row['product_link_french']])->first();
                if ($check) {
                    continue;
                }


                $approved = 1;
                if ($user->user_type == 'seller' && get_setting('product_approve_by_admin') == 1) {
                    //$approved = 0;
                }

                if (isset($row['wholesale'])) {
                    $imgs = explode(';', $row['thumbnail_img']);
                    $selectedImgs = $imgs;

                    if ($count > 1000) {
                        flash(translate('Products imported successfully'))->success();
                        return;
                    }

                    /* foreach($imgs as $img){
                        if(!empty($img)){
                            if(in_array($img, $this->files))
                                continue;

                            if(!Str::contains($img, 'video')){
                                $selectedImgs[] = $img;
                            }
                        }
                    }

                    if(!count($selectedImgs)){
                        continue;
                    }*/

                    $categories = explode(',', $row['category_id']);

                    $cModel = Category::firstOrCreate(['name' => 'Unknown', 'slug' => 'unknown']);
                    foreach ($categories as $key => $category) {
                        $category = trim($category);
                        $categorySlug = Str::slug($category);
                        $cModel = Category::where('slug', "=", $categorySlug)->orWhere('name', $category)->first();
                        if (!$cModel) {
                            if ($key < 1)
                                $cModel = Category::create(['name' => $category, 'level' => 0, 'slug' => $categorySlug]);
                            else {
                                $cModel = Category::create(['name' => $category, 'slug' => $categorySlug, 'parent_id' => $categories[$key - 1], 'level' => $key]);
                            }
                        }
                        $categories[$key] = $cModel->id;
                    }

                    $replace_from = ["'", "\u202f", "\\xa0US", '&gt;', ''];
                    $replace_to = ["\"", '', '', '>'];
                    $str = str_replace($replace_from, $replace_to, $row['unit_price']);

                    $sku = str_replace('.html', '', (explode('_', $row['product_link_english'])[1]));
                    $rows = json_decode($str, true);



                    if (!count($rows)) {
                        continue;
                    }


                    $prices = [];
                    $used_prices = [];
                    foreach ($rows as $range => $price) {
                        $splits = explode(' ', $range);

                        if (!in_array($price, $used_prices)) {
                            $prices[] = [
                                'unit_price' => str_replace(',', '.', $price),
                                'from' => $splits[1] == '-' ? $splits[0] : $splits[1],
                                'to' => $splits[1] == '-' ? $splits[2] : 1000,
                                'unit' =>  $splits[1] == '-' ? $splits[3] : $splits[2]
                            ];
                            $used_prices[] = $price;
                        }
                    }


                    $existences = 0;

                    foreach ($samePrices as $samePrice) {

                        if ($samePrice === $prices) {
                            $existences++;
                            continue;
                        }
                    }

                    if ($existences) {
                        continue;
                    }

                    $samePrices[] = $prices;

                    $isWholePrice = count($prices) > 1 ? 1 : 0;


                    $productId = Product::create([
                        'name' => $row['name'],
                        'description' => $row['description'],
                        'added_by' => $user->user_type == 'seller' ? 'seller' : 'admin',
                        'user_id' => $user->user_type == 'seller' ? $user->id : User::where('user_type', 'admin')->first()->id,
                        'approved' => $approved,
                        'category_id' => $cModel->id,
                        'brand_id' => $row['brand_id'],
                        'video_provider' => $row['video_provider'],
                        'video_link' => $row['video_link'],
                        'tags' => $row['tags'],
                        'unit_price' => $prices[0]['unit_price'],
                        'unit' => $prices[0]['unit'],
                        'meta_title' => $row['meta_title'],
                        'meta_description' => $row['meta_description'],
                        'colors' => json_encode(array()),
                        'choice_options' => json_encode(array()),
                        'variations' => json_encode(array()),
                        'slug' => preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', strtolower($row['name']))) . '-' . Str::random(5),
                        'thumbnail_img' => $this->downloadThumbnail($selectedImgs[0]),
                        'photos' => $this->downloadGalleryImages(implode(',', $selectedImgs)),
                        'wholesale_product' =>  $isWholePrice,
                        'external_link_1' => $row['product_link_french'],
                        'external_link_2' => $row['product_link_english'],
                        'min_qty' => $prices[0]['from'],
                    ]);

                    $product_stock = ProductStock::create([
                        'product_id' => $productId->id,
                        'qty' => $row['current_stock'],
                        'price' => $prices[0]['unit_price'],
                        'sku' => 'ES-' . $sku,
                        'variant' => '',
                    ]);

                    if ($isWholePrice) {
                        foreach ($prices as $price) {
                            $wholesale_price = new WholesalePrice;
                            $wholesale_price->product_stock_id = $product_stock->id;
                            $wholesale_price->min_qty = $price['from'];
                            $wholesale_price->max_qty = $price['to'];
                            $wholesale_price->price = $price['unit_price'];
                            $wholesale_price->save();
                        }
                    }
                } else {
                    $productId = Product::create([
                        'name' => $row['name'],
                        'description' => $row['description'],
                        'added_by' => $user->user_type == 'seller' ? 'seller' : 'admin',
                        'user_id' => $user->user_type == 'seller' ? $user->id : User::where('user_type', 'admin')->first()->id,
                        'approved' => $approved,
                        'category_id' => $row['category_id'],
                        'brand_id' => $row['brand_id'],
                        'video_provider' => $row['video_provider'],
                        'video_link' => $row['video_link'],
                        'tags' => $row['tags'],
                        'unit_price' => $row['unit_price'],
                        'unit' => $row['unit'],
                        'meta_title' => $row['meta_title'],
                        'meta_description' => $row['meta_description'],
                        'colors' => json_encode(array()),
                        'choice_options' => json_encode(array()),
                        'variations' => json_encode(array()),
                        'slug' => preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', strtolower($row['slug']))) . '-' . Str::random(5),
                        'thumbnail_img' => $this->downloadThumbnail($row['thumbnail_img']),
                        'photos' => $this->downloadGalleryImages(str_replace(';', ',', $row['photos'])),
                    ]);

                    ProductStock::create([
                        'product_id' => $productId->id,
                        'qty' => $row['current_stock'],
                        'price' => $row['unit_price'],
                        'sku' => $row['sku'],
                        'variant' => '',
                    ]);
                }

                $this->total++;

                if ($this->total > 1000)
                    break;
            }



            flash(translate('Products imported successfully'))->success();
        }
    }

    public function model(array $row)
    {
        ++$this->rows;
    }

    public function getRowCount(): int
    {
        return $this->rows;
    }

    public function rules(): array
    {
        return [
            // Can also use callback validation rules
            'unit_price' => function ($attribute, $value, $onFailure) {
                if (!is_numeric($value)) {
                    $onFailure('Unit price is not numeric');
                }
            }
        ];
    }

    public function downloadThumbnail($url)
    {
        try {
            $upload = new Upload;
            // $upload->external_link = $url;
            $filename = basename($url);
            $filenameWithoutExtension = pathinfo($filename, PATHINFO_FILENAME);
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $upload->file_original_name = $filenameWithoutExtension;
            $upload->extension = $extension;
            $upload->file_name = $url;
            $upload->user_id = Auth::user()->id;
            $upload->type = 'image';
            $upload->save();

            return $upload->id;
        } catch (\Exception $e) {
        }
        return null;
    }

    public function downloadGalleryImages($urls)
    {
        $data = array();
        foreach (explode(',', str_replace(' ', '', $urls)) as $url) {
            $data[] = $this->downloadThumbnail($url);
        }
        return implode(',', $data);
    }
    public function chunkSize(): int
    {
        return 500;
    }
}
