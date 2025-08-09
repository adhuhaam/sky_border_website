<?php
/**
 * Layout Helper Functions
 * Sky Border Solutions CMS
 * 
 * Helper functions to render views with layouts
 * Similar to Laravel's view system
 */

/**
 * Render a page with the admin layout
 * 
 * @param string $contentFile Path to the content file
 * @param array $data Data to pass to the view
 * @param string $layout Layout file to use (default: admin)
 */
function renderAdminPage($contentFile, $data = [], $layout = 'admin') {
    // Extract data to variables
    extract($data);
    
    // Set default values if not provided
    $pageTitle = $pageTitle ?? 'Admin Panel';
    $pageDescription = $pageDescription ?? '';
    $showPageHeader = $showPageHeader ?? true;
    $pageActions = $pageActions ?? '';
    $bodyClass = $bodyClass ?? '';
    $additionalCSS = $additionalCSS ?? '';
    $additionalJS = $additionalJS ?? '';
    
    // Include the layout
    $layoutFile = __DIR__ . "/../layouts/{$layout}.php";
    if (file_exists($layoutFile)) {
        include $layoutFile;
    } else {
        throw new Exception("Layout file not found: {$layoutFile}");
    }
}

/**
 * Render content with layout (for inline content)
 * 
 * @param string $content HTML content to render
 * @param array $data Data to pass to the layout
 * @param string $layout Layout file to use
 */
function renderWithLayout($content, $data = [], $layout = 'admin') {
    $data['content'] = $content;
    renderAdminPage(null, $data, $layout);
}

/**
 * Create page actions HTML
 * 
 * @param array $actions Array of action configurations
 * @return string HTML for page actions
 */
function createPageActions($actions) {
    $html = '';
    foreach ($actions as $action) {
        $url = $action['url'] ?? '#';
        $label = $action['label'] ?? 'Action';
        $icon = $action['icon'] ?? 'fas fa-plus';
        $class = $action['class'] ?? 'btn-primary';
        $attributes = $action['attributes'] ?? '';
        
        $html .= "<a href=\"{$url}\" class=\"{$class} inline-flex items-center\" {$attributes}>";
        $html .= "<i class=\"{$icon} mr-2\"></i>";
        $html .= htmlspecialchars($label);
        $html .= "</a>";
    }
    return $html;
}

/**
 * Create breadcrumb navigation
 * 
 * @param array $breadcrumbs Array of breadcrumb items
 * @return string HTML for breadcrumbs
 */
function createBreadcrumbs($breadcrumbs) {
    if (empty($breadcrumbs)) return '';
    
    $html = '<nav class="flex mb-4" aria-label="Breadcrumb">';
    $html .= '<ol class="inline-flex items-center space-x-1 md:space-x-3">';
    
    foreach ($breadcrumbs as $index => $crumb) {
        $isLast = ($index === count($breadcrumbs) - 1);
        $url = $crumb['url'] ?? '#';
        $label = $crumb['label'] ?? '';
        $icon = $crumb['icon'] ?? null;
        
        $html .= '<li class="inline-flex items-center">';
        
        if (!$isLast) {
            $html .= "<a href=\"{$url}\" class=\"inline-flex items-center text-sm font-medium text-gray-700 hover:text-brand-blue dark:text-gray-400 dark:hover:text-white\">";
            if ($icon) $html .= "<i class=\"{$icon} mr-2\"></i>";
            $html .= htmlspecialchars($label);
            $html .= '</a>';
            $html .= '<i class="fas fa-chevron-right text-gray-400 mx-2"></i>';
        } else {
            $html .= '<span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">';
            if ($icon) $html .= "<i class=\"{$icon} mr-2\"></i>";
            $html .= htmlspecialchars($label);
            $html .= '</span>';
        }
        
        $html .= '</li>';
    }
    
    $html .= '</ol></nav>';
    return $html;
}

/**
 * Create a data table
 * 
 * @param array $columns Column definitions
 * @param array $data Table data
 * @param array $options Table options
 * @return string HTML for data table
 */
