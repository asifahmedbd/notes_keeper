<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use Auth;
use Carbon\Carbon;
use App\Models\DirectoryMapping;
use LaravelFileViewer;
use Facades\App\Cache\Company;

class DashboardController extends Controller {

    public function __construct() {

    }


    public function index() {

        //$directoryMappings = DB::table('directory_mappings')->get();
        //$directoryMappings = DB::table('directory_mappings')->where('file_type', 'folder')->get();

        $directoryMappings = DB::table('directory_mappings')
            ->leftJoin('users', 'directory_mappings.uploaded_by', '=', 'users.id')
            ->select(
                'directory_mappings.*',
                'users.name as uploaded_by' // Fetch user name
            )->where('file_type', 'folder')
            ->get();
        
        $directoryTree = $this->buildTree($directoryMappings);
        //dd(json_encode($directoryTree));
        return view("app.dashboard.index", [
            'directoryTree' => $directoryTree
        ]);

    }


    private function buildTree($elements, $parentId = 0) {

        $branch = [];

        foreach ($elements as $element) {
            if ($element->parent_id == $parentId) {
                $children = $this->buildTree($elements, $element->file_id);

                $node = [
                    'title' => $element->file_name,
                    'key'   => $element->file_id,
                    'folder' => (!empty($children)),
                    'icon'  => (!empty($children)) ? 'fa fa-folder' : $this->getFileIcon($element->file_type),
                    // Add additional data for columns
                    'uploaded_by' => $element->uploaded_by ?: '-',
                    'memo_created_on' => $element->memo_created_on ?: '-',
                    'expanded' => false,
                    // Add file_type if needed for icons
                    'file_type' => $element->file_type
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

        $folder_details = DirectoryMapping::with('userDetails')->where('file_id', $folderId)->first();
        //dd($folder_details);

        $folder_files = DB::table('directory_mappings')->where('parent_id', $folderId)->get();
        //dd($folder_data);
        // For now, simulate folder details (replace with actual DB lookup or logic)
        $folderData = [
            'folder_name' => $folder_details->file_name,
            'folder_description' => $folder_details->description,
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
