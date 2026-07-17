<?php
/**
 * Template: Levels
 * Version: 3.1
 *
 * See documentation for how to override the PMPro templates.
 * @link https://www.paidmembershipspro.com/documentation/templates/
 *
 * @version 3.1
 *
 * @author Paid Memberships Pro
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

global $wpdb, $pmpro_msg, $pmpro_msgt, $current_user;

$layout_mapping = array(
	'3-col-layout' => 'price_table_column_3',
	'4-col-layout' => 'price_table_column_4',
);

$membership_levels_per_row = get_theme_mod( 'reign_pmpro_per_row', '3-col-layout' );
$membership_levels_per_row = isset( $layout_mapping[ $membership_levels_per_row ] ) ? $layout_mapping[ $membership_levels_per_row ] : $membership_levels_per_row;

$pmpro_levels = pmpro_sort_levels_by_order( pmpro_getAllLevels( false, true ) );
$pmpro_levels = apply_filters( 'pmpro_levels_array', $pmpro_levels );

$level_groups = pmpro_get_level_groups_in_order();

if ( $pmpro_msg ) {
	?>
	<div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_message ' . $pmpro_msgt, $pmpro_msgt ) ); ?>"><?php echo wp_kses_post( $pmpro_msg ); ?></div>
	<?php
}

foreach ( $level_groups as $level_group ) {
	$levels_in_group = pmpro_get_level_ids_for_group( $level_group->id );

	// The pmpro_levels_array filter is sometimes used to hide levels from the levels page.
	// Let's make sure that every level in the group should still be displayed.
	$levels_to_show_for_group = array();
	foreach ( $pmpro_levels as $level ) {
		if ( in_array( $level->id, $levels_in_group ) ) {
			$levels_to_show_for_group[] = $level;
		}
	}

	if ( empty( $levels_to_show_for_group ) ) {
		continue;
	}

	if ( count( $level_groups ) > 1 ) {
		?>
		<h2><?php echo esc_html( $level_group->name ); ?></h2>
		<?php
		if ( ! empty( $level_group->allow_multiple_selections ) ) {
			?>
			<p><?php esc_html_e( 'You may select multiple levels from this group.', 'reign' ); ?></p>
			<?php
		} else {
			?>
			<p><?php esc_html_e( 'You may select only one level from this group.', 'reign' ); ?></p>
			<?php
		}
	}

	?>
	<div id="pmpro_levels_table" class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_table pmpro_levels pmpro_checkout', 'pmpro_levels_table' ) ); ?>">
		<div class="rtm_pmpro_levels_plan">
			<?php
			$count         = 0;
			$has_any_level = false;
			foreach ( $levels_to_show_for_group as $level ) {
				$user_level    = pmpro_getSpecificMembershipLevelForUser( $current_user->ID, $level->id );
				$has_level     = ! empty( $user_level );
				$has_any_level = $has_level ?: $has_any_level;
				?>

				<div class="rtm_pmpro_levels_section items-levels <?php echo esc_attr( $membership_levels_per_row ); ?>">
					<div class="rtm_pmpro_price <?php echo ( $count++ % 2 === 0 ) ? 'odd' : ''; ?><?php echo ( $has_level ) ? ' active' : ''; ?>">
						<div class="rtm_pmpro_price_top">		
							<h2>
								<?php echo $has_level ? '<strong>' . esc_html( $level->name ) . '</strong>' : esc_html( $level->name ); ?>
							</h2>
							<div class="rtm_levels_table_price">
								<?php
								$cost_text       = pmpro_getLevelCost( $level, true, true );
								$expiration_text = pmpro_getLevelExpiration( $level );

								if ( ! empty( $cost_text ) && ! empty( $expiration_text ) ) {
									// Both cost and expiration are present.
									echo '<div class="' . esc_attr( pmpro_get_element_class( 'pmpro_level-price' ) ) . '">' . wp_kses_post( $cost_text ) . '</div>';
									echo '<div class="' . esc_attr( pmpro_get_element_class( 'pmpro_level-expiration' ) ) . '">' . wp_kses_post( $expiration_text ) . '</div>';
								} elseif ( ! empty( $cost_text ) ) {
									// Only cost is present.
									echo '<div class="' . esc_attr( pmpro_get_element_class( 'pmpro_level-price' ) ) . '">' . wp_kses_post( $cost_text ) . '</div>';
								} elseif ( ! empty( $expiration_text ) ) {
									// Only expiration is present.
									echo '<div class="' . esc_attr( pmpro_get_element_class( 'pmpro_level-expiration' ) ) . '">' . wp_kses_post( $expiration_text ) . '</div>';
								}
								?>
							</div>
						</div>
						<div class="rtm_levels_table_des">
							<?php
							/**
							 * All devs to filter the level description at checkout.
							 * We also have a function in includes/filters.php that applies the the_content filters to this description.
							 *
							 * @param string $description The level description.
							 * @param object $pmpro_level The PMPro Level object.
							 */
							// Get level description.
							$level_description = apply_filters( 'rtm_pmpro_level_description', $level->description, $level );
							if ( ! empty( $level_description ) ) {
								// Set the maximum number of words, using a filter to allow for customization.
								$max_words = apply_filters( 'rtm_pmpro_max_words_limit', 20 ); // Default to 20, but can be changed by other code.

								// Trim the description based on the word limit.
								$trimmed_description = wp_trim_words( $level_description, $max_words, '...' ); // Append '...' if trimmed.

								?>
								<div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_level_description_text' ) ); ?>">
									<?php echo wp_kses_post( $trimmed_description ); ?>
								</div> <!-- end pmpro_level_description_text -->
								<?php
							}

							?>

						</div>

						<div class="rtm_levels_table_button">
							<?php if ( ! $has_level ) { ?>                	
								<a class="pmpro_btn pmpro_btn-select" aria-label="<?php echo esc_attr( sprintf( __( 'Select the %s Membership Level', 'reign' ), $level->name ) ); ?>" class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_btn pmpro_btn-select', 'pmpro_btn-select' ) ); ?>" href="<?php echo esc_url( pmpro_url( 'checkout', '?pmpro_level=' . $level->id, 'https' ) ); ?>"><?php esc_html_e( 'Select', 'reign' ); ?></a>
							<?php } else { ?>      
								<?php
								// if it's a one-time-payment level, offer a link to renew.
								if ( pmpro_isLevelExpiringSoon( $user_level ) && $level->allow_signups ) {
									?>
										<a class="pmpro_btn pmpro_btn-select" aria-label="<?php echo esc_attr( sprintf( __( 'Renew your %s Membership Level', 'reign' ), $level->name ) ); ?>" class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_btn pmpro_btn-select', 'pmpro_btn-select' ) ); ?>" href="<?php echo esc_url( pmpro_url( 'checkout', '?pmpro_level=' . $level->id, 'https' ) ); ?>"><?php esc_html_e( 'Renew', 'reign' ); ?></a>
									<?php
								} else {
									?>
										<a class="pmpro_btn pmpro_btn-select" aria-label="<?php echo esc_attr( sprintf( __( 'View your %s Membership Account', 'reign' ), $level->name ) ); ?>" class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_btn disabled', 'pmpro_btn' ) ); ?>" href="<?php echo esc_url( pmpro_url( 'account' ) ); ?>"><?php esc_html_e( 'Your&nbsp;Level', 'reign' ); ?></a>
									<?php
								}
								?>
							<?php } ?>
						</div>
					</div>
				</div>
				<?php
			}
			?>

		</div>
	</div>
	<nav id="nav-below" class="navigation" role="navigation">
		<div class="nav-previous alignleft">
			<?php if ( $has_any_level ) { ?>
				<a href="<?php echo esc_url( pmpro_url( 'account' ) ); ?>" id="pmpro_levels-return-account">&larr; <?php esc_html_e( 'Return to Your Account', 'reign' ); ?></a>
			<?php } else { ?>
				<a href="<?php echo esc_url( home_url() ); ?>" id="pmpro_levels-return-home">&larr; <?php esc_html_e( 'Return to Home', 'reign' ); ?></a>
			<?php } ?>
		</div>
	</nav>
<?php } ?>