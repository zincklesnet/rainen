<!--Tab: Create or edit page popup-->
<?php

$adl_popups = (!empty($args['popups'])) ? $args['popups'] : null; // data are passed to pages wrapped in the $args variable.

?>
<div class="container-fluid" id="adl_legal_popup_container">
	<div class="row">
		<div class="col-md-12">
			<table class="table table-striped">
				<thead>
				<a href="#" id="refreshPage" class="btn btn-primary btn-xs pull-right"> <span class="glyphicon glyphicon-refresh"></span> Refresh popups lists </a>
				<a href="<?= get_admin_url() . 'admin.php?page=adl-create-popup'; ?>" id="createLPPopupBtn" class="btn btn-primary btn-xs pull-left"> <span class="glyphicon glyphicon-plus-sign"></span> Add New Legal Popup </a>
				<tr>
					<th>Popup ID</th>
					<th>Popup Name</th>
					<th>Popup ShortCode</th>
					<th class="text-center">Action</th>
				</tr>
				</thead>

				<?php
                $adl_popups = !empty($adl_popups) ? $adl_popups : array();
				if ( count($adl_popups) ) {


					foreach ($adl_popups as $adl_popup) {
						?>
						<tr id="id-<?= $adl_popup->id; ?>" data-id="<?= $adl_popup->id; ?>" >
							<td><?= $adl_popup->id; ?></td>

							<td><a href="<?= $ADL_LP->adl_lp_action_link($adl_popup->id, 'edit', 'adl-create-popup'); ?>" title="Edit this"> <?= $adl_popup->name; ?>  </a></td>

							<td>[adl_lp_popup id=<?= $adl_popup->id; ?>]</td>

							<td class="text-center"><a class='btn btn-info btn-xs' href="<?php $ADL_LP->adl_lp_action_link($adl_popup->id, 'edit', 'adl-create-popup'); ?>" title="Edit Popup"><span class="glyphicon glyphicon-edit"></span> Edit</a> <a href="<?php $ADL_LP->adl_lp_action_link($adl_popup->id, 'delete', 'adl-create-popup'); ?>" data-id="<?= $adl_popup->id; ?>" class="btn btn-danger btn-xs deleteLegalPopup" title="Delete it permanently"><span class="glyphicon glyphicon-trash"></span> Delete</a></td>
						</tr>

					<?php  }
				}else {
					echo '<tr> <td> <p> Looks like you have not created any popups yet.</p> <a href="'. get_admin_url() . 'admin.php?page=adl-create-popup" <button class="btn button-primary" id="createNewPopup">Create a new popup</button> </td> </tr>';
				}
				?>
			</table> <!--ends .table table-striped-->
		</div>   <!--ends .col-md-12-->
	</div> <!--ends .row-->
</div><!--ends .container-->







