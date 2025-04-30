<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;

use App\Services\Scanner;

use App\Models\User;

class TestController extends Controller {

    protected $startTime;
    protected $endTime;

    public function __construct() {
        $this->startTime = microtime(true);
    }


    public function index() {

        //$this->docConversion();
        //$this->allDocConversion();

        /*
        $user = User::find(1);
        echo "<pre>";
        var_dump($user->getRoleNames());
        var_dump($user->getAllPermissions()->pluck('name'));
        */
    }


    private function allDocConversion() {

        $file_types = [
            'doc',
            'ppt',
            'xls'
        ];

        $query = DB::table('documents_files');

        $query->where(function ($q) use ($file_types) {
            foreach ($file_types as $type) {
                $q->orWhere('original_file_name', 'like', "%.{$type}");
            }
        });

        $results = $query->limit(300)->get();

        echo "Count: " . count($results) . "<br><br>";

        foreach ($results as $result) {

            $original_file_name = $result->original_file_name;
            $inputPath = $result->file_path;
            $outputDir = $this->getFolderPath($inputPath, $original_file_name);

            $input = public_path($inputPath);
            $outputDir = public_path($outputDir);

            $success = $this->convertToModernFormat($input, $outputDir);

            echo "original_file_name: $original_file_name" . "<br>";
            echo "inputPath: $inputPath" . "<br>";
            echo "outputDir: $outputDir" . "<br>";

            if ($success) {

                echo "Converted: {$original_file_name} <br>";

                $newExtension = pathinfo($this->convertExtension($original_file_name), PATHINFO_EXTENSION);
                $newOriginalFileName = $this->replaceExtension($original_file_name, $newExtension);
                $newFilePath = $this->replaceExtension($inputPath, $newExtension);

                DB::table('documents_files')
                    ->where('file_id', $result->file_id)
                    ->update([
                        'original_file_name' => $newOriginalFileName,
                        'file_path' => $newFilePath,
                        'file_type' => $newExtension,
                    ]);

                echo "Updated DB: {$newOriginalFileName} <br><br>";

            }
            else {
                echo "Failed to convert: {$original_file_name} <br>";
            }

        }

    }


    public function convertToModernFormat($inputPath, $outputDir) {

        putenv('HOME=/tmp');

        $libreOfficePath = 'libreoffice';

        // Get extension of input file
        $extension = strtolower(pathinfo($inputPath, PATHINFO_EXTENSION));

        // Define supported conversions
        $formatMap = [
            'doc' => 'docx',
            'ppt' => 'pptx',
            'xls' => 'xlsx',
        ];

        if (!isset($formatMap[$extension])) {
            //exit("Unsupported file format: .$extension");
            return false;
        }

        $targetFormat = $formatMap[$extension];

        // SAFELY ESCAPE paths
        $inputPathEscaped = '"' . addcslashes($inputPath, '"') . '"';
        $outputDirEscaped = '"' . addcslashes($outputDir, '"') . '"';

        $command = "{$libreOfficePath} --headless --convert-to {$targetFormat} --outdir {$outputDirEscaped} {$inputPathEscaped}";
        //exit($command);

        exec($command . ' 2>&1', $output, $return_var);

        //\Log::info("LibreOffice command: $command");
        //\Log::info("LibreOffice output: " . print_r($output, true));
        //\Log::info("LibreOffice exit code: $return_var");

        return $return_var === 0;
    }


    function getFolderPath($fullPath, $fileName) {

        if (str_ends_with($fullPath, $fileName)) {
            $folderPath = substr($fullPath, 0, -strlen($fileName));
            return rtrim($folderPath, '/');
        }

        return rtrim($fullPath, '/');
    }


    private function replaceExtension($filename, $newExt) {
        return preg_replace('/\.[^.]+$/', '.' . $newExt, $filename);
    }


    private function convertExtension($filename) {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $map = [
            'doc' => 'docx',
            'ppt' => 'pptx',
            'xls' => 'xlsx',
        ];
        return isset($map[$ext]) ? $this->replaceExtension($filename, $map[$ext]) : $filename;
    }


    private function docConversion() {

        $input = public_path('notes_data_backup/input.doc');
        $outputDir = public_path('notes_data_backup');

        $success = $this->convertDocToDocx($input, $outputDir);

        return $success ? 'Conversion successful!' : 'Conversion failed!';

    }


    public function convertDocToDocx($inputPath, $outputDir) {

        putenv('HOME=/tmp');

        $libreOfficePath = 'libreoffice';
        $command = "{$libreOfficePath} --headless --convert-to docx --outdir {$outputDir} {$inputPath}";
        //exit($command);
        exec($command . ' 2>&1', $output, $return_var);

        \Log::info("LibreOffice command: $command");
        \Log::info("LibreOffice output: " . print_r($output, true));
        \Log::info("LibreOffice exit code: $return_var");

        return $return_var === 0;
    }


    function __destruct() {
        $this->endTime = microtime(true);
        $executionTime = $this->endTime - $this->startTime;
        echo "<br>Execution time: " . number_format($executionTime, 4) . " seconds";
    }

}
