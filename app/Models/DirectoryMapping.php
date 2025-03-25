<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DirectoryMapping extends Model {

    protected $table = 'directory_mappings';
    protected $primaryKey = 'file_id';

    protected $fillable = [
        'file_name',
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

}
