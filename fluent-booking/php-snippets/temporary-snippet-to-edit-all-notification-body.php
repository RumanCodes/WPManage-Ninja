<?php
add_action('admin_init', function () {
      if (!current_user_can('manage_options')) {
          return;
      }

      // Only run on the /au/ subsite.
      $site_path = trim(parse_url(home_url('/'), PHP_URL_PATH), '/');
      if ($site_path !== 'au') {
          return;
      }

      if (get_option('fb_cleaned_event_1_email_notifications')) {
          return;
      }

      global $wpdb;

      $event_id = 1;
      $table = $wpdb->prefix . 'fcal_meta';

      $row = $wpdb->get_row($wpdb->prepare(
          "SELECT id, value FROM {$table}
           WHERE object_type = %s
           AND object_id = %d
           AND `key` = %s
           ORDER BY id DESC
           LIMIT 1",
          'calendar_event',
          $event_id,
          'email_notifications'
      ));

      if ($row && $row->value) {
          $notifications = maybe_unserialize($row->value);
      } elseif (class_exists('\FluentBooking\App\Services\Helper')) {
          $notifications = \FluentBooking\App\Services\Helper::getDefaultEmailNotificationSettings();
      } else {
          return;
      }

      foreach ($notifications as &$notification) {
          if (empty($notification['email']['body'])) {
              continue;
          }

          $body = $notification['email']['body'];

          $body = preg_replace('#<table\b[^>]*>.*?</table>#is', '', $body);
          $body = preg_replace('#<img\b[^>]*>#is', '', $body);
          $body = preg_replace('/\sstyle=("|\').*?\1/is', '', $body);

          $notification['email']['body'] = wp_kses_post(force_balance_tags($body));
      }
      unset($notification);

      $value = maybe_serialize($notifications);
      $now = current_time('mysql');

      if ($row) {
          $wpdb->update(
              $table,
              ['value' => $value, 'updated_at' => $now],
              ['id' => $row->id],
              ['%s', '%s'],
              ['%d']
          );
      } else {
          $wpdb->insert(
              $table,
              [
                  'object_type' => 'calendar_event',
                  'object_id'   => $event_id,
                  'key'         => 'email_notifications',
                  'value'       => $value,
                  'created_at'  => $now,
                  'updated_at'  => $now,
              ],
              ['%s', '%d', '%s', '%s', '%s', '%s']
          );
      }

      update_option('fb_cleaned_event_1_email_notifications', time(), false);
  });