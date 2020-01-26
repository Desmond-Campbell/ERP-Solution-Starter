<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateListItemDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('list_item_data', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('list_item_id')->unsigned();
            $table->integer('list_field_id')->unsigned();
            $table->string('value_text')->nullable();
            $table->text('value_paragraph')->nullable();
            $table->integer('value_integer')->nullable();
            $table->decimal('value_decimal')->nullable();
            $table->timestamps();
        });

        Schema::table('list_item_data', function (Blueprint $table) {
            $table->foreign('list_field_id')->references('id')->on('list_field')->onDelete('cascade');
            $table->foreign('list_item_id')->references('id')->on('list_item')->onDelete('cascade');
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

        Schema::dropIfExists('list_item_data');
    }
}
