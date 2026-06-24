<?php
  add_action('fluent_booking/author_landing_head', function ($calendar_event = null) {
      ?>
      <style>
          .slot_timing.fcal_icon_item {
                display: none !important;
        }
      </style>
      <?php
  }, 20);