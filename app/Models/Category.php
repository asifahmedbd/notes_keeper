<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model {

    protected $table = 'categories';
    protected $primaryKey = 'category_id';

    protected $fillable = [
        'category_name',
        'original_file_name',
        'description',
        'file_path',
        'file_type',
        'file_size',
        'parent_id',
        'uploaded_by',
    ];

    public function userDetails() {
        return $this->belongsTo('App\Models\User', 'uploaded_by', 'id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'category_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->with('documents', 'children');
    }

    
    public function getCategoryTreeWithDocuments()
    {
        return $categories = Category::where('parent_id', 0)
        ->with('documents', 'children')
        ->get();
    
    }

}
