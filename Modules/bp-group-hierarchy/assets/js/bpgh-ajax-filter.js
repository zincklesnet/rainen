/**
 * BP Group Hierarchy — AJAX Directory Filter
 * Handles category, tag, type, and sort filtering for the enhanced group directory.
 * @since 2.0.0
 */
(function ($) {
    'use strict';

    var $directory = $('#bpgh-directory');
    if (!$directory.length) return;

    var $results = $directory.find('.bpgh-directory-results');
    var perPage  = parseInt($results.data('per-page'), 10) || 20;
    var currentFilters = { category: '', tag: '', group_type: '', sort: 'active', page: 1 };

    // Initial load.
    loadGroups();

    // Category dropdown.
    $directory.on('change', '.bpgh-filter-category', function () {
        currentFilters.category = $(this).val();
        currentFilters.page = 1;
        loadGroups();
    });

    // Sort dropdown.
    $directory.on('change', '.bpgh-filter-sort', function () {
        currentFilters.sort = $(this).val();
        currentFilters.page = 1;
        loadGroups();
    });

    // Group type tabs.
    $directory.on('click', '.bpgh-type-tab', function (e) {
        e.preventDefault();
        $directory.find('.bpgh-type-tab').removeClass('bpgh-active');
        $(this).addClass('bpgh-active');
        currentFilters.group_type = $(this).data('type') || '';
        currentFilters.page = 1;
        loadGroups();
    });

    // Tag search (debounced).
    var tagTimer = null;
    $directory.on('input', '.bpgh-tag-search', function () {
        var query = $(this).val().trim();
        clearTimeout(tagTimer);

        if (query.length < 2) {
            currentFilters.tag = '';
            loadGroups();
            return;
        }

        tagTimer = setTimeout(function () {
            currentFilters.tag = query;
            currentFilters.page = 1;
            loadGroups();
        }, 400);
    });

    // Pagination clicks.
    $directory.on('click', '.bpgh-pagination a', function (e) {
        e.preventDefault();
        currentFilters.page = parseInt($(this).data('page'), 10) || 1;
        loadGroups();
        $('html, body').animate({ scrollTop: $directory.offset().top - 40 }, 300);
    });

    function loadGroups() {
        $results.html('<p class="bpgh-loading">Loading groups...</p>');

        $.ajax({
            url: bpghFilter.ajaxUrl,
            method: 'POST',
            data: {
                action:     'bpgh_filter_groups',
                nonce:      bpghFilter.nonce,
                category:   currentFilters.category,
                tag:        currentFilters.tag,
                group_type: currentFilters.group_type,
                sort:       currentFilters.sort,
                page:       currentFilters.page,
                per_page:   perPage
            },
            success: function (response) {
                if (response.success) {
                    $results.html(response.data.html);
                } else {
                    $results.html('<p class="bpgh-no-results">Unable to load groups.</p>');
                }
            },
            error: function () {
                $results.html('<p class="bpgh-no-results">Connection error. Please try again.</p>');
            }
        });
    }

    // Tag search autocomplete for tag search widgets.
    $(document).on('input', '.bpgh-tag-search-input', function () {
        var $input = $(this);
        var $suggestions = $input.siblings('.bpgh-tag-suggestions');
        var query = $input.val().trim();

        if (query.length < 2) {
            $suggestions.hide().empty();
            return;
        }

        $.ajax({
            url: bpghFilter.ajaxUrl,
            data: {
                action: 'bpgh_search_tags',
                nonce:  bpghFilter.nonce,
                q:      query
            },
            success: function (response) {
                if (response.success && response.data.length) {
                    var html = '';
                    for (var i = 0; i < response.data.length; i++) {
                        html += '<a href="#" data-tag="' + response.data[i] + '">' + response.data[i] + '</a>';
                    }
                    $suggestions.html(html).show();
                } else {
                    $suggestions.hide().empty();
                }
            }
        });
    });

    $(document).on('click', '.bpgh-tag-suggestions a', function (e) {
        e.preventDefault();
        var tag = $(this).data('tag');
        var $widget = $(this).closest('.bpgh-tag-search-widget');
        $widget.find('.bpgh-tag-search-input').val(tag);
        $widget.find('.bpgh-tag-suggestions').hide().empty();
        $widget.find('form').submit();
    });

})(jQuery);
