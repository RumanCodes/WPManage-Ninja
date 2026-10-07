<?php
add_filter('fluent_auth/signup_verification_mail_subject', function ($subject) {
    return 'Confirm your registration';
});

add_filter('fluent_auth/signup_verification_email_body', function ($body, $code, $formData) {
    $name = !empty($formData['first_name']) ? esc_html($formData['first_name']) : 'there';

    return '
        <p>Hello ' . $name . ',</p>
        <p>Please use this code to confirm your registration:</p>
        <p><strong style="font-size: 24px;">' . esc_html($code) . '</strong></p>
        <p>This code expires in 10 minutes.</p>
    ';
}, 10, 3);