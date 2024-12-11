<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->integer('original_id')->nullable();
            $table->string('language_abbr', 2)->nullable();
            $table->integer('category_id')->default('0');
            $table->integer('brand_id')->nullable();
            $table->string('name', 255)->nullable();
            $table->string('slug', 255)->nullable();
            $table->string('image', 255)->nullable();
            $table->longtext('description')->nullable();
            $table->text('short_description', 500)->nullable();
            $table->float('rating')->nullable();
            $table->json('extras')->nullable();
            $table->json('extras_translatable')->nullable();
            $table->boolean('is_hit')->default('0');
            $table->boolean('is_new')->default('0');
            $table->boolean('is_recommended')->default('0');
            $table->boolean('is_active')->default('1');
            $table->string('meta_title', 255)->nullable();
            $table->string('meta_description', 255)->nullable();
            
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
}
