<?php
  /**
   * FluentCommunity: Username-only identity (privacy mode)
   */

  add_action('plugins_loaded', function () {

      if (!function_exists('fc_force_community_username_identity')) {
          function fc_force_community_username_identity(int $userId): void
          {
              $user = get_userdata($userId);
              if (!$user) {
                  return;
              }

              $username = $user->user_login;

              // Keep WordPress core display_name aligned with username
              wp_update_user([
                  'ID'           => $userId,
                  'display_name' => $username,
              ]);

              // Optional: clear WP cached first/last
              update_user_meta($userId, 'first_name', '');
              update_user_meta($userId, 'last_name', '');

              // Ensure FluentCommunity row is forced to username
              if (!class_exists('\FluentCommunity\App\Models\User')) {
                  return;
              }

              $fcUser = \FluentCommunity\App\Models\User::find($userId);
              if (!$fcUser) {
                  return;
              }

              // true = force sync (so row is created/updated if missing)
              $xProfile = $fcUser->syncXProfile(true, true);
              if (!$xProfile) {
                  return;
              }

              $xProfile->display_name = $username;
              $xProfile->save();
          }
      }

      // New user registration (PMPro, WP registration, other flows)
      add_action('user_register', 'fc_force_community_username_identity', 20, 1);

      // If user profile is edited later, force it back to username
      add_action('profile_update', 'fc_force_community_username_identity', 20, 1);

      if (!function_exists('fc_sanitize_name_payload')) {
          function fc_sanitize_name_payload(&$value): void
          {
              if (is_array($value)) {
                  foreach ($value as &$item) {
                      fc_sanitize_name_payload($item);
                  }
                  unset($item);

                  if (array_key_exists('display_name', $value) && array_key_exists('username', $value)) {
                      $value['display_name'] = $value['username'];
                  }
                  if (array_key_exists('first_name', $value)) {
                      $value['first_name'] = '';
                  }
                  if (array_key_exists('last_name', $value)) {
                      $value['last_name'] = '';
                  }
                  if (array_key_exists('user_login', $value) && array_key_exists('display_name', $value) && empty($value['display_name'])) {
                      $value['display_name'] = $value['user_login'];
                  }
                  return;
              }

              if (is_object($value)) {
                  if (isset($value->display_name) && isset($value->username)) {
                      $value->display_name = $value->username;
                  }
                  if (property_exists($value, 'first_name')) {
                      $value->first_name = '';
                  }
                  if (property_exists($value, 'last_name')) {
                      $value->last_name = '';
                  }

                  foreach (get_object_vars($value) as $k => $v) {
                      fc_sanitize_name_payload($value->{$k});
                  }
              }
          }
      }

      // Profile pages
      add_filter('fluent_community/profile_view_data', function ($profile, $xProfile) {
          if (!empty($xProfile->username)) {
              $profile['display_name'] = $xProfile->username;
          }
          $profile['first_name'] = '';
          $profile['last_name'] = '';
          return $profile;
      }, 99, 2);

      // Bootstrapped auth vars
      add_filter('fluent_community/portal_vars', function ($vars) {
          if (!empty($vars['auth']) && is_array($vars['auth'])) {
              if (!empty($vars['auth']['username'])) {
                  $vars['auth']['display_name'] = $vars['auth']['username'];
              }
              $vars['auth']['first_name'] = '';
              $vars['auth']['last_name']  = '';
          }
          return $vars;
      }, 99);

      add_filter('fluent_community/portal_data_vars', function ($vars) {
          if (!empty($vars['auth']) && is_array($vars['auth'])) {
              if (!empty($vars['auth']['username'])) {
                  $vars['auth']['display_name'] = $vars['auth']['username'];
              }
              $vars['auth']['first_name'] = '';
              $vars['auth']['last_name']  = '';
          }
          return $vars;
      }, 99);

      // Directory / directory-like lists
      add_filter('fluent_community/members_api_response', function ($response) {
          if (isset($response['members'])) {
              fc_sanitize_name_payload($response['members']);
          }
          return $response;
      }, 99);

      add_filter('fluent_community/space_members_api_response', function ($response) {
          if (isset($response['members'])) {
              fc_sanitize_name_payload($response['members']);
          }
          return $response;
      }, 99);

      add_filter('fluent_community/mention_members_api_response', function ($response) {
          if (isset($response['members']['data'])) {
              fc_sanitize_name_payload($response['members']['data']);
          }
          return $response;
      }, 99);

      // Extra safety for feed/comment payloads (optional but recommended)
      add_filter('fluent_community/feeds_api_response', function ($response) {
          if (isset($response['feeds'])) {
              fc_sanitize_name_payload($response['feeds']);
          }
          return $response;
      }, 99);

      add_filter('fluent_community/comments_api_response', function ($response) {
          if (isset($response['comments'])) {
              fc_sanitize_name_payload($response['comments']);
          }
          return $response;
      }, 99);

      // Ensure app bootstrap endpoint also follows same rule
      add_filter('fluent_community/app_vars_api_response', function ($data) {
          if (isset($data['appVars'])) {
              fc_sanitize_name_payload($data['appVars']);
          }
          return $data;
      }, 99);

  });