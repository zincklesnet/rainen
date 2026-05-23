<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'RTM_PMPRO_Customization' ) ) :

/**
 * @class RTM_PMPRO_Customization
 */
class RTM_PMPRO_Customization {

	/**
	 * The single instance of the class.
	 *
	 * @var RTM_PMPRO_Customization
	 */
	protected static $_instance = null;

	/**
	 * Main RTM_PMPRO_Customization Instance.
	 *
	 * Ensures only one instance of RTM_PMPRO_Customization is loaded or can be loaded.
	 *
	 * @return RTM_PMPRO_Customization - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * RTM_PMPRO_Customization Constructor.
	 */
	public function __construct() {
		$this->init_hooks();
	}

	/**
	 * Hook into actions and filters.
	 */
	private function init_hooks() {
		/**
		* Frontend design management.
		*/
		add_filter( 'pmpro_level_cost_text', array( $this, 'rtm_pmpro_level_cost_text' ), 10, 4 );

		/**
		* Adding setting option to theme customizer.
		*/
		add_action( 'init', array( $this, 'add_panels_and_sections' ) );
		add_action( 'init', array( $this, 'add_fields' ) );
	}


	public function rtm_pmpro_level_cost_text( $r, $level, $tags, $short ) {
		// initial payment
		if ( ! $short ) {
			$r = sprintf( __( 'The price for membership is <strong>%s</strong> now', 'reign' ), pmpro_formatPrice( $level->initial_payment ) );
		} else {
			$r = sprintf( __( '<div class="rtm-pmpro-now-price">%s</div>', 'reign' ), pmpro_formatPrice( $level->initial_payment ) );
		}

		// recurring part
		if ( $level->billing_amount != '0.00' ) {
			if ( $level->billing_limit > 1 ) {
				if ( $level->cycle_number == '1' ) {
					$r .= sprintf( __( '<div class="rtm-pmpro-recurring-price">%1$s per %2$s for %3$d more %4$s</div>', 'reign' ), pmpro_formatPrice( $level->billing_amount ), pmpro_translate_billing_period( $level->cycle_period ), $level->billing_limit, pmpro_translate_billing_period( $level->cycle_period, $level->billing_limit ) );
				} else {
					$r .= sprintf( __( '<div class="rtm-pmpro-recurring-price">%1$s every %2$d %3$s for %4$d more payments</div>', 'reign' ), pmpro_formatPrice( $level->billing_amount ), $level->cycle_number, pmpro_translate_billing_period( $level->cycle_period, $level->cycle_number ), $level->billing_limit );
				}
			} elseif ( $level->billing_limit == 1 ) {
				$r .= sprintf( __( '<div class="rtm-pmpro-recurring-price">%1$s after %2$d %3$s</div>', 'reign' ), pmpro_formatPrice( $level->billing_amount ), $level->cycle_number, pmpro_translate_billing_period( $level->cycle_period, $level->cycle_number ) );
			} else {
				if ( $level->billing_amount === $level->initial_payment ) {
					if ( $level->cycle_number == '1' ) {
						if ( ! $short ) {
							$r = sprintf( __( '<div class="rtm-pmpro-recurring-price">%1$s per %2$s</div>', 'reign' ), pmpro_formatPrice( $level->initial_payment ), pmpro_translate_billing_period( $level->cycle_period ) );
						} else {
							$r = sprintf( __( '<div class="rtm-pmpro-recurring-price">%1$s per %2$s</div>', 'reign' ), pmpro_formatPrice( $level->initial_payment ), pmpro_translate_billing_period( $level->cycle_period ) );
						}
					} else {
						if ( ! $short ) {
							$r = sprintf( __( '<div class="rtm-pmpro-recurring-price">%1$s every %2$d %3$s</div>', 'reign' ), pmpro_formatPrice( $level->initial_payment ), $level->cycle_number, pmpro_translate_billing_period( $level->cycle_period, $level->cycle_number ) );
						} else {
							$r = sprintf( __( '<div class="rtm-pmpro-recurring-price">%1$s every %2$d %3$s</div>', 'reign' ), pmpro_formatPrice( $level->initial_payment ), $level->cycle_number, pmpro_translate_billing_period( $level->cycle_period, $level->cycle_number ) );
						}
					}
				} else {
					if ( $level->cycle_number == '1' ) {
						$r .= sprintf( __( '<div class="rtm-pmpro-recurring-price">%1$s per %2$s</div>', 'reign' ), pmpro_formatPrice( $level->billing_amount ), pmpro_translate_billing_period( $level->cycle_period ) );
					} else {
						$r .= sprintf( __( '<div class="rtm-pmpro-recurring-price">%1$s every %2$d %3$s</div>', 'reign' ), pmpro_formatPrice( $level->billing_amount ), $level->cycle_number, pmpro_translate_billing_period( $level->cycle_period, $level->cycle_number ) );
					}
				}
			}
		} else {
			// $r .= '.';
		}

		// add a space
		$r .= ' ';
		return $r;
	}

	/**
	 * Add panels and sections
	 */
	public function add_panels_and_sections() {
		new \Kirki\Section(
			'reign_pmpro_support',
			array(
				'title'       => esc_html__( 'Post Membership Pro', 'reign' ),
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

		new \Kirki\Field\Select(
			array(
				'settings'    => 'reign_pmpro_per_row',
				'label'       => esc_html__( 'Membership Levels Per Row', 'reign' ),
				'description' => esc_html__( 'Select how many membership levels to show per row.', 'reign' ),
				'section'     => 'reign_pmpro_support',
				'default'     => '3-col-layout',
				'priority'    => 10,
				'choices'     => array(
					'3-col-layout' => esc_html__( '3 Column Layout', 'reign' ),
					'4-col-layout' => esc_html__( '4 Column Layout', 'reign' ),
				),
			)
		);
	}

}

endif;

/**
 * Main instance of RTM_PMPRO_Customization.
 * @return RTM_PMPRO_Customization
 */
RTM_PMPRO_Customization::instance();
