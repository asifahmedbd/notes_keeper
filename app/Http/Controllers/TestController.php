<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\Scanner;

class TestController extends Controller {

    protected $startTime;
    protected $endTime;

    public function __construct() {
        $this->startTime = microtime(true);
    }


    public function index() {

        $input = public_path('notes_data_backup/input.doc');
        $outputDir = public_path('notes_data_backup');

        $success = $this->convertDocToDocx($input, $outputDir);

        return $success
            ? 'Conversion successful!'
            : 'Conversion failed!';

    }


    public function convertDocToDocx($inputPath, $outputDir) {

        putenv('HOME=/tmp');

        $libreOfficePath = 'libreoffice';
        $command = "{$libreOfficePath} --headless --convert-to docx --outdir {$outputDir} {$inputPath}";
        //exit($command);
        exec($command . ' 2>&1', $output, $return_var);

//        \Log::info("LibreOffice command: $command");
//        \Log::info("LibreOffice output: " . print_r($output, true));
//        \Log::info("LibreOffice exit code: $return_var");

        return $return_var === 0;
    }


    function __destruct() {
        $this->endTime = microtime(true);
        $executionTime = $this->endTime - $this->startTime;
        echo "<br>Execution time: " . number_format($executionTime, 4) . " seconds";
    }

}
