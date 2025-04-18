<?php

namespace App\Services;

use DB;

class Conversion {

    public function __construct() {

    }


    public function convert() {

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

        $response['count'] = count($results);

        foreach ($results as $result) {

            $original_file_name = $result->original_file_name;
            $inputPath = $result->file_path;
            $outputDir = $this->getFolderPath($inputPath, $original_file_name);

            $input = public_path($inputPath);
            $outputDir = public_path($outputDir);

            $success = $this->convertToModernFormat($input, $outputDir);

            //echo "original_file_name: $original_file_name" . "<br>";
            //echo "inputPath: $inputPath" . "<br>";
            //echo "outputDir: $outputDir" . "<br>";

            if ($success) {

                //echo "Converted: {$original_file_name} <br>";

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

                //echo "Updated DB: {$newOriginalFileName} <br><br>";

            }
            else {
                echo "Failed to convert: {$original_file_name} <br>";
            }

        }

        return $response;

    }


    private function convertToModernFormat($inputPath, $outputDir) {

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


    private function getFolderPath($fullPath, $fileName) {

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


    public function convertToPDF($file_path) {
        //exit($file_path);
        putenv('HOME=/tmp');
        $libreOfficePath = 'libreoffice';

        $directoryPath = dirname($file_path) . '/';

        //$inputPathEscaped = '"' . addcslashes($file_path, '"') . '"';
        //$inputPathEscaped = escapeshellarg($file_path);
        $outputDirEscaped = '"' . addcslashes($directoryPath, '"') . '"';
        //$outputDirEscaped = '"/var/www/html/notes_keeper/public/temp_upload"';

        $command = "{$libreOfficePath} --headless --convert-to pdf \"{$file_path}\" --outdir {$outputDirEscaped}";
        //exit($command);
        exec($command . ' 2>&1', $output, $return_var);

        if ($return_var === 0) {
            $pdfPath = preg_replace('/\.pptx?$/i', '.pdf', $file_path);
            return $pdfPath;
        }

        return null;

    }

}