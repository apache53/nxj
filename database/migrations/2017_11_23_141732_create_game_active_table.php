<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameActiveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        for ($i=0;$i<25;$i++){
            Schema::create('h5_yun_active_'.$i,function(Blueprint $table){
                $table->integer('yun_uid')->comment('云uid');
                $table->integer('game_id')->default(0)->comment('游戏id');
                $table->integer('platform_id')->default(0)->comment('平台id');
                $table->integer('platform_user_id')->default(0)->comment('平台uid');
                $table->string('platform_user_name')->default('')->comment('平台用户名');
                $table->integer('first_login_time')->default(0)->comment('第一次登录时间');
                $table->integer('last_login_time')->default(0)->comment('最后登录时间');
                $table->integer('first_pay_time')->default(0)->comment('第一次充值时间');
                $table->integer('last_pay_time')->default(0)->comment('最后充值时间');
                $table->string('first_login_ip')->default('')->comment('第一次登录ip');
                $table->string('last_login_ip')->default('')->comment('最后登录ip');
                $table->primary(['yun_uid','game_id']);
                $table->index(['platform_id','platform_user_id']);
                $table->index(['first_login_time']);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        for ($i=0;$i<25;$i++){
            Schema::dropIfExists('h5_yun_active_'.$i);
        }
    }
}
