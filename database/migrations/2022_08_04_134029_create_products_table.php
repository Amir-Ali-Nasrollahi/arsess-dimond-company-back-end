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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
	        $table->string('name');
            $table->boolean("is_own")->nullable();
            $table->boolean('is_product')->default(0);
            $table->unsignedBigInteger("category_id");
            $table->foreign("category_id")->on("category_products")->references("id")->onDelete('cascade');
            $table->unsignedBigInteger("user_creator");
            $table->foreign("user_creator")->on("users")->references("id");
            $table->string('code')->unique();
            $table->string("serial")->unique();
            $table->string("imei");
            $table->unsignedBigInteger('store_id')->nullable();
            $table->foreign('store_id')->on('stores')->references('id')->onDelete('cascade');
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
        Schema::dropIfExists('products');
    }
};
