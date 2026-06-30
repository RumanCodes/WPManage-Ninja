<?php
  add_filter('fluent_community/menu_groups', function ($menuGroups) {
      if (!\FluentCommunity\App\Services\Helper::isFeatureEnabled('course_module')) {
          return $menuGroups;
      }

      $myCourses = [
          'slug'         => 'my_courses',
          'title'        => 'My Courses',
          'enabled'      => 'yes',
          'is_custom'    => 'yes',
          'privacy'      => 'logged_in',
          'permalink'    => '#{{user_url}}/courses',
          'link_classes' => 'fcom_my_courses route_url',
          'shape_svg'    => '<svg width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M10.734 5.84746L14.7114
          6.9072M9.88139 9.01146L11.8701 9.54132M9.98031 14.9723L10.7758 15.1843C13.0258 15.7838 14.1508 16.0835 15.037
          15.5747C15.9233 15.0659 16.2247 13.9473 16.8276 11.71L17.6802 8.54599C18.2831 6.3087 18.5845 5.19006 18.0728
          4.30879C17.5611 3.42752 16.4362 3.12778 14.1862 2.52831L13.3907 2.31636C11.1407 1.71688 10.0157 1.41714 9.12948
          1.92594C8.24322 2.43474 7.94178 3.55338 7.3389 5.79067L6.4863 8.95466C5.88342 11.1919 5.58198 12.3106 6.09367
          13.1919C6.60536 14.0731 7.73034 14.3729 9.98031 14.9723Z" stroke="currentColor" stroke-width="1.5" stroke-
          linecap="round"/><path d="M9.99996 17.4559L9.20634 17.672C6.96165 18.2832 5.83931 18.5889 4.95512 18.0701C4.07093
          17.5513 3.7702 16.4107 3.16874 14.1295L2.31814 10.9035C1.71668 8.62232 1.41595 7.48174 1.92643 6.58318C2.36802
          5.80591 3.33329 5.83421 4.58329 5.83411" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',
      ];

      $items = $menuGroups['profileDropdownItems'] ?? [];
      $newItems = [];

      foreach ($items as $key => $item) {
          $newItems[$key] = $item;

          if ($key === 'my_spaces' || ($item['slug'] ?? '') === 'my_spaces') {
              $newItems['my_courses'] = $myCourses;
          }
      }

      if (!isset($newItems['my_courses'])) {
          $newItems['my_courses'] = $myCourses;
      }

      $menuGroups['profileDropdownItems'] = $newItems;

      return $menuGroups;
  });