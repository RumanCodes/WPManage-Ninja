<?php 
add_filter('fluentform/integration_data_pipedrive', function ($postData, $feed, $entry) {
      $formId = absint($entry->form_id ?? 0);
      $mapping = [
          70 => [ // Form ID 70 maps to Pipeline ID 2 and Stage ID 8
              'pipeline_id' => 2,
              'stage_id'    => 8,
          ],
          85 => [ // Form ID 85 maps to Pipeline ID 3 and Stage ID 13
              'pipeline_id' => 3,
              'stage_id'    => 13,
          ],
      ];
      if (isset($mapping[$formId])) {
          $postData['pipeline_id'] = $mapping[$formId]['pipeline_id'];
          $postData['stage_id']    = $mapping[$formId]['stage_id'];
      }
      return $postData;
  }, 10, 3);

  // To find your pipeline and stage IDs, you can call the Pipedrive API directly:
//
// Pipelines: https://api.pipedrive.com/v1/pipelines?api_token=YOUR_TOKEN
// Stages: https://api.pipedrive.com/v1/stages?api_token=YOUR_TOKEN&pipeline_id=1