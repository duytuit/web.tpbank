<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnToWsSocketRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ws_socket_rooms', function (Blueprint $table) {
            $table->string('Code',256)->nullable()->change();
            $table->string('Hub',256)->nullable()->change();
            $table->string('Name',256)->nullable()->change();
            $table->longText('Users',256)->nullable()->change();
            $table->text('Owner',256)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ws_socket_rooms', function (Blueprint $table) {
            //
        });
    }
}
