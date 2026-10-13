<?php
/**
 * SureCart → FluentCRM: add the contact and the product tags as soon as a checkout
 * is confirmed, including "Purchase on Invoice" orders that are still Processing.
 */
add_action('surecart/checkout_confirmed', function ($checkout) {
    if (!function_exists('FluentCrmApi') || !class_exists('\SureCart\Models\Checkout') || empty($checkout->id)) {
        return;
    }

    // SureCart product name (or product ID) => FluentCRM tag name(s). Use your own names.
    $productTags = [
        'Beispielprodukt' => ['KAUF: Beispielprodukt'],
    ];

    // Status for NEW contacts only ('pending' sends FluentCRM's double opt-in email).
    // Existing contacts keep their current status.
    $newContactStatus = 'subscribed';

    // Reload the checkout with its line items, prices and products.
    $checkout = \SureCart\Models\Checkout::with(['line_items', 'line_item.price', 'price.product'])->find($checkout->id);
    if (!$checkout || is_wp_error($checkout) || empty($checkout->email)) {
        return;
    }

    $tagNames = [];
    foreach ($checkout->line_items->data ?? [] as $item) {
        $product = $item->price->product ?? null;
        $keys    = is_object($product) ? [$product->name ?? '', $product->id ?? ''] : [(string) $product];
        foreach ($keys as $key) {
            if ($key !== '' && isset($productTags[$key])) {
                $tagNames = array_merge($tagNames, (array) $productTags[$key]);
            }
        }
    }

    if (!$tagNames) {
        return;
    }

    $tagIds = \FluentCrm\App\Models\Tag::whereIn('title', array_unique($tagNames))->pluck('id')->toArray();
    if (!$tagIds) {
        return;
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