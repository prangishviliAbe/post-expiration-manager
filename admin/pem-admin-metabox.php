<?php
// Security check
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add the meta box to the post editor screen.
 */
function pem_add_expiration_meta_box() {
    add_meta_box(
        'pem_expiration_settings',
        'Post Expiration Settings',
        'pem_expiration_meta_box_html',
        'post',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'pem_add_expiration_meta_box');

/**
 * The HTML for the meta box.
 *
 * @param WP_Post $post The current post object.
 */
function pem_expiration_meta_box_html($post) {
    $expiration_date = get_post_meta($post->ID, '_pem_expiration_date', true);
    $expiration_action = get_post_meta($post->ID, '_pem_expiration_action', true);
    wp_nonce_field('pem_save_meta', 'pem_meta_nonce');
    ?>
    <p>
        <label for="pem_expiration_date">Expiration Date:</label><br>
        <input type="datetime-local" id="pem_expiration_date" name="pem_expiration_date" 
               value="<?php echo esc_attr($expiration_date); ?>">
    </p>
    <p>
        <label for="pem_expiration_action">Action on Expiration:</label><br>
        <select id="pem_expiration_action" name="pem_expiration_action">
            <option value="draft" <?php selected($expiration_action, 'draft'); ?>>Move to Draft</option>
            <option value="delete" <?php selected($expiration_action, 'delete'); ?>>Delete</option>
        </select>
    </p>
    <?php
}

/**
 * Save the meta data.
 *
 * @param int $post_id The ID of the post being saved.
 */
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
