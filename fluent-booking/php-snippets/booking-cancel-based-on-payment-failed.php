<?php
  add_action('fluentform/after_payment_status_change', function ($newStatus, $submission) {
      if (!in_array($newStatus, ['failed', 'cancelled'], true)) {
          return;
      }

      if (empty($submission->id) || !class_exists('\FluentBooking\App\Models\Booking')) {
          return;
      }

      $bookings = \FluentBooking\App\Models\Booking::where('source', 'fluentform')
          ->where('source_id', $submission->id)
          ->whereIn('status', ['scheduled', 'pending'])
          ->get();

      foreach ($bookings as $booking) {
          $booking->cancelMeeting(
              'Payment was not completed successfully.',
              'system'
          );
      }
  }, 10, 2);