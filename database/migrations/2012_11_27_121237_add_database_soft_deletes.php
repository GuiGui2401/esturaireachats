<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Testing\Constraints\SoftDeletedInDatabase;

class AddDatabaseSoftDeletes extends Migration
{

    private  $tables = [

        'zones',
        'wishlists',
        // 'wholesale_prices' => ['prodct_stock_id'=>['deleted_at'],
        'wallets',
        'users',
        'uploads',
        'translations',
        'transactions',
        'ticket_replies',
        'tickets',
        'taxes',
        'subscribers',
        'states',
        'staff',
        // 'social_credentials' => ['user_id'=>['deleted_at'], provider_id'=>['deleted_at'],
        'shops',
        'seller_withdraw_requests',
        'sellers',
        'searches',
        'role_translations',
        'role_has_permissions',
        'roles',
        'reviews',
        // 'refund_requests' => ['user_id'=>['deleted_at'], 'order_id'=>['deleted_at'], 'order_detail_id'=>['deleted_at'] 'seller_id'=>['deleted_at'],
        'proxypay_payments',
        'product_translations',
        'product_taxes',
        'product_stocks',
        'product_queries',
        'products',
        'pickup_point_translations',
        'pickup_points',
        'personal_access_tokens',
        'permissions',
        'payments',
        'payku_transactions',
        'payku_payments',
        'password_resets',
        'page_translations',
        'pages',
        'order_details',
        'orders',
        'notifications',
        'model_has_roles',
        'model_has_permissions',
        'migrations',
        'messages',
        'languages',
        'home_categories',
        'flash_deal_translations',
        'flash_deal_products',
        'flash_deals',
        'firebase_notifications',
        // 'delivery_histories' => ['delivery_boy_id'=>'deleted_at, 'order_id'=>['deleted_at'],
        // 'delivery_boys' =>['deleted_at'],
        // 'delivery_boy_payments' =>['deleted_at'],
        // 'delivery_boy_collections' =>['deleted_at'],
        'customer_product_translations',
        'customer_products',
        'customer_package_translations',
        'customer_package_payments',
        'customer_packages',
        'currencies',
        'coupon_usages',
        'coupons',
        'countries',
        'conversations',
        'commission_histories',
        'combined_orders',
        'colors',
        'city_translations',
        'cities',
        // 'club_points' => ['user_id'=>'deleted_at, 'order_id'=>['deleted_at'],
        // 'club_point_details' => ['club_point_id'=>['deleted_at'],'product_id'=>['deleted_at'],
        'categories',
        'carts',
        'carrier_range_prices',
        'carrier_ranges',
        'carriers',
        'capacities',
        'capacity_translations',
        //'business_settings',
        'brands',
        'brand_translations',
        'category_translations',
        'blog_categories',
        'blogs',
        'attribute_values',
        'attribute_translations',
        'attribute_category',
        'attributes',
        'app_translations',
        'addresses',
        'addons',
    ];

    public function up()
    {
        $sm = Schema::getConnection()->getDoctrineSchemaManager();

        foreach ($this->tables as $tableName) {

            if (!Schema::hasColumn($tableName, 'deleted_at')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->softDeletes();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        foreach ($this->tables as $tableName) {

            if (Schema::hasColumn($tableName, 'deleted_at')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropSoftDeletes();
                });
            }
        }
    }
}
