<?php
/*
Plugin Name: Post Expiration Manager
Plugin URI: https://github.com/prangishviliAbe/post-expiration-manager
Description: Control post expiration - automatically set to draft or delete.
Version: 1.4.3
Author: Abe Prangishvili
Author URI: https://github.com/prangishviliAbe
Last Updated: September 27, 2025
*/

// Security check
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('PEM_PLUGIN_FILE', __FILE__);
define('PEM_PLUGIN_DIR', plugin_dir_path(__FILE__));

// Include the core functions and admin metabox
if (file_exists(PEM_PLUGIN_DIR . 'includes/pem-core-functions.php')) {
    require_once(PEM_PLUGIN_DIR . 'includes/pem-core-functions.php');
} else {
    add_action('admin_notices', function() {
        echo '<div class="error"><p>Post Expiration Manager: Missing core functions file.</p></div>';
    });
}

if (file_exists(PEM_PLUGIN_DIR . 'admin/pem-admin-metabox.php')) {
    require_once(PEM_PLUGIN_DIR . 'admin/pem-admin-metabox.php');
} else {
    add_action('admin_notices', function() {
        echo '<div class="error"><p>Post Expiration Manager: Missing admin metabox file.</p></div>';
    });
}

// Activation and deactivation hooks
if (function_exists('pem_activation')) {
    register_activation_hook(PEM_PLUGIN_FILE, 'pem_activation');
}
if (function_exists('pem_deactivation')) {
    register_deactivation_hook(PEM_PLUGIN_FILE, 'pem_deactivation');
}
