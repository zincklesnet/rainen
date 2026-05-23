<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'RTM_MPP_Customization' ) ) :

	/**
	 * @class RTM_MPP_Customization
	 */
	class RTM_MPP_Customization {

		/**
		 * The single instance of the class.
		 *
		 * @var RTM_MPP_Customization
		 */
		protected static $_instance = null;

		/**
		 * Main RTM_MPP_Customization Instance.
		 *
		 * Ensures only one instance of RTM_MPP_Customization is loaded or can be loaded.
		 *
		 * @return RTM_MPP_Customization - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * RTM_MPP_Customization Constructor.
		 */
		public function __construct() {
			$this->init_hooks();
		}

		/**
		 * Hook into actions and filters.
		 */
		private function init_hooks() {
			add_action( 'init', array( $this, 'add_panels_and_sections' ) );
			add_action( 'init', array( $this, 'add_fields' ) );

			$reign_mpp_media_view = get_theme_mod( 'reign_mpp_media_view', true );

			if ( true == $reign_mpp_media_view ) {
				remove_action( 'bp_activity_entry_content', 'mpp_activity_inject_attached_media_html' );
				add_action( 'bp_activity_entry_content', array( $this, 'reign_mpp_activity_inject_attached_media_html' ) );
			}
		}

		public function reign_mpp_activity_inject_attached_media_html() {
			$activity_id = bp_get_activity_id();
			echo $this->reign_get_mpp_injected_attached_media_html( $activity_id ); // phpcs:ignore
		}

		public function reign_get_mpp_injected_attached_media_html( $activity_id = false ) {
			$activity_id = $activity_id ? $activity_id : bp_get_activity_id();
			$media_ids   = mpp_activity_get_attached_media_ids( $activity_id );

			if ( empty( $media_ids ) ) {
				return;
			}

			$count            = count( $media_ids );
			$photo_count      = 0; // Track number of photos.
			$photo_limit      = 5; // Maximum visible photos.
			$remaining_photos = 0;

			ob_start();
			?>
			<div class="reign-mpp-container mpp-container mpp-activity-container mpp-media-list mpp-activity-media-list post-row column-<?php echo esc_attr( min( $count, $photo_limit ) ); ?>">
				<?php
				foreach ( $media_ids as $key => $media_id ) :
					$media = mpp_get_media( $media_id );
					if ( ! $media ) {
						continue;
					}

					$type  = $media->type;
					$class = 'post-column mpp-media-' . esc_attr( $type ); // Add media type class.

					if ( $type === 'photo' ) {
						$class .= ' ' . $this->get_media_col_class( $count, $key ); // Assuming this function handles your column classes.
						++$photo_count; // Increment the photo count for each photo.

						// Only apply the 5+ layout when the count is 5 or more.
						if ( $count >= 5 ) {
							// Start building the HTML for count >= 5.
							if ( $photo_count === 1 ) {
								// Start the 'col-left' container when the first image is encountered.
								echo '<div class="col-left ' . esc_attr( $class ) . '">';
							}
							// For the first 2 images: Normal display in col-left.
							if ( $photo_count <= 2 ) {
								?>
								<div class="col-12">
									<?php echo $this->reign_generate_image_div( $media, $activity_id, $count, $key ); // phpcs:ignore ?>
								</div>
								<?php
							}
							// For the 3rd, 4th, and 5th images: Normal display in col-right.
							if ( $photo_count > 2 && $photo_count <= 5 ) {
								// Start the 'col-right' container once we reach the 3rd image.
								if ( $photo_count === 3 ) {
									echo '<div class="col-right ' . esc_attr( $class ) . '">';
								}

								?>
								<div class="col-12 <?php echo $photo_count === 5 ? 'overlay-container' : ''; ?>">
									<?php echo $this->reign_generate_image_div( $media, $activity_id, $count, $key ); // phpcs:ignore ?>
									
									<?php
									if ( $photo_count === 5 && $count > 5 ) :
										$media_src = mpp_get_media_permalink( $media_id );
										?>
										<a href="<?php echo esc_url( $media_src ); ?>" class="more-overlay-link">
											<div class="more-overlay">+ <?php echo esc_html( $count - 5 ); ?></div>
										</a>
									<?php endif; ?>
								</div>
								<?php
							}

							// Close the 'col-left' container after the second image.
							if ( $photo_count === 2 ) {
								echo '</div>'; // Close col-left.
							}

							// Close the 'col-right' container after the fifth image.
							if ( $photo_count === 5 ) {
								echo '</div>'; // Close col-right.
							}
						} else {
							// For cases where the photo count is less than 5, handle normally.
							?>
							<div class="<?php echo esc_attr( $class ); ?>">
								<?php echo $this->reign_generate_image_div( $media, $activity_id, $count, $key ); // phpcs:ignore ?>
							</div>
							<?php
						}
					} else {
						// Display other media types as normal.
						?>
						<div class="<?php echo esc_attr( $class ); ?>">
							<?php echo $this->reign_generate_image_div( $media, $activity_id, $count, $key ); // phpcs:ignore ?>
						</div>
						<?php
					}
				endforeach;
				?>
			</div><!-- end of .mpp-activity-media-list -->
			<?php
			return ob_get_clean();
		}

		// Function to return correct column class.
		private function get_media_col_class( $count, $key ) {
			if ( $count == 1 ) {
				return 'col-12';
			}
			if ( $count == 2 ) {
				return 'col-6';
			}
			if ( $count == 3 ) {
				return ( $key == 0 ) ? 'col-12' : 'col-6';
			}
			if ( $count == 4 ) {
				return 'col-6';
			}
			if ( $count >= 5 ) {
				return ( $key < 5 ) ? 'col-6' : 'd-none';
			}
		}

		// Function to generate media HTML.
		private function reign_generate_image_div( $media, $activity_id, $count, $key ) {
			$type      = $media->type;
			$media_id  = $media->id;
			$media_src = mpp_get_media_permalink( $media_id );

			if ( $type == 'photo' ) {
				return '<a href="' . esc_url( $media_src ) . '" data-mpp-type="photo" data-mpp-activity-id="' . esc_attr( $activity_id ) . '" data-mpp-media-id="' . esc_attr( $media_id ) . '" class="mpp-media mpp-activity-media mpp-activity-media-photo">
							<img src="' . esc_url( mpp_get_media_src( 'large', $media_id ) ) . '" class="mpp-attached-media-item" alt="' . esc_attr( mpp_get_media_title( $media_id ) ) . '" title="' . esc_attr( mpp_get_media_title( $media_id ) ) . '" loading="lazy">
						</a>';
			} elseif ( $type == 'video' ) {
				if ( mpp_is_oembed_media( $media ) ) {
					return '<div class="reign_video_height post-wrap-inner mpp-activity-media-list mpp-activity-video-player">' . mpp_get_oembed_content( $media, 'full' ) . '</div>';
				} else {
					$media_file = mpp_get_media_src( '', $media );
					return '<div class="reign_video_height post-wrap-inner mpp-activity-media-list mpp-activity-video-player">' . do_shortcode( '[video src=' . $media_file . ' controls]' ) . '</div>';
				}
			} elseif ( $type == 'audio' ) {
				$div_html = '<div class="post-wrap-inner mpp-activity-media-list mpp-activity-audio-player"><audio src="' . mpp_get_media_src( '', $media ) . '" controls></audio></div>';
				return $div_html;
			} else {
				$url      = ! mpp_is_doc_viewable( $media ) ? mpp_get_media_src( '', $media ) : mpp_get_media_permalink( $media );
				$class    = ! mpp_is_doc_viewable( $media ) ? 'mpp-no-lightbox' : '';
				$target   = ! mpp_is_doc_viewable( $media ) ? '' : 'target=_blank';
				$div_html = '<div class="post-wrap-inner mpp-activity-media-list" data-mpp-type="' . esc_attr( $type ) . '">
							<a href="' . esc_url( $url ) . '" ' . esc_attr( $target ) . ' class="mpp-media mpp-activity-media mpp-activity-media-doc ' . esc_attr( $class ) . '" data-mpp-type="' . esc_attr( $type ) . '" data-mpp-activity-id="' . esc_attr( $activity_id ) . '" data-mpp-media-id="' . esc_attr( $media_id ) . '">
								<img src="' . mpp_get_media_src( 'thumbnail', $media_id ) . '" class="mpp-attached-media-item " alt="' . esc_attr( mpp_get_media_title( $media_id ) ) . '" title="' . esc_attr( mpp_get_media_title( $media_id ) ) . '" loading="lazy" />
							</a></div>';
				return $div_html;
			}
		}

		/**
		 * Add panels and sections
		 */
		public function add_panels_and_sections() {
			new \Kirki\Section(
				'reign_mpp_support',
				array(
					'title'       => esc_html__( 'MediaPress', 'reign' ),
					'priority'    => 10,
					'panel'       => 'reign_plugin_support_panel',
					'description' => '',
				)
			);
		}

		/**
		 * Add fields
		 */
		public function add_fields() {

			new \Kirki\Field\Checkbox_Switch(
				array(
					'settings'    => 'reign_mpp_media_view',
					'label'       => esc_html__( 'Override Activity Media List View', 'reign' ),
					'description' => esc_html__( 'This setting helps enhance the user interface of your activity media listings in MediaPress.', 'reign' ),
					'tooltip'     => esc_html__( 'Note: The Media Size and Activity Media List View settings in the backend may not function as expected.', 'reign' ),
					'section'     => 'reign_mpp_support',
					'priority'    => 10,
					'default'     => 'on',
					'choices'     => array(
						'on'  => esc_html__( 'Enable', 'reign' ),
						'off' => esc_html__( 'Disable', 'reign' ),
					),
				)
			);
		}
	}

endif;

/**
 * Main instance of RTM_MPP_Customization.
 *
 * @return RTM_MPP_Customization
 */
RTM_MPP_Customization::instance();
