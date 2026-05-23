<?php

 /**
 * Template for displaying a single Event
 * You can copy this file to your-theme
 * and then edit the layout.
 */

?>


	<?php

	$meta = get_post_meta($post->ID );

	$author_id = get_the_author_meta('ID');
	$author_name = get_the_author_meta('display_name');
	$user_link = bp_core_get_user_domain( $author_id );

	$is_author = ( get_current_user_id() == $author_id ? true : false );

	?>


	<?php // show auther's avatar and name  ?>

	<div>
		<a href="<?php echo bp_core_get_user_domain( $author_id ); ?>">
		<?php echo bp_core_fetch_avatar( array( 'item_id' => $author_id, 'type' => 'thumb' ) ); ?>
		&nbsp;
		<?php echo $author_name; ?></a>
	</div>


	<?php

	// maybe show the featured image

	$display_image = get_site_option( 'pp_events_display_image' );

	if ( $display_image == '1' ) {

		if ( has_post_thumbnail() ) {
			echo '<div>';
			the_post_thumbnail( 'large' );
			echo '</div>';
		}

	}
	?>

	<?php

	// show the content / description

	$content = get_the_content();
	$content = str_replace(']]>', ']]&gt;', $content);
	echo '<div>' . wpautop( $content ) . '</div>';

	?>

	<?php
	$meta = get_post_meta($post->ID );

	if ( ! empty( $meta['event-date'][0] ) ) {
		echo '<div>' . __( 'Date', 'bp-simple-events' ) . ':&nbsp;' . $meta['event-date'][0] . '</div>';;
	}

	if ( ! empty( $meta['event-time'][0] ) ) {
		echo '<div>' . __( 'Time', 'bp-simple-events' ) . ':&nbsp;' . $meta['event-time'][0] . '</div>';;
	}

	if ( ! empty( $meta['event-url'][0] ) ) {
		echo '<div>' . __( 'Url', 'bp-simple-events' ) . ':&nbsp;' . pp_event_convert_url( $meta['event-url'][0] ) . '</div>';;
	}

	?>


	<?php $categories = get_the_category(); ?>

	<?php if ( ! empty( $categories ) ) : ?>

		<div><?php _e( 'Category:', 'bp-simple-events' ); ?> <?php the_category(', ') ?></div>

	<?php endif; ?>


	<?php
	
	if ( ! empty( $meta['event-address'][0] ) ) {
		echo '<div>' . __( 'Location', 'bp-simple-events' ) . ':&nbsp;' . $meta['event-address'][0] . '</div>';;
	}

	//  MAP

	if ( ! empty( $meta['event-latlng'][0] ) ) : ?>
		<p>
		<?php

			if ( wp_script_is( 'google-places-api', 'registered' ) ) {
				wp_print_scripts( 'google-places-api' );
			}

			echo '<style type="text/css"> .single_map_canvas img { max-width: none; } </style>';

			$map_id = uniqid( 'pp_map_' );
		?>

		<div class="pp_map_canvas" id="<?php echo esc_attr( $map_id ); ?>" style="height: 250px; width: 100%;"></div>

		<script type="text/javascript">
			var map_<?php echo $map_id; ?>;
			function pp_run_map_<?php echo $map_id ; ?>() {
				var location = new google.maps.LatLng(<?php echo $meta['event-latlng'][0]; ?>);
				var map_options = {
					zoom: 12,
					maxZoom: 15,
					center: location,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				}
				map_<?php echo $map_id ; ?> = new google.maps.Map(document.getElementById("<?php echo $map_id ; ?>"), map_options);
				var marker = new google.maps.Marker({
				position: location,
				map: map_<?php echo $map_id ; ?>
				});
			}
			pp_run_map_<?php echo $map_id ; ?>();
		</script>
		</p>
	<?php endif; ?>

