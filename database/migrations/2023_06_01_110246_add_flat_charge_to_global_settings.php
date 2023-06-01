<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFlatChargeToGlobalSettings extends Migration
{

    public function up()
    {
        Schema::table('global_settings', function (Blueprint $table) {
            $table->decimal('flat_charge',8,2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('global_settings', function (Blueprint $table) {
            $table->dropColumn('flat_charge');
        });
    }
}
