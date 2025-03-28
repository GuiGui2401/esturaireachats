<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDatabaseIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    private $tables = [
        'wishlists' => ['product_id', 'user_id'],
        'wholesale_prices' => ['product_stock_id'],
        'wallets' => ['user_id'],
        'users' => ['referred_by'],
        'uploads' => ['user_id'],
        'transactions' => ['user_id'],
        'tickets' => ['user_id'],
        'ticket_replies' => ['ticket_id', 'user_id'],
        'states' => ['country_id'],
        'staff' => ['user_id', 'role_id'],
        'social_credentials' => ['user_id', 'provider_id'],
        'shops' => ['user_id', 'seller_package_id'],
        'sellers' => ['user_id'],
        'seller_withdraw_requests' => ['user_id'],
        'reviews' => ['product_id', 'user_id'],
        'refund_requests' => ['user_id', 'order_id', 'order_detail_id', 'seller_id'],
        'proxypay_payments' => ['order_id', 'reference_id', 'package_id', 'user_id'],
        'products' => ['added_by', 'user_id', 'category_id', 'brand_id'],
        'product_translations' => ['product_id'],
        'product_taxes' => ['product_id', 'tax_id'],
        'product_stocks' => ['product_id'],
        'product_queries' => ['seller_id', 'customer_id', 'product_id'],
        'pickup_points' => ['staff_id'],
        'pickup_point_translations' => ['pickup_point_id'],
        'payments' => ['seller_id'],
        'page_translations' => ['page_id'],
        'orders' => ['user_id', 'combined_order_id', 'guest_id', 'seller_id', 'carrier_id', 'pickup_point_id'],
        'order_details' => ['order_id', 'seller_id', 'product_id', 'pickup_point_id'],
        'home_categories' => ['category_id'],
        'flash_deal_translations' => ['flash_deal_id'],
        'flash_deal_products' => ['flash_deal_id', 'product_id'],
        'firebase_notifications' => ['receiver_id'],
        'delivery_histories' => ['delivery_boy_id', 'order_id'],
        'delivery_boys' => ['user_id'],
        'delivery_boy_payments' => ['user_id'],
        'delivery_boy_collections' => ['user_id'],
        'customer_products' => ['user_id', 'brand_id', 'category_id', 'subcategory_id', 'subsubcategory_id'],
        'customer_product_translations' => ['customer_product_id'],
        'coupons' => ['user_id'],
        'conversations' => ['sender_id', 'receiver_id'],
        'commission_histories' => ['order_id', 'order_detail_id', 'seller_id'],
        'combined_orders' => ['user_id'],
        'cities' => ['state_id'],
        'club_points' => ['user_id', 'order_id'],
        'club_point_details' => ['club_point_id', 'product_id'],
        'categories' => ['parent_id'],
        'carts' => ['owner_id', 'user_id', 'temp_user_id', 'address_id', 'product_id', 'carrier_id'],
        'carrier_ranges' => ['carrier_id'],
        'blogs' => ['category_id'],
        'attribute_values' => ['attribute_id'],
        'attribute_translations' => ['attribute_id']

    ];





    public function up()
    {
        $sm = Schema::getConnection()->getDoctrineSchemaManager();

        foreach ($this->tables as $tableName => $columns) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName, $columns, $sm) {
                $indexesFound = $sm->listTableIndexes($tableName);

                foreach ($columns as $column) {
                    $indexName = "{$tableName}_{$column}_index";
                    if (!array_key_exists($indexName, $indexesFound))
                        $table->index($column, $indexName);
                }
            });
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
        $sm = Schema::getConnection()->getDoctrineSchemaManager();

        foreach ($this->tables as $tableName => $columns) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName, $columns, $sm) {
                $indexesFound = $sm->listTableIndexes($tableName);

                foreach ($columns as $column) {
                    $indexName = "{$tableName}_{$column}_index";
                    if (array_key_exists($indexName, $indexesFound))
                        $table->dropIndex($indexName);
                }
            });
        }
    }
}
