<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 128)->unique();
            $table->string('type')->nullable()->default(1)->comment('1 - Company, 2 - Partnership, 3 - Sole Proprietor, 4 - Individual');
            $table->string('tax_id', 128)->nullable();
            $table->string('licence_number', 128)->nullable();
            $table->integer('employee_count')->nullable()->default(0);
            $table->dateTime('employee_count_timestamp')->nullable()->comment('Last time employee count was updated.');
            $table->integer('status')->nullable()->default(1)->comment('1 - active, 2 - closed, 3 - deleted');
            $table->mediumText('directors')->nullable();
            $table->mediumText('managers')->nullable();
            $table->mediumText('phone_numbers')->nullable();
            $table->mediumText('emails')->nullable();
            $table->mediumText('addresses')->nullable();
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
        Schema::dropIfExists('company');
    }
}
