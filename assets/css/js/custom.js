jQuery( document ).ready(function( $ ) {

	if ($('.reign_load_more').length > 0) {
        $('.reign_load_more').hide();
        $('.wb-post-listing').infiniteScroll({
            path: '.reign_load_more a',
            append: 'article.post',
            button: '.infinite-scroll-request',
            status: '.page-load-status',
        });
    }
});