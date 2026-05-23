<?php
$zombify_poll_results = get_post_meta( get_the_ID(), 'zombify_poll_results', true );
?>
<div id="zfWrap">
		<?php
		$index = 1;

		$main_data = $data;

		if( isset($main_data["story"]) )
		foreach ($main_data["story"] as $story) {

			foreach( $story as $st_index=>$st_val ) {

				$story_data = $st_val[0];

				include zombify()->locate_template( zombify()->quiz_view_dir('story/' . $st_index . '.php'));

			}
		}
		?>

    <?php do_action( 'zombify_after_post_layout' ); ?>
</div>


