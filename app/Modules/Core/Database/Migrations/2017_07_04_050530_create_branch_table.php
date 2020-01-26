<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBranchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branch', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->unsigned();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('address')->nullable();
            $table->string('manager')->nullable();
            $table->integer('status')->nullable()->default(1)->comment('1 - active, 2 - closed, 3 - deleted');
            $table->timestamps();
        });

        Schema::table('branch', function (Blueprint $table) {
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
        Schema::table('branch', function (Blueprint $table) {
            $table->dropForeign('branch_company_id_foreign');
        });

        Schema::dropIfExists('branch');
    }
}
