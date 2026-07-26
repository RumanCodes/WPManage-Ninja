(function () {
        var FORM_ID   = 118;
        var FROM_NAME = 'arrival_date';
        var TO_NAME   = 'departure_date';

        function link(from, to) {
            function sync(dates) {
                if (!dates || !dates.length) {
                    to.set('minDate', 'today');
                    return;
                }
                var min = new Date(dates[0]);
                min.setDate(min.getDate() + 1); // departure at least the next day
                to.set('minDate', min);

                var current = to.selectedDates[0];
                if (current && current < min) {
                    to.clear();
                }
            }
            from.config.onChange.push(sync);
            sync(from.selectedDates);
        }

        function init() {
            var forms = document.querySelectorAll('form[data-form_id="' + FORM_ID + '"]');
            Array.prototype.forEach.call(forms, function (form) {
                var from = form.querySelector('[name="' + FROM_NAME + '"]');
                var to   = form.querySelector('[name="' + TO_NAME + '"]');
                if (!from || !to) return;

                var tries = 0;
                var timer = setInterval(function () {
                    if (from._flatpickr && to._flatpickr) {
                        clearInterval(timer);
                        link(from._flatpickr, to._flatpickr);
                    } else if (++tries > 50) {
                        clearInterval(timer);
                    }
                }, 100);
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    })();