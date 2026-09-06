<?php
  add_filter('fluent_booking/schedule_validation_rules_data', function ($config) {
      foreach (['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'] as $field) {
          $config['rules'][$field] = 'string|max:192';
      }

      return $config;
  });