<?php

use Flarum\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

return Migration::createTable(
    'guardian_footprint',
    function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->integer('user_id')->unsigned();

        // request
        $table->ipAddress('ip')->nullable();
        $table->string('hostname')->nullable();
        $table->string('accept_language')->nullable();
        $table->string('user_agent')->nullable();
        $table->boolean('do_not_track')->default(0);
        $table->string('timezone')->nullable();
        $table->string('operating_system')->nullable();
        $table->string('device')->nullable();
        $table->string('browser')->nullable();
        $table->string('robot')->nullable();

        // user preferences
        $table->string('email')->nullable();
        $table->string('locale')->nullable();

        // event information
        $table->string('event');
        $table->integer('score')->default(0);
        $table->integer('since_last_event')->unsigned()->nullable();

        // dates
        $table->timestamp('created_at')->useCurrent();

        // in case user is hard deleted, drop data
        $table->foreign('user_id')
            ->references('id')
            ->on('users')
            ->onDelete('cascade');
    }
);
