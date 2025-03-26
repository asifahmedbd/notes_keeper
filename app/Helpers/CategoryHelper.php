<?php

// Prevent redeclaration
if (!function_exists('renderCategoryOptions')) {
    function renderCategoryOptions($categories, $prefix = '') {
        $html = '';
        foreach ($categories as $category) {
            $html .= '<option value="' . e($category['title']) . '">' . $prefix . e($category['title']) . '</option>';

            // Recursively render children with deeper indentation
            if (!empty($category['children'])) {
                $html .= renderCategoryOptions($category['children'], $prefix . '-- ');
            }
        }
        return $html;
    }
}
