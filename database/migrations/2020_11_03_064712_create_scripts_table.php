<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScriptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scripts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('conditional_id')->index('conditional_id')->nullable();
            $table->integer('user_id')->index('user_id')->nullable();
            $table->integer('switch_id')->index('switch_id')->nullable();
            $table->string('name',256);
            $table->tinyInteger('command')->comment('lệnh tắt / bật all');
            $table->tinyInteger('action')->comment('trạng thái thiết bị');
            $table->tinyInteger('go_home')->comment('hiển thị trên trang chủ');
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
        Schema::dropIfExists('scripts');
    }
}
