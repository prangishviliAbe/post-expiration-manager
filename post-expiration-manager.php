<?php
/*
Plugin Name: Post Expiration Manager
Plugin URI: https://github.com/prangishviliAbe/post-expiration-manager
Description: Control post expiration - automatically set to draft or delete.
Version: 1.1
Author: Abe Prangishvili
Author URI: https://github.com/prangishviliAbe
*/

// Security check
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('PEM_PLUGIN_FILE', __FILE__);
define('PEM_PLUGIN_DIR', plugin_dir_path(__FILE__));

// Include the core functions and admin metabox
require_once(PEM_PLUGIN_DIR . 'includes/pem-core-functions.php');
require_once(PEM_PLUGIN_DIR . 'admin/pem-admin-metabox.php');
