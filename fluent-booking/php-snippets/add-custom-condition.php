<?php
add_filter('fluentform_is_form_renderable', 'ff_one_request_per_user_per_day', 10, 2);
  add_filter('fluentform/is_form_renderable', 'ff_one_request_per_user_per_day', 10, 2);

  function ff_one_request_per_user_per_day($isRenderable, $form)
  {
      if (empty($isRenderable['status'])) {
          return $isRenderable;
      }

      // Replace with your actual form ID(s)
      $target_form_ids = [1];

      if (empty($form->id) || !in_array((int) $form->id, $target_form_ids, true)) {
          return $isRenderable;
      }

      if (!is_user_logged_in()) {
          $isRenderable['status'] = false;
          $isRenderable['message'] = __('You must be logged in to submit this form.', 'textdomain');
          return $isRenderable;
      }

      $user_id = get_current_user_id();
      $today_start = date('Y-m-d 00:00:00', strtotime(current_time('mysql')));
      $today_end   = date('Y-m-d 23:59:59', strtotime(current_time('mysql')));

      global $wpdb;
      $table = $wpdb->prefix . 'fluentform_submissions';

      $count = (int) $wpdb->get_var(
          $wpdb->prepare(
              "SELECT COUNT(*) FROM {$table}
               WHERE form_id = %d
                 AND user_id = %d
                 AND status != 'trashed'
                 AND created_at >= %s
                 AND created_at <= %s",
              (int) $form->id,
              (int) $user_id,
              $today_start,
              $today_end
          )
      );

      if ($count >= 1) {
          $isRenderable['status'] = false;
          $isRenderable['message'] = __('You already submitted your request today. Try again tomorrow.', 'textdomain');
      }

      return $isRenderable;
  }