<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use Auth;
use Carbon\Carbon;
use App\Models\Category;
use App\Models\Document;
use LaravelFileViewer;
use Facades\App\Cache\Company;

class DashboardController extends Controller {

    public function __construct() {

    }


    public function index() {

        $categories = (new Category())->getCategoryTreeWithDocuments();
        $tree = $this->formatForFancyTree($categories);

        return view("app.dashboard.index", [
            'directoryTree' => $tree
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

            foreach ($category->documents as $document) {
                $node['children'][] = [
                    'title' => $document->document_subject,
                    'key' => $document->document_id,
                    'folder' => false, // Documents are leaves
                    'extraClasses' => 'document-node' // Add class for red color
                ];
            }

            if ($category->children->isNotEmpty()) {
                $node['children'] = array_merge($node['children'], $this->formatForFancyTree($category->children));
            }

            $tree[] = $node;
        }

        return $tree;
    }


    private function buildTree($elements, $parentId = 0) {

        $branch = [];

        foreach ($elements as $element) {
            if ($element->parent_id == $parentId) {
                $children = $this->buildTree($elements, $element->category_id);

                $node = [
                    'title' => $element->category_name,
                    'key'   => $element->category_id,
                    'folder' => (!empty($children)),
                    'icon'  => (!empty($children)) ? 'fa fa-folder' : 'fa fa-file',
                    // Add additional data for columns
                    'uploaded_by' => $element->uploaded_by ?: '-',
                    'memo_created_on' => $element->memo_created_on ?: '-',
                    'expanded' => false,
                ];

                if (!empty($children)) {
                    $node['children'] = $children;
                }

                $branch[] = $node;
            }
        }

        return $branch;
    }


    private function getFileIcon($fileType) {

        $icons = [
            'jpg' => 'fa fa-file-image',
            'png' => 'fa fa-file-image',
            'gif' => 'fa fa-file-image',
            'pdf' => 'fa fa-file-pdf',
            'doc' => 'fa fa-file-word',
            'docx' => 'fa fa-file-word',
            'xls' => 'fa fa-file-excel',
            'xlsx' => 'fa fa-file-excel',
            'ppt' => 'fa fa-file-powerpoint',
            'pptx' => 'fa fa-file-powerpoint',
            'zip' => 'fa fa-file-archive',
            'rar' => 'fa fa-file-archive',
            'txt' => 'fa fa-file-alt',
            'mp3' => 'fa fa-file-audio',
            'mp4' => 'fa fa-file-video',
            'csv' => 'fa fa-file-csv',
            'php' => 'fa fa-file-code',
            'js' => 'fa fa-file-code',
            'css' => 'fa fa-file-code',
            'html' => 'fa fa-file-code',
        ];

        return isset($icons[$fileType]) ? $icons[$fileType] : 'fa fa-file';
    }


    public function getDetails(Request $request) {
        
        $folderId = $request->get('folderId');

        $folder_details = Document::with('userDetails')->where('document_id', $folderId)->first();
        //dd($folder_details);

        $folder_files = DB::table('documents_files')->where('document_id', $folderId)->get();
        //dd($folder_files);
        // For now, simulate folder details (replace with actual DB lookup or logic)
        $folderData = [
            'folder_name' => $folder_details->document_subject,
            'folder_description' => $folder_details->document_text,
            'folder_created_on' => $folder_details->memo_created_on,
            'folder_created_by' => $folder_details->userDetails->name,
            'folder_files' => $folder_files
        ];

        return response()->json($folderData);
    }


    public function viewFile(Request $request) {

        // $filepath = public_path('notes_data/' . $request->input('filePath'));
        // $file_url=public_path('notes_data/' . $request->input('filePath'));
        // $file_data=[
        //   [
        //     'label' => __('Label'),
        //     'value' => "Value"
        //   ]
        // ];
        $filename = 'PDM_Business Roles_v1.0.pptx';
        $filepath='public/'.$filename;
        $file_url=asset('storage/'.$filename);
        $file_data=[
          [
            'label' => __('Label'),
            'value' => "Value"
          ]
        ];
            
        return LaravelFileViewer::show($filename,$filepath,$file_url);
    }

}
