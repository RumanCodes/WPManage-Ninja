<?php
add_action('init', function () {
      if (!current_user_can('manage_options')) {
          return;
      }

      if (get_option('fb_fullname_calendar_slugs_updated')) {
          return;
      }
      $calendars = \FluentBooking\App\Models\Calendar::where('type', 'simple')->get();
      foreach ($calendars as $calendar) {
          $user = get_user_by('id', $calendar->user_id);

          if (!$user) {
              continue;
          }

          $fullName = trim($user->first_name . ' ' . $user->last_name);

          if (!$fullName) {
              $fullName = $user->display_name;
          }

          if (!$fullName) {
              continue;
          }

          $baseSlug = sanitize_title($fullName);
          $slug = $baseSlug;
          $counter = 1;

          while (
              \FluentBooking\App\Models\Calendar::where('slug', $slug)
                  ->where('id', '!=', $calendar->id)
                  ->first()
          ) {
              $slug = $baseSlug . '-' . $counter;
              $counter++;
          }

          $calendar->slug = $slug;
          $calendar->save();
      }

      update_option('fb_fullname_calendar_slugs_updated', time(), false);
  });