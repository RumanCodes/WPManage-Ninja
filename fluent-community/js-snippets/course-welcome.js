 <script>
  (function () {
      const bannerId = 'fcom-course-listing-welcome-banner';

      function isCourseListingPage() {
          return window.location.pathname.replace(/\/$/, '') === '/portal/courses';
      }

      function ensureBanner() {
          const portal = document.querySelector('#fluent_com_portal');
          if (!portal) return;

          let banner = document.getElementById(bannerId);

          if (!banner) {
              banner = document.createElement('div');
              banner.id = bannerId;
              banner.className = 'fcom-custom-course-banner';
              banner.innerHTML = `
                  <div class="fcom-custom-course-banner-inner">
                      <h2>Welcome to Our Courses</h2>
                      <p>
                          Browse our available courses and choose the learning path that fits your goals.
                          Each course includes structured lessons, resources, and community access.
                      </p>
                  </div>
              `;

              portal.parentNode.insertBefore(banner, portal);
          }

          banner.style.display = isCourseListingPage() ? '' : 'none';
      }

      function hookHistoryMethod(method) {
          const original = history[method];
          history[method] = function () {
              const result = original.apply(this, arguments);
              setTimeout(ensureBanner, 100);
              return result;
          };
      }

      hookHistoryMethod('pushState');
      hookHistoryMethod('replaceState');

      window.addEventListener('popstate', function () {
          setTimeout(ensureBanner, 100);
      });

      document.addEventListener('DOMContentLoaded', ensureBanner);

      const observer = new MutationObserver(ensureBanner);
      observer.observe(document.body, {
          childList: true,
          subtree: true
      });

      setTimeout(ensureBanner, 300);
      setTimeout(ensureBanner, 1000);
  })();
  </script>