<?php
/**
 * Search
 *
 * @package bbPress
 * @subpackage Theme
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

$placeholder = esc_attr__( 'Search forum posts...', 'reign' );

if ( bbp_allow_search() ) :
	?>

	<div class="bbp-search-form">
		<form role="search" method="get" id="bbp-search-form">
			<div>
				<label class="screen-reader-text hidden" for="bbp_search"><?php esc_html_e( 'Search for:', 'reign' ); ?></label>
				<input type="hidden" name="action" value="bbp-search-request" />
				<input type="text" value="<?php bbp_search_terms(); ?>" name="bbp_search" id="bbp_search" placeholder="<?php echo esc_attr( $placeholder ); ?>" />
				<input class="button" type="submit" id="bbp_search_submit" value="<?php esc_attr_e( 'Search', 'reign' ); ?>" />
			</div>
		</form>
	</div>

	<?php
endif;
