<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setting', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->nullable()->unsigned();
            $table->string('key', 128)->unique();
            $table->text('value');
            $table->text('category');
            $table->timestamps();
        });

        Schema::table('setting', function (Blueprint $table) {
            $table->foreign('company_id')->references('id')->on('company')->onDelete('cascade
                ');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('setting', function (Blueprint $table) {
            $table->dropForeign('setting_company_id_foreign');
        });

        Schema::dropIfExists('setting');
    }
}
