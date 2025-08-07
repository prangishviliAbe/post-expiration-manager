<?php
/*
Plugin Name: Post Expiration Manager
Plugin URI: https://github.com/prangishviliAbe/post-expiration-manager
Description: პოსტების ვადის კონტროლი - ავტომატური გადაყვანა დრაფტში ან წაშლა
Version: 1.0
Author: აბე ფრანგიშვილი
Author URI: https://github.com/prangishviliAbe
*/

// უსაფრთხოების შემოწმება
if (!defined('ABSPATH')) {
    exit;
}

// მეტა ბოქსის დამატება პოსტის რედაქტირების გვერდზე
function pem_add_expiration_meta_box() {
    add_meta_box(
        'pem_expiration_settings',
        'პოსტის ვადის პარამეტრები',
        'pem_expiration_meta_box_html',
        'post',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'pem_add_expiration_meta_box');

// მეტა ბოქსის HTML
function pem_expiration_meta_box_html($post) {
    $expiration_date = get_post_meta($post->ID, '_pem_expiration_date', true);
    $expiration_action = get_post_meta($post->ID, '_pem_expiration_action', true);
    wp_nonce_field('pem_save_meta', 'pem_meta_nonce');
    ?>
    <p>
        <label for="pem_expiration_date">ვადის გასვლის თარიღი:</label><br>
        <input type="datetime-local" id="pem_expiration_date" name="pem_expiration_date" 
               value="<?php echo esc_attr($expiration_date); ?>">
    </p>
    <p>
        <label for="pem_expiration_action">მოქმედება ვადის გასვლისას:</label><br>
        <select id="pem_expiration_action" name="pem_expiration_action">
            <option value="draft" <?php selected($expiration_action, 'draft'); ?>>დრაფტში გადაყვანა</option>
            <option value="delete" <?php selected($expiration_action, 'delete'); ?>>წაშლა</option>
        </select>
    </p>
    <?php
}

// მეტა მონაცემების შენახვა
function pem_save_expiration_meta($post_id) {
    if (!isset($_POST['pem_meta_nonce']) || 
        !wp_verify_nonce($_POST['pem_meta_nonce'], 'pem_save_meta')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['pem_expiration_date'])) {
        update_post_meta($post_id, '_pem_expiration_date', 
            sanitize_text_field($_POST['pem_expiration_date']));
    }

    if (isset($_POST['pem_expiration_action'])) {
        update_post_meta($post_id, '_pem_expiration_action', 
            sanitize_text_field($_POST['pem_expiration_action']));
    }
}
add_action('save_post', 'pem_save_expiration_meta');

// ვადის შემოწმების კრონ ჯობი
function pem_check_expired_posts() {
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => '_pem_expiration_date',
                'value' => '',
                'compare' => '!='
            )
        )
    );

    $posts = get_posts($args);

    foreach ($posts as $post) {
        $expiration_date = get_post_meta($post->ID, '_pem_expiration_date', true);
        $expiration_action = get_post_meta($post->ID, '_pem_expiration_action', true);
        
        if (strtotime($expiration_date) <= current_time('timestamp')) {
            if ($expiration_action === 'delete') {
                wp_delete_post($post->ID, true);
            } else {
                wp_update_post(array(
                    'ID' => $post->ID,
                    'post_status' => 'draft'
                ));
            }
        }
    }
}
add_action('pem_check_expiration', 'pem_check_expired_posts');

// კრონ ჯობის აქტივაცია
function pem_activation() {
    if (!wp_next_scheduled('pem_check_expiration')) {
        wp_schedule_event(time(), 'hourly', 'pem_check_expiration');
    }
}
register_activation_hook(__FILE__, 'pem_activation');

// კრონ ჯობის დეაქტივაცია
function pem_deactivation() {
    wp_clear_scheduled_hook('pem_check_expiration');
}
register_deactivation_hook(__FILE__, 'pem_deactivation');
