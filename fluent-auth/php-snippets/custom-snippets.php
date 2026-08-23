<?php
 /**
   * Temporary FluentAuth login limiter reset.
   *
   * Remove after the migration/reset workflow is complete.
   */
  add_action('init', function () {
      $settings = get_option('__fls_auth_settings', []);

      if (!is_array($settings)) {
          $settings = [];
      }

      $changed = false;

      if (!array_key_exists('login_try_limit', $settings) || (int) $settings['login_try_limit'] !== 0) {
          $settings['login_try_limit'] = 0;
          $changed = true;
      }

      if (!array_key_exists('login_try_timing', $settings) || (int) $settings['login_try_timing'] !== 0) {
          $settings['login_try_timing'] = 0;
          $changed = true;
      }

      if ($changed) {
          update_option('__fls_auth_settings', $settings, false);
      }

      if (class_exists('\FluentAuth\App\Helpers\Helper')) {
          \FluentAuth\App\Helpers\Helper::resetStatics();
      }
  }, 1);

  add_action('admin_init', function () {
      if (!current_user_can('manage_options')) {
          return;
      }

      global $wpdb;

      $table = $wpdb->prefix . 'fls_auth_logs';

      $table_exists = $wpdb->get_var(
          $wpdb->prepare('SHOW TABLES LIKE %s', $table)
      );

      if ($table_exists !== $table) {
          return;
      }

      $table_sql = '`' . str_replace('`', '``', $table) . '`';

      $wpdb->query(
          $wpdb->prepare(
              "DELETE FROM {$table_sql} WHERE `status` IN (%s, %s, %s)",
              'failed',
              'blocked',
              'password_reset'
          )
      );
  });