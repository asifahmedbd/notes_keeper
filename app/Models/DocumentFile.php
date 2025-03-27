<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentFile extends Model {

    protected $table = 'documents_files';
    protected $primaryKey = 'file_id';

    protected $fillable = [
        'file_name',
        'original_file_name',
        'description',
        'file_path',
        'file_type',
        'file_size',
        'document_id',
        'uploaded_by',
        'memo_created_on',
    ];

}
