<?php
add_filter('fluentform/integration_data_pipedrive', function($postData, $feed, $entry) {
// Hardcode pipeline and stage
$postData['pipeline_id'] = 1; // Pipeline ID
$postData['stage_id'] = 2; // Stage ID

return $postData;
}, 10, 3);

// To find your pipeline and stage IDs, you can call the Pipedrive API directly:
//
// Pipelines: https://api.pipedrive.com/v1/pipelines?api_token=YOUR_TOKEN
// Stages: https://api.pipedrive.com/v1/stages?api_token=YOUR_TOKEN&pipeline_id=1