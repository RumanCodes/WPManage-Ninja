<?php
  add_filter('fluent_booking/backend_sanitized_values', function ($inputs, $originalValues) {
      if (
          !empty($inputs['user_id']) &&
          !empty($inputs['type']) &&
          $inputs['type'] === 'simple'
      ) {
          $user = get_user_by('id', $inputs['user_id']);

          if ($user) {
              $fullName = trim($user->first_name . ' ' . $user->last_name);

              if ($fullName) {
                  $inputs['slug'] = sanitize_title($fullName);
              }
          }
      }

      return $inputs;
  }, 10, 2);

add_action('fluent_booking/after_create_calendar', function ($calendar) {
      if (!$calendar || $calendar->type !== 'simple') {
          return;
      }

      $user = get_user_by('id', $calendar->user_id);

      if (!$user) {
          return;
      }

      $fullName = trim($user->first_name . ' ' . $user->last_name);

      if (!$fullName) {
          $fullName = $user->display_name;
      }

      if (!$fullName) {
          return;
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
  }, 20);