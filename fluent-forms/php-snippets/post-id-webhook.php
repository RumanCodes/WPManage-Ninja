<?php
  add_filter('fluentform/webhook_request_data', function ($requestData, $settings, $formData, $form, $entry) {
      // Optional: limit to one form.
      // if ((int) $form->id !== 123) {
      //     return $requestData;
      // }

      $createdPost = wpFluent()->table('fluentform_submission_meta')
          ->where('response_id', $entry->id)
          ->where('meta_key', '__postFeeds_created_id')
          ->first();

      if (!empty($createdPost->value)) {
          $postId = (int) $createdPost->value;

          $requestData['created_post_id'] = $postId;
          $requestData['created_post_permalink'] = get_permalink($postId);
      }

      return $requestData;
  }, 10, 5);