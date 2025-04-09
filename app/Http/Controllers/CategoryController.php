<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use Auth;

use App\Models\Category;

class CategoryController extends Controller {


    public function addCategory(Request $request) {

        $created_by = Auth::user()->id;
        $data = $request->input('params');

        if ($data['parent_id'] == 0) {
            $file_path =  'notes_data/' . $data['category_name'];
            createFolderIfNotExists($file_path);
        }
        else {
            $parentCategory = Category::find($data['parent_id']);
            $file_path = $parentCategory->file_path . '/' . $data['category_name'];
            createFolderIfNotExists($file_path);
        }

        $category = new Category();

        $category->category_name = $data['category_name'];
        $category->original_file_name = $data['category_name'];
        $category->file_path = $file_path;
        $category->file_type = 'folder';
        $category->file_size = 0;
        $category->parent_id = $data['parent_id'];
        $category->uploaded_by = $created_by;
        $category->memo_created_on = date('Y-m-d H:i:s');
        $category->save();

        $category_id = $category->category_id;

        $response = [
            'status' => 'success',
            'category_id' => $category_id,
            'category' => $category,
        ];

        return response()->json($response);
    }

    public function getFolderStructure()
    {
        $categories = Category::all();
        $folderStructure = [];

        foreach ($categories as $category) {
            $folderStructure[] = [
                'id' => $category->id,
                'parent' => $category->parent_id == 0 ? '#' : $category->parent_id,
                'text' => $category->name,
            ];
        }

        return response()->json($folderStructure);
    }

}
