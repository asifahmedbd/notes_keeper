<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

use App\Models\Category;
use App\Models\Document;

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
        $document_subject = $request->input('subject');
        $document_text = $request->input('document_text');
        $uploaded_by = Auth::id();

        // 🗂 Get path to category and create final folder
        $categoryPath = $this->getCategoryFolderPath($category_id); // e.g. "Category1/Subcat2"
        $basePath = 'notes_data/' . $categoryPath;
        $finalFolderPath = $basePath . '/' . $document_subject;

        if (!file_exists(public_path($finalFolderPath))) {
            mkdir(public_path($finalFolderPath), 0777, true);
        }

        // 💾 Create the document first and get ID
        $documentId = DB::table('documents')->insertGetId([
            'document_subject' => $document_subject,
            'document_text' => '', // Will update later
            'doc_status' => $request->input('document_status'),
            'doc_unit' => $request->input('unit'),
            'doc_keywords' => $request->input('keywords'),
            'category_id' => $category_id,
            'uploaded_by' => $uploaded_by,
            'memo_created_on' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 🧠 Parse and move files from temp_upload, and update document_text
        $updatedText = preg_replace_callback('/href="([^"]*\/temp_upload\/([^"]+))"/', function ($matches) use ($finalFolderPath, $documentId, $uploaded_by) {
            //$fileName = $matches[2];
            $fileName = ltrim($matches[2], '/'); // Remove leading slash if present
            $tempPath = public_path('temp_upload/' . $fileName);
            $finalPath = public_path($finalFolderPath . '/' . $fileName);

            Log::info('Checking if file exists at: ' . $tempPath);
            Log::info('Does file exist? ' . (File::exists($tempPath) ? 'Yes' : 'No'));


            if (file_exists($tempPath)) {
                Log::info($fileName);
                try {
                    rename($tempPath, $finalPath);
                    Log::info("Moved successfully.");
                } catch (\Exception $e) {
                    Log::error("Rename failed: " . $e->getMessage());
                }
                

                $fileSize = File::size($finalPath);
                $fileType = File::mimeType($finalPath);
                $filePath = $finalFolderPath . '/' . $fileName;

                DB::table('documents_files')->insert([
                    'file_name' => $fileName,
                    'original_file_name' => $fileName, // Replace with original if stored separately
                    'description' => null,
                    'file_path' => $filePath,
                    'file_type' => $fileType,
                    'file_size' => $fileSize,
                    'document_id' => $documentId,
                    'uploaded_by' => $uploaded_by,
                    'memo_created_on' => now(),
                ]);
            }

            $newUrl = asset($finalFolderPath . '/' . $fileName);
            return 'href="' . $newUrl . '"';
        }, $document_text);

        // 🖋 Update the document text now
        DB::table('documents')->where('document_id', $documentId)->update([
            'document_text' => $updatedText,
        ]);

        // Optional: Store editors/readers in pivot tables if needed

        return redirect()->route('dashboard')->with('success', 'Document successfully created!');
    }


    public function uploadFile(Request $request) {

        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $categoryId = $request->input('category_id', 0);
            
            $ext = strtolower($file->getClientOriginalExtension());
            $allowedExts = ['jpg','jpeg','png','gif','doc','docx','pdf','zip','txt','ppt','pptx'];
            if (!in_array($ext, $allowedExts)) {
                return response()->json([
                    'uploaded' => 0,
                    'error' => ['message' => 'Invalid file extension.']
                ], 422);
            }

            // Store file
            $categoryPath = $this->getCategoryFolderPath($categoryId);
            $filename = time() . '_' . $file->getClientOriginalName();
            //$path = 'notes_data/' . $categoryPath;
            $path = '/temp_upload/';

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


    public function getCategoryFolderPath($categoryId) {

        $segments = [];

        while ($categoryId && $category = Category::find($categoryId)) {
            array_unshift($segments, $this->sanitizeFolderName($category->category_name));
            $categoryId = $category->parent_id;
        }

        $fullPath = implode(DIRECTORY_SEPARATOR, $segments);
        return str_replace('\\', '/', $fullPath); // Normalize for web
    }


    private function sanitizeFolderName($name) {
        return preg_replace('/[^\w\- ]+/u', '', $name); // Removes special chars, allows letters, numbers, dash, space
    }

    public function edit($id) {
        
        $memo = Document::with(['userDetails', 'category'])->findOrFail($id);
        //dd($memo);
        return view('app.dashboard.edit-memo', compact('memo'));
    }

    public function update(Request $request){
        //dd($request->all());
        $request->validate([
            'subject' => 'required|string|max:255',
            'document_text' => 'required|string',
            'document_status' => 'required|string|max:10',
            'unit' => 'required|string|max:255',
            'keywords' => 'nullable|string|max:255',
            'editors' => 'required|array',
            'readers' => 'required|array',
        ]);

        $documentId = $request->input('document_id');
        $document = DB::table('documents')->where('document_id', $documentId)->first();

        if (!$document) {
            return redirect()->back()->with('error', 'Document not found.');
        }

        $category_id = (int) $request->input('category_id', 0);
        $document_subject = $request->input('subject');
        $document_text = $request->input('document_text');
        $uploaded_by = Auth::id();

        // Calculate folder paths
        $oldFolderPath = 'notes_data/' . $this->getCategoryFolderPath($document->category_id) . '/' . $document->document_subject;
        $newFolderPath = 'notes_data/' . $this->getCategoryFolderPath($category_id) . '/' . $document_subject;

        $oldAbsolutePath = public_path($oldFolderPath);
        $newAbsolutePath = public_path($newFolderPath);

        // Move existing folder if changed
        if ($oldFolderPath !== $newFolderPath) {
            if (!file_exists($newAbsolutePath)) {
                mkdir($newAbsolutePath, 0777, true);
            }

            $movedFiles = [];

            if (file_exists($oldAbsolutePath)) {
                foreach (glob($oldAbsolutePath . '/*') as $oldFilePath) {
                    $fileName = basename($oldFilePath);
                    $newFilePath = $newAbsolutePath . '/' . $fileName;

                    rename($oldFilePath, $newFilePath);
                    $movedFiles[$fileName] = $newFolderPath . '/' . $fileName;
                }

                @rmdir($oldAbsolutePath);

                // Update document_text links
                foreach ($movedFiles as $fileName => $newRelPath) {
                    $oldUrl = asset($oldFolderPath . '/' . $fileName);
                    $newUrl = asset($newRelPath);
                    $document_text = str_replace($oldUrl, $newUrl, $document_text);
                }

                // Update file paths in DB
                foreach ($movedFiles as $fileName => $newRelPath) {
                    DB::table('documents_files')
                        ->where('document_id', $documentId)
                        ->where('file_name', $fileName)
                        ->update(['file_path' => $newRelPath]);
                }
            }
        }

        // Move new files from temp_upload and update text
        $updatedText = preg_replace_callback('/href="([^"]*\/temp_upload\/([^"]+))"/', function ($matches) use ($newFolderPath, $documentId, $uploaded_by) {
            $fileName = $matches[2];
            $tempPath = public_path('/temp_upload' . $fileName);
            $finalPath = public_path($newFolderPath . '/' . $fileName);

            if (file_exists($tempPath)) {
                try {
                    rename($tempPath, $finalPath);

                    $fileSize = File::size($finalPath);
                    $fileType = File::mimeType($finalPath);
                    $filePath = $newFolderPath . '/' . $fileName;

                    DB::table('documents_files')->insert([
                        'file_name' => $fileName,
                        'original_file_name' => $fileName,
                        'description' => null,
                        'file_path' => $filePath,
                        'file_type' => $fileType,
                        'file_size' => $fileSize,
                        'document_id' => $documentId,
                        'uploaded_by' => $uploaded_by,
                        'memo_created_on' => now(),
                    ]);
                } catch (\Exception $e) {
                    Log::error("File move failed: " . $e->getMessage());
                }
            }

            return 'href="' . asset($newFolderPath . '/' . $fileName) . '"';
        }, $document_text);

        // Update document table
        DB::table('documents')->where('document_id', $documentId)->update([
            'document_subject' => $document_subject,
            'document_text' => $updatedText,
            'doc_status' => $request->input('document_status'),
            'doc_unit' => $request->input('unit'),
            'doc_keywords' => $request->input('keywords'),
            'category_id' => $category_id,
            'updated_at' => now(),
        ]);

        // TODO: Handle updating editors and readers if needed

        return redirect()->route('dashboard')->with('success', 'Document successfully updated!');
    }




}