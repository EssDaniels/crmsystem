<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('importers', function (Blueprint $table) {

            $table->increments('id');
            $table->string('work_order_number');
            $table->string('category');
            $table->string('fin_loc');
            $table->string('priority');
            $table->dateTime('received_date');
            $table->string('external_id');
            $table->dateTime('updated_at');
            $table->dateTime('created_at');
        });
    }
    public function down()
    {
        Schema::drop('importers');
    }
};
