  (function () {
    const form = document.querySelector('form[data-form_id="115"]');
    if (!form) return;

    const input = form.querySelector('input[name="datetime"]');
    if (!input) return;

    const pad = (n) => String(n).padStart(2, '0');
    const toYMD = (d) => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;

    const isBlockedDynamic = (date) => {
      const today = new Date();
      today.setHours(0, 0, 0, 0);

      const blocked = [
        toYMD(today),
        toYMD(new Date(today.getFullYear(), today.getMonth(), today.getDate() + 1)),
        toYMD(new Date(today.getFullYear(), today.getMonth(), today.getDate() + 2))
      ];

      const dow = date.getDay(); // 0=Sun, 6=Sat
      return dow === 0 || dow === 6 || blocked.includes(toYMD(date));
    };

    const timer = setInterval(() => {
      if (!input._flatpickr) return;
      const fp = input._flatpickr;
      const existing = fp.config.disable || [];
      fp.set('disable', [...existing, isBlockedDynamic]);
      clearInterval(timer);
    }, 100);
  })();