<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Categories extends Migration {

    public function up() {

        Schema::create('categories', function (Blueprint $table) {

            $table->increments('category_id')->unsigned();

            $table->string('category_name');
            $table->string('original_file_name');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('file_type');
            $table->integer('file_size');

            $table->integer('parent_id')->default(0);
            $table->integer('uploaded_by')->default(0);
            $table->timestamp('memo_created_on')->nullable();

            $table->timestamps();
        });

    }

    public function down() {
        Schema::dropIfExists('categories');
    }
}
