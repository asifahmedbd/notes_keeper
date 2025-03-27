<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\Scanner;

class TestController extends Controller {


    public function index() {

        $directory = 'notes_data';

        $scanner = new Scanner();
        $scanner->scanDirectory($directory);

        echo "Scan Completed";

    }

    public function testOfficeHtml() {

        return view('app.dashboard.office-html');

    }

}
