<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function(Blueprint $table){
            $table->integer('standard_delivery_time')->after('tax')->nullable();
            $table->integer('express_delivery_time')->after('tax')->nullable();
            $table->integer('height')->after('tax')->nullable();
            $table->integer('length')->after('tax')->nullable();
            $table->integer('width')->after('tax')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function(Blueprint $table){
            $table->dropColumn('standard_delivery_time');
            $table->dropColumn('express_delivery_time');
            $table->dropColumn('height');
            $table->dropColumn('length');
            $table->dropColumn('width');

            
        });
    }
}
