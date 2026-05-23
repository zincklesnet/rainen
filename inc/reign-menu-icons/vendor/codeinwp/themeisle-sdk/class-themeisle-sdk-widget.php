<?php
/**
 * The widget model class for Reign SDK
 *
 * @package     ReignSDK
 * @subpackage  Widgets
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 * @since Menu Icons      1.0.0
 */
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'Reign_SDK_Widget' ) ) :
	/**
	 * Widget model for Reign SDK.
	 */
	abstract class Reign_SDK_Widget {
		/**
		 * @var Reign_SDK_Product $product Themeisle Product.
		 */
		protected $product;

		/**
		 * Reign_SDK_Widget constructor.
		 *
		 * @param Reign_SDK_Product $product_object Product Object.
		 */
		public function __construct( $product_object ) {
			if ( $product_object instanceof Reign_SDK_Product ) {
				$this->product = $product_object;
			}
			$this->setup_hooks();
		}

		/**
		 * Registers the hooks and then delegates to the child
		 */
		public function setup_hooks() {
			$this->setup_hooks_child();
		}

		/**
		 * Abstract function for delegating to the child
		 */
		protected abstract function setup_hooks_child();

	}
endif;
