<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMinAndMaxPricesOnProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function(Blueprint $table){
            $table->float('min_price')->after('unit_price')->nullable();
            $table->float('max_price')->after('min_price')->nullable();
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
            $table->dropColumn('min_price');
            $table->dropColumn('max_price');
        });
    }
}
