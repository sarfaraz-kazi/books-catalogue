<?php


// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

if (!empty($data['books'])) : ?>
    <div class="books-list" id="books-list">
        <ul class="book-list-view">
            <?php foreach ($data['books'] as $book) : ?>
                <li class="book-item">
                    <div class="image-wrapper">
                        <img src="<?php echo esc_url($book['image']); ?>" alt="<?php echo esc_attr($book['title']); ?>" class="book-image">
                    </div>
                    <div class="book-details">
                        <h3><?php echo esc_html($book['title']); ?> by <small class="author-name"><?php echo esc_html(implode(', ', $book['authors'])); ?></small></h3>
                        <p><strong><?php esc_html_e('Subjects:', 'books-catalogue'); ?></strong> <?php echo esc_html(implode(', ', $book['subjects'])); ?></p>
                        <p><strong><?php esc_html_e('Available in:', 'books-catalogue'); ?></strong> <?php echo esc_html(implode(', ', $book['languages'])); ?></p>
                        <a href="<?php echo esc_url($book['download_link']); ?>" class="download-btn" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Download', 'books-catalogue'); ?></a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="pagination">
        <?php if ($page > 1) : ?>
            <a href="?<?php echo esc_attr(self::build_query(['book-page' => $page - 1])); ?>" class="pagination-btn download-btn"><?php esc_html_e('Previous', 'books-catalogue'); ?></a>
        <?php endif; ?>
        <?php if ($page < $data['total_pages']) : ?>
            <a href="?<?php echo esc_attr(self::build_query(['book-page' => $page + 1])); ?>" class="pagination-btn download-btn"><?php esc_html_e('Next', 'books-catalogue'); ?></a>
        <?php endif; ?>
    </div>
<?php else : ?>
    <p><?php esc_html_e('No books found.', 'books-catalogue'); ?></p>
<?php endif; ?>
