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

            // user_idは不要でattendance_idを持つように変更する
            $table->unsignedBigInteger('user_id');
            $table->boolean('is_approved')->default(false)->comment('0: pending approval, 1: approved');
            $table->date('request_date');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->time('break_time');
            $table->string('reason');
            $table->timestamps();

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
