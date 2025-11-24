<?php
add_filter('gettext', function ($translated, $original, $domain) {
    if ($domain === 'fluent-booking' && $original === 'Please login to view your bookings') {
        return 'Please <a href="https://plansmith.co.uk/portal/?fcom_action=auth">login to view your bookings.
</a> , and please check your email for your credentials';
    }
    return $translated;
}, 10, 3);
