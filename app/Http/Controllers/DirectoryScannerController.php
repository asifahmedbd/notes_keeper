<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\Scanner;


class DirectoryScannerController extends Controller {

    public function index() {

        return view("app.scanner.index", [

        ]);

    }


    public function directoryScanner(Request $request) {

        $directory = 'notes_data';

        $scanner = new Scanner();
        $scanner->scanDirectory($directory);

        $response = [
            'status' => 'success',
            'message' => 'Directory Scan Completed!',
        ];

        return response()->json($response);
    }

}
