<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSearchIndexTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_index', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->unsigned()->nullable();
            $table->integer('search_register_id')->unsigned();
            $table->string('token');
            $table->string('metaphone');
            $table->decimal('score', 11, 4);
            $table->string('entity_type');
            $table->dateTime('entity_created_at');
            $table->dateTime('entity_updated_at');
            $table->timestamps();
        });

        Schema::table('search_index', function (Blueprint $table) {
            $table->foreign('company_id')->references('id')->on('company')->onDelete('cascade');
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
        Schema::dropIfExists('search_index');
    }
}
