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
        Schema::create('store_ditails', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("store_sender_id")->nullable();
            $table->unsignedBigInteger("store_geter_id");
            $table->unsignedBigInteger("user_sender_id");
            $table->unsignedBigInteger("user_geter_id")->nullable();
            $table->unsignedBigInteger("product_id");
            $table->timestamp("getProduct")->nullable();
            $table->foreign("store_sender_id")->on("stores")->references("id")->onDelete('cascade');
            $table->foreign("store_geter_id")->on("stores")->references("id")->onDelete('cascade');
            $table->foreign("user_sender_id")->on("users")->references("id");
            $table->foreign("user_geter_id")->on("users")->references("id");
            $table->foreign("product_id")->on("products")->references("id")->onDelete('cascade');
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
        Schema::dropIfExists('store_ditails');
    }
};
