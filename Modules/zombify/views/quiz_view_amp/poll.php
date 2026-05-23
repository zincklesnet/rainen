<?php
$zombify_poll_results = get_post_meta( get_the_ID(), 'zombify_poll_results', true );
/* Set AMP scripts to load */
zombify()->amp()->set_scripts( array( 'amp-form', 'amp-bind', 'amp-analytics' ) );
?>
<div id="zfWrap">
	<ol class="zfListSet">
		<?php
		if( isset($data["questions"]) ) {
			foreach( $data["questions"] as $question_index => $question ) {
				include zombify()->locate_template( zombify()->quiz_view_dir('poll/question_'.$question["answers_format"].'.php'));
			}
		}
		?>
	</ol>
	<?php do_action( 'zombify_after_post_layout' ); ?>
</div>
