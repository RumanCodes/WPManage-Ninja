<?php
function redirect_wp_login_to_custom_login() {
    $login_page = home_url('/login');

    global $pagenow;

    // Redirect wp-login.php
    if ($pagenow === 'wp-login.php' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        wp_redirect($login_page);
        exit;
    }

    // Redirect wp-admin (for non-logged-in users)
    if (is_admin() && !is_user_logged_in() && !defined('DOING_AJAX')) {
        wp_redirect($login_page);
        exit;
    }
}
add_action('init', 'redirect_wp_login_to_custom_login');