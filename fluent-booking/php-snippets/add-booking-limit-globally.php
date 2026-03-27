<?php
add_action('fluent_booking/before_creating_schedule', function ($bookingData, $postedData, $calendarEvent) {

    // Define limits per event ID
    $event_limits = [
        2 => 100,  // Event ID 12; max 50 bookings
        15 => 30,  // Event ID 15; max 30 bookings
        20 => 100  // Event ID 20; max 100 bookings
    ];
    
    // If this event has no limit - allow booking
    if (!isset($event_limits[$calendarEvent->id])) {
        return;
    }

    $max_limit = $event_limits[$calendarEvent->id];
    
    global $wpdb;

    // FluentBooking bookings table (adjust if your prefix is different)
    $table = $wpdb->prefix . 'fcal_bookings';

    // Count total bookings for this event
    $total_bookings = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table WHERE event_id = %d AND status IN ('scheduled', 'pending')",
        $calendarEvent->id
    ));

    // Check limit
    if ($total_bookings >= $max_limit) {
        wp_send_json([
            'success' => false,
            'message' => __('Booking limit reached. No more appointments available.', 'fluent-booking')
        ], 423);
    }

}, 10, 3);