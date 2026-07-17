<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Wbcom_Render_Postmeta_Fields' ) ) :

	/**
	 * @class Wbcom_Render_Postmeta_Fields
	 */
	class Wbcom_Render_Postmeta_Fields {

		/**
		 * The single instance of the class.
		 *
		 * @var Wbcom_Render_Postmeta_Fields
		 */
		protected static $_instance   = null;
		protected static $_theme_slug = 'reign';

		/**
		 * Main Wbcom_Render_Postmeta_Fields Instance.
		 *
		 * Ensures only one instance of Wbcom_Render_Postmeta_Fields is loaded or can be loaded.
		 *
		 * @return Wbcom_Render_Postmeta_Fields - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Wbcom_Render_Postmeta_Fields Constructor.
		 */
		public function __construct() {
		}

		/**
		 * function to render dropdown.
		 *
		 * @since 1.0.4
		 */
		public function render_dropdown_option( $args = array() ) {
			$defaults = array(
				'label'         => '',
				'desc'          => '',
				'section_name'  => '',
				'field_name'    => '',
				'options_array' => array(),
			);
			$args     = wp_parse_args( $args, $defaults );

			global $post;
			$wbcom_metabox_data = get_post_meta( $post->ID, self::$_theme_slug . '_wbcom_metabox_data', true );
			$args['value']      = isset( $wbcom_metabox_data[ $args['section_name'] ][ $args['field_name'] ] ) ? $wbcom_metabox_data[ $args['section_name'] ][ $args['field_name'] ] : '';
			$field_name_to_use  = $args['section_name'] . '[' . $args['field_name'] . ']';
			?>
		<div class="wbcom-metabox-control wbcom-metabox-control-dropdown">
			<div class="rtm-left-side">
				<div class="rtm-tooltip-wrap">
					<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/question.png' ); ?>" class="rtm-tooltip-image" alt="<?php esc_attr_e( 'Tooltip Image', 'reign' ); ?>" />
					<label class="rtm-tooltip-label">
						<?php echo esc_html( $args['label'] ); ?>
					</label>
				</div>
				<div class="rtm-tooltiptext">
					<?php echo esc_html( $args['desc'] ); ?>
				</div>
			</div>

			<div class="wbcom-metabox-field">
				<select name="<?php echo esc_attr( $field_name_to_use ); ?>" class="wbcom-metabox-select2">
					<?php
					if ( ! empty( $args['options_array'] ) && is_array( $args['options_array'] ) ) {
						foreach ( $args['options_array'] as $key => $value ) {
							echo '<option value="' . esc_attr( $key ) . '" ' . selected( $args['value'], $key, false ) . '>' . esc_html( $value ) . '</option>';
						}
					}
					?>
				</select>
			</div>
		</div>
			<?php
		}

		/**
		 * function to render radio buttons.
		 *
		 * @since 1.0.4
		 */
		public function render_radio_option( $args = array() ) {
			$defaults = array(
				'label'         => '',
				'desc'          => '',
				'section_name'  => '',
				'field_name'    => '',
				'options_array' => array(),
			);
			$args     = wp_parse_args( $args, $defaults );

			global $post;
			$wbcom_metabox_data = get_post_meta( $post->ID, self::$_theme_slug . '_wbcom_metabox_data', true );
			$args['value']      = isset( $wbcom_metabox_data[ $args['section_name'] ][ $args['field_name'] ] ) ? $wbcom_metabox_data[ $args['section_name'] ][ $args['field_name'] ] : '';
			$field_name_to_use  = $args['section_name'] . '[' . $args['field_name'] . ']';
			?>
		<div class="wbcom-metabox-control wbcom-metabox-control-radio">
			<div class="rtm-left-side">
				<div class="rtm-tooltip-wrap">
					<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/question.png' ); ?>" class="rtm-tooltip-image" alt="<?php esc_attr_e( 'Tooltip Image', 'reign' ); ?>" />
					<label class="rtm-tooltip-label">
						<?php echo esc_html( $args['label'] ); ?>
					</label>
				</div>
				<div class="rtm-tooltiptext">
					<?php echo esc_html( $args['desc'] ); ?>
				</div>
			</div>
			<div class="wbcom-metabox-field">
				<?php
				if ( ! empty( $args['options_array'] ) && is_array( $args['options_array'] ) ) {
					echo '<ul class="wbcom-metabox-radio-wrapper">';
					foreach ( $args['options_array'] as $key => $value ) {
						echo '<li>';
							echo '<input type="radio" id="wbcom-metabox-radio-' . esc_attr( $key ) . '" name="' . esc_attr( $field_name_to_use ) . '" value="' . esc_attr( $key ) . '" />';
							echo '<label for="wbcom-metabox-radio-' . esc_attr( $key ) . '">' . esc_attr( $value ) . '</label>';
						echo '</li>';
					}
					echo '</ul>';
				}
				?>
			</div>
		</div>
			<?php
		}

		public function render_checkbox_option( $args = array() ) {
			$defaults = array(
				'label'        => '',
				'desc'         => '',
				'section_name' => '',
				'field_name'   => '',
				'default'      => '',
				'option'       => array(),
			);
			$args     = wp_parse_args( $args, $defaults );

			global $post;
			$wbcom_metabox_data = get_post_meta( $post->ID, self::$_theme_slug . '_wbcom_metabox_data', true );
			$args['value']      = isset( $wbcom_metabox_data[ $args['section_name'] ][ $args['field_name'] ] ) ? $wbcom_metabox_data[ $args['section_name'] ][ $args['field_name'] ] : $args['default'];
			$field_name_to_use  = $args['section_name'] . '[' . $args['field_name'] . ']';
			?>
		<div class="wbcom-metabox-control wbcom-metabox-control-radio">
			<div class="rtm-left-side">
				<div class="rtm-tooltip-wrap">
					<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/question.png' ); ?>" class="rtm-tooltip-image" alt="<?php esc_attr_e( 'Tooltip Image', 'reign' ); ?>" />
					<label class="rtm-tooltip-label">
						<?php echo esc_html( $args['label'] ); ?>
					</label>
				</div>
				<div class="rtm-tooltiptext">
					<?php echo esc_html( $args['desc'] ); ?>
				</div>
			</div>
			<div class="wbcom-metabox-field">
				<?php
				echo '<input type="checkbox" name="' . esc_attr( $field_name_to_use ) . '" value="on" ' . checked( 'on', $args['value'], false ) . '>';
				?>
			</div>
		</div>
			<?php
		}

		public function render_subheader_height( $args = array() ) {
			$defaults = array(
				'label'        => '',
				'desc'         => '',
				'section_name' => '',
				'field_name'   => '',
				'default'      => '',
			);
			$args     = wp_parse_args( $args, $defaults );

			global $post;
			$wbcom_metabox_data = get_post_meta( $post->ID, self::$_theme_slug . '_wbcom_metabox_data', true );
			$args['value']      = isset( $wbcom_metabox_data[ $args['section_name'] ][ $args['field_name'] ] ) ? $wbcom_metabox_data[ $args['section_name'] ][ $args['field_name'] ] : $args['default'];
			$field_name_to_use  = $args['section_name'] . '[' . $args['field_name'] . ']';
			?>
			<div class="wbcom-metabox-control wbcom-metabox-text-box">
				<div class="rtm-left-side">
					<div class="rtm-tooltip-wrap">
						<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/question.png' ); ?>" class="rtm-tooltip-image" alt="<?php esc_attr_e( 'Tooltip Image', 'reign' ); ?>" />
						<label class="rtm-tooltip-label">
							<?php echo esc_html( $args['label'] ); ?>
						</label>
					</div>
					<div class="rtm-tooltiptext">
						<?php echo esc_html( $args['desc'] ); ?>
					</div>
				</div>
				<div class="wbcom-metabox-field">
			<?php
			echo '<input type="text" name="' . esc_attr( $field_name_to_use ) . '" value="' . esc_attr( $args['value'] ) . '">';
			?>
				</div>
			</div>
			<?php
		}

		public function render_subheader_colorpicker_option( $args = array() ) {
			$defaults = array(
				'label'        => '',
				'desc'         => '',
				'section_name' => '',
				'field_name'   => '',
				'default'      => '',
			);
			$args     = wp_parse_args( $args, $defaults );

			global $post;
			$wbcom_metabox_data = get_post_meta( $post->ID, self::$_theme_slug . '_wbcom_metabox_data', true );
			$args['value']      = isset( $wbcom_metabox_data[ $args['section_name'] ][ $args['field_name'] ] ) ? $wbcom_metabox_data[ $args['section_name'] ][ $args['field_name'] ] : $args['default'];
			$field_name_to_use  = $args['section_name'] . '[' . $args['field_name'] . ']';
			?>
			<div class="wbcom-metabox-control wbcom-metabox-color-picker">
				<div class="rtm-left-side">
					<div class="rtm-tooltip-wrap">
						<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/question.png' ); ?>" class="rtm-tooltip-image" alt="<?php esc_attr_e( 'Tooltip Image', 'reign' ); ?>" />
						<label class="rtm-tooltip-label">
							<?php echo esc_html( $args['label'] ); ?>
						</label>
					</div>
					<div class="rtm-tooltiptext">
						<?php echo esc_html( $args['desc'] ); ?>
					</div>
				</div>
				<div class="wbcom-metabox-field">
					<?php
					echo '<input type="text" name="' . esc_attr( $field_name_to_use ) . '" value="' . esc_attr( $args['value'] ) . '" class="reign-color-picker color-picker" data-alpha-enabled="true">';
					?>
				</div>
			</div>
			<?php
		}

		public function render_subheader_breadcrumbs( $args = array() ) {
			$defaults = array(
				'label'        => '',
				'desc'         => '',
				'section_name' => '',
				'field_name'   => '',
				'default'      => '',
			);
			$args     = wp_parse_args( $args, $defaults );

			global $post;
			$wbcom_metabox_data = get_post_meta( $post->ID, self::$_theme_slug . '_wbcom_metabox_data', true );
			$args['value']      = isset( $wbcom_metabox_data[ $args['section_name'] ][ $args['field_name'] ] ) ? $wbcom_metabox_data[ $args['section_name'] ][ $args['field_name'] ] : $args['default'];
			$field_name_to_use  = $args['section_name'] . '[' . $args['field_name'] . ']';
			?>
			<div class="wbcom-metabox-control wbcom-metabox-text-box">
				<div class="rtm-left-side">
					<div class="rtm-tooltip-wrap">
						<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/question.png' ); ?>" class="rtm-tooltip-image" alt="<?php esc_attr_e( 'Tooltip Image', 'reign' ); ?>" />
						<label class="rtm-tooltip-label">
							<?php echo esc_html( $args['label'] ); ?>
						</label>
					</div>
					<div class="rtm-tooltiptext">
						<?php echo esc_html( $args['desc'] ); ?>
					</div>
				</div>
				<div class="wbcom-metabox-field">
			<?php
			echo '<input type="text" name="' . esc_attr( $field_name_to_use ) . '" value="' . esc_attr( $args['value'] ) . '">';
			?>
			</div>
			</div>
			<?php
		}
	}

endif;

/**
 * Main instance of Wbcom_Render_Postmeta_Fields.
 *
 * @return Wbcom_Render_Postmeta_Fields
 */
$GLOBALS['wbcom_render_postmeta_fields'] = Wbcom_Render_Postmeta_Fields::instance();
?>
