<?php
  add_action('fluent_booking/before_creating_schedule', function ($bookingData, $postedData, $calendarEvent) {                                      
      // First, check if user is logged in                           
      if (!is_user_logged_in()) {
          wp_send_json([
              'success' => false,
              'message' => __('You need to be logged in to schedule a meeting. Please log in and try again. <a href="/login" target="_blank">Login
  Now</a>', 'fluent-booking')
          ], 403);
      }
      // Define the user roles that are allowed to book meetings
      $allowed_roles = ['administrator', 'editor']; // Add or remove roles as needed
      $current_user = wp_get_current_user();
      $has_allowed_role = false;
      foreach ($allowed_roles as $role) {
          if (in_array($role, $current_user->roles)) {
              $has_allowed_role = true;
              break;
          }
      }
      // If user doesn't have an allowed role, block the booking
      if (!$has_allowed_role) {
          wp_send_json([
              'success' => false,
              'message' => __('Sorry, your account type does not have permission to schedule a meeting.', 'fluent-booking')
          ], 403);
      }

  }, 10, 3);