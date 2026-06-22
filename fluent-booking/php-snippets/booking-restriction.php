<?php
 add_action('fluent_booking/before_creating_schedule', function ($bookingData, $postedData, $calendarEvent) {
      $email = sanitize_email($bookingData['email'] ?? '');

      if (!$email || empty($calendarEvent->id)) {
          return;
      }

      $maxBookings = 1;

      $existingBookings = \FluentBooking\App\Models\Booking::where('email', $email)
          ->where('event_id', $calendarEvent->id)
          ->whereIn('status', ['scheduled', 'pending', 'reserved', 'completed'])
          ->count();

      if ($existingBookings >= $maxBookings) {
          wp_send_json([
              'message' => __('You have already booked this event.', 'fluent-booking')
          ], 422);
      }
  }, 10, 3);