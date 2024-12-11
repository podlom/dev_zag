<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();

            $table->string('email', 255);
            $table->string('region')->nullable();
            $table->json('latlng')->nullable();
            $table->integer('radius')->nullable();
            $table->boolean('news')->default(0);
            $table->boolean('adding')->default(0);
            $table->boolean('status')->default(0);
            $table->boolean('price')->default(0);
            $table->json('types')->nullable();

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
        Schema::dropIfExists('subscription');
    }
}
