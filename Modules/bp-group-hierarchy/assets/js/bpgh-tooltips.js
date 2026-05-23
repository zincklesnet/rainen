/**
 * BP Group Hierarchy — Tooltip JS
 * Fetches and displays rich tooltips on group avatar hover.
 * @since 2.0.0
 */
(function ($) {
    'use strict';

    var cache = {};
    var hoverTimer = null;
    var activeTooltip = null;

    $(document).on('mouseenter', '[data-bpgh-tooltip]', function (e) {
        var $el = $(this);
        var groupId = $el.data('bpgh-tooltip');

        if (!groupId) return;

        clearTimeout(hoverTimer);

        hoverTimer = setTimeout(function () {
            showTooltip($el, groupId);
        }, 300);
    });

    $(document).on('mouseleave', '[data-bpgh-tooltip]', function () {
        clearTimeout(hoverTimer);
        hideTooltip();
    });

    function showTooltip($el, groupId) {
        if (cache[groupId]) {
            renderTooltip($el, cache[groupId]);
            return;
        }

        $.ajax({
            url: bpghTooltips.ajaxUrl,
            data: {
                action: 'bpgh_tooltip_data',
                group_id: groupId
            },
            success: function (response) {
                if (response.success && response.data) {
                    cache[groupId] = response.data;
                    renderTooltip($el, response.data);
                }
            }
        });
    }

    function renderTooltip($el, data) {
        hideTooltip();

        var html = '<div class="bpgh-tooltip-popup bpgh-visible">';

        // Header
        html += '<div class="bpgh-tooltip__header">';
        if (data.avatar) {
            html += '<img src="' + escHtml(data.avatar) + '" class="bpgh-tooltip__avatar" alt="" />';
        }
        html += '<div class="bpgh-tooltip__title"><strong>' + escHtml(data.name) + '</strong>';
        if (data.is_premium) {
            html += ' <span class="bpgh-badge bpgh-badge--premium">&#9733;</span>';
        }
        html += '</div></div>';

        // Description
        if (data.description) {
            html += '<p class="bpgh-tooltip__desc">' + escHtml(data.description) + '</p>';
        }

        // Meta
        html += '<div class="bpgh-tooltip__meta">';
        html += '<span>' + data.member_count + ' member' + (data.member_count !== 1 ? 's' : '') + '</span>';
        if (data.children_count > 0) {
            html += ' &middot; <span>' + data.children_count + ' sub-group' + (data.children_count !== 1 ? 's' : '') + '</span>';
        }
        html += '</div>';

        // Parent
        if (data.parent) {
            html += '<p class="bpgh-tooltip__parent">Parent: ' + escHtml(data.parent) + '</p>';
        }

        // Category
        if (data.category) {
            html += '<p class="bpgh-tooltip__cat"><span class="bpgh-tooltip__label">Category:</span> ' + escHtml(data.category) + '</p>';
        }

        // Tags
        if (data.tags && data.tags.length) {
            html += '<p class="bpgh-tooltip__tags">';
            for (var i = 0; i < data.tags.length; i++) {
                html += '<span class="bpgh-tag">' + escHtml(data.tags[i]) + '</span> ';
            }
            html += '</p>';
        }

        // Admins
        if (data.admins && data.admins.length) {
            html += '<p class="bpgh-tooltip__admins"><span class="bpgh-tooltip__label">Admins:</span> ' + escHtml(data.admins.join(', ')) + '</p>';
        }

        html += '</div>';

        var $tooltip = $(html);
        $el.css('position', 'relative').append($tooltip);
        activeTooltip = $tooltip;

        // Position adjustment
        var offset = $tooltip.offset();
        var winWidth = $(window).width();
        if (offset && offset.left + $tooltip.outerWidth() > winWidth) {
            $tooltip.css({ right: 0, left: 'auto' });
        }
    }

    function hideTooltip() {
        if (activeTooltip) {
            activeTooltip.remove();
            activeTooltip = null;
        }
        $('.bpgh-tooltip-popup').remove();
    }

    function escHtml(str) {
        if (!str) return '';
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }

})(jQuery);
