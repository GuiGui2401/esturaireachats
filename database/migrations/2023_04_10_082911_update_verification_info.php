<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateVerificationInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shops', function (Blueprint $table) {
            //
            $table->string('verify_shop_name')->nullable();
            $table->string('email')->nullable();
            $table->string('licence_no')->nullable(); 
            $table->string('full_address')->nullable(); 
            $table->string('tax_papers')->nullable(); 
            $table->string('time_response')->nullable(); 
            $table->string('delivery_response')->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shops', function (Blueprint $table) {
            //
        });
    }
}