function createDataTable($columns, $data, $options = []) {
    $tableClass = $options['class'] ?? 'modern-card bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg theme-transition';
    $emptyMessage = $options['empty_message'] ?? 'No data available';
    $emptyIcon = $options['empty_icon'] ?? 'fas fa-inbox';
    
    $html = "<div class=\"{$tableClass}\">";
    
    if (empty($data)) {
        // Empty state
        $html .= '<div class="text-center py-12">';
        $html .= "<i class=\"{$emptyIcon} text-4xl text-gray-400 dark:text-gray-600 mb-4\"></i>";
        $html .= '<h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2 theme-transition">No data found</h3>';
        $html .= '<p class="text-gray-600 dark:text-gray-400 theme-transition">' . htmlspecialchars($emptyMessage) . '</p>';
        $html .= '</div>';
    } else {
        // Table with data
        $html .= '<div class="overflow-x-auto">';
        $html .= '<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">';
        
        // Table header
        $html .= '<thead class="bg-gray-50 dark:bg-gray-700">';
        $html .= '<tr>';
        foreach ($columns as $column) {
            $label = $column['label'] ?? '';
            $class = $column['header_class'] ?? 'px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider';
            $html .= "<th class=\"{$class}\">" . htmlspecialchars($label) . '</th>';
        }
        $html .= '</tr>';
        $html .= '</thead>';
        
        // Table body
        $html .= '<tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">';
        foreach ($data as $row) {
            $html .= '<tr class="hover:bg-gray-50 dark:hover:bg-gray-700 theme-transition">';
            foreach ($columns as $column) {
                $key = $column['key'] ?? '';
                $class = $column['cell_class'] ?? 'px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white';
                $formatter = $column['formatter'] ?? null;
                
                $value = $row[$key] ?? '';
                if ($formatter && is_callable($formatter)) {
                    $value = $formatter($value, $row);
                } else {
                    $value = htmlspecialchars($value);
                }
                
                $html .= "<td class=\"{$class}\">{$value}</td>";
            }
            $html .= '</tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';
    }
    
    $html .= '</div>';
    return $html;
}

/**
 * Create form field HTML
 * 
 * @param array $field Field configuration
 * @param mixed $value Current value
 * @return string HTML for form field
 */
function createFormField($field, $value = '') {
    $type = $field['type'] ?? 'text';
    $name = $field['name'] ?? '';
    $label = $field['label'] ?? '';
    $required = $field['required'] ?? false;
    $placeholder = $field['placeholder'] ?? '';
    $options = $field['options'] ?? [];
    $attributes = $field['attributes'] ?? '';
    $class = $field['class'] ?? 'mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-blue focus:ring-brand-blue sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white theme-transition';
    
    $html = '<div>';
    
    // Label
    if ($label) {
        $requiredMark = $required ? ' *' : '';
        $html .= "<label for=\"{$name}\" class=\"block text-sm font-medium text-gray-700 dark:text-gray-300 theme-transition\">";
        $html .= htmlspecialchars($label) . $requiredMark;
        $html .= '</label>';
    }
    
    // Field
    switch ($type) {
        case 'textarea':
            $rows = $field['rows'] ?? 3;
            $html .= "<textarea name=\"{$name}\" id=\"{$name}\" rows=\"{$rows}\" class=\"{$class}\" placeholder=\"{$placeholder}\" {$attributes}>";
            $html .= htmlspecialchars($value);
            $html .= '</textarea>';
            break;
            
        case 'select':
            $html .= "<select name=\"{$name}\" id=\"{$name}\" class=\"{$class}\" {$attributes}>";
            foreach ($options as $optValue => $optLabel) {
                $selected = ($value == $optValue) ? 'selected' : '';
                $html .= "<option value=\"" . htmlspecialchars($optValue) . "\" {$selected}>";
                $html .= htmlspecialchars($optLabel);
                $html .= '</option>';
            }
            $html .= '</select>';
            break;
            
        case 'checkbox':
            $checked = $value ? 'checked' : '';
            $html .= "<input type=\"checkbox\" name=\"{$name}\" id=\"{$name}\" value=\"1\" class=\"h-4 w-4 text-brand-blue focus:ring-brand-blue border-gray-300 rounded\" {$checked} {$attributes}>";
            break;
            
        default:
            $requiredAttr = $required ? 'required' : '';
            $html .= "<input type=\"{$type}\" name=\"{$name}\" id=\"{$name}\" value=\"" . htmlspecialchars($value) . "\" class=\"{$class}\" placeholder=\"{$placeholder}\" {$requiredAttr} {$attributes}>";
            break;
    }
    
    $html .= '</div>';
    return $html;
}
?>
