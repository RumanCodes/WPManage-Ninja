<?php
add_filter('fluent_community/main_menu_items', function ($items, $scope) {
if (!is_user_logged_in()) {
return $items;
}
$items['my_booking'] = [
'slug' => 'my_booking',
'title' => __('My Booking', ''),
'enabled' => 'yes',
'is_custom' => 'yes',
'permalink' => site_url('/my-booking/'),
'link_classes' => 'fcom_my_booking',
'new_tab' => 'no',
];
return $items;
}, 20, 2);