<?php
/**
 * BP Group Hierarchy — Premium Group Features.
 *
 * Manages premium group tiers, ZCreds integration, and premium-only
 * features such as custom backgrounds, animated images, and video backgrounds.
 *
 * @package BPGroupHierarchy
 * @since   2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BPGH_Premium {

    /**
     * Group meta keys.
     */
    const TIER_META            = 'bpgh_premium_tier';
    const BG_IMAGE_META        = 'bpgh_bg_image';
    const BG_VIDEO_META        = 'bpgh_bg_video';
    const BG_ANIMATED_META     = 'bpgh_bg_animated';
    const CUSTOM_COLORS_META   = 'bpgh_custom_colors';

    /**
     * Available tiers.
     */
    const TIER_FREE    = 'free';
    const TIER_PREMIUM = 'premium';

    /* =============================================================
     * Tier Management
     * ============================================================= */

    /**
     * Get the tier for a group.
     *
     * @param int $group_id Group ID.
     * @return string 'free' or 'premium'.
     */
    public static function get_tier( $group_id ) {
        $tier = groups_get_groupmeta( absint( $group_id ), self::TIER_META, true );
        return ( self::TIER_PREMIUM === $tier ) ? self::TIER_PREMIUM : self::TIER_FREE;
    }

    /**
     * Set the tier for a group.
     *
     * @param int    $group_id Group ID.
     * @param string $tier     'free' or 'premium'.
     * @return bool|int
     */
    public static function set_tier( $group_id, $tier ) {
        $tier = ( self::TIER_PREMIUM === $tier ) ? self::TIER_PREMIUM : self::TIER_FREE;
        return groups_update_groupmeta( absint( $group_id ), self::TIER_META, $tier );
    }

    /**
     * Check if a group has premium status.
     *
     * @param int $group_id Group ID.
     * @return bool
     */
    public static function is_premium( $group_id ) {
        return self::TIER_PREMIUM === self::get_tier( $group_id );
    }

    /**
     * Upgrade a group to premium using ZCreds.
     *
     * Checks if myCred is active and user has enough ZCreds,
     * then deducts the cost and upgrades the group.
     *
     * @param int $group_id Group ID.
     * @param int $user_id  User paying for the upgrade.
     * @return bool|WP_Error True on success, WP_Error on failure.
     */
    public static function upgrade_with_zcreds( $group_id, $user_id = 0 ) {
        $group_id = absint( $group_id );
        $user_id  = $user_id > 0 ? $user_id : get_current_user_id();

        if ( 0 === $user_id ) {
            return new WP_Error( 'no_user', __( 'You must be logged in to upgrade a group.', 'bp-group-hierarchy' ) );
        }

        // Already premium?
        if ( self::is_premium( $group_id ) ) {
            return new WP_Error( 'already_premium', __( 'This group is already premium.', 'bp-group-hierarchy' ) );
        }

        $cost = self::get_upgrade_cost();

        // Check myCred availability.
        if ( ! function_exists( 'mycred_get_users_balance' ) ) {
            return new WP_Error( 'mycred_missing', __( 'myCred is not active. Cannot process ZCreds payment.', 'bp-group-hierarchy' ) );
        }

        $point_type = self::get_zcred_point_type();
        $balance    = mycred_get_users_balance( $user_id, $point_type );

        if ( $balance < $cost ) {
            return new WP_Error(
                'insufficient_zcreds',
                sprintf(
                    /* translators: 1: cost, 2: current balance */
                    __( 'Insufficient ZCreds. Upgrade costs %1$s but you have %2$s.', 'bp-group-hierarchy' ),
                    $cost,
                    $balance
                )
            );
        }

        // Deduct ZCreds.
        $mycred = mycred( $point_type );

        if ( $mycred ) {
            $mycred->update_users_balance(
                $user_id,
                0 - $cost,
                'bpgh_premium_upgrade',
                array(
                    'ref_type' => 'group',
                    'ref_id'   => $group_id,
                )
            );

            $mycred->add_to_log(
                'bpgh_premium_upgrade',
                $user_id,
                0 - $cost,
                sprintf(
                    /* translators: %d: group ID */
                    __( 'Premium upgrade for group #%d', 'bp-group-hierarchy' ),
                    $group_id
                ),
                $group_id,
                array( 'ref_type' => 'group' ),
                $point_type
            );
        }

        // Upgrade the group.
        self::set_tier( $group_id, self::TIER_PREMIUM );

        /**
         * Fires when a group is upgraded to premium.
         *
         * @param int $group_id Group ID.
         * @param int $user_id  User who paid.
         * @param int $cost     ZCreds spent.
         */
        do_action( 'bpgh_group_upgraded', $group_id, $user_id, $cost );

        return true;
    }

    /**
     * Downgrade a group to free tier.
     *
     * @param int $group_id Group ID.
     * @return bool|int
     */
    public static function downgrade( $group_id ) {
        $group_id = absint( $group_id );

        // Clean up premium assets.
        groups_delete_groupmeta( $group_id, self::BG_IMAGE_META );
        groups_delete_groupmeta( $group_id, self::BG_VIDEO_META );
        groups_delete_groupmeta( $group_id, self::BG_ANIMATED_META );
        groups_delete_groupmeta( $group_id, self::CUSTOM_COLORS_META );

        return self::set_tier( $group_id, self::TIER_FREE );
    }

    /* =============================================================
     * Premium Features: Custom Backgrounds
     * ============================================================= */

    /**
     * Set a custom background image for a premium group.
     *
     * @param int    $group_id       Group ID.
     * @param string $attachment_url WordPress attachment URL.
     * @return bool|WP_Error
     */
    public static function set_background_image( $group_id, $attachment_url ) {
        if ( ! self::is_premium( $group_id ) ) {
            return new WP_Error( 'not_premium', __( 'Background images are a premium feature.', 'bp-group-hierarchy' ) );
        }

        $url = esc_url_raw( $attachment_url );
        return groups_update_groupmeta( absint( $group_id ), self::BG_IMAGE_META, $url );
    }

    /**
     * Get the custom background image URL for a group.
     *
     * @param int $group_id Group ID.
     * @return string URL or empty string.
     */
    public static function get_background_image( $group_id ) {
        if ( ! self::is_premium( $group_id ) ) {
            return '';
        }
        return esc_url( groups_get_groupmeta( absint( $group_id ), self::BG_IMAGE_META, true ) );
    }

    /**
     * Set a video background URL for a premium group.
     *
     * @param int    $group_id  Group ID.
     * @param string $video_url Video URL (mp4, webm, or embed).
     * @return bool|WP_Error
     */
    public static function set_background_video( $group_id, $video_url ) {
        if ( ! self::is_premium( $group_id ) ) {
            return new WP_Error( 'not_premium', __( 'Video backgrounds are a premium feature.', 'bp-group-hierarchy' ) );
        }

        $url = esc_url_raw( $video_url );
        return groups_update_groupmeta( absint( $group_id ), self::BG_VIDEO_META, $url );
    }

    /**
     * Get the video background URL for a group.
     *
     * @param int $group_id Group ID.
     * @return string URL or empty string.
     */
    public static function get_background_video( $group_id ) {
        if ( ! self::is_premium( $group_id ) ) {
            return '';
        }
        return esc_url( groups_get_groupmeta( absint( $group_id ), self::BG_VIDEO_META, true ) );
    }

    /**
     * Set an animated image (GIF/WebP) background for a premium group.
     *
     * @param int    $group_id Group ID.
     * @param string $anim_url Animated image URL.
     * @return bool|WP_Error
     */
    public static function set_animated_background( $group_id, $anim_url ) {
        if ( ! self::is_premium( $group_id ) ) {
            return new WP_Error( 'not_premium', __( 'Animated backgrounds are a premium feature.', 'bp-group-hierarchy' ) );
        }

        $url = esc_url_raw( $anim_url );
        return groups_update_groupmeta( absint( $group_id ), self::BG_ANIMATED_META, $url );
    }

    /**
     * Get the animated background URL.
     *
     * @param int $group_id Group ID.
     * @return string URL or empty string.
     */
    public static function get_animated_background( $group_id ) {
        if ( ! self::is_premium( $group_id ) ) {
            return '';
        }
        return esc_url( groups_get_groupmeta( absint( $group_id ), self::BG_ANIMATED_META, true ) );
    }

    /* =============================================================
     * Premium Features: Custom Color Scheme
     * ============================================================= */

    /**
     * Set a custom color scheme for a premium group.
     *
     * @param int   $group_id Group ID.
     * @param array $colors   Associative array: 'primary', 'secondary', 'accent', 'text', 'bg'.
     * @return bool|WP_Error
     */
    public static function set_color_scheme( $group_id, $colors ) {
        if ( ! self::is_premium( $group_id ) ) {
            return new WP_Error( 'not_premium', __( 'Custom colors are a premium feature.', 'bp-group-hierarchy' ) );
        }

        $allowed_keys = array( 'primary', 'secondary', 'accent', 'text', 'bg' );
        $clean        = array();

        foreach ( $allowed_keys as $key ) {
            if ( isset( $colors[ $key ] ) ) {
                $clean[ $key ] = sanitize_hex_color( $colors[ $key ] );
            }
        }

        return groups_update_groupmeta( absint( $group_id ), self::CUSTOM_COLORS_META, $clean );
    }

    /**
     * Get the custom color scheme for a group.
     *
     * @param int $group_id Group ID.
     * @return array Associative array of colours, or empty array.
     */
    public static function get_color_scheme( $group_id ) {
        if ( ! self::is_premium( $group_id ) ) {
            return array();
        }

        $colors = groups_get_groupmeta( absint( $group_id ), self::CUSTOM_COLORS_META, true );
        return is_array( $colors ) ? $colors : array();
    }

    /* =============================================================
     * Configuration Helpers
     * ============================================================= */

    /**
     * Get the ZCreds cost for a premium upgrade.
     *
     * @return int
     */
    public static function get_upgrade_cost() {
        return absint( get_option( 'bpgh_premium_cost', 100 ) );
    }

    /**
     * Get the myCred point type used for ZCreds.
     *
     * @return string
     */
    public static function get_zcred_point_type() {
        return sanitize_text_field( get_option( 'bpgh_zcred_point_type', 'mycred_default' ) );
    }

    /**
     * Get available premium features list (for admin UI and tooltip).
     *
     * @return array Feature key => label.
     */
    public static function get_feature_list() {
        return array(
            'bg_image'     => __( 'Custom Background Image', 'bp-group-hierarchy' ),
            'bg_animated'  => __( 'Animated Background (GIF/WebP)', 'bp-group-hierarchy' ),
            'bg_video'     => __( 'Video Background', 'bp-group-hierarchy' ),
            'color_scheme' => __( 'Custom Color Scheme', 'bp-group-hierarchy' ),
            'priority'     => __( 'Priority Listing in Directory', 'bp-group-hierarchy' ),
            'badge'        => __( 'Premium Badge on Group Card', 'bp-group-hierarchy' ),
            'analytics'    => __( 'Group Activity Analytics', 'bp-group-hierarchy' ),
            'pin_posts'    => __( 'Pin Posts in Group Feed', 'bp-group-hierarchy' ),
        );
    }

    /**
     * Check if premium features system is enabled.
     *
     * @return bool
     */
    public static function is_enabled() {
        return 'yes' === get_option( 'bpgh_enable_premium', 'yes' );
    }
}

