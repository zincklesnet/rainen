<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Wbcom_Postmeta_Mgmt_Layout_Section' ) ) :

	/**
	 * @class Wbcom_Postmeta_Mgmt_Layout_Section
	 */
	class Wbcom_Postmeta_Mgmt_Layout_Section {

		/**
		 * The single instance of the class.
		 *
		 * @var Wbcom_Postmeta_Mgmt_Layout_Section
		 */
		protected static $_instance   = null;
		protected static $_slug       = 'layout';
		protected static $_theme_slug = 'reign';

		/**
		 * Main Wbcom_Postmeta_Mgmt_Layout_Section Instance.
		 *
		 * Ensures only one instance of Wbcom_Postmeta_Mgmt_Layout_Section is loaded or can be loaded.
		 *
		 * @return Wbcom_Postmeta_Mgmt_Layout_Section - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Wbcom_Postmeta_Mgmt_Layout_Section Constructor.
		 */
		public function __construct() {
			$this->init_hooks();
		}

		/**
		 * Hook into actions and filters.
		 */
		private function init_hooks() {
			add_filter( 'wbcom_metabox_add_vertical_tab', array( $this, 'add_vertical_tab' ), 10, 1 );
			add_filter( 'render_wbcom_metabox_content_for_' . self::$_slug, array( $this, 'render_metabox_content' ), 10 );
		}

		public function add_vertical_tab( $tabs ) {
			$tabs[ self::$_slug ] = array(
				'label'      => __( 'Layout', 'reign' ),
				'icon-class' => 'dashicons dashicons-grid-view',
			);
			return $tabs;
		}

		public function render_metabox_content( $post ) {
			global $wbcom_render_postmeta_fields;

			$post_id                  = $post->ID;
			$reign_subheader_settings = get_post_meta( $post_id, '_subheader_overwrite', true );
			$wbcom_metabox_data       = get_post_meta( $post_id, 'reign_wbcom_metabox_data', true );

			$sub_header_banner_image = ( isset( $wbcom_metabox_data['subheader']['sub_header_banner_image'] ) && $wbcom_metabox_data['subheader']['sub_header_banner_image'] != '' ) ? $wbcom_metabox_data['subheader']['sub_header_banner_image'] : '';

			?>
			<div id="reign-settings-tabs">
				<div class="reign-settings-loader"></div>
				<div class="reign-settings-main-wrapper">
					<div class="reign-settings-nav-wrapper">
						<ul>
							<li><a href="#reign-layout-settings" class="reign-layout-settings"><?php esc_html_e( 'Layout', 'reign' ); ?></a></li>
							<li><a href="#reign-subheader-settings" class="reign-subheader-settings"><?php esc_html_e( 'Subheader', 'reign' ); ?></a></li>
						</ul>
					</div>
					<div class="reign-settings-options-wrapper">
						<div id="reign-layout-settings">
							<h3><?php esc_html_e( 'Layout Settings', 'reign' ); ?></h3>
							<div class="reign-options-inner-wrapper">
								<?php
								/**
								 * render check box options :: start
								 */
								$post_type = get_post_type();
								if ( $post_type == 'page' ) {
									$args = array(
										'label'        => __( 'Display Page Title', 'reign' ),
										'desc'         => __( 'Allows you to display page title for this post.', 'reign' ),
										'section_name' => self::$_slug,
										'field_name'   => 'display_page_title',
										'option'       => 'on',
										'default'      => 'on',
									);
									$wbcom_render_postmeta_fields->render_checkbox_option( $args );
								}
								if ( 'page' === $post_type ) {
									$options_array = array(
										'0'   => __( 'Default', 'reign' ),
										'no'  => __( 'No', 'reign' ),
										'yes' => __( 'Yes', 'reign' ),
									);

									$args = array(
										'label'         => __( 'Hide Left Panel Menu', 'reign' ),
										'desc'          => __( 'Allows you to enable or disable left panel menu.', 'reign' ),
										'section_name'  => self::$_slug,
										'field_name'    => 'display_left_panel_menu',
										'default'       => '0',
										'options_array' => $options_array,
									);
									$wbcom_render_postmeta_fields->render_dropdown_option( $args );
								}
								/**
								 * render check box options :: end
								 */

								/**
								 * render content layout selection :: start
								 */
								$options_array = array(
									'0'                   => __( 'Default', 'reign' ),
									'right_sidebar'       => __( 'Right Sidebar', 'reign' ),
									'left_sidebar'        => __( 'Left Sidebar', 'reign' ),
									'both_sidebar'        => __( 'Both Sidebars', 'reign' ),
									'full_width'          => __( 'Full Width', 'reign' ),
									'full_width_no_title' => __( 'Full Width( No Subheader )', 'reign' ),
									'stretched_view'      => __( 'Stretched View', 'reign' ),
									'stretched_view_no_title' => __( 'Stretched View( No Subheader )', 'reign' ),
								);
								$args          = array(
									'label'         => __( 'Content Layout', 'reign' ),
									'desc'          => __( 'Select your custom layout.', 'reign' ),
									'section_name'  => self::$_slug,
									'field_name'    => 'site_layout',
									'options_array' => $options_array,
								);
								$wbcom_render_postmeta_fields->render_dropdown_option( $args );
								/**
								 * render content layout selection :: end
								 */
								/**
								 * render sidebar selection :: start
								 */
								global $wp_registered_sidebars;
								$widgets_areas    = array( '0' => __( 'Default', 'reign' ) );
								$get_widget_areas = $wp_registered_sidebars;
								if ( ! empty( $get_widget_areas ) ) {
									foreach ( $get_widget_areas as $widget_area ) {
										$name = isset( $widget_area['name'] ) ? $widget_area['name'] : '';
										$id   = isset( $widget_area['id'] ) ? $widget_area['id'] : '';
										if ( $name && $id ) {
											$widgets_areas[ $id ] = $name;
										}
									}
								}
								$args = array(
									'label'         => __( 'Right Sidebar', 'reign' ),
									'desc'          => __( 'Select your custom right sidebar.', 'reign' ),
									'section_name'  => self::$_slug,
									'field_name'    => 'primary_sidebar',
									'options_array' => $widgets_areas,
								);
								$wbcom_render_postmeta_fields->render_dropdown_option( $args );

								$args = array(
									'label'         => __( 'Left Sidebar', 'reign' ),
									'desc'          => __( 'Select your custom left sidebar.', 'reign' ),
									'section_name'  => self::$_slug,
									'field_name'    => 'secondary_sidebar',
									'options_array' => $widgets_areas,
								);
								$wbcom_render_postmeta_fields->render_dropdown_option( $args );

								/**
								 * render sidebar selection :: end
								 */

								?>
							</div>
						</div><!-- Finish reign Layout Settings -->

						<div id="reign-subheader-settings">
							<h3><?php esc_html_e( 'Subheader Settings', 'reign' ); ?></h3>
							<div class="reign-options-inner-wrapper">
								<div class="reign_subheader_settings">
									<div class="reign-subheader-fields">
										<label>
											<input type="checkbox" name="_subheader_overwrite" value='yes' <?php checked( $reign_subheader_settings, 'yes' ); ?>/>
											<?php esc_html_e( 'Overwrite Subheader Customizer Settings', 'reign' ); ?>
										</label>
									</div>
									<?php
									$args = array(
										'label'        => __( 'Subheader Height (px)', 'reign' ),
										'desc'         => __( 'Allows you to change sub header height.', 'reign' ),
										'section_name' => 'subheader',
										'field_name'   => 'sub_header_height',
										'default'      => '286',
									);
									$wbcom_render_postmeta_fields->render_subheader_height( $args );
									$args = array(
										'label'        => __( 'Subheader Background Color', 'reign' ),
										'desc'         => __( 'Allows you to change sub header background color.', 'reign' ),
										'section_name' => 'subheader',
										'field_name'   => 'sub_header_bg_color',
										'default'      => '#555555',
									);
									$wbcom_render_postmeta_fields->render_subheader_colorpicker_option( $args );
									$args = array(
										'label'        => __( 'Subheader Overlay Color', 'reign' ),
										'desc'         => __( 'Allows you to change sub header overlay color.', 'reign' ),
										'section_name' => 'subheader',
										'field_name'   => 'sub_header_overlay_color',
										'default'      => 'rgba(38,38,38,0.6)',
									);
									$wbcom_render_postmeta_fields->render_subheader_colorpicker_option( $args );
									$args = array(
										'label'        => __( 'Subheader Text Color', 'reign' ),
										'desc'         => __( 'Allows you to change sub header text color.', 'reign' ),
										'section_name' => 'subheader',
										'field_name'   => 'sub_header_text_color',
										'default'      => '#ffffff',
									);
									$wbcom_render_postmeta_fields->render_subheader_colorpicker_option( $args );
									$args = array(
										'label'        => __( 'Subheader Link Color', 'reign' ),
										'desc'         => __( 'Allows you to change sub header link color.', 'reign' ),
										'section_name' => 'subheader',
										'field_name'   => 'sub_header_link_color',
										'default'      => '#ffffff',
									);
									$wbcom_render_postmeta_fields->render_subheader_colorpicker_option( $args );
									$args = array(
										'label'        => __( 'Subheader Breadcrumbs', 'reign' ),
										'desc'         => __( 'Allows you to hide/show breadcrumbs.', 'reign' ),
										'section_name' => 'subheader',
										'field_name'   => 'sub_header_breadcrumbs',
										'default'      => 'on',
									);
									$wbcom_render_postmeta_fields->render_checkbox_option( $args );

									$post_type = get_post_type();
									if ( $post_type == 'page' ) {

										$args = array(
											'label'        => __( 'Display Header Image with Featured Image', 'reign' ),
											'desc'         => __( 'Allows you to display page header image as Featured Image.', 'reign' ),
											'section_name' => 'layout',
											'field_name'   => 'display_page_header_image',
											'option'       => 'on',
										);
										$wbcom_render_postmeta_fields->render_checkbox_option( $args );
									}
									?>
									<div class="wbcom-metabox-control wbcom-metabox-control-image">
										<div class="rtm-left-side">
											<div class="rtm-tooltip-wrap">
												<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/question.png' ); ?>" class="rtm-tooltip-image" alt="<?php esc_attr_e( 'Tooltip Image', 'reign' ); ?>" />
												<label class="rtm-tooltip-label">
													<?php esc_html_e( 'Subheader Banner Image', 'reign' ); ?>
												</label>
											</div>
											<div class="rtm-tooltiptext">
												<?php esc_html_e( 'Allows you to set subheader banner image', 'reign' ); ?>
											</div>
										</div>
										<div class="wbcom-metabox-field">
											<div class="subheader_banner_image_section" 
											<?php
											if ( $sub_header_banner_image == '' ) :
												?>
												style="display:none;" <?php endif; ?>>
												<div class="image" >
													<div class="attachment-preview-image">
														<div class="thumbnail">
															<div class="centered">
																<img src="<?php echo esc_url( $sub_header_banner_image ); ?>" alt="" />
															</div>
														</div>
													</div>
													<div class="actions">
														<a href="#" class="delete" title="<?php echo __( 'Delete image', 'reign' ); ?>"><i class="dashicons dashicons-no"></i></a>
													</div>
												</div>
											</div>
											<input type="hidden" id="sub_header_banner_image" name="subheader[sub_header_banner_image]" value="<?php echo esc_url( $sub_header_banner_image ); ?>" >
											<div class="clearfix reign_subheader_banner_image_description">
												<p class="add_subheader_banner_image hide-if-no-js">
													<a class="components-button is-primary button button-primary button-large" href="#"><?php echo __( 'Add Subheader Banner Image', 'reign' ); ?></a>
												</p>
											</div>
										</div>
									</div>

								</div>
							</div>
						</div>
					</div> <!-- Finish Reign sub header settings -->
				</div>
			</div>
			<script>
			( function ( $ ) {
				'use strict';

				$( document ).ready( function () {
					$('.reign-color-picker').wpColorPicker();

					var file_frame;
					$(document).on('click', '.add_subheader_banner_image a', function(event) {
						event.preventDefault();
						if (file_frame) {
							file_frame.open();
							return;
						}

						file_frame = wp.media.frames.file_frame = wp.media({
							title: $(this).data('uploader_title'),
							button: {
								text: $(this).data('uploader_button_text'),
							},
							multiple: false
						});

						file_frame.on('select', function() {
							var selection = file_frame.state().get('selection');
							selection.map( function( attachment ) {
								$('.subheader_banner_image_section').html('\
										<div class="image" data-attachment_id="' + attachment.id + '">\
											<div class="attachment-preview-image">\
												<div class="thumbnail">\
													<div class="centered">\
														<img src="' + attachment.changed.url + '" />\
													</div>\
												</div>\
											</div>\
											<div class="actions">\
												<a href="#" class="delete" title="<?php echo __( 'Delete image', 'reign' ); ?>"><i class="dashicons dashicons-no"></i></a>\
											</div>\
										</div>');
								$('#sub_header_banner_image').val(attachment.changed.url);
								$('.subheader_banner_image_section').show();
							});

						});
						file_frame.open();
					});

					/* Remove images */
					$('.subheader_banner_image_section').on( 'click', 'a.delete', function() {
						$(this).closest('div.image').remove();
						$('#sub_header_banner_image').val('');
						$('.subheader_banner_image_section').hide();
						return false;
					} );

				});
			} )( jQuery );
			</script>
			<?php
		}

	}

	endif;

/**
 * Main instance of Wbcom_Postmeta_Mgmt_Layout_Section.
 *
 * @return Wbcom_Postmeta_Mgmt_Layout_Section
 */
Wbcom_Postmeta_Mgmt_Layout_Section::instance();

