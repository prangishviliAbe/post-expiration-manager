<?php
/*
Plugin Name: Post Expiration Manager
Plugin URI: https://github.com/prangishviliAbe/post-expiration-manager
Description: Control post expiration - automatically set to draft or delete.
Version: 1.4.0
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
require_once(PEM_PLUGIN_DIR . 'includes/pem-core-functions.php');
require_once(PEM_PLUGIN_DIR . 'admin/pem-admin-metabox.php');

// Set up the update checker
require_once(PEM_PLUGIN_DIR . 'plugin-update-checker-master/plugin-update-checker.php');
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://github.com/prangishviliAbe/post-expiration-manager/',
    __FILE__,
    'post-expiration-manager'
);

// Set the branch that contains the stable release.
$myUpdateChecker->setBranch('main');

// Activation and deactivation hooks
register_activation_hook(PEM_PLUGIN_FILE, 'pem_activation');
register_deactivation_hook(PEM_PLUGIN_FILE, 'pem_deactivation');
