<?php
/*
Plugin Name: Post Expiration Manager Test
Version: 1.0
Description: Test version to check for errors
*/

// Security check
if (!defined('ABSPATH')) {
    exit;
}

// Simple activation hook
register_activation_hook(__FILE__, function() {
    // Do nothing, just activate
});

// Simple deactivation hook
register_deactivation_hook(__FILE__, function() {
    // Do nothing, just deactivate
});