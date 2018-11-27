<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            
            $table->increments('id');
            $table->integer('user_id');
            $table->text('body');
            $table->enum('recipients_type', ['all', 'customize', 'custom'])->default('all');
            $table->integer('customized_recipients_id')->nullable();
            $table->text('custom_recipients')->nullable();
            $table->boolean('scheduled')->default(false);
            $table->date('schedule_date')->nullable();
            $table->time('schedule_time')->nullable();
            $table->enum('repitition_type', ['none', 'daily', 'weekly', 'monthly', 'quarterly', 'yearly'])->default('none');
            $table->string('repitition_value')->nullable();
            $table->integer('sms_units')->nullable()->default(0);
            $table->string('sender_name')->nullable();
            $table->enum('status', ['pending', 'queued', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('new_message');
    }
}
