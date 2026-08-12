<?php
 add_filter('fluentform/insert_response_data', function ($formData, $formId, $inputConfigs) {
      foreach ($inputConfigs as $fieldName => $field) {
          if (($field['element'] ?? '') !== 'textarea') {
              continue;
          }

          if (empty($formData[$fieldName]) || !is_string($formData[$fieldName])) {
              continue;
          }

          $value = str_replace(["\r\n", "\r"], "\n", $formData[$fieldName]);
          $value = preg_replace('/[ \t]+$/m', '', $value);
          $value = preg_replace("/\n{2,}/", "\n", $value);

          $formData[$fieldName] = trim($value);
      }

      return $formData;
  }, 10, 3);