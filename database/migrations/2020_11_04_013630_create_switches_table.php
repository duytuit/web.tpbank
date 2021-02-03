<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSwitchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('switches', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('device_id')->index('device_id')->nullable();
            $table->integer('user_id')->index('user_id')->nullable();
            $table->string('name',256);
            $table->text('image')->nullable();
            $table->tinyInteger('notify')->default(1)->comment('đăng ký nhận thông báo');
            $table->string('interval')->index('interval')->nullable()->comment('thời gian nhận thông báo');
            $table->tinyInteger('action')->default(1)->comment('trạng thái thiết bị');
            $table->integer('type_id')->index('type_id')->nullable()->comment('kiểu hiển thị');
            $table->tinyInteger('status')->default(1)->comment('ẩn / hiện');
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
        Schema::dropIfExists('switches');
    }
}
