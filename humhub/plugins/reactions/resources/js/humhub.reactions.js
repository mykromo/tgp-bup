humhub.module('reactions', function (module, require, $) {
    var client = require('client');
    var baseUrl = humhub.config.baseUrl || '';

    var showPicker = function (evt) {
        var $container = evt.$trigger.closest('.reaction-container');
        var $picker = $container.find('.reaction-picker');
        $('.reaction-picker').not($picker).hide();
        $picker.toggle();
        if ($picker.is(':visible')) {
            $(document).one('click', function (e) {
                if (!$(e.target).closest('.reaction-container').is($container)) {
                    $picker.hide();
                }
            });
        }
        evt.finish();
    };

    var react = function (evt) {
        var $container = evt.$trigger.closest('.reaction-container');
        var $picker = $container.find('.reaction-picker');
        var type = evt.$trigger.data('type');
        var url = $container.data('toggle-url');
        $picker.hide();
        client.post(url, {data: {type: type}}).then(function (response) {
            updateDisplay($container, response);
        }).catch(function (err) {
            module.log.error(err, true);
        });
        evt.finish();
    };

    var updateDisplay = function ($container, data) {
        var emojis = data.emojis || {};
        var summary = data.summary || {};
        var userReaction = data.userReaction;
        var total = data.total || 0;
        var userListUrl = $container.data('userlist-url');

        var $btn = $container.find('.reaction-btn');
        if (userReaction && emojis[userReaction]) {
            $btn.html(emojis[userReaction]);
        } else {
            $btn.html('\uD83D\uDC4D');
        }
        $container.data('user-reaction', userReaction || '');

        var html = '';
        for (var type in summary) {
            if (summary[type] > 0 && emojis[type]) {
                html += '<a href="' + userListUrl + '&type=' + type + '" data-target="#globalModal" '
                    + 'class="tt" data-toggle="tooltip" title="' + type.charAt(0).toUpperCase() + type.slice(1) + '" '
                    + 'style="text-decoration:none">'
                    + emojis[type] + '<small>' + summary[type] + '</small></a> ';
            }
        }
        if (total > 0) {
            html += '<a href="' + userListUrl + '" data-target="#globalModal" '
                + 'style="text-decoration:none; color:#999; font-size:11px">(' + total + ')</a>';
        }
        $container.find('.reaction-summary').html(html);
    };

    // Inject reactions into mail conversation entries
    var initMessageReactions = function () {
        $('.mail-conversation-entry').each(function () {
            var $entry = $(this);
            if ($entry.data('reactions-init')) return;
            $entry.data('reactions-init', true);

            var entryId = $entry.data('entry-id');
            if (!entryId) return;

            var model = 'humhub\\modules\\mail\\models\\MessageEntry';
            var toggleUrl = baseUrl + '/reactions/reaction/toggle?contentModel=' + encodeURIComponent(model) + '&contentId=' + entryId;
            var userListUrl = baseUrl + '/reactions/reaction/user-list?contentModel=' + encodeURIComponent(model) + '&contentId=' + entryId;

            var $reactions = $('<span class="reaction-container reaction-message" '
                + 'data-toggle-url="' + toggleUrl + '" '
                + 'data-userlist-url="' + userListUrl + '" '
                + 'data-user-reaction="" style="font-size:12px; margin-left:5px">'
                + '<span class="reaction-trigger" style="position:relative; cursor:pointer">'
                + '<span class="reaction-btn" data-action-click="reactions.showPicker">\uD83D\uDC4D</span>'
                + '<span class="reaction-picker" style="display:none; position:absolute; bottom:20px; left:0; '
                + 'background:#fff; border:1px solid #ddd; border-radius:20px; padding:3px 6px; '
                + 'box-shadow:0 2px 8px rgba(0,0,0,.15); white-space:nowrap; z-index:1000">'
                + '<span class="reaction-emoji" data-type="like" data-action-click="reactions.react" title="Like" style="cursor:pointer;font-size:16px;padding:1px 3px;display:inline-block">\uD83D\uDC4D</span>'
                + '<span class="reaction-emoji" data-type="love" data-action-click="reactions.react" title="Love" style="cursor:pointer;font-size:16px;padding:1px 3px;display:inline-block">\u2764\uFE0F</span>'
                + '<span class="reaction-emoji" data-type="haha" data-action-click="reactions.react" title="Haha" style="cursor:pointer;font-size:16px;padding:1px 3px;display:inline-block">\uD83D\uDE02</span>'
                + '<span class="reaction-emoji" data-type="wow" data-action-click="reactions.react" title="Wow" style="cursor:pointer;font-size:16px;padding:1px 3px;display:inline-block">\uD83D\uDE2E</span>'
                + '<span class="reaction-emoji" data-type="sad" data-action-click="reactions.react" title="Sad" style="cursor:pointer;font-size:16px;padding:1px 3px;display:inline-block">\uD83D\uDE22</span>'
                + '<span class="reaction-emoji" data-type="angry" data-action-click="reactions.react" title="Angry" style="cursor:pointer;font-size:16px;padding:1px 3px;display:inline-block">\uD83D\uDE21</span>'
                + '</span></span>'
                + '<span class="reaction-summary"></span>'
                + '</span>');

            $entry.find('.conversation-entry-content').append($reactions);
        });
    };

    // Run on page load and on new messages loaded via AJAX
    $(document).on('humhub:ready humhub:modules:mail:afterLoadEntries', function () {
        initMessageReactions();
    });

    // Also run on MutationObserver for dynamic content
    if (typeof MutationObserver !== 'undefined') {
        var observer = new MutationObserver(function () {
            initMessageReactions();
        });
        $(function () {
            var target = document.querySelector('#mail-conversation-root, .mail-conversation');
            if (target) {
                observer.observe(target, {childList: true, subtree: true});
            }
        });
    }

    module.export({
        showPicker: showPicker,
        react: react,
        initMessageReactions: initMessageReactions
    });
});
