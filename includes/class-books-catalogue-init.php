<?php

namespace BooksCatalogue;

/**
 * Class Init
 *
 * Handles the initialization of the plugin by registering actions, filters, and shortcodes.
 */
class Init {
    /**
     * Register plugin hooks and shortcodes.
     */
    public static function register() {
        // Enqueue styles and scripts.
        add_action('wp_enqueue_scripts', [self::class, 'enqueue_scripts']);

        // Register the [books_catalogue] shortcode.
        add_shortcode('books_catalogue', [self::class, 'render_shortcode']);
    }

    /**
     * Enqueue the required CSS and JavaScript files for the plugin.
     */
    public static function enqueue_scripts() {
        wp_enqueue_style('books-catalogue-style', plugin_dir_url(__DIR__) . 'assets/css/bca-style.css');
        wp_enqueue_script('jquery');
        wp_enqueue_script(
            'books-catalogue-script',
            plugin_dir_url(__DIR__) . 'assets/js/bca-script.js',
            ['jquery'],
            null,
            true
        );
        wp_localize_script('books-catalogue-script', 'booksCatalogue', [
            'ajax_url' => admin_url('admin-ajax.php'),
        ]);
    }

    /**
     * Render the [books_catalogue] shortcode output.
     *
     * @return string HTML content for the books catalogue.
     */
    public static function render_shortcode() {
        // Get parameters from the URL for filtering and pagination.
        $page = isset($_GET['book-page']) ? intval($_GET['book-page']) : 1;
        $books_per_page = isset($_GET['books_per_page']) ? intval($_GET['books_per_page']) : 12;
        $search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
        $language = isset($_GET['language']) ? sanitize_text_field($_GET['language']) : '';
        $author = isset($_GET['author']) ? sanitize_text_field($_GET['author']) : '';
        $subject = isset($_GET['subject']) ? sanitize_text_field($_GET['subject']) : '';

        // Fetch books using a helper function.
        $data = Fetch::fetch_books($page, $books_per_page, $search, $language, $author, $subject);

        ob_start();
        ?>
        <div id="books-catalogue">
            <?php include_once 'templates/books-catalogue-render-form.php' ?>
            <?php include_once 'templates/books-catalogue-render.php' ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Build a query string while maintaining other parameters.
     *
     * @param array $new_params
     * @return string
     */
    private static function build_query($new_params) {
        $params = array_merge($_GET, $new_params);
        // Make sure we preserve the 'page' param while adding the new one
        return http_build_query($params);
    }

}
