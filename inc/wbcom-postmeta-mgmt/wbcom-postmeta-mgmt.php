<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Wbcom_Postmeta_Management' ) ) :

	/**
	 * @class Wbcom_Postmeta_Management
	 */
	class Wbcom_Postmeta_Management {

		/**
		 * The single instance of the class.
		 *
		 * @var Wbcom_Postmeta_Management
		 */
		protected static $_instance   = null;
		protected static $_theme_slug = 'reign';

		/**
		 * Main Wbcom_Postmeta_Management Instance.
		 *
		 * Ensures only one instance of Wbcom_Postmeta_Management is loaded or can be loaded.
		 *
		 * @return Wbcom_Postmeta_Management - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Wbcom_Postmeta_Management Constructor.
		 */
		public function __construct() {
			$this->includes();
			$this->init_hooks();
		}

		public function includes() {
			include_once REIGN_INC_DIR . 'wbcom-postmeta-mgmt/class-wbcom-render-postmeta.php';
			include_once REIGN_INC_DIR . 'wbcom-postmeta-mgmt/sections/class-layout-section.php';
		}

		/**
		 * Hook into actions and filters.
		 */
		private function init_hooks() {
			add_action( 'add_meta_boxes', array( $this, 'reign_add_meta_box' ) );
			add_action( 'save_post', array( $this, 'reign_save_post_meta' ), 10, 1 );

			add_action( 'admin_print_scripts-post-new.php', array( $this, 'enqueue_reign_metabox_style_n_script' ), 11 );
			add_action( 'admin_print_scripts-post.php', array( $this, 'enqueue_reign_metabox_style_n_script' ), 11 );
		}

		public function enqueue_reign_metabox_style_n_script() {
			$url_prefix = get_template_directory_uri() . '/inc/wbcom-postmeta-mgmt';
			wp_register_style(
				$handle = 'wbcom-postmeta-mgmt',
				$src    = $url_prefix . '/assets/wbcom-postmeta-mgmt.css',
				$deps   = array(),
				$ver    = REIGN_THEME_VERSION,
				$media  = 'all'
			);
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( 'wbcom-postmeta-mgmt' );

			wp_register_style(
				$handle = 'select2.min.css',
				$src    = $url_prefix . '/assets/select2.min.css',
				$deps   = array(),
				$ver    = REIGN_THEME_VERSION,
				$media  = 'all'
			);
			wp_enqueue_style( 'select2.min.css' );

			wp_register_script(
				$handle    = 'select2.min.js',
				$src       = $url_prefix . '/assets/select2.min.js',
				$deps      = array( 'jquery' ),
				$ver       = REIGN_THEME_VERSION,
				$in_footer = true
			);

			wp_register_script(
				$handle    = 'wbcom-postmeta-mgmt',
				$src       = $url_prefix . '/assets/wbcom-postmeta-mgmt.js',
				$deps      = array( 'jquery', 'select2.min.js' ),
				$ver       = REIGN_THEME_VERSION,
				$in_footer = true
			);

			wp_register_script(
				$handle    = 'wp-color-picker-alpha',
				$src       = $url_prefix . '/assets/wp-color-picker-alpha.js',
				$deps      = array( 'jquery', 'wp-color-picker' ),
				$ver       = REIGN_THEME_VERSION,
				$in_footer = true
			);

			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker-alpha' );
			wp_enqueue_script( 'wbcom-postmeta-mgmt' );
			wp_enqueue_script( 'select2.min.js' );
		}

		public function render_reign_add_meta_box( $post ) {

			wp_nonce_field( 'reign_save_postmeta', 'reign_postmeta_nonce' );

			$current_active_tab = 0;
			$vertical_tabs      = apply_filters( 'wbcom_metabox_add_vertical_tab', array() );

			echo '<div class="wbcom-metabox-wrapper">';
			// if ( ! empty( $vertical_tabs ) && is_array( $vertical_tabs ) ) {
			// echo '<div class="wbcom-metabox-tab">';
			// $counter = 0;
			// foreach ( $vertical_tabs as $key => $value ) {
			// $active = ( $current_active_tab == $counter ) ? ' active' : '';
			// echo '<span class="' . $value['icon-class'] . '"></span>';
			// echo '<button class="wbcom-metabox-tablinks ' . $key . ' ' . $active . '" data-container-id="' . $key . '"><span class="' . $value['icon-class'] . '"></span>' . $value['label'] . '</button>';
			// $counter++;
			// }
			// echo '</div>';
			// }
			if ( ! empty( $vertical_tabs ) && is_array( $vertical_tabs ) ) {
				$inline_css = '';
				foreach ( $vertical_tabs as $key => $value ) {
					echo '<div class="wbcom-metabox-content ' . $key . '" ' . $inline_css . '>';
					do_action( 'render_wbcom_metabox_content_for_' . $key, $post );
					echo '</div>';
					$inline_css = 'style="display:none;"';
				}
			}
			echo '</div>';
		}

		public function reign_add_meta_box() {
			global $post;
			if ( isset( $post ) && $post->ID == get_option( 'page_for_posts' ) && empty( $post->post_content ) ) {
				return;
			}
			$args = array(
				'public'   => true,
				'_builtin' => false,
			);

			$output   = 'names'; // 'names' or 'objects' (default: 'names')
			$operator = 'and'; // 'and' or 'or' (default: 'and')

			$post_types = get_post_types( $args, $output, $operator );
			add_meta_box(
				self::$_theme_slug . '_postmeta_settings',
				ucfirst( self::$_theme_slug ) . __( ' Custom Settings', 'reign' ),
				array( $this, 'render_reign_add_meta_box' ),
				array( 'post', 'page', $post_types ),
				'normal',
				'high'
			);

			add_meta_box(
				self::$_theme_slug . '_postformat_settings',
				__( 'Post Format Settings', 'reign' ),
				array( $this, 'render_reign_add_post_format_meta_box' ),
				array( 'post' ),
				'normal',
				'high'
			);
		}

		public function render_reign_add_post_format_meta_box( $post ) {
			wp_nonce_field( 'reign_save_postmeta', 'reign_postmeta_nonce' );
			$post_format       = get_post_format( $post );
			$post_id           = $post->ID;
			$post_video        = get_post_meta( $post_id, '_reign_post_video', true );
			$post_audio        = get_post_meta( $post_id, '_reign_post_audio', true );
			$post_quote        = get_post_meta( $post_id, '_reign_post_quote', true );
			$post_quote_author = get_post_meta( $post_id, '_reign_post_quote_author', true );

			$post_link_title    = get_post_meta( $post_id, '_reign_post_link_title', true );
			$post_link_url      = get_post_meta( $post_id, '_reign_post_link_url', true );
			$post_image_gallery = get_post_meta( $post_id, '_reign_image_gallery', true );
			?>
			<div class="reign_post_format-settings">
				<input type="hidden" value="<?php echo esc_attr( $post_format ); ?>" id="reign_post_format"/>
				<div class="reign_video_format_setting">
					<p class="description"><?php esc_html_e( 'Enter Youtube, Vimeo and etc video url.', 'reign' ); ?></p>
					<div class="reign_input_section">
						<div class="format-setting-label">
							<label class="label"><?php esc_html_e( 'Video URL', 'reign' ); ?></label>
						</div>
						<input type="text" id="reign_post_video" name="reign_post_video" value="<?php echo esc_url( $post_video ); ?>" class="reign-input-text"/>
						<a href="javascript:void(0);" class="reign_upload_media option-tree-ui-button button button-primary light" data-id="reign_post_video" rel="<?php echo esc_attr( $post_id ); ?>" title="<?php esc_attr_e( 'Add Media', 'reign' ); ?>">
							<span class="dashicons dashicons-insert"></span>
						</a>
					</div>
				</div>

				<div class="reign_audio_format_setting">
					<p class="description"><?php esc_html_e( 'Enter audio url.', 'reign' ); ?></p>
					<div class="reign_input_section">
						<div class="format-setting-label">
							<label class="label"><?php esc_html_e( 'Audio URL', 'reign' ); ?></label>
						</div>
						<input type="text" id="reign_post_audio" name="reign_post_audio" value="<?php echo esc_url( $post_audio ); ?>" class="reign-input-text"/>
						<a href="javascript:void(0);" class="reign_upload_media option-tree-ui-button button button-primary light" data-id="reign_post_audio" rel="<?php echo esc_attr( $post_id ); ?>" title="<?php esc_attr_e( 'Add Media', 'reign' ); ?>">
							<span class="dashicons dashicons-insert"></span>
						</a>
					</div>
				</div>

				<div class="reign_quote_format_setting">
					<p class="description"><?php esc_html_e( 'Input your quote.', 'reign' ); ?></p>
					<div class="reign_input_section">
						<div class="format-setting-label">
							<label class="label"><?php esc_html_e( 'Quote Text', 'reign' ); ?></label>
						</div>
						<textarea name="reign_post_quote" class="reign-input-textare"><?php echo esc_textarea( $post_quote ); ?></textarea>
					</div>	
					<div class="reign_input_section">
						<div class="format-setting-label">
							<label class="label"><?php esc_html_e( 'Quote Author', 'reign' ); ?></label>
						</div>
						<input type="text" name="reign_post_quote_author" value="<?php echo esc_attr( $post_quote_author ); ?>" class="reign-input-text"/>						
					</div>
				</div>
				
				<div class="reign_link_format_setting">
					<p class="description"><?php esc_html_e( 'Input your link.', 'reign' ); ?></p>
					<div class="reign_input_section">
						<div class="format-setting-label">
							<label class="label"><?php esc_html_e( 'Link Title', 'reign' ); ?></label>
						</div>
						<input type="text" name="reign_post_link_title" value="<?php echo esc_attr( $post_link_title ); ?>" class="reign-input-text"/>						
					</div>
					<div class="reign_input_section">
						<div class="format-setting-label">
							<label class="label"><?php esc_html_e( 'Link URL', 'reign' ); ?></label>
						</div>
						<input type="text" name="reign_post_link_url" value="<?php echo esc_url( $post_link_url ); ?>" class="reign-input-text"/>
					</div>
				</div>
				
				<div class="reign_gallery_format_setting">
					<p class="description"><?php esc_html_e( 'To create a gallery, upload your images and then select "Uploaded to this post" from the dropdown (in the media popup) to view images attached to this post. You can drag to re-order or delete them there.', 'reign' ); ?></p>
					<div id="images_gallery_container" class="reign_images_gallery_container">
					<ul class="reign_images_gallery images_gallery">
						<?php
							$image_gallery = '';
						if ( ! empty( $post_image_gallery ) ) {
							$post_image_gallery = explode( ',', $post_image_gallery );

							foreach ( $post_image_gallery as $image_id ) {
								if ( trim( $image_id ) != '' ) {
									// $image = wp_get_attachment_image_src($image_id, 'thumbnail');

									echo '<li class="image" data-attachment_id="' . $image_id . '">
											<div class="attachment-preview type-image">
												<div class="thumbnail">
													<div class="centered">
													' . wp_get_attachment_image( $image_id, 'thumbnail' ) . '
													</div>
												</div>
											</div>

											<div class="actions">
												<a href="#" id="' . $image_id . '" class="delete" title="' . __( 'Delete image', 'reign' ) . '"><i class="dashicons dashicons-no"></i></a>
											</div>
										</li>';
									$image_gallery .= $image_id . ',';
								}
							}
						}

						?>
					</ul>
					<input type="hidden" id="reign_image_gallery" name="reign_image_gallery" value="<?php echo esc_attr( substr( @$image_gallery, 0, -1 ) ); ?>" />
					</div>
					<div class="clearfix reign_image_gallery_description">
						<p class="add_reign_images hide-if-no-js">
							<a class="components-button is-primary" href="#"><?php echo __( 'Add Image Gallery', 'reign' ); ?></a>
						</p>
					</div>
				</div>
				

				<script>
				( function ( $ ) {
					'use strict';
					$('#reign_postformat_settings').hide();
					$( document ).ready( function () {
						
						var post_format = $('input[name=post_format]:checked').val();						
						if ( typeof post_format == 'undefined' ) {
							post_format = $('#reign_post_format').val();
						}						
						if ( post_format == 'video' || post_format == 'audio' || post_format == 'quote' || post_format == 'link' || post_format == 'gallery' ) {
							$('#reign_postformat_settings').show();
							$( '.reign_video_format_setting').hide();
							$( '.reign_audio_format_setting').hide();
							$( '.reign_quote_format_setting').hide();
							$( '.reign_link_format_setting').hide();
							$( '.reign_gallery_format_setting').hide();
							$( '.reign_' + post_format + '_format_setting').show();
						}
						
						
						$(document).on( "change", 'input[name=post_format], .editor-post-format__content select.components-select-control__input' , function(e){
							var post_format = $( this ).val();
							$( '.reign_video_format_setting').hide();
							$( '.reign_audio_format_setting').hide();
							$( '.reign_quote_format_setting').hide();
							$( '.reign_link_format_setting').hide();
							$( '.reign_gallery_format_setting').hide();
							if ( post_format == 'video' ) {
								$('#reign_postformat_settings').show();
								$( '.reign_video_format_setting').show();
							} else if ( post_format == 'audio' ) {
								$('#reign_postformat_settings').show();
								$( '.reign_audio_format_setting').show();
							} else if ( post_format == 'quote' ) {
								$('#reign_postformat_settings').show();
								$( '.reign_quote_format_setting').show();
							} else if ( post_format == 'link' ) {
								$('#reign_postformat_settings').show();
								$( '.reign_link_format_setting').show();
							}else if ( post_format == 'gallery' ) {
								$('#reign_postformat_settings').show();
								$( '.reign_gallery_format_setting').show();
							} else {
								$('#reign_postformat_settings').hide();
							}
						});
						
						/* Uploading files */
						var image_gallery_frame;
						var $image_gallery_ids = $('#reign_image_gallery');
						var $images_gallery = $('#images_gallery_container ul.images_gallery');
						$('.add_reign_images').on( 'click', 'a', function( event ) {
							var $el = $(this);
							var attachment_ids = $image_gallery_ids.val();
							event.preventDefault();
							/* If the media frame already exists, reopen it. */
							if ( image_gallery_frame ) {
								image_gallery_frame.open();
								return;
							}
							/* Create the media frame.  */
							image_gallery_frame = wp.media.frames.downloadable_file = wp.media({
								/* Set the title of the modal.  */
								title: '<?php echo __( 'Add Image Gallery', 'reign' ); ?>',
								button: {
									text: '<?php echo __( 'Add to gallery', 'reign' ); ?>',
								},
								multiple: true
							});
							/* When an image is selected, run a callback.  */
							image_gallery_frame.on( 'select', function() {
								var selection = image_gallery_frame.state().get('selection');
								selection.map( function( attachment ) {
									attachment = attachment.toJSON();
									if ( attachment.id ) {
										attachment_ids = attachment_ids ? attachment_ids + "," + attachment.id : attachment.id;
										$images_gallery.append('\
											<li class="image" data-attachment_id="' + attachment.id + '">\
																							<div class="attachment-preview type-image">\
																								<div class="thumbnail">\
																									<div class="centered">\
																										<img src="' + attachment.url + '" />\
																									</div>\
																								</div>\
																							</div>\
																							<div class="actions">\
																								<a href="#" class="delete" title="<?php echo __( 'Delete image', 'reign' ); ?>"><i class="dashicons dashicons-no"></i></a>\
																							</div>\
											</li>');
									}
								} );
								$image_gallery_ids.val( attachment_ids );
							});
							/* Finally, open the modal. */
							image_gallery_frame.open();
						});
						/* Image ordering */
						$images_gallery.sortable({
							items: 'li.image',
							cursor: 'move',
							scrollSensitivity:40,
							forcePlaceholderSize: true,
							forceHelperSize: false,
							helper: 'clone',
							opacity: 0.65,
							placeholder: 'wc-metabox-sortable-placeholder',
							start:function(event,ui){
								ui.item.css('background-color','#f6f6f6');
							},
							stop:function(event,ui){
								ui.item.removeAttr('style');
							},
							update: function(event, ui) {
								var attachment_ids = '';
								$('#images_gallery_container ul li.image').css('cursor','default').each(function() {
									var attachment_id = $(this).attr( 'data-attachment_id' );
									attachment_ids = attachment_ids + attachment_id + ',';
								});
								$image_gallery_ids.val( attachment_ids );
							}
						});
						/* Remove images */
						$('#images_gallery_container').on( 'click', 'a.delete', function() {

							$(this).closest('li.image').remove();
							var attachment_ids = '';
							$('#images_gallery_container ul li.image').css('cursor','default').each(function() {
								var attachment_id = $(this).attr( 'data-attachment_id' );
								attachment_ids = attachment_ids + attachment_id + ',';
							});
							$image_gallery_ids.val( attachment_ids );
							return false;
						} );


						$('.reign_upload_media').on( 'click',  function( event ) {
							var $el = $(this);	
							var media_id = $(this).data( 'id' );							
							event.preventDefault();
							/* If the media frame already exists, reopen it. */
							if ( image_gallery_frame ) {
								image_gallery_frame.open();
								return;
							}
							/* Create the media frame.  */
							image_gallery_frame = wp.media.frames.downloadable_file = wp.media({
								/* Set the title of the modal.  */
								title: '<?php echo __( 'Add Media', 'reign' ); ?>',
								button: {
									text: '<?php echo __( 'Add to Library', 'reign' ); ?>',
								},
								multiple: true
							});
							/* When an image is selected, run a callback.  */
							image_gallery_frame.on( 'select', function() {
								var selection = image_gallery_frame.state().get('selection');
								selection.map( function( attachment ) {
									attachment = attachment.toJSON();
									if ( attachment.id ) {
										
										$( '#' + media_id ).val(attachment.url);
									}
								} );								
							});
							/* Finally, open the modal. */
							image_gallery_frame.open();
						});
						
					});
				} )( jQuery );
				</script>

			</div>
			<?php
		}

		public function reign_save_post_meta( $post_id ) {
			// Bail if we're doing an autosave — nothing the user submitted.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			// Bail if the metabox form wasn't actually submitted in this request
			// (covers REST API, wp_insert_post(), cron, CLI, bulk edit, quick edit).
			if ( ! isset( $_POST['reign_postmeta_nonce'] ) ) {
				return;
			}

			// Verify the nonce.
			if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['reign_postmeta_nonce'] ) ), 'reign_save_postmeta' ) ) {
				return;
			}

			// Post-specific capability check.
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

			$post_type = isset( $_POST['post_type'] )
				? sanitize_key( wp_unslash( $_POST['post_type'] ) )
				: '';

			if ( '' === $post_type ) {
				return;
			}

			$reign_post_types_support = Reign_Kirki_Post_Types_Support::instance();
			$post_types               = $reign_post_types_support->get_post_types_to_support();
			foreach ( $post_types as $post_type_group ) {
				if ( 'post' === $post_type || 'page' === $post_type || in_array( $post_type, (array) $post_type_group, true ) ) {
					$vertical_tabs      = apply_filters( 'wbcom_metabox_add_vertical_tab', array() );
					$wbcom_metabox_data = array();

					foreach ( $vertical_tabs as $key => $value ) {
						if ( isset( $_POST[ $key ] ) && is_array( $_POST[ $key ] ) && ! empty( $_POST[ $key ] ) ) {
							$raw_tab = wp_unslash( $_POST[ $key ] );
							foreach ( $raw_tab as $_key => $_value ) {
								if ( is_scalar( $_value ) ) {
									$wbcom_metabox_data[ $key ][ $_key ] = sanitize_text_field( (string) $_value );
								}
							}
						}
					}
					if ( ! isset( $_POST['layout']['display_page_title'] ) ) {
						$wbcom_metabox_data['layout']['display_page_title'] = '';
					}
					if ( ! isset( $_POST['layout']['display_page_header_image'] ) ) {
						$wbcom_metabox_data['layout']['display_page_header_image'] = '';
					}

					if ( isset( $_POST['subheader'] ) && is_array( $_POST['subheader'] ) ) {
						$raw_subheader = wp_unslash( $_POST['subheader'] );
						foreach ( $raw_subheader as $sub_key => $subheader ) {
							if ( is_scalar( $subheader ) ) {
								$wbcom_metabox_data['subheader'][ $sub_key ] = sanitize_text_field( (string) $subheader );
							}
						}
					}

					if ( ! isset( $_POST['subheader']['sub_header_breadcrumbs'] ) ) {
						$wbcom_metabox_data['subheader']['sub_header_breadcrumbs'] = '';
					}

					if ( ! isset( $_POST['_subheader_overwrite'] ) ) {
						update_post_meta( $post_id, '_subheader_overwrite', '' );
					} else {
						update_post_meta(
							$post_id,
							'_subheader_overwrite',
							sanitize_text_field( wp_unslash( $_POST['_subheader_overwrite'] ) )
						);
					}

					$wbcom_metabox_data = apply_filters( 'modify_wbcom_metabox_data_before_update', $wbcom_metabox_data, $vertical_tabs, $_POST );
					update_post_meta( $post_id, self::$_theme_slug . '_wbcom_metabox_data', $wbcom_metabox_data );
				}
			}

			if ( 'post' === $post_type ) {
				if ( isset( $_POST['reign_post_video'] ) ) {
					update_post_meta(
						$post_id,
						'_reign_post_video',
						esc_url_raw( wp_unslash( $_POST['reign_post_video'] ) )
					);
				}
				if ( isset( $_POST['reign_post_audio'] ) ) {
					update_post_meta(
						$post_id,
						'_reign_post_audio',
						esc_url_raw( wp_unslash( $_POST['reign_post_audio'] ) )
					);
				}

				if ( isset( $_POST['reign_post_quote'] ) ) {
					update_post_meta(
						$post_id,
						'_reign_post_quote',
						wp_kses_post( wp_unslash( $_POST['reign_post_quote'] ) )
					);
					$quote_author = isset( $_POST['reign_post_quote_author'] )
						? sanitize_text_field( wp_unslash( $_POST['reign_post_quote_author'] ) )
						: '';
					update_post_meta( $post_id, '_reign_post_quote_author', $quote_author );
				}
				if ( isset( $_POST['reign_post_link_title'] ) ) {
					update_post_meta(
						$post_id,
						'_reign_post_link_title',
						sanitize_text_field( wp_unslash( $_POST['reign_post_link_title'] ) )
					);
					$link_url = isset( $_POST['reign_post_link_url'] )
						? esc_url_raw( wp_unslash( $_POST['reign_post_link_url'] ) )
						: '';
					update_post_meta( $post_id, '_reign_post_link_url', $link_url );
				}
				if ( isset( $_POST['reign_image_gallery'] ) ) {
					$raw_gallery = sanitize_text_field( wp_unslash( $_POST['reign_image_gallery'] ) );
					$gallery_ids = array_filter( array_map( 'absint', explode( ',', $raw_gallery ) ) );
					update_post_meta( $post_id, '_reign_image_gallery', implode( ',', $gallery_ids ) );
				}
			}
		}
	}

	endif;

/**
 * Main instance of Wbcom_Postmeta_Management.
 *
 * @return Wbcom_Postmeta_Management
 */
Wbcom_Postmeta_Management::instance();
