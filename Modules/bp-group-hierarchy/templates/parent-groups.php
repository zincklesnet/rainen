<?php
/**
 * BP Group Hierarchy — Parent Groups Page Template.
 *
 * Dedicated template for displaying all top-level parent groups
 * with avatars, tooltips, sorting, and hierarchy info.
 *
 * This template can be loaded via the [bpgh_parent_groups] shortcode
 * or overridden in themes at:
 *   yourtheme/bpgh/parent-groups.php
 *
 * @package BPGroupHierarchy
 * @since   2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Template args (passed via $args or extracted from shortcode atts).
$per_page     = isset( $args['per_page'] ) ? absint( $args['per_page'] ) : 20;
$orderby      = isset( $args['orderby'] ) ? sanitize_text_field( $args['orderby'] ) : 'name';
$order        = isset( $args['order'] ) ? strtoupper( sanitize_text_field( $args['order'] ) ) : 'ASC';
$show_avatars = isset( $args['show_avatars'] ) ? (bool) $args['show_avatars'] : true;
$layout       = isset( $args['layout'] ) ? sanitize_text_field( $args['layout'] ) : 'grid';
$paged        = isset( $args['paged'] ) ? absint( $args['paged'] ) : 1;

// Query parent groups (groups with no parent).
$query_args = array(
    'per_page'   => $per_page,
    'page'       => $paged,
    'orderby'    => $orderby,
    'order'      => $order,
    'meta_query' => array(
        'relation' => 'OR',
        array(
            'key'     => 'bpgh_parent_id',
            'value'   => '0',
            'compare' => '=',
        ),
        array(
            'key'     => 'bpgh_parent_id',
            'compare' => 'NOT EXISTS',
        ),
    ),
);

// Optional category filter.
if ( ! empty( $args['category'] ) ) {
    $query_args['meta_query'][] = array(
        'key'   => 'bpgh_category',
        'value' => sanitize_text_field( $args['category'] ),
    );
    $query_args['meta_query']['relation'] = 'AND';
}

$groups_query = groups_get_groups( $query_args );
$groups       = $groups_query['groups'];
$total        = $groups_query['total'];
$max_pages    = ceil( $total / $per_page );

$layout_class = 'grid' === $layout ? 'bpgh-grid-layout' : 'bpgh-list-layout';
?>

<div class="bpgh-parent-groups-page <?php echo esc_attr( $layout_class ); ?>">

    <?php if ( ! empty( $args['title'] ) ) : ?>
        <h2 class="bpgh-page-title"><?php echo esc_html( $args['title'] ); ?></h2>
    <?php endif; ?>

    <!-- Sort controls -->
    <div class="bpgh-sort-controls">
        <label for="bpgh-sort"><?php esc_html_e( 'Sort by:', 'bp-group-hierarchy' ); ?></label>
        <select id="bpgh-sort" class="bpgh-sort-select" data-current="<?php echo esc_attr( $orderby ); ?>">
            <option value="name" <?php selected( $orderby, 'name' ); ?>><?php esc_html_e( 'Name', 'bp-group-hierarchy' ); ?></option>
            <option value="date_created" <?php selected( $orderby, 'date_created' ); ?>><?php esc_html_e( 'Date Created', 'bp-group-hierarchy' ); ?></option>
            <option value="last_activity" <?php selected( $orderby, 'last_activity' ); ?>><?php esc_html_e( 'Last Activity', 'bp-group-hierarchy' ); ?></option>
            <option value="total_member_count" <?php selected( $orderby, 'total_member_count' ); ?>><?php esc_html_e( 'Member Count', 'bp-group-hierarchy' ); ?></option>
        </select>
    </div>

    <?php if ( empty( $groups ) ) : ?>

        <p class="bpgh-no-results"><?php esc_html_e( 'No parent groups found.', 'bp-group-hierarchy' ); ?></p>

    <?php else : ?>

        <div class="bpgh-groups-container">
            <?php foreach ( $groups as $group ) : ?>
                <?php
                // Build URL.
                if ( function_exists( 'bp_get_group_url' ) ) {
                    $group_url = bp_get_group_url( $group );
                } elseif ( function_exists( 'bp_get_group_permalink' ) ) {
                    $group_url = bp_get_group_permalink( $group );
                } else {
                    $group_url = '#';
                }

                $children    = BP_Group_Hierarchy::get_children( $group->id );
                $child_count = count( $children );
                ?>

                <div class="bpgh-group-card" data-group-id="<?php echo esc_attr( $group->id ); ?>">

                    <?php if ( $show_avatars && function_exists( 'bp_get_group_avatar' ) ) : ?>
                        <div class="bpgh-card-avatar">
                            <a href="<?php echo esc_url( $group_url ); ?>"
                               data-bpgh-tooltip="<?php echo esc_attr( $group->id ); ?>">
                                <?php
                                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                echo bp_get_group_avatar( array(
                                    'item_id' => $group->id,
                                    'type'    => 'full',
                                    'width'   => 150,
                                    'height'  => 150,
                                ) );
                                ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="bpgh-card-content">
                        <h3 class="bpgh-card-title">
                            <a href="<?php echo esc_url( $group_url ); ?>"
                               data-bpgh-tooltip="<?php echo esc_attr( $group->id ); ?>">
                                <?php echo esc_html( $group->name ); ?>
                            </a>
                        </h3>

                        <?php if ( ! empty( $group->description ) ) : ?>
                            <p class="bpgh-card-desc">
                                <?php echo esc_html( wp_trim_words( wp_strip_all_tags( $group->description ), 20 ) ); ?>
                            </p>
                        <?php endif; ?>

                        <div class="bpgh-card-meta">
                            <?php if ( isset( $group->total_member_count ) ) : ?>
                                <span class="bpgh-member-count">
                                    <?php
                                    printf(
                                        esc_html( _n( '%d member', '%d members', $group->total_member_count, 'bp-group-hierarchy' ) ),
                                        absint( $group->total_member_count )
                                    );
                                    ?>
                                </span>
                            <?php endif; ?>

                            <?php if ( $child_count > 0 ) : ?>
                                <span class="bpgh-child-count">
                                    <?php
                                    printf(
                                        esc_html( _n( '%d sub-group', '%d sub-groups', $child_count, 'bp-group-hierarchy' ) ),
                                        $child_count
                                    );
                                    ?>
                                </span>
                            <?php endif; ?>

                            <?php
                            // Category badge.
                            if ( class_exists( 'BPGH_Categories' ) ) {
                                $cat = BPGH_Categories::get_group_category( $group->id );
                                if ( $cat ) {
                                    $all_cats  = BPGH_Categories::get_categories();
                                    $cat_label = isset( $all_cats[ $cat ] ) ? $all_cats[ $cat ] : $cat;
                                    echo ' <span class="bpgh-cat-badge">' . esc_html( $cat_label ) . '</span>';
                                }
                            }
                            ?>

                            <?php
                            // Tags.
                            if ( class_exists( 'BPGH_Tags' ) ) {
                                $tags = BPGH_Tags::get_tags( $group->id );
                                if ( ! empty( $tags ) ) {
                                    foreach ( $tags as $tag ) {
                                        echo ' <span class="bpgh-tag">' . esc_html( $tag ) . '</span>';
                                    }
                                }
                            }
                            ?>

                            <?php
                            // Premium badge.
                            if ( class_exists( 'BPGH_Premium' ) && 'premium' === BPGH_Premium::get_tier( $group->id ) ) {
                                echo ' <span class="bpgh-badge bpgh-badge--premium">&#9733; ' . esc_html__( 'Premium', 'bp-group-hierarchy' ) . '</span>';
                            }
                            ?>
                        </div>

                        <?php if ( $child_count > 0 ) : ?>
                            <div class="bpgh-card-children-preview">
                                <a href="<?php echo esc_url( add_query_arg( 'bpgh_parent', $group->id, bpgh_get_directory_url() ) ); ?>">
                                    <?php esc_html_e( 'View Sub-groups →', 'bp-group-hierarchy' ); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ( $max_pages > 1 ) : ?>
            <div class="bpgh-pagination">
                <?php
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                echo paginate_links( array(
                    'base'      => add_query_arg( 'bpgh_page', '%#%' ),
                    'format'    => '',
                    'current'   => $paged,
                    'total'     => $max_pages,
                    'prev_text' => '&laquo; ' . esc_html__( 'Previous', 'bp-group-hierarchy' ),
                    'next_text' => esc_html__( 'Next', 'bp-group-hierarchy' ) . ' &raquo;',
                ) );
                ?>
            </div>
        <?php endif; ?>

    <?php endif; ?>
</div>
