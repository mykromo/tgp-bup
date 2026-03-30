humhub.module('reactions', function (module, require, $) {
    var client = require('client');

    var showPicker = function (evt) {
        var $container = evt.$trigger.closest('.reaction-container');
        var $picker = $container.find('.reaction-picker');

        // Close all other pickers
        $('.reaction-picker').not($picker).hide();

        $picker.toggle();

        // Close picker when clicking outside
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

        // Update the reaction button emoji
        var $btn = $container.find('.reaction-btn');
        if (userReaction && emojis[userReaction]) {
            $btn.html(emojis[userReaction]);
        } else {
            $btn.html('👍');
        }

        $container.data('user-reaction', userReaction || '');

        // Rebuild summary
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

    module.export({
        showPicker: showPicker,
        react: react
    });
});
