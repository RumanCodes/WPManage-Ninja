<?php
function fluent_auth_honeypot_field()
{
    return '<div aria-hidden="true" style="position:absolute;left:-10000px;top:auto;width:1px;height:1px;overflow:hidden;">'
        . '<label for="fluent_auth_contact_url">Company website</label>'
        . '<input type="text" id="fluent_auth_contact_url" name="contact_url" value="" tabindex="-1" autocomplete="off">'
        . '</div>';
}

add_filter('login_form_middle', function ($html, $args) {
    $actionUrl = isset($args['action_url'])
        ? untrailingslashit((string) $args['action_url'])
        : '';

    // Limit this to FluentAuth's frontend login form.
    if ($actionUrl !== untrailingslashit(site_url('/'))) {
        return $html;
    }

    return $html . fluent_auth_honeypot_field();
}, 10, 2);

add_filter('do_shortcode_tag', function ($output, $tag) {
    if ($tag !== 'fluent_auth_signup') {
        return $output;
    }

    return str_replace(
        '<button type="submit" id="fls_submit">',
        fluent_auth_honeypot_field() . '<button type="submit" id="fls_submit">',
        $output
    );
}, 10, 2);

function reject_fluent_auth_honeypot()
{
    $value = isset($_POST['contact_url'])
        ? trim(wp_unslash($_POST['contact_url']))
        : '';

    if ($value !== '') {
        wp_send_json([
            'message' => 'We could not process that request. Please reload the page and try again.'
        ], 422);
    }
}

add_action(
    'wp_ajax_nopriv_fluent_auth_login',
    'reject_fluent_auth_honeypot',
    1
);

add_action(
    'wp_ajax_nopriv_fluent_auth_signup',
    'reject_fluent_auth_honeypot',
    1
);