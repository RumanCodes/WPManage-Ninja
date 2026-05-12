<?php
add_filter('pre_get_avatar_data', function ($args, $id_or_email) {
    if (!class_exists('\FluentCommunity\App\Models\XProfile')) {
        return $args;
    }
    $user_id = 0;
    if (is_numeric($id_or_email)) {
        $user_id = (int) $id_or_email;
    } elseif ($id_or_email instanceof WP_User) {
        $user_id = $id_or_email->ID;
    } elseif ($id_or_email instanceof WP_Comment) {
        $user_id = $id_or_email->user_id;
    } elseif (is_string($id_or_email)) {
        $user = get_user_by('email', $id_or_email);
        $user_id = $user ? $user->ID : 0;
    }
    if (!$user_id) {
        return $args;
    }
    static $cache = [];
    if (isset($cache[$user_id])) {
        if ($cache[$user_id]) {
            $args['url'] = $cache[$user_id];
        }
        return $args;
    }
    $xprofile = \FluentCommunity\App\Models\XProfile::where('user_id', $user_id)->first();
    if ($xprofile && $xprofile->hasCustomAvatar()) {
        $url = $xprofile->avatar;
        if ($url) {
            $cache[$user_id] = $url;
            $args['url'] = $url;
            return $args;
        }
    }
    $cache[$user_id] = false;
    return $args;
}, 10, 2);