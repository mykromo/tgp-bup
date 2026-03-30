humhub.module('reactions', function (module, require, $) {
    var client = require('client');
    var hideTimer;

    // Show picker on hover (desktop) or click (mobile)
    $(document).on('mouseenter', '.reaction-trigger', function () {
        clearTimeout(hideTimer);
        var $picker = $(this).find('.reaction-picker');
        $('.reaction-picker').not($picker).hide();
        $picker.stop(true).fadeIn(150);
    });

    $(document).on('mouseleave', '.reaction-trigger', function () {
        var $picker = $(this).find('.reaction-picker');
        hideTimer = setTimeout(function () {
            $picker.stop(true).fadeOut(100);
        }, 300);
    });

    // Fallback: click to toggle (mobile)
    $(document).on('click', '.reaction-btn-toggle', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var $picker = $(this).siblings('.reaction-picker');
        $('.reaction-picker').not($picker).hide();
        $picker.toggle();
    });

    // React on emoji click
    $(document).on('click', '.reaction-emoji-btn', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var $emoji = $(this);
        var $container = $emoji.closest('.reaction-container');
        var type = $emoji.data('type');
        var url = $container.data('toggle-url');

        $container.find('.reaction-picker').hide();

        // Optimistic UI: show selected emoji immediately
        $container.find('.reaction-btn').html($emoji.html());

        client.post(url, {data: {type: type}}).then(function (res) {
            refresh($container, res);
        }).catch(function (err) {
            module.log.error(err, true);
        });
    });

    // Close all pickers on outside click
    $(document).on('click', function (e) {
        if (!$(e.target).closest('.reaction-trigger').length) {
            $('.reaction-picker').hide();
        }
    });

    // Remove bullet separators (·) from post links and comment/reply links
    function cleanSeparators() {
        $('.wall-entry-links, .wall-entry-controls').each(function () {
            var el = this;
            if ($(el).data('reactions-cleaned')) return;
            $(el).data('reactions-cleaned', true);
            var nodes = el.childNodes;
            for (var i = nodes.length - 1; i >= 0; i--) {
                if (nodes[i].nodeType === 3) {
                    nodes[i].textContent = '';
                }
            }
        });
    }

    $(document).on('humhub:ready', cleanSeparators);
    $(document).on('humhub:stream:afterAppend humhub:stream:afterInit', cleanSeparators);
    $(function () { setTimeout(cleanSeparators, 500); });
    // Also clean after comments are loaded/added
    $(document).on('humhub:comment:afterLoad humhub:comment:afterAdd', cleanSeparators);
    // MutationObserver fallback for dynamic content
    if (typeof MutationObserver !== 'undefined') {
        $(function () {
            new MutationObserver(function () {
                cleanSeparators();
            }).observe(document.body, { childList: true, subtree: true });
        });
    }

    function refresh($c, data) {
        var emojis = data.emojis || {};
        var summary = data.summary || {};
        var ur = data.userReaction;
        var total = data.total || 0;
        var listUrl = $c.data('userlist-url');

        // Update button
        $c.find('.reaction-btn').html(ur && emojis[ur] ? emojis[ur] : '\uD83D\uDC4D');
        $c.find('.reaction-label').html(ur ? ur.charAt(0).toUpperCase() + ur.slice(1) : 'Like');
        $c.attr('data-user-reaction', ur || '').data('user-reaction', ur || '');

        // Rebuild summary
        var h = '';
        for (var t in summary) {
            if (summary[t] > 0 && emojis[t]) {
                h += '<a href="' + listUrl + '&type=' + t + '" data-target="#globalModal" '
                   + 'title="' + t.charAt(0).toUpperCase() + t.slice(1) + '">'
                   + emojis[t] + '<small>' + summary[t] + '</small></a> ';
            }
        }
        if (total > 0) {
            h += '<a href="' + listUrl + '" data-target="#globalModal" class="reaction-total">(' + total + ')</a>';
        }
        $c.find('.reaction-summary').html(h);
    }

    module.export({});
});
