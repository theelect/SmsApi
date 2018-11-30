<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('user_id');
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('othername')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->integer('month')->nullable();
            $table->integer('day')->nullable();
            $table->datetime('birth_date')->nullable();
            $table->integer('age_bracket_id')->nullable();
            $table->enum('marital_status', ['unspecified', 'single', 'married'])->default('unspecified')->nullable();
            $table->integer('state_id')->nullable();
            $table->string('state')->nullable();
            $table->string('lga')->nullable();
            $table->string('ward')->nullable();
            $table->string('vin')->nullable();
            $table->string('occupation')->nullable();
            $table->integer('local_id')->nullable();
            $table->enum('language', ['english', 'yoruba', 'igbo', 'hausa'])->default('english')->nullable();
            $table->enum('gender', ['male', 'female', 'none'])->default('none')->nullable();
            $table->enum('status', ['active', 'inactive']);
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
        Schema::dropIfExists('contacts');
    }
}
