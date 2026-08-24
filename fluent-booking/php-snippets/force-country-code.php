<?php
add_filter('fluent_calendar/global_booking_vars', function ($vars) {
      $vars['user_country'] = 'GR'; // Greece
      return $vars;
  }, 99);

