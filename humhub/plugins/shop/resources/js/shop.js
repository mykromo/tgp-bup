// Lazy loading for product images
(function () {
    function lazyLoad() {
        var images = document.querySelectorAll('img[data-src]:not(.loaded)');
        images.forEach(function (img) {
            var rect = img.getBoundingClientRect();
            if (rect.top < window.innerHeight + 200 && rect.bottom > -200) {
                img.src = img.getAttribute('data-src');
                img.removeAttribute('data-src');
                img.onload = function () { img.classList.add('loaded'); };
                img.onerror = function () { img.classList.add('loaded'); };
            }
        });
    }

    // Run on scroll, resize, and initial load
    var throttle;
    function onScroll() {
        clearTimeout(throttle);
        throttle = setTimeout(lazyLoad, 100);
    }

    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onScroll, { passive: true });
    document.addEventListener('DOMContentLoaded', lazyLoad);

    // Also run after HumHub PJAX loads
    if (typeof jQuery !== 'undefined') {
        jQuery(document).on('humhub:ready humhub:modules:content:afterLoad', lazyLoad);
    }

    // Run after a short delay for dynamic content
    setTimeout(lazyLoad, 300);
})();

// Countdown timers for sale expiration
(function () {
    function updateCountdowns() {
        var els = document.querySelectorAll('.shop-countdown[data-expires]');
        els.forEach(function (el) {
            var expires = parseInt(el.getAttribute('data-expires'), 10) * 1000;
            var now = Date.now();
            var diff = expires - now;
            var textEl = el.querySelector('.shop-timer-text');
            if (!textEl) return;
            if (diff <= 0) {
                textEl.textContent = 'Sale ended';
                el.classList.add('shop-countdown-ended');
                return;
            }
            var d = Math.floor(diff / 86400000);
            var h = Math.floor((diff % 86400000) / 3600000);
            var m = Math.floor((diff % 3600000) / 60000);
            var s = Math.floor((diff % 60000) / 1000);
            var parts = [];
            if (d > 0) parts.push(d + 'd');
            if (h > 0) parts.push(h + 'h');
            parts.push(m + 'm');
            parts.push(s + 's');
            textEl.textContent = parts.join(' ') + ' left';
        });
    }

    updateCountdowns();
    setInterval(updateCountdowns, 1000);

    // Re-init after PJAX
    if (typeof jQuery !== 'undefined') {
        jQuery(document).on('humhub:ready humhub:modules:content:afterLoad', updateCountdowns);
    }
})();

// Wishlist toggle via AJAX
(function () {
    if (typeof jQuery === 'undefined') return;
    jQuery(document).on('click', '.shop-wishlist-btn', function (e) {
        e.preventDefault();
        var $btn = jQuery(this);
        var url = $btn.data('url');
        if (!url) return;
        jQuery.post(url).done(function (res) {
            if (res.wishlisted) {
                $btn.addClass('active').find('i').removeClass('fa-heart-o').addClass('fa-heart');
            } else {
                $btn.removeClass('active').find('i').removeClass('fa-heart').addClass('fa-heart-o');
            }
        });
    });
})();
