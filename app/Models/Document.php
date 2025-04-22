<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model {

    protected $table = 'documents';
    protected $primaryKey = 'document_id';

    protected $fillable = [
        'document_subject',
        'document_text',
        'doc_status',
        'doc_unit',
        'doc_keywords',
        'category_id',
        'uploaded_by',
    ];

    public function userDetails() {
        return $this->belongsTo('App\Models\User', 'uploaded_by', 'id');
    }

    public function category(){
        return $this->belongsTo(Category::class, 'category_id');
    }

}
