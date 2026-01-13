<?php
add_filter('fluent_booking/update_recurring_settings_validation_rule', function ($validationConfig, $event) {

    if (!empty($validationConfig['rules']['recurring_config.max_count'])) {

        // Replace the max value (e.g., max:24 → max:52)
        $validationConfig['rules']['recurring_config.max_count'] = array_map(function ($rule) {
            if (strpos($rule, 'max:') === 0) {
                return 'max:52';
            }
            return $rule;
        }, $validationConfig['rules']['recurring_config.max_count']);
    }

    return $validationConfig;

}, 10, 2);
