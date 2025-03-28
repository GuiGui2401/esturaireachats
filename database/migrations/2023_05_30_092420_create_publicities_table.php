<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePublicitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publicities', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable(); 
            $table->string('details')->nullable();
            $table->string("link")->nullable();
            $table->string("image_path")->nullable();
            $table->boolean("state")->default(false);
            $table->string("position")->nullable();
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
        Schema::dropIfExists('publicities');
    }
}
