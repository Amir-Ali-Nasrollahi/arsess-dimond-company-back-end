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
        Schema::create('describes', function (Blueprint $table) {
            $table->id();
            $table->boolean("status")->nullable();
            $table->string("user_sender");
            $table->string("user_geter")->nullable();
            $table->string("store_sender")->nullable();
            $table->string("store_geter")->nullable();
            $table->morphs("describe");
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
        Schema::dropIfExists('describes');
    }
};
