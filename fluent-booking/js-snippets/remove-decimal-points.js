jQuery(document).ready(function($) {
    $('.fcal_slot_payment_item').each(function() {
        var priceText = $(this).text().trim();
        // Remove comma and decimals like ",00"
        priceText = priceText.replace(/(\.|,)00$/, '');

        $(this).text(priceText);
    });
});
