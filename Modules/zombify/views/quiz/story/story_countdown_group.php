<div class="zf-type-wrapper"><i class="zf-icon zf-icon-type-countdown"></i></div>

<div class="zf-list-container zf-item-wrapper" data-reverse="1">
	<?php echo $this->renderAddGroupButton(['story', 'story_countdown', 'list'], esc_html__('Add item', 'zombify'), '', ['reverse_order' => 1]) ?>
	<div class="zf-list_container">
		<?php echo $this->renderGroups(['story', 'story_countdown', 'list'], $group_name_prefix, $data, $action, ['story', 'story_countdown'], 'list', '', ''); ?>
	</div>
</div>