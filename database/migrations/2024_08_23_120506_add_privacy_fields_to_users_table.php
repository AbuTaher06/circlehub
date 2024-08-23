<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrivacyFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('post_visibility', ['public', 'friends', 'private'])->default('public');
            $table->enum('friend_list_visibility', ['public', 'friends', 'private'])->default('public');
            $table->enum('profile_visibility', ['public', 'friends', 'private'])->default('public');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['post_visibility', 'friend_list_visibility', 'profile_visibility']);
        });
    }
}
