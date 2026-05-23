<?php
/* Set AMP scripts to load */
zombify()->amp()->set_scripts( array( 'amp-form', 'amp-bind', 'amp-analytics' ) );
?>
<ol class="zfListSet">
	<?php
	$data = $story_data;
	if( isset($data["questions"]) ) {
		foreach( $data["questions"] as $question_index => $question ){

			include zombify()->locate_template( zombify()->quiz_view_dir('poll/question_'.$question["answers_format"].'.php'));

		}
	}
	?>
</ol>