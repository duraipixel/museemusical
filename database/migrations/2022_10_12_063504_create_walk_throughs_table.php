<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalkThroughsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('walk_throughs', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('video_url')->nullable();
            $table->string('file_path')->nullable();
            $table->string('description')->nullable();
            $table->string('type')->nullable();
            $table->integer('order_by');
            $table->enum( 'status', ['published', 'unpublished'])->default('published');
            $table->unsignedBigInteger('added_by')->nullable();
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
        Schema::dropIfExists('walk_throughs');
    }
}
