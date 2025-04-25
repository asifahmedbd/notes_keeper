<?php


function renderTree($items) {
    $html = '<ul>';
    foreach ($items as $item) {
        $isFolder = $item->file_type === 'folder';
        $html .= '<li class="' . ($isFolder ? 'folder' : 'file') . '">';
        $html .= '<a href="#">' . $item->file_name . '</a>';
        if ($isFolder && isset($item->children)) {
            $html .= renderTree($item->children);
        }
        $html .= '</li>';
    }
    $html .= '</ul>';
    return $html;
}


function createFolderIfNotExists($folderPath, $location='public') {

    $directoryPath = public_path($folderPath);
    if ($location == 'storage') $directoryPath = storage_path($folderPath);

    if (! is_dir($directoryPath)) mkdir($directoryPath, 0755, true);
}


function formatDate($date, $format='F j, Y') {
    return date($format, strtotime( $date ));
}


function camelize($input, $separator = '_') {
    return str_replace($separator, ' ', ucwords($input, $separator));
}

