<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
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


    public function store(Request $request)
    {
        // 🔒 Validate incoming form data
        $request->validate([
            'subject' => 'required|string|max:255',
            'document_text' => 'required|string',
            'document_status' => 'required|string|max:10',
            'unit' => 'required|string|max:255',
            'keywords' => 'nullable|string|max:255',
            'editors' => 'required|array',
            'readers' => 'required|array',
        ]);

        $category_id = (int) $request->input('category_id', 0);

        $uploaded_by = Auth::id();

        DB::table('documents')->insert([
            'document_subject' => $request->input('subject'),
            'document_text' => $request->input('document_text'), // ← CKEditor content
            'doc_status' => $request->input('document_status'),
            'doc_unit' => $request->input('unit'),
            'doc_keywords' => $request->input('keywords'),
            'category_id' => $category_id,
            'uploaded_by' => $uploaded_by,
            'memo_created_on' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Optional: Store editors/readers in separate pivot tables if needed later

        return redirect()->route('dashboard')->with('success', 'Document successfully created!');
    }

    public function uploadFile(Request $request)
    {
        \Log::info("File upload request received");

        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $categoryId = $request->input('category_id', 0);

            // Allow PPT and DOC
            $request->validate([
                'upload' => 'required|mimes:jpg,jpeg,png,gif,doc,docx,pdf,zip,txt,ppt,pptx|max:20480' // 20MB limit
            ]);

            // Store file
            $categoryPath = $this->getCategoryFolderPath($categoryId);
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = 'notes_data/' . $categoryPath;
            //$path = $file->storeAs('uploads', $filename, 'public');

            // Ensure directory exists
            if (!file_exists(public_path($path))) {
                mkdir(public_path($path), 0777, true);
            }

            $file->move(public_path($path), $filename);

            return response()->json([
                'uploaded' => 1,
                'fileName' => $filename,
                'url' => asset($path . '/' . $filename)
            ]);
        }

        \Log::error("No file received for upload.");
        return response()->json(['error' => 'File upload failed.'], 400);
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

    public function getCategoryFolderPath($categoryId)
    {
        $segments = [];

        while ($categoryId && $category = Category::find($categoryId)) {
            array_unshift($segments, $this->sanitizeFolderName($category->category_name));
            $categoryId = $category->parent_id;
        }

        $fullPath = implode(DIRECTORY_SEPARATOR, $segments);
        return str_replace('\\', '/', $fullPath); // Normalize for web
    }

    private function sanitizeFolderName($name)
    {
        return preg_replace('/[^\w\- ]+/u', '', $name); // Removes special chars, allows letters, numbers, dash, space
    }

}