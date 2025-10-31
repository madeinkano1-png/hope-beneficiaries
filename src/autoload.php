<?php
/**
 * Simple PSR-4 Autoloader for HoPE System
 */

spl_autoload_register(function ($class) {
    // Base directory for the namespace prefix
    $base_dir = __DIR__ . '/';
    
    // Replace namespace separators with directory separators
    $relative_class = str_replace('\\', '/', $class);
    
    // Build the file path
    $file = $base_dir . $relative_class . '.php';
    
    // If the file exists, require it
    if (file_exists($file)) {
        require $file;
        return true;
    }
    
    // Try without namespace (for simple class names)
    $simple_file = $base_dir . basename($relative_class) . '.php';
    if (file_exists($simple_file)) {
        require $simple_file;
        return true;
    }
    
    return false;
});

// Helper function to load all files in a directory
function load_directory($dir) {
    if (is_dir($dir)) {
        $files = glob($dir . '/*.php');
        foreach ($files as $file) {
            require_once $file;
        }
    }
}
?>
