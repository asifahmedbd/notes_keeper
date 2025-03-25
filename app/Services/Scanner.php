<?php

namespace App\Services;

use DB;

use App\Models\DirectoryMapping;


class Scanner {

    public function __construct() {

    }


    function scanDirectory($directory, $parent_id = 0) {
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