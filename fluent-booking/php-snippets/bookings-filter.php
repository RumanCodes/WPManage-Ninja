<?php
  add_action('fluent_booking/schedules_query', function ($query) {
      $filters = isset($_GET['filters']) && is_array($_GET['filters'])
          ? wp_unslash($_GET['filters'])
          : [];

      $period = isset($filters['period'])
          ? sanitize_key($filters['period'])
          : '';

      if ('completed' === $period) {
          $query->where('status', '!=', 'no_show');
      }
  });