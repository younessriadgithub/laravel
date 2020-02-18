<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('categorie_id');
            $table->string('title');
            $table->string('titleurl');
            $table->string('description');
            $table->integer('area_size');
            $table->double('price');
            $table->string('near_city');
            $table->string('address');
            $table->string('phone');
            $table->string('image', 250)->nullable();
            $table->boolean('balcony');
            $table->boolean('garage');
            $table->boolean('enable');
            $table->boolean('sale');
            $table->boolean('rent');
            $table->date('date_build');
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
        Schema::dropIfExists('properties');
    }
}
