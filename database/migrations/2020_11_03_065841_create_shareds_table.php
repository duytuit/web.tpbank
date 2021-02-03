<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSharedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shareds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index('user_id')->nullable()->comment('tài khoản chia sẻ');
            $table->integer('shared_user_id')->index('shared_user_id')->nullable()->comment('tài khoản nhận chia sẻ');
            $table->integer('switche_id')->index('switche_id')->nullable()->comment('công tắc chia sẻ');
            $table->string('name',256);
            $table->string('description',256);
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
        Schema::dropIfExists('shareds');
    }
}
