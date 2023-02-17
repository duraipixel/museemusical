<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartShiprocketResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_shiprocket_responses', function (Blueprint $table) {
            $table->id();
            $table->string('cart_token');
            $table->text('rocket_token');
            $table->string('request_type');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->longText('rocket_order_request_data')->nullable();
            $table->longText('rocket_order_response_data')->nullable();
            $table->longText('shipping_charge_request_data')->nullable();
            $table->longText('shipping_charge_response_data')->nullable();
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
        Schema::dropIfExists('cart_shiprocket_responses');
    }
}
