<?php
function my_fcom_force_handle_names($data) {
if (is_object($data)) {
if (!empty($data->user_id)) {
$user = get_user_by('ID', $data->user_id);

if ($user && !empty($user->user_login)) {
$data->display_name = $user->user_login;
}
}
foreach ($data as $key => $value) {
$data->{$key} = my_fcom_force_handle_names($value);
}
return $data;
}
if (is_array($data)) {
if (!empty($data['user_id'])) {
$user = get_user_by('ID', $data['user_id']);
if ($user && !empty($user->user_login)) {
$data['display_name'] = $user->user_login;
}
}
foreach ($data as $key => $value) {
$data[$key] = my_fcom_force_handle_names($value);
}
}
return $data;
}
add_filter('fluent_community/profile_view_data', 'my_fcom_force_handle_names', 999);
add_filter('fluent_community/members_api_response', 'my_fcom_force_handle_names', 999);
add_filter('fluent_community/mention_members_api_response', 'my_fcom_force_handle_names', 999);
add_filter('fluent_community/space_members_api_response', 'my_fcom_force_handle_names', 999);
add_filter('fluent_community/feeds_api_response', 'my_fcom_force_handle_names', 999);
add_filter('fluent_community/feed_api_response', 'my_fcom_force_handle_names', 999);