<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->integer('usermeta_id')->nullable();
            $table->integer('delivery_id')->nullable();
            $table->integer('payment_id')->nullable();
            $table->string('code', 20);
            $table->string('status', 30)->default('new');
            $table->boolean('is_paid')->default(0);
            $table->float('price')->default(0);
            $table->json('info')->nullable();
            
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
        Schema::dropIfExists('orders');
    }
}
