<?php 
// Change duration to always show as minutes 
add_filter('fluent_booking/meeting_durations_schema', function($durations) {
    $customDurations = [5, 10, 15, 20, 30, 45, 60, 75, 90, 120, 150, 180];
    return array_map(function($min) {
        return [
            'value' => (string)$min,
            'label' => $min . ' Minutes'
        ];
    }, $customDurations);
});
add_filter('fluent_booking/meeting_multi_durations_schema', function($durations) {
    $customDurations = [5, 10, 15, 20, 30, 45, 60, 75, 90, 120, 150, 180];
    return array_map(function($min) {
        return [
            'value' => (string)$min,
            'label' => $min . ' Minutes'
        ];
    }, $customDurations);
});