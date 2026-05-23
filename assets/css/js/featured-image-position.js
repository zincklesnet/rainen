( function( wp ) {
	var registerPlugin = wp.plugins.registerPlugin;
	var PluginDocumentSettingPanel = wp.editPost.PluginDocumentSettingPanel;
	var el = wp.element.createElement;
	var useSelect = wp.data.useSelect;
	var useDispatch = wp.data.useDispatch;
	var SelectControl = wp.components.SelectControl;
	var __ = wp.i18n.__;

	var FeaturedImagePositionPanel = function() {
		var meta = useSelect( function( select ) {
			return select( 'core/editor' ).getEditedPostAttribute( 'meta' ) || {};
		}, [] );

		var editPost = useDispatch( 'core/editor' );

		var position = meta.reign_featured_image_position || '';

		return el(
			PluginDocumentSettingPanel,
			{
				name: 'reign-featured-image-position',
				title: __( 'Featured Image Layout', 'reign' ),
				icon: 'format-image',
			},
			el( SelectControl, {
				label: __( 'Display Position', 'reign' ),
				value: position,
				options: [
					{ label: __( 'Default (Above Content)', 'reign' ), value: '' },
					{ label: __( 'Behind Title (Hero)', 'reign' ), value: 'behind' },
					{ label: __( 'Beside Title', 'reign' ), value: 'beside' },
					{ label: __( 'Hidden', 'reign' ), value: 'hidden' },
				],
				onChange: function( value ) {
					editPost.editPost( {
						meta: { reign_featured_image_position: value },
					} );
				},
				help: __( 'Choose how the featured image appears on the single post page.', 'reign' ),
			} )
		);
	};

	registerPlugin( 'reign-featured-image-position', {
		render: FeaturedImagePositionPanel,
	} );
} )( window.wp );
