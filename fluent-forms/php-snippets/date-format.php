<?php

  add_action('admin_enqueue_scripts', function () {
      $page = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';

      if (! in_array($page, ['fluent_forms', 'fluent_forms_all_entries'], true)) {
          return;
      }

      $script = "
          window.fluent_forms_global_var = window.fluent_forms_global_var || {};
          window.fluent_forms_global_var.disable_time_diff = true;
          window.fluent_forms_global_var.wp_date_time_format = 'DD/MM/YYYY HH:mm';
      ";

      // Global Fluent Forms admin script.
      wp_add_inline_script('fluent_forms_global', $script, 'after');

      // Single form entries page.
      wp_add_inline_script('fluentform_form_entries', $script, 'before');

      // All entries page: admin.php?page=fluent_forms_all_entries
      wp_add_inline_script('fluentform_all_entries', $script, 'before');
  }, 999);