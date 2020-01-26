<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateListFieldTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('list_field', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lists_id')->unsigned();
            $table->string('label')->nullable();
            $table->string('type');
            $table->text('meta')->nullable();
            $table->string('description')->nullable();
            $table->integer('status')->nullable()->default(1);
            $table->timestamps();
        });

        Schema::table('list_field', function (Blueprint $table) {
            $table->foreign('lists_id')->references('id')->on('list')->onDelete('cascade');
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
        Schema::dropIfExists('list_field');
    }
}
