<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->boolean('is_moderated')->default(0);
            $table->string('reviewable_type')->nullable();
            $table->integer('reviewable_id')->nullable();
            $table->string('type')->nullable();
            $table->string('name', 255);
            $table->string('email', 255)->nullable();
            $table->integer('rating')->nullable();
            $table->string('file', 255)->nullable();
            $table->json('images')->nullable();
            $table->text('text');
            
            $table->integer('parent_id')->default(0)->nullable();
            $table->integer('lft')->default(0)->nullable();
            $table->integer('rgt')->default(0)->nullable();
            $table->integer('depth')->default(0)->nullable();
            
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
        Schema::dropIfExists('reviews');
    }
}
