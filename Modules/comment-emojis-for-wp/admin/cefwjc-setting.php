<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'CEFWJC_COMMENT_SETTING' ) ) {

	class CEFWJC_COMMENT_SETTING {

		/**
		 * Top-level menu slug.
		 *
		 * @var string
		 */
		private $menu_slug = 'comment-emojis-for-wp';

		/**
		 * Help submenu slug.
		 *
		 * @var string
		 */
		private $help_slug = 'comment-emojis-for-wp-help';

		/**
		 * Support submenu slug.
		 *
		 * @var string
		 */
		private $support_slug = 'comment-emojis-for-wp-support';

		/**
		 * Constructor.
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'wp_comment_emojis_setup' ) );
			add_action( 'admin_init', array( $this, 'update_wp_comment_emojis_options' ) );
			add_filter( 'plugin_action_links_' . CEFWJC_PLUGIN_BASE, array( $this, 'comment_emojis_settings_link' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_script' ) );
		}

		/**
		 * Register top-level plugin admin pages.
		 *
		 * @return void
		 */
		public function wp_comment_emojis_setup() {
			add_menu_page(
				__( 'Comment Emojis for WP', 'comment-emojis-for-wp' ),
				__( 'Comment Emojis', 'comment-emojis-for-wp' ),
				'manage_options',
				$this->menu_slug,
				array( $this, 'render_settings_page' ),
				'dashicons-smiley',
				58
			);

			add_submenu_page(
				$this->menu_slug,
				__( 'Comment Emojis Settings', 'comment-emojis-for-wp' ),
				__( 'Settings', 'comment-emojis-for-wp' ),
				'manage_options',
				$this->menu_slug,
				array( $this, 'render_settings_page' )
			);

			add_submenu_page(
				$this->menu_slug,
				__( 'Comment Emojis Help', 'comment-emojis-for-wp' ),
				__( 'Help', 'comment-emojis-for-wp' ),
				'manage_options',
				$this->help_slug,
				array( $this, 'render_help_page' )
			);

			add_submenu_page(
				$this->menu_slug,
				__( 'Comment Emojis Support', 'comment-emojis-for-wp' ),
				__( 'Support', 'comment-emojis-for-wp' ),
				'manage_options',
				$this->support_slug,
				array( $this, 'render_support_page' )
			);
		}

	    /**
		 * Enqueue settings page assets.
		 *
		 * @param string $hook_suffix Current admin page hook.
		 * @return void
		 */
		public function enqueue_admin_script( $hook_suffix ) {
			if ( false === strpos( $hook_suffix, $this->menu_slug ) ) {
				return;
			}

			wp_enqueue_style( 'cefwjc-admin', plugins_url( 'css/cefwjc-admin.css', __FILE__ ), array(), CEFWJC_PLUGIN_VERSION );
			wp_enqueue_script( 'cefwjc-admin', plugins_url( 'js/cefwjc-admin.js', __FILE__ ), array( 'jquery' ), CEFWJC_PLUGIN_VERSION, true );
		}

		/**
		 * Render the settings page.
		 *
		 * @return void
		 */
		public function render_settings_page() {
			$options           = CEFWJC_Plugin::get_frontend_settings();
			$search_row_style  = 'yes' === $options['search'] ? 'display:none;' : '';
			$skin_row_style    = 'yes' === $options['skintone'] ? 'display:none;' : '';
			?>
			<div class="wrap cefwjc-admin">
				<?php $this->render_page_header( 'settings' ); ?>

				<?php settings_errors(); ?>

				<div class="cefwjc-admin__layout">
					<div class="cefwjc-admin__main">
						<form method="post" action="options.php">
							<?php settings_fields( CEFWJC_Plugin::OPTION_GROUP ); ?>

							<div class="cefwjc-card">
								<div class="cefwjc-card__header">
									<h2><?php esc_html_e( 'Picker Layout', 'comment-emojis-for-wp' ); ?></h2>
									<p><?php esc_html_e( 'Control where the picker appears and how people navigate emoji groups.', 'comment-emojis-for-wp' ); ?></p>
								</div>
								<table class="form-table" role="presentation">
									<tbody>
											<?php
											$this->render_select_row(
												'cefwjc_position_emojis',
												__( 'Position of the emoji picker', 'comment-emojis-for-wp' ),
												__( 'Choose where the picker opens around the comment textarea.', 'comment-emojis-for-wp' ),
												$options['position_emojis'],
												array(
													'top'    => __( 'Top', 'comment-emojis-for-wp' ),
													'right'  => __( 'Right', 'comment-emojis-for-wp' ),
													'bottom' => __( 'Bottom', 'comment-emojis-for-wp' ),
												)
											);

											$this->render_select_row(
												'cefwjc_filter_position',
												__( 'Position of the filter header', 'comment-emojis-for-wp' ),
												__( 'Move emoji category filters to the top or bottom of the picker.', 'comment-emojis-for-wp' ),
												$options['filter_position'],
												array(
													'top'    => __( 'Top', 'comment-emojis-for-wp' ),
													'bottom' => __( 'Bottom', 'comment-emojis-for-wp' ),
												)
											);
											?>
									</tbody>
								</table>
							</div>

							<div class="cefwjc-card">
								<div class="cefwjc-card__header">
									<h2><?php esc_html_e( 'Visibility Controls', 'comment-emojis-for-wp' ); ?></h2>
									<p><?php esc_html_e( 'Hide individual picker features when you want a simpler comment experience.', 'comment-emojis-for-wp' ); ?></p>
								</div>
								<table class="form-table" role="presentation">
									<tbody>
											<?php
											$this->render_checkbox_row(
												'cefwjc_skintone',
												__( 'Hide skin tone buttons', 'comment-emojis-for-wp' ),
												__( 'Removes skin tone controls from the picker interface.', 'comment-emojis-for-wp' ),
												$options['skintone']
											);

											$this->render_select_row(
												'cefwjc_skintone_style',
												__( 'Skin tone selector style', 'comment-emojis-for-wp' ),
												__( 'Applies only when skin tone controls are visible.', 'comment-emojis-for-wp' ),
												$options['skintone_style'],
												array(
													'bullet'   => __( 'Bullet', 'comment-emojis-for-wp' ),
													'radio'    => __( 'Radio', 'comment-emojis-for-wp' ),
													'square'   => __( 'Square', 'comment-emojis-for-wp' ),
													'checkbox' => __( 'Checkbox', 'comment-emojis-for-wp' ),
												),
												'skintone_hide',
												$skin_row_style
											);

											$this->render_checkbox_row(
												'cefwjc_search',
												__( 'Disable emoji search', 'comment-emojis-for-wp' ),
												__( 'Use this when you want a minimal picker with no search field.', 'comment-emojis-for-wp' ),
												$options['search']
											);

											$this->render_select_row(
												'cefwjc_search_position',
												__( 'Search panel position', 'comment-emojis-for-wp' ),
												__( 'Applies only when emoji search is enabled.', 'comment-emojis-for-wp' ),
												$options['search_position'],
												array(
													'top'    => __( 'Top', 'comment-emojis-for-wp' ),
													'bottom' => __( 'Bottom', 'comment-emojis-for-wp' ),
												),
												'search_hide',
												$search_row_style
											);

											$this->render_checkbox_row(
												'cefwjc_recent_emojis',
												__( 'Hide recently selected emojis', 'comment-emojis-for-wp' ),
												__( 'Removes the recent emoji list for a cleaner picker.', 'comment-emojis-for-wp' ),
												$options['recent_emojis']
											);
											?>
									</tbody>
								</table>

								<div class="cefwjc-card__footer">
									<?php submit_button( __( 'Save Changes', 'comment-emojis-for-wp' ), 'primary', 'submit', false ); ?>
								</div>
							</div>
						</form>
					</div>

					<aside class="cefwjc-admin__sidebar">
						<div class="cefwjc-sidecard cefwjc-sidecard--highlight">
							<h3><?php esc_html_e( 'Quick Summary', 'comment-emojis-for-wp' ); ?></h3>
							<ul class="cefwjc-feature-list">
								<li><?php esc_html_e( 'Works with the default WordPress comment textarea.', 'comment-emojis-for-wp' ); ?></li>
								<li><?php esc_html_e( 'Lets you simplify the picker without touching code.', 'comment-emojis-for-wp' ); ?></li>
								<li><?php esc_html_e( 'Keeps front-end behavior lightweight for visitors.', 'comment-emojis-for-wp' ); ?></li>
							</ul>
						</div>

						<div class="cefwjc-sidecard">
							<h3><?php esc_html_e( 'Compatibility', 'comment-emojis-for-wp' ); ?></h3>
							<p><?php esc_html_e( 'Custom comment form markup may need template-specific integration because this plugin targets the default WordPress comment field.', 'comment-emojis-for-wp' ); ?></p>
							<p><?php esc_html_e( 'Use a utf8mb4 database collation so emoji comments are stored correctly.', 'comment-emojis-for-wp' ); ?></p>
						</div>

						<div class="cefwjc-sidecard">
							<h3><?php esc_html_e( 'Recommended Setup', 'comment-emojis-for-wp' ); ?></h3>
							<ul class="cefwjc-feature-list cefwjc-feature-list--recommend">
								<li><?php esc_html_e( 'Bottom picker position for the least overlap on small screens.', 'comment-emojis-for-wp' ); ?></li>
								<li><?php esc_html_e( 'Top filter position for faster category switching.', 'comment-emojis-for-wp' ); ?></li>
								<li><?php esc_html_e( 'Keep search enabled on content-heavy community sites.', 'comment-emojis-for-wp' ); ?></li>
							</ul>
						</div>
					</aside>
				</div>
			</div>
			<?php
		}

		/**
		 * Render the help page.
		 *
		 * @return void
		 */
		public function render_help_page() {
			?>
			<div class="wrap cefwjc-admin">
				<?php $this->render_page_header( 'help' ); ?>

				<div class="cefwjc-card cefwjc-card--narrow">
					<div class="cefwjc-card__header">
						<h2><?php esc_html_e( 'How To Use', 'comment-emojis-for-wp' ); ?></h2>
						<p><?php esc_html_e( 'A quick guide for configuring the plugin safely in wp-admin.', 'comment-emojis-for-wp' ); ?></p>
					</div>
					<ol class="cefwjc-help-steps">
						<li><?php esc_html_e( 'Open the Settings submenu under Comment Emojis.', 'comment-emojis-for-wp' ); ?></li>
						<li><?php esc_html_e( 'Choose where the emoji picker should appear around the comment field.', 'comment-emojis-for-wp' ); ?></li>
						<li><?php esc_html_e( 'Decide whether search, skin tone controls, and recent emojis should be visible.', 'comment-emojis-for-wp' ); ?></li>
						<li><?php esc_html_e( 'Save changes and test one post comment form on the front end.', 'comment-emojis-for-wp' ); ?></li>
					</ol>

					<div class="notice notice-warning inline">
						<p><?php esc_html_e( 'If your theme replaces the default comment textarea markup, the picker may require custom integration.', 'comment-emojis-for-wp' ); ?></p>
					</div>

					<div class="notice notice-info inline">
						<p><?php esc_html_e( 'Use a utf8mb4 database collation so emoji comments are stored correctly.', 'comment-emojis-for-wp' ); ?></p>
					</div>
				</div>
			</div>
			<?php
		}

		/**
		 * Render the support page.
		 *
		 * @return void
		 */
		public function render_support_page() {
			?>
			<div class="wrap cefwjc-admin">
				<?php $this->render_page_header( 'support' ); ?>

				<div class="cefwjc-card cefwjc-card--narrow">
					<div class="cefwjc-card__header">
						<h2><?php esc_html_e( 'Support This Plugin', 'comment-emojis-for-wp' ); ?></h2>
						<p><?php esc_html_e( 'If Comment Emojis for WP helps your site, you can support ongoing maintenance, compatibility updates, and improvements.', 'comment-emojis-for-wp' ); ?></p>
					</div>

					<div class="cefwjc-support-card__body">
						<p><?php esc_html_e( 'Your donation helps keep the plugin lightweight, updated for new WordPress releases, and maintained for future fixes.', 'comment-emojis-for-wp' ); ?></p>
						<p>
							<a class="button button-primary button-hero" href="<?php echo esc_url( CEFWJC_Plugin::get_donate_url() ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Donate', 'comment-emojis-for-wp' ); ?></a>
						</p>
						<p class="description"><?php esc_html_e( 'Every contribution is appreciated. Thank you for supporting the project.', 'comment-emojis-for-wp' ); ?></p>
					</div>
				</div>
			</div>
			<?php
		}

		/**
		 * Render shared page header.
		 *
		 * @param string $active_page Active page slug label.
		 * @return void
		 */
		private function render_page_header( $active_page ) {
			?>
			<div class="cefwjc-admin__hero">
				<div>
					<h1><?php esc_html_e( 'Comment Emojis for WP', 'comment-emojis-for-wp' ); ?></h1>
					<p class="cefwjc-admin__lead"><?php esc_html_e( 'Manage the emoji picker from a dedicated admin menu built for this plugin.', 'comment-emojis-for-wp' ); ?></p>
					<div class="cefwjc-admin__badges">
						<span class="cefwjc-admin__badge"><?php esc_html_e( 'Lightweight', 'comment-emojis-for-wp' ); ?></span>
						<span class="cefwjc-admin__badge"><?php esc_html_e( 'Default comment form support', 'comment-emojis-for-wp' ); ?></span>
						<span class="cefwjc-admin__badge">
							<?php
							echo esc_html(
								'settings' === $active_page
									? __( 'Settings view', 'comment-emojis-for-wp' )
									: ( 'help' === $active_page
										? __( 'Help view', 'comment-emojis-for-wp' )
										: __( 'Support view', 'comment-emojis-for-wp' ) )
							);
							?>
						</span>
					</div>
					<div class="cefwjc-admin__page-links">
						<a class="button <?php echo 'settings' === $active_page ? 'button-primary' : 'button-secondary'; ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=' . $this->menu_slug ) ); ?>"><?php esc_html_e( 'Settings', 'comment-emojis-for-wp' ); ?></a>
						<a class="button <?php echo 'help' === $active_page ? 'button-primary' : 'button-secondary'; ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=' . $this->help_slug ) ); ?>"><?php esc_html_e( 'Help', 'comment-emojis-for-wp' ); ?></a>
						<a class="button <?php echo 'support' === $active_page ? 'button-primary' : 'button-secondary'; ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=' . $this->support_slug ) ); ?>"><?php esc_html_e( 'Support', 'comment-emojis-for-wp' ); ?></a>
					</div>
				</div>
				<div class="cefwjc-admin__hero-icon" aria-hidden="true">
					<span class="dashicons dashicons-smiley"></span>
				</div>
			</div>
			<?php
		}

		/**
		 * Render a select field row.
		 *
		 * @param string $field_id Field id.
		 * @param string $label Field label.
		 * @param string $description Field description.
		 * @param string $selected_value Current value.
		 * @param array  $choices Available choices.
		 * @param string $row_id Optional row id.
		 * @param string $row_style Optional row style.
		 * @return void
		 */
		private function render_select_row( $field_id, $label, $description, $selected_value, $choices, $row_id = '', $row_style = '' ) {
			?>
			<tr<?php echo $row_id ? ' id="' . esc_attr( $row_id ) . '"' : ''; ?><?php echo $row_style ? ' style="' . esc_attr( $row_style ) . '"' : ''; ?>>
				<th scope="row">
					<label for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $label ); ?></label>
				</th>
				<td>
					<select name="<?php echo esc_attr( $field_id ); ?>" id="<?php echo esc_attr( $field_id ); ?>">
						<?php foreach ( $choices as $value => $choice_label ) : ?>
							<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $selected_value ); ?>><?php echo esc_html( $choice_label ); ?></option>
						<?php endforeach; ?>
					</select>
					<p class="description"><?php echo esc_html( $description ); ?></p>
				</td>
			</tr>
			<?php
		}

		/**
		 * Render a checkbox field row.
		 *
		 * @param string $field_id Field id.
		 * @param string $label Field label.
		 * @param string $description Field description.
		 * @param string $value Current value.
		 * @return void
		 */
		private function render_checkbox_row( $field_id, $label, $description, $value ) {
			?>
			<tr>
				<th scope="row"><?php echo esc_html( $label ); ?></th>
				<td>
					<label class="cefwjc-checkbox" for="<?php echo esc_attr( $field_id ); ?>">
						<input name="<?php echo esc_attr( $field_id ); ?>" id="<?php echo esc_attr( $field_id ); ?>" type="checkbox" value="yes" <?php checked( 'yes', $value ); ?>>
						<span><?php echo esc_html( $description ); ?></span>
					</label>
				</td>
			</tr>
			<?php
		}

		/**
		 * Register plugin settings.
		 *
		 * @return void
		 */
		public function update_wp_comment_emojis_options() {
			$defaults = CEFWJC_Plugin::get_defaults();

			register_setting(
				CEFWJC_Plugin::OPTION_GROUP,
				'cefwjc_position_emojis',
				array(
					'type'              => 'string',
					'sanitize_callback' => static function ( $value ) {
						return CEFWJC_Plugin::sanitize_choice( 'cefwjc_position_emojis', $value );
					},
					'default'           => $defaults['cefwjc_position_emojis'],
				)
			);

			register_setting(
				CEFWJC_Plugin::OPTION_GROUP,
				'cefwjc_filter_position',
				array(
					'type'              => 'string',
					'sanitize_callback' => static function ( $value ) {
						return CEFWJC_Plugin::sanitize_choice( 'cefwjc_filter_position', $value );
					},
					'default'           => $defaults['cefwjc_filter_position'],
				)
			);

			register_setting(
				CEFWJC_Plugin::OPTION_GROUP,
				'cefwjc_skintone',
				array(
					'type'              => 'string',
					'sanitize_callback' => array( 'CEFWJC_Plugin', 'sanitize_toggle' ),
					'default'           => $defaults['cefwjc_skintone'],
				)
			);

			register_setting(
				CEFWJC_Plugin::OPTION_GROUP,
				'cefwjc_skintone_style',
				array(
					'type'              => 'string',
					'sanitize_callback' => static function ( $value ) {
						return CEFWJC_Plugin::sanitize_choice( 'cefwjc_skintone_style', $value );
					},
					'default'           => $defaults['cefwjc_skintone_style'],
				)
			);

			register_setting(
				CEFWJC_Plugin::OPTION_GROUP,
				'cefwjc_search',
				array(
					'type'              => 'string',
					'sanitize_callback' => array( 'CEFWJC_Plugin', 'sanitize_toggle' ),
					'default'           => $defaults['cefwjc_search'],
				)
			);

			register_setting(
				CEFWJC_Plugin::OPTION_GROUP,
				'cefwjc_search_position',
				array(
					'type'              => 'string',
					'sanitize_callback' => static function ( $value ) {
						return CEFWJC_Plugin::sanitize_choice( 'cefwjc_search_position', $value );
					},
					'default'           => $defaults['cefwjc_search_position'],
				)
			);

			register_setting(
				CEFWJC_Plugin::OPTION_GROUP,
				'cefwjc_recent_emojis',
				array(
					'type'              => 'string',
					'sanitize_callback' => array( 'CEFWJC_Plugin', 'sanitize_toggle' ),
					'default'           => $defaults['cefwjc_recent_emojis'],
				)
			);
		}
	    
	    public function comment_emojis_settings_link( $links ) {
			$links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=' . $this->menu_slug ) ) . '">' . esc_html__( 'Settings', 'comment-emojis-for-wp' ) . '</a>';
			return $links;
		}
	}
}
