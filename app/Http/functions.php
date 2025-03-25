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

