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
        Schema::create('owns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->on('users')->references('id')->onDelete('cascade');
            $table->boolean('addProducts')->default(0);
            $table->boolean('loginProduct')->default(0);
            $table->boolean('authUser')->default(0);
            $table->boolean('authProducts')->default(0);
            $table->boolean("sendProducts")->default(0);
            $table->boolean("makeStore")->default(0);
            $table->boolean('isAdmin')->default(0);
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
        Schema::dropIfExists('owns');
    }
};
