<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMpesa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

     private $sql = "INSERT INTO `business_settings` (`id`, `type`, `value`, `created_at`, `updated_at`) VALUES (NULL, 'mpesa', '0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO `business_settings` (`id`, `type`, `value`, `created_at`, `updated_at`) VALUES (NULL, 'flutterwave', '0', CURRENT_TIME(), CURRENT_TIME());

ALTER TABLE `combined_orders` ADD `request` VARCHAR(190) NULL DEFAULT NULL AFTER `grand_total`;
ALTER TABLE `combined_orders` ADD `receipt` VARCHAR(190) NULL DEFAULT NULL AFTER `request`;

INSERT INTO `business_settings` (`id`, `type`, `value`, `created_at`, `updated_at`) VALUES (NULL, 'payfast_sandbox', '1', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO `business_settings` (`id`, `type`, `value`, `created_at`, `updated_at`) VALUES (NULL, 'payfast', '1', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";


    public function up()
    {
        //

        $statements = explode(";", $this->sql);

        foreach($statements as $statement)
            \DB::insert($statement);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
