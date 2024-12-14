<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestBreakTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_break_times', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stamp_correction_request_id');
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();

            $table->foreign('stamp_correction_request_id')->references('id')->on('stamp_correction_requests')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('request_break_times');
    }
}
