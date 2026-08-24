<?php
function my_fcom_is_reddit_url($url) {
      $host = strtolower((string) wp_parse_url($url, PHP_URL_HOST));

      return (bool) preg_match(
          '/(^|\.)reddit\.com$|(^|\.)redd\.it$/',
          $host
      );
  }

  function my_fcom_reddit_as_metadata($preview) {
      $preview['type'] = 'meta_data';
      $preview['content_type'] = 'link';

      // Reddit's script-based HTML cannot render safely.
      unset($preview['html']);

      return $preview;
  }

  // Fix the preview shown in the composer.
  add_filter('fluent_community/feed_oembed_api_response', function ($response) {
      $oembed = $response['oembed'] ?? [];

      if (!empty($oembed['url']) && my_fcom_is_reddit_url($oembed['url'])) {
          $response['oembed'] = my_fcom_reddit_as_metadata($oembed);
      }

      return $response;
  }, 10, 2);

  // Ensure the converted preview is stored on new and edited posts.
  function my_fcom_save_reddit_preview($data, $request) {
      $media = $request['media'] ?? [];

      if (!empty($media['url']) && my_fcom_is_reddit_url($media['url'])) {
          $data['meta'] = is_array($data['meta'] ?? null)
              ? $data['meta']
              : [];

          $data['meta']['media_preview'] =
              my_fcom_reddit_as_metadata($media);
      }

      return $data;
  }

  add_filter(
      'fluent_community/feed/new_feed_data',
      'my_fcom_save_reddit_preview',
      10,
      2
  );

  add_filter(
      'fluent_community/feed/update_feed_data',
      'my_fcom_save_reddit_preview',
      10,
      2
  );

  add_filter('fluent_community/rendering_feed_model', function ($feed, $config) {
      $meta = $feed->meta;
      $preview = $meta['media_preview'] ?? [];

      if (
          is_array($preview) &&
          !empty($preview['url']) &&
          my_fcom_is_reddit_url($preview['url'])
      ) {
          $meta['media_preview'] = my_fcom_reddit_as_metadata($preview);
          $feed->meta = $meta;
      }

      return $feed;
  }, 10, 2);

