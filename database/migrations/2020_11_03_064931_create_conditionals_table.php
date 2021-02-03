<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConditionalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conditionals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('switch_id')->index('switch_id')->nullable();
            $table->integer('user_id')->index('user_id')->nullable();
            $table->string('interval')->index('interval')->nullable()->comment('thời gian để bật / tắt');
            $table->string('name',256);
            $table->integer('type_id')->index('type_id')->nullable();
            $table->tinyInteger('action')->comment('trạng thái thiết bị');
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('conditionals');
    }
}
