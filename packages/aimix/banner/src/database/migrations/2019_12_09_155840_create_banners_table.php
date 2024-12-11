<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->integer('original_id')->nullable();
            $table->string('language_abbr', 2)->nullable();
            $table->string('title', 255)->nullable();
            $table->string('image', 255)->nullable();
            $table->string('short_desc', 255)->nullable();
            $table->text('desc', 255)->nullable();
            $table->string('button_text', 255)->nullable();
            $table->string('link', 255)->nullable();
            
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
        Schema::dropIfExists('Banners');
    }
}
