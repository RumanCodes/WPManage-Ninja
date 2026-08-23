<?php
add_action('fluent_community/portal_head', 'fcom_ios15_pwa_meta_tags', 1);
add_action('fluent_community/template_header', 'fcom_ios15_pwa_meta_tags', 1);

function fcom_ios15_pwa_meta_tags() {
static $printed = false;

if ($printed) {
return;
}

$printed = true;

$appName = get_bloginfo('name');

if (class_exists('\FluentCommunityPro\App\Modules\Pwa\PwaHelper')) {
$appName = \FluentCommunityPro\App\Modules\Pwa\PwaHelper::getAppName();
}

echo '<meta name="apple-mobile-web-app-capable" content="yes">' . "\n";
echo '<meta name="mobile-web-app-capable" content="yes">' . "\n";
echo '<meta name="apple-mobile-web-app-status-bar-style" content="default">' . "\n";
echo '<meta name="apple-mobile-web-app-title" content="' . esc_attr($appName) . '">' . "\n";
}