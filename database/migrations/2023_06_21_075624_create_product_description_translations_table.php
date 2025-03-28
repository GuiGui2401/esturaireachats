<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductDescriptionTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_description_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_description_id')->constrained()->onDelete('cascade');
            $table->string('lang');
            $table->string('description')->nullable();
            $table->string('description_2')->nullable();
            $table->string('description_3')->nullable();
            $table->string('description_4')->nullable();
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
        Schema::dropIfExists('product_description_translations');
    }
}
