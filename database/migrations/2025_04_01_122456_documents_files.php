<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DocumentsFiles extends Migration {

    public function up() {

        Schema::create('documents_files', function (Blueprint $table) {

            $table->increments('file_id')->unsigned();

            $table->string('file_name');
            $table->string('original_file_name');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('file_type');
            $table->integer('file_size');

            $table->integer('document_id')->default(0);
            $table->integer('uploaded_by')->default(0);
            $table->timestamp('memo_created_on')->nullable();

            $table->timestamps();
        });

    }

    public function down() {
        Schema::dropIfExists('documents_files');
    }
}
