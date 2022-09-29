<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('brand_name');
            $table->string('brand_logo')->nullable();
            $table->string('brand_banner')->nullable();
            $table->string('short_description')->nullable();
            $table->string('notes')->nullable();
            $table->integer('order_by');
            $table->unsignedBigInteger('added_by')->nullable();
            $table->integer('status')->default(1)->comment('1-active,2-inactive');
            $table->softDeletes();
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
        Schema::dropIfExists('brands');
    }
}
