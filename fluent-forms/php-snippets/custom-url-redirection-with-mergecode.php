<?php
 add_filter('fluentform/form_submission_confirmation', function ($confirmation, $formData, $form) {
      if ((int) $form->id !== 13) {
          return $confirmation;
      }
      $redirectUrl = $formData['Redirect_to_URL'] ?? '';
      if (!$redirectUrl || !filter_var($redirectUrl, FILTER_VALIDATE_URL)) {
          return $confirmation;
      }
      $confirmation['redirectTo'] = 'customUrl';
      $confirmation['customUrl'] = esc_url_raw($redirectUrl);
      $confirmation['enable_query_string'] = 'no';
      $confirmation['query_strings'] = '';

      return $confirmation;
  }, 20, 3);