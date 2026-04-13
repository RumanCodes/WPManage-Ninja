<?php
add_filter('fluent_auth/registration_form_fields', function ($fields) {

    // Remove username field
    if (isset($fields['username'])) {
        unset($fields['username']);
    }

    return $fields;
});