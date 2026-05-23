<?php

/**
 * Template for displaying the Events Loop
 * You can copy this file to your-theme
 * and then edit the layout.
 */


$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

$args = array(
	'post_type'      => 'event',
	'order'          => 'ASC',
	'orderby'		 => 'meta_value_num',
	'meta_key'		 => 'event-unix',
	'paged'          => $paged,
	'posts_per_page' => 9,

	'meta_query' => array(
		array(
			'key'		=> 'event-unix',
			'value'		=> current_time( 'timestamp' ),
			'compare'	=> '>=',
			'type' 		=> 'NUMERIC',
		),
	),

);

$wp_query = new WP_Query( $args );
?>

	<?php if ( $wp_query->have_posts() ) : ?>

		<style>
			.event-wrapper {
				display: grid;
				grid-template-columns: minmax(200px, 1fr) minmax(200px, 1fr) minmax(200px, 1fr);
				grid-gap: 10px;
				//background-color: #fff;
				color: #222;
			}

			.event-box {
				background-color: #eee;
				border-radius: 5px;
				padding: 20px;
				//color: #fff;
				//font-color: #111;
			}
		</style>
		

		<div class="event-wrapper">
		
			<?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); 	?>

				<div class="event-box">

					<h3>
						<a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>">
						<?php the_title(); ?></a>
					</h3>


					<?php 
					
					//the_excerpt(); 
					
					echo wp_trim_words(
						get_the_content(), 
						25, // the number of words
						'...'
					);					
					
					?>
					
					<p>

						<?php
						
						$meta = get_post_meta($post->ID );

						if ( ! empty( $meta['event-date'][0] ) ) {
							echo '<br>' . __( 'Date', 'bp-simple-events' ) . ':&nbsp;' . $meta['event-date'][0];
						}

						if ( ! empty( $meta['event-address'][0] ) ) {
							echo '<br>' . __( 'Location', 'bp-simple-events' ) . ':&nbsp;' . $meta['event-address'][0];
						}

						?>

						<?php $categories = get_the_category(); ?>

						<?php if ( ! empty( $categories ) ) : ?>

							<?php echo '<br>' . __( 'Category:', 'bp-simple-events' ); ?> <?php the_category(', ') ?>

						<?php endif; ?>
						
					</p>

				</div>

			<?php endwhile; ?>
	

		</div><!-- end wrapper div -->

	<div class="">
		<?php echo pp_events_pagination( $wp_query ); ?>
	</div>

	<?php else : ?>

		<div class="">
			<?php _e( 'There are no upcoming Events.', 'bp-simple-events' ); ?>
		</div>

	<?php endif; ?>


<?php wp_reset_query(); ?>

