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
        Schema::create('history', function (Blueprint $table) {
            $table->id();
            $table->string('action_type');
            $table->string('changes');
            $table->string('columnname');
            $table->string('date_created');
            $table->dateTime('person_id');
            $table->string('record_id');
            $table->string('related_record_id');
            $table->string('related_tablename');
            $table->dateTime('tablename');
            $table->string('value_from');
            $table->string('value_to');
            $table->dateTime('updated_at');
            $table->dateTime('created_at');
        });
    }
    public function down()
    {
        Schema::drop('history');
    }
};
