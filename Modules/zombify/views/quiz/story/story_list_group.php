<div class="zf-type-wrapper"><i class="zf-icon zf-icon-type-list"></i></div>

<div class="zf-list-container zf-item-wrapper">
	<div class="zf-list_container">
		<?php echo $this->renderGroups(['story', 'story_list', 'list'], $group_name_prefix, $data, $action, ['story', 'story_list'], 'list', '', ''); ?>
	</div>
	<?php echo $this->renderAddGroupButton(['story', 'story_list', 'list'], esc_html__('Add item', 'zombify')) ?>
</div>