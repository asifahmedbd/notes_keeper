<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;

class DocumentController extends Controller {

    public function createMemo() {

        $categories = DB::table('categories')
            ->leftJoin('users', 'categories.uploaded_by', '=', 'users.id')
            ->select(
                'categories.*',
                'users.name as uploaded_by' // Fetch user name
            )->where('file_type', 'folder')
            ->get();

        $directoryTree = $this->buildTree($categories);

        $flattenedCategories = $this->flattenCategories($directoryTree);
        
        return view('app.dashboard.create-memo', [
            'categories' => $categories,
            'flattenedCategories' => $flattenedCategories,
        ]);
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

        // 🧠 Convert category_id safely from input (hidden field)
        $category_id = (int) $request->input('category_id', 0);

        // 🧑‍💼 Get currently logged-in user
        $uploaded_by = Auth::id();

        // 🧾 Insert document into DB
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

        return redirect()->back()->with('success', 'Document successfully created!');
    }

    public function uploadFile(Request $request)
    {
        \Log::info("File upload request received");

        if ($request->hasFile('upload')) {
            $file = $request->file('upload');

            // Log file details for debugging
            \Log::info("Uploaded file details:", [
                'name' => $file->getClientOriginalName(),
                'type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);

            // Allow PPT and DOC
            $request->validate([
                'upload' => 'required|mimes:jpg,jpeg,png,gif,doc,docx,pdf,zip,txt,ppt,pptx|max:20480' // 20MB limit
            ]);

            // Store file
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('uploads', $filename, 'public');

            \Log::info("File uploaded successfully at path: " . $path);

            return response()->json([
                'url' => asset('storage/' . $path)
            ]);
        }

        \Log::error("No file received for upload.");
        return response()->json(['error' => 'File upload failed.'], 400);
    }


}