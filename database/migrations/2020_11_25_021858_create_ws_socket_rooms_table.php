<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWsSocketRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ws_socket_rooms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('Code',256);
            $table->string('Hub',256);
            $table->string('Name',256);
            $table->longText('Users',256);
            $table->text('Owner',256);
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
        Schema::dropIfExists('ws_socket_rooms');
    }
}
