<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreLocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_location', function (Blueprint $table) {
            $table->id();
            $table->string( 'name' );
            $table->string( 'email' )->nullable();
            $table->string( 'mobile_no' )->nullable();
            $table->string( 'gstin_no' )->nullable();
            $table->string( 'address' )->nullable();
            $table->string( 'country' )->nullable();
            $table->string( 'state' )->nullable();
            $table->string( 'city' )->nullable();
            $table->enum( 'is_primary', [0,1] );
            $table->enum( 'is_shipping_location', [0,1] );
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
        Schema::dropIfExists('store_location');
    }
}
