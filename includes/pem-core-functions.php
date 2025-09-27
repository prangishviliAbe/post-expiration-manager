<?php
// Security check
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Cron job to check for expired posts.
 */
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

/**
 * Activate the cron job.
 */
function pem_activation() {
    if (!wp_next_scheduled('pem_check_expiration')) {
        wp_schedule_event(time(), 'hourly', 'pem_check_expiration');
    }
}

/**
 * Deactivate the cron job.
 */
function pem_deactivation() {
    wp_clear_scheduled_hook('pem_check_expiration');
}