/* -----------------------------------------------------------
 * Hook: Render premium background on group pages
 * ----------------------------------------------------------- */

add_action( 'bp_before_group_header', function () {
    if ( ! bp_is_group() || ! BPGH_Premium::is_enabled() ) {
        return;
    }

    $group_id = bp_get_current_group_id();

    if ( ! BPGH_Premium::is_premium( $group_id ) ) {
        return;
    }

    // Video background takes priority.
    $video = BPGH_Premium::get_background_video( $group_id );
    if ( $video ) {
        echo '<div class="bpgh-premium-bg bpgh-premium-bg--video">';
        echo '<video autoplay muted loop playsinline><source src="' . esc_url( $video ) . '"></video>';
        echo '</div>';
        return;
    }

    // Animated background.
    $animated = BPGH_Premium::get_animated_background( $group_id );
    if ( $animated ) {
        echo '<div class="bpgh-premium-bg bpgh-premium-bg--animated" style="background-image:url(' . esc_url( $animated ) . ');"></div>';
        return;
    }

    // Static background image.
    $bg = BPGH_Premium::get_background_image( $group_id );
    if ( $bg ) {
        echo '<div class="bpgh-premium-bg bpgh-premium-bg--image" style="background-image:url(' . esc_url( $bg ) . ');"></div>';
    }
}, 5 );

/* -----------------------------------------------------------
 * Hook: Inject custom color scheme as inline CSS
 * ----------------------------------------------------------- */

add_action( 'wp_head', function () {
    if ( ! function_exists( 'bp_is_group' ) || ! bp_is_group() || ! BPGH_Premium::is_enabled() ) {
        return;
    }

    $group_id = bp_get_current_group_id();
    $colors   = BPGH_Premium::get_color_scheme( $group_id );

    if ( empty( $colors ) ) {
        return;
    }

    echo '<style id="bpgh-premium-colors">';
    echo '.buddypress .group-header {';
    if ( ! empty( $colors['bg'] ) ) {
        echo 'background-color:' . esc_attr( $colors['bg'] ) . ';';
    }
    if ( ! empty( $colors['text'] ) ) {
        echo 'color:' . esc_attr( $colors['text'] ) . ';';
    }
    echo '}';
    if ( ! empty( $colors['primary'] ) ) {
        echo '.buddypress .group-header a { color:' . esc_attr( $colors['primary'] ) . '; }';
    }
    if ( ! empty( $colors['accent'] ) ) {
        echo '.buddypress .group-header .action a { background-color:' . esc_attr( $colors['accent'] ) . '; }';
    }
    echo '</style>';
} );
