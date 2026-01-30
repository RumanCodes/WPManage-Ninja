<?php

add_filter('fluentform/email_styles', function ($css, $form, $notification) {

    $custom = '
        table.ff_entry_table_field.ff-table{
        text-align: center !important;
        }
    ';
    return $css . $custom;
}, 10, 3);
