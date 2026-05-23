<?php
/**
 * Reign Theme - Better Messages Compatibility
 *
 * Adds theme-level support for Better Messages Theme Scheme option
 * without modifying plugin files directly.
 *
 * @package Reign
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Main class for Better Messages compatibility
 */
class Reign_Better_Messages_Compatibility {

    /**
     * Single instance of class
     */
    private static $instance = null;

    /**
     * Get single instance
     */
    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        if ( ! class_exists( 'Better_Messages' ) ) {
            return;
        }

        add_action( 'after_switch_theme', array( $this, 'reign_set_default_bm_theme' ) );
        add_action( 'customize_register', array( $this, 'reign_bm_messages_theme_scheme_option' ), 30 );
        add_filter( 'theme_mod_bm-theme', array( $this, 'reign_bm_default_theme_scheme' ) );
        add_filter( 'body_class', array( $this, 'reign_bm_messages_body_class' ), 20 );
    }

    /**
     * Set default theme scheme on theme activation
     */
    public function reign_set_default_bm_theme() {
        if ( ! get_theme_mod( 'bm-theme' ) ) {
            set_theme_mod( 'bm-theme', 'theme_scheme' );
        }
    }

    /**
     * Add Theme Scheme option directly to Better Messages without removing control
     */
    public function reign_bm_messages_theme_scheme_option( $wp_customize ) {
        // Only run if Better Messages is active
        if ( ! class_exists( 'Better_Messages' ) ) {
            return;
        }
        
        // Get the original control
        $original_control = $wp_customize->get_control( 'bm-theme' );
        
        if ( $original_control ) {
            // Add Theme Scheme choice to existing control
            $original_control->choices['theme_scheme'] = _x( 'Theme', 'WP Customizer', 'reign' );
            
            // Set Theme as the default
            $original_control->setting->default = 'theme_scheme';
        }
    }

    /**
     * Set default theme scheme value
     */
    public function reign_bm_default_theme_scheme( $value ) {
        if ( empty( $value ) ) {
            return 'theme_scheme';
        }
        return $value;
    }

    /**
     * Filter Better Messages body class to add bm-messages-theme-scheme when Theme Scheme is selected
     * and remove bm-messages-light class when Theme Scheme is selected
     */
    public function reign_bm_messages_body_class( $classes ) {
        // Check if Better Messages is active
        if ( ! class_exists( 'Better_Messages' ) ) {
            return $classes;
        }
        
        // Check if Theme Scheme is selected
        $selected_scheme = get_theme_mod( 'bm-theme', 'theme_scheme' );
        
        if ( $selected_scheme === 'theme_scheme' ) {
            // Remove bm-messages-light class if it exists
            $classes = array_diff( $classes, array( 'bm-messages-light' ) );
            
            // Add bm-messages-theme-scheme class
            $classes[] = 'bm-messages-theme-scheme';
        }
        
        return $classes;
    }
}

// Initialize the compatibility class
if ( class_exists( 'Better_Messages' ) ) {
    Reign_Better_Messages_Compatibility::instance();
}