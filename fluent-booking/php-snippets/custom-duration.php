<?php
  add_filter('fluent_booking/meeting_multi_durations_schema', function ($durations) {
      $extra_durations = [75, 105];

      foreach ($extra_durations as $minutes) {
          $exists = false;

          foreach ($durations as $duration) {
              if ((int) $duration['value'] === $minutes) {
                  $exists = true;
                  break;
              }
          }

          if (! $exists) {
              $durations[] = [
                  'value' => (string) $minutes,
                  'label' => $minutes . ' Minutes',
              ];
          }
      }

      usort($durations, function ($a, $b) {
          return (int) $a['value'] <=> (int) $b['value'];
      });

      return $durations;
  });