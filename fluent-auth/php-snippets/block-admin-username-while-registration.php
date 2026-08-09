<?php
add_filter('illegal_user_logins', function ($usernames) {
    $usernames[] = 'admin';
    $usernames[] = sanitize_user(get_bloginfo('name'), true);

    return array_unique(array_map('strtolower', $usernames));
});