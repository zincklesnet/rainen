<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$cefwjc_options = array(
	'cefwjc_position_emojis',
	'cefwjc_filter_position',
	'cefwjc_skintone',
	'cefwjc_skintone_style',
	'cefwjc_search',
	'cefwjc_search_position',
	'cefwjc_recent_emojis',
);

foreach ( $cefwjc_options as $cefwjc_option ) {
	delete_option( $cefwjc_option );
}