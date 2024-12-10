<?php

namespace BooksCatalogue;

/**
 * Class Fetch
 *
 * Handles the fetch request and returns result as array.
 */
class Fetch {

    /**
     * Fetch books from the Gutendex API or from the WordPress transient cache.
     *
     * @param int $page
     * @param int $books_per_page
     * @param string $search
     * @param string $language
     * @param string $author
     * @param string $subject
     * @return array
     */
    public static function fetch_books($page, $books_per_page, $search, $language, $author, $subject) {
        $cache_key = "books_catalogue_{$page}_{$books_per_page}_" . md5("$search|$language|$author|$subject");
        $cached_data = get_transient($cache_key);

        if ($cached_data) {
            return $cached_data;
        }

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
            return ['books' => [], 'total_pages' => 0];
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['books' => [], 'total_pages' => 0];
        }

        $books = [];
        foreach ($data['results'] as $book) {
            $books[] = [
                'title' => $book['title'],
                'authors' => array_map(function ($author) {
                    return $author['name'];
                }, $book['authors']),
                'author_info'=>$book['authors'],
                'subjects' => $book['subjects'],
                'languages' => $book['languages'],
                'image' => $book['formats']['image/jpeg'] ?? 'https://via.placeholder.com/150',
                'download_link' => $book['formats']['application/octet-stream'] ?? '#',
                'download_count' => $book['download_count']
            ];
        }

        $total_pages = ceil($data['count'] / $books_per_page);

        $result = ['books' => $books, 'total_pages' => $total_pages];
        set_transient($cache_key, $result, HOUR_IN_SECONDS);

        return $result;
    }
}
