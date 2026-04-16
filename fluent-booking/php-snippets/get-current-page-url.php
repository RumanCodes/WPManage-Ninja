<?php 
add_action('wp_footer', function() {
    ?>
    <script>
    (function() {
        var sourceURL = window.location.href;
        var maxTries = 30;
        var tries = 0;

        var interval = setInterval(function() {
            tries++;

            var field = document.querySelector('input[name="source_page"]');

            if (field) {
                field.value = sourceURL;
                // Trigger Svelte reactivity
                field.dispatchEvent(new Event('input', { bubbles: true }));
                field.dispatchEvent(new Event('change', { bubbles: true }));
                clearInterval(interval);
            }

            if (tries >= maxTries) clearInterval(interval);
        }, 500);
    })();
    </script>
    <?php
});