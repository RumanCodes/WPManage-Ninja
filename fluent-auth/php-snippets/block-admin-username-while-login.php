<?php
  add_filter('authenticate', function ($user, $username, $password) {
      $blocked_usernames = [
          'admin',
          sanitize_user(get_bloginfo('name'), true),
      ];

      if ($username && in_array(strtolower($username), $blocked_usernames, true)) {
          return new WP_Error(
              'fluent_auth_blocked_username',
              __('This username is not allowed.', 'fluent-security')
          );
      }

      return $user;
  }, 998, 3);