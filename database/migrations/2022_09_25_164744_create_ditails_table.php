<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ditails', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bag_id');
            $table->foreign('bag_id')->on('bags')->references('id')->onDelete('cascade');
	        $table->unsignedBigInteger('product_id');
            $table->foreign("product_id")->on('products')->references('id')->onDelete('cascade');
            $table->boolean("status");
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
        Schema::dropIfExists('ditails');
    }
};
