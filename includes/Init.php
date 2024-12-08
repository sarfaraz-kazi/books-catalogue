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
            plugin_dir_url(__DIR__) . 'assets/bca-script.js',
            ['jquery'],
            null,
            true
        );
        wp_localize_script('books-catalogue-script', 'BooksCatalogue', [
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
                <input type="text" id="filter-author" placeholder="<?php esc_html_e( 'Filter by Author', 'books-catalogue' ); ?>" name="author">
                <input type="text" id="filter-topic" placeholder="<?php esc_html_e( 'Filter by Topic', 'books-catalogue' ); ?>" name="topic">
                <input type="text" id="filter-language" placeholder="<?php esc_html_e( 'Filter by Language', 'books-catalogue' ); ?>" name="language">
                <button type="submit"><?php esc_html_e( 'Filter', 'books-catalogue' ); ?></button>
            </form>
            <div id="books-list"></div>
            <div id="pagination">
                <button id="prev-page"><?php esc_html_e( 'Previous', 'books-catalogue' ); ?></button>
                <span id="current-page">1</span>
                <button id="next-page"><?php esc_html_e( 'Next', 'books-catalogue' ); ?></button>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Handle AJAX requests for fetching books from the Gutendex API.
     */
    public static function fetch_books() {
        // Sanitize input parameters.
        $author = sanitize_text_field($_POST['author'] ?? '');
        $topic = sanitize_text_field($_POST['topic'] ?? '');
        $language = sanitize_text_field($_POST['language'] ?? '');
        $page = absint($_POST['page'] ?? 1);

        $api_url = 'https://gutendex.com/books/?page=' . $page;
        if (!empty($author)) {
            $api_url .= '&author=' . urlencode($author);
        }
        if (!empty($topic)) {
            $api_url .= '&topic=' . urlencode($topic);
        }
        if (!empty($language)) {
            $api_url .= '&language=' . urlencode($language);
        }

        $response = wp_remote_get($api_url);
        if (is_wp_error($response)) {
            wp_send_json_error('Failed to fetch books.');
        }

        $books = wp_remote_retrieve_body($response);
        wp_send_json_success($books);
    }
}
