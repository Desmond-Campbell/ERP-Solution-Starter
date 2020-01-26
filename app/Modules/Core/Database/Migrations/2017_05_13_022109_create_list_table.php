<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('list', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->unsigned()->nullable();
            $table->string('name', 64);
            $table->string('slug', 64);
            $table->integer('list_category_id')->unsigned()->nullable();
            $table->string('description')->nullable();
            $table->boolean('global')->nullable()->default(0);
            $table->timestamps();
        });

        Schema::table('list', function (Blueprint $table) {
            $table->foreign('company_id')->references('id')->on('company')->onDelete('cascade');
            $table->foreign('list_category_id')->references('id')->on('list_category')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('list');
    }
}
