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


    private function convertToModernFormat($inputPath, $outputDir, $targetFormat = NULL) {

        putenv('HOME=/tmp');

        $libreOfficePath = 'libreoffice';

        $extension = strtolower(pathinfo($inputPath, PATHINFO_EXTENSION));
        $filenameWithoutExt = pathinfo($inputPath, PATHINFO_FILENAME);

        $formatMap = [
            'doc' => 'docx',
            'ppt' => 'pptx',
            'xls' => 'xlsx',
        ];

        if (!isset($formatMap[$extension]) || $targetFormat == NULL) {
            //exit("Unsupported file format: .$extension");
            //return false;
        }

        if ($targetFormat == NULL) $targetFormat = $formatMap[$extension];

        $inputPathEscaped = '"' . addcslashes($inputPath, '"') . '"';
        $outputDirEscaped = '"' . addcslashes($outputDir, '"') . '"';
        //dd(is_readable($inputPath), is_writable($outputDir));
        $command = "{$libreOfficePath} --headless --convert-to {$targetFormat} --outdir {$outputDirEscaped} {$inputPathEscaped}";
        //exit($command);

        exec($command . ' 2>&1', $output, $return_var);

        //\Log::info("LibreOffice command: $command");
        //\Log::info("LibreOffice output: " . print_r($output, true));
        //\Log::info("LibreOffice exit code: $return_var");

        if ($return_var === 0) {

            $newFilePath = rtrim($outputDir, '/') . '/' . $filenameWithoutExt . '.' . $targetFormat;

            if (file_exists($newFilePath)) {
                // Extract path after /public/
                $publicPos = strpos($newFilePath, '/public/');
                if ($publicPos !== false) {
                    $relativePath = substr($newFilePath, $publicPos + strlen('/public/'));
                    $finalPath = env('APP_PATH', '') . '/'. $relativePath;
                    return $finalPath;
                }

                // fallback to full path if /public/ not found
                return $newFilePath;
            }

        }

        return false;
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

        $directoryPath = dirname($file_path) . '/';

        $targetFormat = 'pdf';
        return $this->convertToModernFormat($file_path, $directoryPath, $targetFormat);

    }

}