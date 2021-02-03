<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnPasswordAndTypeInVerifyCodeOtps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('verify_code_otps', function (Blueprint $table) {
              $table->string('password')->nullable();
              $table->string('type')->nullable();
              $table->integer('user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('verify_code_otps', function (Blueprint $table) {
              $table->dropColumn('password');
              $table->dropColumn('type');
        });
    }
}
