<?php
/**
 * SureCart → FluentCRM: add the contact and a "KAUF:" tag as soon as a checkout is confirmed,
 * including "Kauf auf Rechnung" orders that are still Processing.
 * No per-seminar edits needed: the date part after " | " in the product name is ignored.
 */
add_action('surecart/checkout_confirmed', function ($checkout) {
    if (!function_exists('FluentCrmApi') || !class_exists('\SureCart\Models\Checkout') || empty($checkout->id)) {
        return;
    }

    // Optional rules: text the product NAME contains (or an exact SureCart product ID) => FluentCRM tag name(s).
    // Leave empty to rely only on the automatic tag below.
    $productTags = [
        // 'Unmittelbarer Zwang' => ['KAUF: Unmittelbarer Zwang'],
    ];

    // Automatic tag when no rule matches: "KAUF: <product name without the part after ' | '>".
    // The tag is created in FluentCRM if it doesn't exist yet. Set to false to disable.
    $autoTag   = true;
    $tagPrefix = 'KAUF: ';

    // Status for NEW contacts only ('pending' sends FluentCRM's double opt-in email).
    $newContactStatus = 'subscribed';

    $checkout = \SureCart\Models\Checkout::with(['line_items', 'line_item.price', 'price.product'])->find($checkout->id);
    if (!$checkout || is_wp_error($checkout) || empty($checkout->email)) {
        return;
    }

    $tagNames = [];
    foreach ($checkout->line_items->data ?? [] as $item) {
        $product     = $item->price->product ?? null;
        $productId   = is_object($product) ? (string) ($product->id ?? '') : (string) $product;
        $productName = is_object($product) ? (string) ($product->name ?? '') : '';

        $matched = false;
        foreach ($productTags as $key => $tags) {
            $key = (string) $key;
            if ($key !== '' && ($key === $productId || ($productName !== '' && stripos($productName, $key) !== false))) {
                $tagNames = array_merge($tagNames, (array) $tags);
                $matched  = true;
            }
        }

        if (!$matched && $autoTag && $productName !== '') {
            $baseName = trim(explode(' | ', $productName)[0]);
            if ($baseName !== '') {
                $tagNames[] = $tagPrefix . $baseName;
            }
        }
    }

    $tagNames = array_unique(array_filter($tagNames));
    if (!$tagNames) {
        return;
    }

    $tagIds = [];
    foreach ($tagNames as $title) {
        $tag = \FluentCrm\App\Models\Tag::where('title', $title)->first();
        if (!$tag) {
            $tag = \FluentCrm\App\Models\Tag::create(['title' => $title, 'slug' => sanitize_title($title)]);
        }
        if ($tag) {
            $tagIds[] = $tag->id;
        }
    }

    $data = [
        'email'      => $checkout->email,
        'first_name' => $checkout->first_name ?? '',
        'last_name'  => $checkout->last_name ?? '',
        'tags'       => $tagIds,
    ];

    if (!FluentCrmApi('contacts')->getContact($checkout->email)) {
        $data['status'] = $newContactStatus;
    }

    FluentCrmApi('contacts')->createOrUpdate($data);
}, 20);
