<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserThirdTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_third', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->unsigned()->comment('用户ID');
            $table->string('platform', 20)->comment('平台名称');
            $table->string('openid', 64)->comment('第三方平台用户ID');
            $table->string('nickname', 64)->nullable()->comment('第三方平台昵称');
            $table->string('avatar', 255)->nullable()->comment('头像URL');
            $table->text('access_token')->nullable()->comment('访问令牌');
            $table->text('refresh_token')->nullable()->comment('刷新令牌');
            $table->integer('expires_at')->unsigned()->nullable()->comment('令牌过期时间');
            $table->timestamps();
            
            $table->unique(['platform', 'openid']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_third');
    }
}
