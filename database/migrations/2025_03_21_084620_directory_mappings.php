<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DirectoryMappings extends Migration {

    public function up() {

        Schema::create('directory_mappings', function (Blueprint $table) {

            $table->increments('file_id')->unsigned();

            $table->string('file_name');
            $table->string('original_file_name');
            $table->text('description')->nullable();

            $table->string('file_path');
            $table->string('file_type');
            $table->integer('file_size');

            $table->integer('parent_id')->default(0);
            $table->integer('uploaded_by')->default(0);

            $table->timestamps();
        });

    }

    public function down() {
        Schema::dropIfExists('directory_mappings');
    }
}
