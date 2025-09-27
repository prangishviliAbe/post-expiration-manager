<?php
// Security check
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Cron job to check for expired posts.
 */
function pem_check_expired_posts() {
    // Ensure WordPress functions are available
    if (!function_exists('get_posts') || !function_exists('current_time')) {
        return;
    }
    
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
        
        if ($expiration_date && strtotime($expiration_date) <= current_time('timestamp')) {
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
    // Ensure WordPress cron functions are available
    if (!function_exists('wp_next_scheduled') || !function_exists('wp_schedule_event')) {
        return;
    }
    
    // Only schedule if not already scheduled
    if (!wp_next_scheduled('pem_check_expiration')) {
        // Use time() instead of current_time() during activation
        wp_schedule_event(time(), 'hourly', 'pem_check_expiration');
    }
}

/**
 * Deactivate the cron job.
 */
function pem_deactivation() {
    // Ensure WordPress cron functions are available
    if (function_exists('wp_clear_scheduled_hook')) {
        wp_clear_scheduled_hook('pem_check_expiration');
    }
}
