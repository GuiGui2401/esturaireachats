<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProductDescriptionsAddDescriptionId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_descriptions', function (Blueprint $table) {
            //
            $table->string('external_id')->nullable();
            $table->string('lang')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_descriptions', function (Blueprint $table) {
            //
            $table->dropColumn('external_id');
            $table->dropColumn('lang');
        });
    }
}
