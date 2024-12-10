<?php
/*
Plugin Name: Books Catalogue
Description: Displays a collection of books using the Gutendex API with pagination and filters.
Version: 1.0
Author: Sarfaraz Kazi
Author URI: sarfarajkazi7.link
Text Domain: books-catalogue
License: GPL2
*/


namespace BooksCatalogue;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Autoloader function to include plugin dependencies dynamically.
 *
 * @param string $class The fully qualified class name.
 */
spl_autoload_register(function ($class) {
    $prefix = __NAMESPACE__ . '\\';
    $base_dir = __DIR__ . '/includes/';
    $len = strlen($prefix);

    // Check if the class belongs to the current namespace.
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    // Replace namespace separators with directory separators in the relative class name.
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // Include the file if it exists.
    if (file_exists($file)) {
        require $file;
    }
});

// Include the main plugin initialization file.
require_once __DIR__ . '/includes/class-books-catalogue-init.php';
require_once __DIR__ . '/includes/class-books-catalogue-fetch.php';

// Initialize the plugin.
Init::register();