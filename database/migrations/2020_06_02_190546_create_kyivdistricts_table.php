<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKyivdistrictsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kyivdistricts', function (Blueprint $table) {
            $table->id();

            $table->integer('original_id')->nullable();
            $table->string('language_abbr', 2);
            $table->integer('kyivdistrict_id');
            $table->string('name');
            $table->string('slug');


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
        Schema::dropIfExists('kyivdistricts');
    }
}
