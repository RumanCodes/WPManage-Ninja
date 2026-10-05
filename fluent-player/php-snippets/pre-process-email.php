<?php
 */
add_filter('fluent_player/pre_process_email_submit', function ($result, $data) {

    // Another handler already answered this submission; do not interfere.
    if (null !== $result) {
        return $result;
    }

    if (!class_exists('\FluentCrm\App\Models\Subscriber')
        || !class_exists('\FluentPlayer\App\Models\EmailCollection')) {
        return null;
    }

    $email      = isset($data['email']) ? trim((string) $data['email']) : '';
    $mediaId    = isset($data['media_id']) ? (int) $data['media_id'] : 0;
    $type       = isset($data['type']) ? (string) $data['type'] : 'preset';
    $presetSlug = isset($data['preset_slug']) ? (string) $data['preset_slug'] : '';
    $layerId    = isset($data['layer_id']) ? (string) $data['layer_id'] : '';

    if (!$email || !$mediaId) {
        return null;
    }

    // Only repeat submissions need help. A first submission already runs the
    // providers, and the FluentCRM provider handles pending/unsubscribed itself.
    $existing = \FluentPlayer\App\Models\EmailCollection::where('email', $email)
        ->where('media_id', $mediaId)
        ->when('preset' === $type, function ($q) use ($presetSlug) {
            return $q->where('preset_slug', $presetSlug);
        })
        ->when('layer' === $type, function ($q) use ($layerId) {
            return $q->where('layer_id', $layerId);
        })
        ->first();

    if (!$existing) {
        return null;
    }

    $contact = \FluentCrm\App\Models\Subscriber::where('email', $email)->first();
    if (!$contact) {
        return null;
    }

    // Suppression — see the note above. Only these two statuses may be re-invited.
    if (!in_array($contact->status, ['unsubscribed', 'pending'], true)) {
        return null;
    }

    // Our own throttle, for two reasons:
    //  - short-circuiting this filter skips FluentPlayer's own rate limiter, and
    //    that limiter ignores logged-in users in any case;
    //  - FluentCRM's internal guard is only 150 seconds.
    $throttleKey = 'flp_reoptin_' . md5($email . '|' . $mediaId . '|' . $presetSlug . '|' . $layerId);
    $throttleFor = (int) apply_filters('sa_player_reoptin_throttle_seconds', 15 * MINUTE_IN_SECONDS);

    if (get_transient($throttleKey)) {
        return [
            'message' => __('We have already sent you a confirmation email. Please check your inbox.', 'sound-africa'),
        ];
    }

    if (!$contact->sendDoubleOptinEmail()) {
        // Nothing was sent (already subscribed, throttled by FluentCRM, or the
        // double opt-in email is not configured). Let FluentPlayer respond normally.
        return null;
    }

    if ($throttleFor > 0) {
        set_transient($throttleKey, time(), $throttleFor);
    }

    // Mirror what the core duplicate path would have recorded on the stored row.
    if (isset($data['ip_address'])) { $existing->ip_address = $data['ip_address']; }
    if (isset($data['browser']))    { $existing->browser    = $data['browser']; }
    if (isset($data['device']))     { $existing->device     = $data['device']; }
    $existing->updated_at = current_time('mysql');
    $existing->save();

    return [
        'message' => __('Please check your inbox to confirm your email address.', 'sound-africa'),
    ];
}, 10, 2);