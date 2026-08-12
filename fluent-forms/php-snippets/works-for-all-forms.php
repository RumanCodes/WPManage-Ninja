<?php

  add_filter('fluentform/response_render_textarea', function ($value, $field, $formId, $isHtml) {
      if (!$isHtml || !is_string($value)) {
          return $value;
      }

      return preg_replace('/(<br\s*\/?>\s*){2,}/i', '<br />', $value);
  }, 20, 4);