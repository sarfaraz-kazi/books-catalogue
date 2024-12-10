<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
?>

<form method="get" action="" id="books-filter-form">
    <input type="text" name="search" placeholder="<?php esc_attr_e('Search', 'books-catalogue'); ?>" value="<?php echo esc_attr($search); ?>">
    <input type="text" name="language" placeholder="<?php esc_attr_e('Filter by Language', 'books-catalogue'); ?>" value="<?php echo esc_attr($language); ?>">
    <input type="text" name="author" placeholder="<?php esc_attr_e('Filter by Author', 'books-catalogue'); ?>" value="<?php echo esc_attr($author); ?>">
    <input type="text" name="subject" placeholder="<?php esc_attr_e('Filter by Subject', 'books-catalogue'); ?>" value="<?php echo esc_attr($subject); ?>">
    <input type="hidden" name="page" value="1">
    <button type="submit"><?php esc_html_e('Filter', 'books-catalogue'); ?></button>
</form>
