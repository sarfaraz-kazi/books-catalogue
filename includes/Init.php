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

        // Handle AJAX requests for fetching books (authenticated and unauthenticated).
        add_action('wp_ajax_fetch_books', [self::class, 'fetch_books']);
        add_action('wp_ajax_nopriv_fetch_books', [self::class, 'fetch_books']);
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
        ob_start();
        ?>
        <div id="books-catalogue">
            <form id="books-filter-form">
                <input type="text" id="search-input" placeholder="<?php esc_html_e('Search', 'books-catalogue'); ?>" name="search">
                <input type="text" id="language-filter" placeholder="<?php esc_html_e('Filter by Language', 'books-catalogue'); ?>" name="language">
                <input type="text" id="author-filter" placeholder="<?php esc_html_e('Filter by Author', 'books-catalogue'); ?>" name="author">
                <input type="text" id="subject-filter" placeholder="<?php esc_html_e('Filter by Subject', 'books-catalogue'); ?>" name="subject">
                <button type="submit"><?php esc_html_e('Filter', 'books-catalogue'); ?></button>
            </form>
            <div id="books-list"></div>
            <div id="pagination"></div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Handle AJAX requests for fetching books from the Gutendex API.
     */
    public static function fetch_books() {
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $books_per_page = isset($_POST['books_per_page']) ? intval($_POST['books_per_page']) : 12;
        $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
        $language = isset($_POST['language']) ? sanitize_text_field($_POST['language']) : '';
        $author = isset($_POST['author']) ? sanitize_text_field($_POST['author']) : '';
        $subject = isset($_POST['subject']) ? sanitize_text_field($_POST['subject']) : '';

        $api_url = "https://gutendex.com/books/?page=$page&page_size=$books_per_page";

        if (!empty($search)) {
            $api_url .= "&search=" . urlencode($search);
        }
        if (!empty($language)) {
            $api_url .= "&languages=" . urlencode($language);
        }
        if (!empty($author)) {
            $api_url .= "&author=" . urlencode($author);
        }
        if (!empty($subject)) {
            $api_url .= "&subject=" . urlencode($subject);
        }

        $response = wp_remote_get($api_url);
        if (is_wp_error($response)) {
            wp_send_json_error(['message' => 'Failed to fetch data from the API.']);
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_send_json_error(['message' => 'Invalid JSON returned from the API.']);
        }

        $total_books = $data['count'] ?? 0;
        $books = $data['results'] ?? [];
        $total_pages = ceil($total_books / $books_per_page);

        wp_send_json_success([
            'books' => $books,
            'total_pages' => $total_pages,
        ]);
    }
}
