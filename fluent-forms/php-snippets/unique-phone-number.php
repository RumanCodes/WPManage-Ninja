<?php
add_filter('fluentform/validate_input_item_phone', function ($error, $field, $formData, $fields, $form) {

    // Optional: only apply to specific forms, e.g. [5, 12]. Leave empty for all forms.
    $onlyForms = [];
    if ($onlyForms && !in_array((int) $form->id, $onlyForms, true)) {
        return $error;
    }

    // Turn on the built-in unique check for this phone field.
    $field['raw']['settings']['is_unique'] = 'yes';
    $field['raw']['settings']['unique_validation_message'] = 'This phone number has already been used.';

    return \FluentForm\App\Helpers\Helper::isUniqueValidation($error, $field, $formData, $fields, $form);
}, 10, 5);