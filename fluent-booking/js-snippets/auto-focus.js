        (function() {
            let preventFocus = true;

            // Stop auto focus for first 2 seconds
            setTimeout(() => {
                preventFocus = false;
            }, 2000);

            document.addEventListener('focusin', function(e) {
                if (!preventFocus) return;

                const isBookingField = e.target.closest('.fcal_booking_form');
                if (isBookingField) {
                    e.target.blur();

                    // Also reset scroll position (important!)
                    window.scrollTo({
                        top: 0,
                        behavior: 'instant'
                    });
                }
            }, true);

        })();