<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class DocumentController extends Controller {

    public function createMemo()
    {
        $categories = DB::table('directory_mappings')
            ->leftJoin('users', 'directory_mappings.uploaded_by', '=', 'users.id')
            ->select(
                'directory_mappings.*',
                'users.name as uploaded_by' // Fetch user name
            )->where('file_type', 'folder')
            ->get();

        $directoryTree = $this->buildTree($categories);

        $flattenedCategories = $this->flattenCategories($directoryTree);
        
        return view('app.dashboard.create-memo', compact('flattenedCategories'));
    }

    function flattenCategories($categories, $prefix = '') {
        $flattened = [];
    
        foreach ($categories as $category) {
            $title = $prefix . $category['title'];
            $flattened[] = $title;
    
            if (!empty($category['children'])) {
                $flattened = array_merge($flattened, $this->flattenCategories($category['children'], $title . ' > '));
            }
        }
    
        return $flattened;
    }
    
    
    public function create()
    {

        return view('create-memo', compact('categories'));
    }

    private function buildTree($elements, $parentId = 0) {

        $branch = [];

        foreach ($elements as $element) {
            if ($element->parent_id == $parentId) {
                $children = $this->buildTree($elements, $element->file_id);

                $node = [
                    'title' => $element->file_name,
                ];

                if (!empty($children)) {
                    $node['children'] = $children;
                }

                $branch[] = $node;
            }
        }

        return $branch;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'category' => 'required|exists:categories,id',
            'document_text' => 'required|string',
            'files.*' => 'nullable|file|max:2048', // Validate each file
            'uploader_info' => 'required|string|max:255',
            'comments' => 'nullable|string',
        ]);

        // Handle file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $file->store('documents'); // Save files to the 'documents' directory
            }
        }

        // Save document details to the database
        Document::create($validated);

        return redirect()->route('documents.index')->with('success', 'Document created successfully!');
    }

}