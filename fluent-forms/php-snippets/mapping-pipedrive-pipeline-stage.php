<?php
add_filter('fluentform/integration_data_pipedrive', function($postData, $feed, $entry) {
// Hardcode pipeline and stage
$postData['pipeline_id'] = 1; // Pipeline ID
$postData['stage_id'] = 2; // Stage ID

return $postData;
}, 10, 3);