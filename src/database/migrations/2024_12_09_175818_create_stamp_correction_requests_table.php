<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStampCorrectionRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stamp_correction_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attendance_id');
            $table->unsignedBigInteger('user_id');
            $table->boolean('is_approved')->default(false)->comment('0: pending approval, 1: approved');
            $table->date('request_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('remarks');
            $table->timestamps();

            $table->foreign('attendance_id')->references('id')->on('attendances');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stamp_correction_requests');
    }
}
