<?php
add_filter('login_site_html_link', function ($html_link) {
       $login_url = add_query_arg(
           [
               'fcom_action' => 'auth',
               'form'        => 'login',
          ],
           home_url('/portal/')
       );
  
       return '<a href="' . esc_url($login_url) . '">' .
           esc_html__('← Back to Login') .
           '</a>';
 });