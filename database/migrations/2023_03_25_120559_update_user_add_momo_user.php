<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUserAddMomoUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->string('momo_reference_number')->nullable();
            $table->string('momo_user')->nullable(); 
            $table->string('momo_user_key')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropColumn('momo_reference_number')->nullable();
            $table->dropColumn('momo_user');
            $table->dropColumn('momo_user_key');
        });
    }
}
