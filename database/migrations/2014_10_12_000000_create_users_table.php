<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration {

    public function up() {

        Schema::create('users', function (Blueprint $table) {

            $table->increments('id')->unsigned();

            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role')->default('admin');

            $table->string('user_image')->default("default_user.png");
            $table->enum('user_status', [0, 1])->default(1);

            $table->string('locale')->default("en");
            $table->enum('dark_mode', [0, 1])->default(0);
            $table->string('timezone')->default("Asia/Dhaka");

            $table->rememberToken();
            $table->timestamps();
        });

    }

    public function down() {
        Schema::dropIfExists('users');
    }
}
