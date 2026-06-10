<?php
add_action('wp_footer', function () {
?>
<script>
document.addEventListener('fluentform_submission_success', function(event) {
var detail = event.detail || {};
var response = detail.response || {};
var result = response.data && response.data.result ? response.data.result : {};
var redirectUrl = result.redirectUrl || 'https://second-site-domain.com/thank-you/';

window.parent.postMessage({
type: 'fluentform_redirect',
redirectUrl: redirectUrl
}, 'https://second-site-domain.com');
});
</script>
<?php
});