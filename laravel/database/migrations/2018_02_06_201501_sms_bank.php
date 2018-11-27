<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SmsBank extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_bank', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('message_id');
            $table->ipAddress('phone');
            $table->text('sms')->nullable();
            $table->enum('status', ['queued', 'sent'])->default('queued');
            $table->text('response')->nullable();
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
        //
    }
}
