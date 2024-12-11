<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attributes', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->string('language_abbr', 2)->nullable();
            $table->string('name', 255);
            $table->string('slug', 255);
            $table->integer('attribute_group_id')->nullable();
            $table->text('icon', 1000)->nullable();
            $table->text('description', 1000)->nullable();
            $table->string('si', 50)->nullable();
            $table->longtext('default_value')->nullable();
            $table->json('values')->nullable();
            $table->enum('type', ['checkbox','radio','number','string','longtext','color','colors'])->default('checkbox');
            $table->boolean('is_important')->default(0);
            $table->boolean('is_active')->default(1);
            $table->boolean('in_filters')->default(1);
            $table->boolean('in_properties')->default(1);
            
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
        Schema::dropIfExists('attributes');
    }
}
