<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvitationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('inviter_uid')->comment('邀请人UID');
            $table->integer('invitee_uid')->comment('被邀请人UID');
            $table->tinyInteger('status')->default(0)->comment('0:未验证邮箱 1:已验证邮箱并奖励');
            $table->timestamps();
            
            $table->index('inviter_uid');
            $table->index('invitee_uid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invitations');
    }
} 