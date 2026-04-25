<?php
add_filter('fluent_community/profile_view_data', function($profile, $xprofile) {
    $profileBaseUrl = \FluentCommunity\App\Services\Helper::baseUrl('u/' . $xprofile->username . '/');
    
    $profile['profile_navs'][] = [
        'slug'          => 'user_notification_settings',
        'wrapper_class' => 'fcom_notification_settings',
        'title'         => __('Notifications', 'fluent-community'),
        'url'           => $profileBaseUrl . 'notification-settings',
        'route'         => [
            'name' => 'user_notification_settings'
        ]
    ];
    
    return $profile;
}, 10, 2);