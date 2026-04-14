<?php
add_filter('fluent_booking/meeting_multi_durations_schema', function($durations) {
    $new_duration = [
        'value' => '20',
        'label' => __('20 Minutes', 'fluent-booking')
    ];
    foreach ($durations as $index => $duration) {
        if ($duration['value'] === '15') {
            array_splice($durations, $index + 1, 0, [$new_duration]);
            break;
        }
    }

    return $durations;
});