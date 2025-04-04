<?php

namespace App\Services;

use DB;

use App\Models\Category;
use App\Models\Document;
use App\Models\DocumentFile;

class Scanner {

    public function __construct() {

        Category::truncate();
        Document::truncate();
        DocumentFile::truncate();

    }


    function scanDirectory($directory, $parent_id = 0, $isRoot = true) {

        $path = public_path($directory);

        if (!is_dir($path)) {
            return;
        }

        $items = scandir($path);
        $hasSubFolder = false;
        $files = [];

        foreach ($items as $item) {

            if ($item === '.' || $item === '..') {
                continue;
            }

            $itemPath = $path . DIRECTORY_SEPARATOR . $item;
            $relativePath = $directory . '/' . $item;

            if (is_dir($itemPath)) {
                $hasSubFolder = true;
            }

            else {
                $files[] = $item;
            }
        }

        if (!$isRoot) {

            if ($hasSubFolder) {

                $folder = Category::create([
                    'category_name' => basename($directory),
                    'original_file_name' => basename($directory),
                    'description' => null,
                    'file_path' => $directory,
                    'file_type' => 'folder',
                    'file_size' => 0,
                    'parent_id' => $parent_id,
                ]);

                foreach ($items as $item) {
                    if ($item !== '.' && $item !== '..') {
                        $this->scanDirectory($directory . '/' . $item, $folder->category_id, false);
                    }
                }
            }

            else {

                $document = Document::create([
                    'document_subject' => basename($directory),
                    'document_text' => null,
                    'doc_status' => null,
                    'doc_unit' => null,
                    'doc_keywords' => null,
                    'category_id' => $parent_id,
                    'uploaded_by' => auth()->id(),
                    'memo_created_on' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                foreach ($files as $file) {
                    $filePath = $directory . '/' . $file;
                    $fileSize = filesize(public_path($filePath));

                    DocumentFile::create([
                        'file_name' => pathinfo($file, PATHINFO_FILENAME),
                        'original_file_name' => $file,
                        'description' => null,
                        'file_path' => $filePath,
                        'file_type' => pathinfo($file, PATHINFO_EXTENSION),
                        'file_size' => $fileSize,
                        'document_id' => $document->document_id,
                        'uploaded_by' => auth()->id(),
                        'memo_created_on' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        else {
            foreach ($items as $item) {
                if ($item !== '.' && $item !== '..') {
                    $this->scanDirectory($directory . '/' . $item, $parent_id, false);
                }
            }
        }
    }

}