<?php

namespace App\Services;

use DB;

use App\Models\DirectoryMapping;
use App\Models\Document;
use App\Models\DocumentFile;

class Scanner {

    public function __construct() {

        DirectoryMapping::truncate();
        Document::truncate();
        DocumentFile::truncate();

    }


    function scanDirectory($directory, $parent_id = 0, $isRoot = true) {

        $path = public_path($directory);

        if (!is_dir($path)) {
            return;
        }

        $items = scandir($path);
        $hasSubfolder = false;
        $files = [];

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $itemPath = $path . DIRECTORY_SEPARATOR . $item;
            $relativePath = $directory . '/' . $item;

            if (is_dir($itemPath)) {
                $hasSubfolder = true;
            }

            else {
                $files[] = $item;
            }
        }

        if (!$isRoot) { // Skip inserting the root folder into the database

            if ($hasSubfolder) {
                // Insert into directory_mappings
                $folder = DirectoryMapping::create([
                    'file_name'         => basename($directory),
                    'original_file_name'=> basename($directory),
                    'description'       => null,
                    'file_path'         => $directory,
                    'file_type'         => 'folder',
                    'file_size'         => 0,
                    'parent_id'         => $parent_id,
                ]);

                foreach ($items as $item) {
                    if ($item !== '.' && $item !== '..') {
                        $this->scanDirectory($directory . '/' . $item, $folder->file_id, false);
                    }
                }
            }

            else {
                // Insert into documents table
                $document = Document::create([
                    'document_subject'  => basename($directory),
                    'document_text'     => null,
                    'doc_status'        => null,
                    'doc_unit'          => null,
                    'doc_keywords'      => null,
                    'parent_id'         => $parent_id,
                    'uploaded_by'       => auth()->id(),
                    'memo_created_on'   => now(),
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);

                // Insert files into documents_files table
                foreach ($files as $file) {
                    $filePath = $directory . '/' . $file;
                    $fileSize = filesize(public_path($filePath));

                    DocumentFile::create([
                        'file_name'         => pathinfo($file, PATHINFO_FILENAME),
                        'original_file_name'=> $file,
                        'description'       => null,
                        'file_path'         => $filePath,
                        'file_type'         => pathinfo($file, PATHINFO_EXTENSION),
                        'file_size'         => $fileSize,
                        'document_id'       => $document->document_id,
                        'uploaded_by'       => auth()->id(),
                        'memo_created_on'   => now(),
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ]);
                }
            }
        }

        else {
            // If root, just scan its contents without inserting into DB
            foreach ($items as $item) {
                if ($item !== '.' && $item !== '..') {
                    $this->scanDirectory($directory . '/' . $item, $parent_id, false);
                }
            }
        }
    }


    function scanDirectory_old($directory, $parent_id = 0) {
        // Get absolute path
        $path = public_path($directory);

        // Check if directory exists
        if (!is_dir($path)) {
            return;
        }

        // Scan directory contents
        $items = scandir($path);

        foreach ($items as $item) {
            // Skip . and ..
            if ($item === '.' || $item === '..') {
                continue;
            }

            // Full path of item
            $itemPath = $path . DIRECTORY_SEPARATOR . $item;

            // Relative file path (to store in DB)
            $relativePath = $directory . '/' . $item;

            if (is_dir($itemPath)) {
                // Insert folder
                $folder = DirectoryMapping::create([
                    'file_name'         => $item,
                    'original_file_name'=> $item,
                    'description'       => null,
                    'file_path'         => $relativePath,
                    'file_type'         => 'folder',
                    'file_size'         => 0,
                    'parent_id'         => $parent_id,
                ]);

                // Recursively scan subdirectory
                $this->scanDirectory($relativePath, $folder->file_id);
            }

            else {
                // Get file size
                $fileSize = filesize($itemPath);

                // Insert file
                DirectoryMapping::create([
                    'file_name'         => pathinfo($item, PATHINFO_FILENAME),
                    'original_file_name'=> $item,
                    'description'       => null,
                    'file_path'         => $relativePath,
                    'file_type'         => pathinfo($item, PATHINFO_EXTENSION),
                    'file_size'         => $fileSize,
                    'parent_id'         => $parent_id,
                ]);
            }
        }
    }

}