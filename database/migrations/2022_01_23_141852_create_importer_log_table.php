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
        Schema::create('importer_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            $table->string('run_at');
            $table->string('entries_processed');
            $table->string('entries_created');
            $table->dateTime('updated_at');
            $table->dateTime('created_at');
        });
    }
    public function down()
    {
        Schema::drop('importer_log');
    }
};
