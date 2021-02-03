<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTypeDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('type_devices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index('user_id')->nullable();
            $table->string('name',256);
            $table->integer('type')->comment('1:công tắc 2:cảm biến 3:loai kịch bản');
            $table->integer('feature')->comment('1:công tắc 2:router 3:công tắc rèm 4: ổ cắm 5:nhiệt độ 6: chuyển động 7: rung 8: cửa 9: báo cháy' );
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
        // Insert some type_devices

        $data_model =[
            [
                'name' => 'Công tắc 1 chạm',
                'type' => 1,
                'feature' => 1,
                'status' => 1
            ],
            [
                'name' => 'Công tắc 2 chạm',
                'type' => 1,
                'feature' => 1,
                'status' => 1
            ],
            [
                'name' => 'Công tắc 3 chạm',
                'type' => 1,
                'feature' => 1,
                'status' => 1
            ],
            [
                'name' => 'Công tắc 4 chạm',
                'type' => 1,
                'feature' => 1,
                'status' => 1
            ],
            [
                'name' => 'Router',
                'type' => 1,
                'feature' => 2,
                'status' => 1
            ],
            [
                'name' => 'Công tắc Rèm',
                'type' => 1,
                'feature' => 3,
                'status' => 1
            ],
            [
                'name' => 'Ổ cắm',
                'type' => 1,
                'feature' => 4,
                'status' => 1
            ],
            [
                'name' => 'Cảm biến cửa',
                'type' => 2,
                'feature' => 8,
                'status' => 1
            ],
            [
                'name' => 'Cảm biến chuyển động',
                'type' => 2,
                'feature' => 6,
                'status' => 1
            ],
            [
                'name' => 'Cảm biến nhiệt độ',
                'type' => 2,
                'feature' => 5,
                'status' => 1
            ],
            [
                'name' => 'Cảm biến rung',
                'type' => 2,
                'feature' => 7,
                'status' => 1
            ],
            [
                'name' => 'Cảm biến báo cháy',
                'type' => 2,
                'feature' => 9,
                'status' => 1
            ]
        ];
        foreach ($data_model as $key => $value) {
            DB::table('type_devices')->insert($value);
        }
       
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('type_devices');
    }
}
