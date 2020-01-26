<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->unsigned();
            $table->string('name');
            $table->string('description')->nullable();
            $table->integer('status')->nullable()->default(1)->comment('1 - active, 2 - deleted');
            $table->boolean('all_permissions')->nullable()->default(0);
            $table->timestamps();
        });

        Schema::table('role', function (Blueprint $table) {
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
        Schema::table('role', function (Blueprint $table) {
            $table->dropForeign('role_company_id_foreign');
        });

        Schema::dropIfExists('role');
    }
}
