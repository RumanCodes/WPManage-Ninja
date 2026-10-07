<?php
add_action('woocommerce_login_form_end', function () {
    echo do_shortcode(
        '[fs_auth_buttons title="" title_prefix="Continue with"]'
    );
});