<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductFunctionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_functions', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id'); 
            $table->string('name');
            $table->string('value');
            $table->string('name_id')->nullable(); 
            $table->string('attr_id')->nullable();
            $table->string('lang');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_functions');
    }
}
