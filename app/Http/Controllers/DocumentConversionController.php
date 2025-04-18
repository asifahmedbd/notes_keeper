<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\Conversion;

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

        $file_path = $data['file_path'];
        //exit($$data);
        $absolute_path = str_replace('/notes_keeper/', '/var/www/html/notes_keeper/public/', $file_path);

        $conversion = new Conversion();
        $file_path = $conversion->convertToPDF($absolute_path);

        $response = [
            'status' => 'success',
            'file_path' => '/notes_keeper/notes_data/PDM Access Request/Description on PDM Business Roles/PDM Business Roles v1.1.pdf',
            //'file_path' => $file_path,
        ];

        return response()->json($response);
    }


}
