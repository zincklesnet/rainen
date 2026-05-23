<!--Tab: Create or edit popup-->
<?php

// Popup creation starts here
if ( !empty($_GET['action']) && 'edit' == $_GET['action'] ) {
	global $ADL_LP, $wpdb;
	$popup_id = (!empty($_GET['id'])) ? absint($_GET['id']) : 0;
	$sql = $wpdb->prepare("SELECT * FROM {$ADL_LP->popups_table_name} WHERE id=%d LIMIT 1", $popup_id);
	$popup = $wpdb->get_row($sql);
	?>
	<!--popup editing code goes here-->
	<div class="container">
		<div class="row">
			<div class="col-md-8">
				<h1 class="adl_lp_title">Edit Popup</h1>
				<form action="" method="post" data-id="<?= !empty( $popup->id)? $popup->id : ''; ?>" id="editPopup">
					<div class="form-group">
						<input type="text" name="lp_title" value="<?= !empty( $popup->name)? $popup->name : ''; ?>" class="form-control" id="lp_title" placeholder="Enter a title or Choose a Template from the right">
					</div>
					<?php
					$content = ( !empty( $popup->content  ) ) ? wp_kses_post( $popup->content ): 'Enter some content or Choose a template from the right side to edit and save as popup content';

					wp_editor($content, 'content', array('editor_height' => 400));

					?>
					<button type="submit" class="btn btn-primary btn-outline-rounded"> Update Popup</button>
					<?php wp_nonce_field( $ADL_LP->nonceAction(), $ADL_LP->nonceName()); ?>
				</form>
			</div> <!--ends .col-md-8 left column-->



			<div class="col-md-4">
				<h4>Show Template Type: </h4>
				<form action="" method="post" id="showTemplateTypeForm">
					<?php
					$site_types = array(
						'All types of Sites',
						'Business Website',
						'Business Website With Products',
						'Business Website With Products & Affiliate Program',
						'Affiliate/Review Site',
						'Niche Sites',
						'Medical Niche Site',
						'Amazon Site',
						'Adsense Site',
						'Ecommerce Site',
						'ISP - Hosting Provider Site',
					);

					?>
					<p>
						<select name="adl_lp_type" id="adl_lp_type">
							<?php
							$adl_lp_type = 0; // for test purpose only
							foreach ( $site_types as $site_type => $name ) { ?>
								<option value="<?= $site_type; ?>"  <?php selected($site_type, $adl_lp_type); ?> > <?= $name; ?></option>
							<?php }  ?>


						</select>
					</p>
					<?php wp_nonce_field( $ADL_LP->nonceAction(), $ADL_LP->nonceName()); ?>
				</form>

				<div id="ChooseTemplate">
					<p>Choose a Template:</p>
					<?php
					global $wpdb;
					// show all templates
					$sql = 'SELECT * FROM '.$ADL_LP->template_table_name .' LIMIT 30';
					$results = $wpdb->get_results($sql);
					$html ='';
					foreach ( $results as $result   ) {
						$html .= "<h6><a href='#' id='id-{$result->id}' data-id='{$result->id}'>{$result->name} </a></h6>";
					}
					echo $html;
					?>
				</div>
			</div>  <!--ends .col-md-4 right column-->

		</div>
	</div>





	<?php

} else {
// popup creation codes go here
	?>
	<!--Tab: Create page content-->
	<!--<h3 class="head text-center">Create New Legal Page</h3>-->
	<div class="container">
		<div class="row">
			<div class="col-md-8">
				<h1 class="adl_lp_title">Add New Popup</h1>
				<form action="" method="post" id="addNewPopup">
					<div class="form-group">
						<input type="text" name="lp_title" class="form-control" id="lp_title" placeholder="Enter a title or Choose a Template from the right">
					</div>
					<?php

					$content = ( isset( $_POST['content']  ) ) ? wp_kses_post( $_POST['content'] ): 'Enter some content or Choose a template from the right side to edit and save as popup content';

					wp_editor($content, 'content', array('editor_height' => 400));

					?>
					<button type="submit" class="btn btn-primary btn-outline-rounded"> Save Popup</button>
					<?php wp_nonce_field( $ADL_LP->nonceAction(), $ADL_LP->nonceName()); ?>
				</form>
			</div> <!--ends .col-md-8 left column-->



			<div class="col-md-4">
				<h4>Show Template Type: </h4>
				<form action="" method="post" id="showTemplateTypeForm">
					<?php
					$site_types = array(
						'All types of Sites',
						'Business Website',
						'Business Website With Products',
						'Business Website With Products & Affiliate Program',
						'Affiliate/Review Site',
						'Niche Sites',
						'Medical Niche Site',
						'Amazon Site',
						'Adsense Site',
						'Ecommerce Site',
						'ISP - Hosting Provider Site',
					);

					?>
					<p>
						<select name="adl_lp_type" id="adl_lp_type">
							<?php
							$adl_lp_type = 0; // for test purpose only
							foreach ( $site_types as $site_type => $name ) { ?>
								<option value="<?= $site_type; ?>"  <?php selected($site_type, $adl_lp_type); ?> > <?= $name; ?></option>
							<?php }  ?>


						</select>
					</p>
					<?php wp_nonce_field( $ADL_LP->nonceAction(), $ADL_LP->nonceName()); ?>
				</form>

				<div id="ChooseTemplate">
					<p>Choose a Template:</p>
					<?php
					global $wpdb;
					// show all templates
					$sql = 'SELECT * FROM '.$ADL_LP->template_table_name .' LIMIT 30';
					$results = $wpdb->get_results($sql);
					$html ='';
					foreach ( $results as $result   ) {
						$html .= "<h6><a href='#' id='id-{$result->id}' data-id='{$result->id}'>{$result->name} </a></h6>";
					}
					echo $html;
					?>
				</div>
			</div>  <!--ends .col-md-4 right column-->

		</div>
	</div>





	<?php
} // ends popup creation code
?>




