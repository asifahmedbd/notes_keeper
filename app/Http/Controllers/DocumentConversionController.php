<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\Conversion;

use App\Models\DocumentFile;

class DocumentConversionController extends Controller {


    public function index() {

        return view('app.dashboard.create-memo', [

        ]);
    }


    public function convert(Request $request) {

        $conversion = new Conversion();
        $output = $conversion->convert();

        $response = [
            'status' => 'success',
            'message' => 'Document Conversion Completed!',
            'output' => $output,
        ];

        return response()->json($response);
    }


    public function convertToPDF(Request $request) {

        $data = $request->input('params');

        $file_id = $data['file_id'];
        $file_path = $data['file_path'];

        $documents_file = DocumentFile::find($file_id);
        $file_path = $documents_file->file_path;
        //echo "file_path: $file_path<br>";
        $absolute_path = "/var/www/html/notes_keeper/public/{$file_path}";
        //echo "absolute_path: $absolute_path<br>";
        //exit();
        $conversion = new Conversion();
        $file_path = $conversion->convertToPDF($absolute_path);

        $response = [
            'status' => 'success',
            'file_path' => $file_path,
        ];

        return response()->json($response);
    }


}
