<?php
  add_filter('fluentform/insert_response_data', function ($form_data, $form_id) {
      if ((int) $form_id !== 5) {
          return $form_data;
      }

      $quiz_score = (string) ($form_data['quiz-score'] ?? '0');
      $percentage = (float) str_replace('%', '', $quiz_score);
      $percentage = max(0, min(100, $percentage));

      $grade = $percentage < 80
          ? (($percentage / 80) * 3) + 1
          : ((($percentage - 80) / 20) * 3) + 4;

      $form_data['numeric_field']     = number_format(max(1, min(7, $grade)), 1, '.', '');
      $form_data['porcentaje_logro']  = number_format($percentage, 0, '.', '');
      $form_data['estado']            = $percentage >= 80 ? 'APROBADO' : 'ASISTENTE';
      $form_data['codigo_fmc_seguro'] = 'VAL-2026-' . strtoupper(wp_generate_password(12, false, false));

      return $form_data;
  }, 20, 2);