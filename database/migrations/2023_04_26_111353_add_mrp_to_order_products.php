<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMrpToOrderProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_products', function (Blueprint $table) {
            $table->decimal('mrp_price')->after('price');
            $table->decimal('discount_price')->after('mrp_price');
            $table->decimal('discount_percentage')->after('discount_price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_products', function (Blueprint $table) {
            $table->dropColumn('mrp_price');
            $table->dropColumn('discount_price');
            $table->dropColumn('discount_percentage');
        });
    }
}
