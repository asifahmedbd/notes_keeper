<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;

use App\Models\Category;

class DocumentController extends Controller {


    public function createMemo() {

        return view('app.dashboard.create-memo', [

        ]);
    }


    private function formatForFancyTree($categories) {

        $tree = [];

        foreach ($categories as $category) {

            $node = [
                'title' => $category->category_name,
                'key' => $category->category_id,
                'folder' => true,
                'extraClasses' => 'category-node',
                'children' => []
            ];

            if ($category->children->isNotEmpty()) {
                $node['children'] = $this->formatForFancyTree($category->children);
            }

            $tree[] = $node;
        }

        return $tree;
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
    
    
    public function create() {

        return view('create-memo', compact('categories'));

    }


    private function buildTree($elements, $parentId = 0) {

        $branch = [];

        foreach ($elements as $element) {
            if ($element->parent_id == $parentId) {
                $children = $this->buildTree($elements, $element->category_id);

                $node = [
                    'title' => $element->category_name,
                ];

                if (!empty($children)) {
                    $node['children'] = $children;
                }

                $branch[] = $node;
            }
        }

        return $branch;
    }


    public function store(Request $request) {

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


    public function getCategoryStructure() {

        $categories = (new Category())->getCategoryTreeWithDocuments();
        $tree = $this->formatForFancyTree($categories);

        $response = [
            'status' => 'success',
            'fancyTree' => $tree,
        ];

        return response()->json($response);

    }

}