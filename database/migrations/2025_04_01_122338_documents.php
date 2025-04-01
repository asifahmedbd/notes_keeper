<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Documents extends Migration {

    public function up() {

        Schema::create('documents', function (Blueprint $table) {

            $table->increments('document_id')->unsigned();

            $table->string('document_subject');
            $table->text('document_text')->nullable();
            $table->string('doc_status', 5)->nullable();
            $table->integer('doc_unit')->nullable();
            $table->string('doc_keywords', 10)->nullable();

            $table->integer('category_id')->default(0);
            $table->integer('uploaded_by')->default(0);
            $table->timestamp('memo_created_on')->nullable();

            $table->timestamps();
        });

    }

    public function down() {
        Schema::dropIfExists('documents');
    }
}
