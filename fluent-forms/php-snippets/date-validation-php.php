<?php
add_filter('fluentform/validation_errors', function ($errors, $formData, $form, $fields) {

    if ((int) $form->id !== 118) {
        return $errors;
    }

    // Must match the field's "Date Format" setting in the editor.
    $format = 'd/m/Y';

    $toDate = function ($value) use ($format) {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }
        // "!" zeroes any unparsed component, so we compare dates not times.
        $date = DateTime::createFromFormat('!' . $format, $value);
        $err  = DateTime::getLastErrors();
        if (!$date || (is_array($err) && ($err['warning_count'] || $err['error_count']))) {
            return null;
        }
        return $date;
    };

    $arrival   = $toDate($formData['arrival_date'] ?? '');
    $departure = $toDate($formData['departure_date'] ?? '');

    // 2. Arrival within the next year
    if ($arrival) {
        $today = new DateTime('today');
        $limit = (new DateTime('today'))->modify('+1 year');

        if ($arrival < $today) {
            $errors['arrival_date'][] = __('Arrival date cannot be in the past.', 'fluentform');
        } elseif ($arrival > $limit) {
            $errors['arrival_date'][] = __('Arrival date must be within one year from today.', 'fluentform');
        }
    }

    // 1. Departure strictly after arrival
    if ($arrival && $departure && $departure <= $arrival) {
        $errors['departure_date'][] = __('Departure date must be after the arrival date.', 'fluentform');
    }

    return $errors;
}, 20, 4);