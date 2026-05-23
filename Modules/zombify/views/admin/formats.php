<h2><?php esc_html_e( 'Post formats', 'zombify' ); ?></h2>

<form method="post" action="options.php">

    <?php wp_nonce_field('update-options'); ?>
    <?php settings_fields( 'zf-settings-group-formats' ); ?>

    <table class="form-table zf-table">
        <tbody>
        <tr>
            <th scope="row">
                <?php esc_html_e( 'Active formats', 'zombify' ); ?>
                <br>
                <span style="font-weight: normal">(<?php esc_html_e( 'drag to reorder', 'zombify' ); ?>)</span>
            </th>
            <td>
                <div id="sortable">
                    <?php 
					//we need active formats for all roles,so, per-role must be false
					$zombify_active_formats = zombify()->get_active_formats(false, 0, false);

                    $post_types = array();
                    $zombify_types_order = [];
                    $zombify_subtypes_order = [];

                    if( zf_get_option('zombify_types_order') ) {
                        $zombify_types_order = json_decode( zf_get_option('zombify_types_order') );
                    }

                    if( zf_get_option('zombify_subtypes_order') ) {
                        $zombify_subtypes_order = json_decode( zf_get_option('zombify_subtypes_order') );
                    }

                    foreach( zombify()->get_post_types(true) as $post_type_slug => $post_type_data ){
                        $post_type = $post_type_data;
                        $post_type["post_type_slug"] = $post_type_slug;
                        $post_type["post_type_level"] = 1;

                        if( zf_get_option('zombify_types_order') ) {
                            if( isset($zombify_types_order->$post_type_slug) ) {
                                $post_type["order"] = $zombify_types_order->$post_type_slug;
                            }
                        }

                        $post_types[$post_type_slug] = $post_type;
                    }

                    foreach( zombify()->get_post_subtypes(true) as $post_type_slug => $post_type_data ){
                        $post_type = $post_type_data;
                        $post_type["post_type_slug"] = $post_type_slug;
                        $post_type["post_type_level"] = 2;

                        if( zf_get_option('zombify_subtypes_order') ) {
                            if( $post_type_slug === 'main' ) {
                                $post_type_slug = 'story';
                            }

                            if( isset($zombify_subtypes_order->$post_type_slug) ) {
                                $post_type_slug = 'subtype_' . $post_type_slug;
                                $post_type["order"] = $zombify_subtypes_order->$post_type_slug;
                            }
                        }

                        $post_types[$post_type_slug] = $post_type;
                    }

                    uasort($post_types,function($a, $b) {
                        return $a['order'] - $b['order'];
                    });

                    foreach( $post_types as $format_value => $format_data ) { ?>
                        <?php if( $format_data['post_type_level'] == 1 ){ ?>
                            <p>
                                <label>
                                    <input type="hidden" name="zombify_active_formats[<?php echo $format_value; ?>]" value="">
                                    <input type="checkbox" class="type_checkbox" name="zombify_active_formats[<?php echo $format_value; ?>]" value="<?php echo $format_value; ?>" <?php echo ( is_array( $zombify_active_formats ) && in_array($format_value, $zombify_active_formats) ) ? 'checked' : '' ?>> <?php echo $format_data['name']; ?>
                                </label>
                            </p>
                        <?php } else { ?>
                            <p>
                                <label>
                                    <input type="hidden" name="zombify_active_formats[<?php echo 'subtype_'.$format_value; ?>]" value="">
                                    <input type="checkbox" class="subtype_checkbox" name="zombify_active_formats[<?php echo 'subtype_'.$format_value; ?>]" value="<?php echo 'subtype_'.$format_value; ?>"<?php echo ( is_array( $zombify_active_formats ) && in_array('subtype_'.$format_value, $zombify_active_formats) ) ? 'checked' : '' ?>> <?php echo $format_data['name']; ?>
                                </label>
                            </p>
                        <?php }
                    } ?>
                </div>
                <input type="hidden" name="zombify_types_order" class="zombify_types_order" value='<?php echo zf_get_option('zombify_types_order'); ?>'>
                <input type="hidden" name="zombify_subtypes_order" class="zombify_subtypes_order" value='<?php echo zf_get_option('zombify_subtypes_order'); ?>'>
            </td>
        </tr>

        <tr>
            <th scope="row"><?php esc_html_e( 'Categories & Tags', 'zombify' ); ?></th>
            <td>
                <table>
                    <tr>
                        <th><?php esc_html_e( 'Post type', 'zombify' ); ?></th>
                        <th><?php esc_html_e( 'Category to save', 'zombify' ); ?></th>
                        <th><?php esc_html_e( 'Tag to save', 'zombify' ); ?></th>
                    </tr>
                    <?php

                    $post_tags_array = zf_get_option('zombify_post_tags');
                    $post_cats_array = zf_get_option('zombify_post_categroies');

                    foreach( zombify()->get_post_types(true) as $format_val => $format_data ){
                        ?>
                        <tr>
                            <td><?php echo $format_data['name']; ?></td>
                            <td>
                                <?php
                                $args = array(
                                    'show_option_all'    => '',
                                    'show_option_none'   => 'None',
                                    'option_none_value'  => '-1',
                                    'orderby'            => 'ID',
                                    'order'              => 'ASC',
                                    'show_count'         => 0,
                                    'hide_empty'         => 0,
                                    'child_of'           => 0,
                                    'exclude'            => '',
                                    'include'            => '',
                                    'echo'               => 1,
                                    'selected'           => ( isset($post_cats_array[ $format_val ]) ? $post_cats_array[ $format_val ] : 0 ),
                                    'hierarchical'       => 0,
                                    'name'               => 'zombify_post_categroies['.$format_val.']',
                                    'id'                 => '',
                                    'class'              => 'postform',
                                    'depth'              => 0,
                                    'tab_index'          => 0,
                                    'taxonomy'           => 'category',
                                    'hide_if_empty'      => false,
                                    'value_field'	     => 'term_id',
                                );

                                wp_dropdown_categories( $args );
                                ?>
                            </td>
                            <td>
                                <input type="text" name="zombify_post_tags[<?php echo $format_val; ?>]" value="<?php echo isset( $post_tags_array[ $format_val ] ) ? $post_tags_array[ $format_val ] : '' ?>">
                            </td>
                        </tr>
                        <?php
                    }

                    foreach( zombify()->get_post_subtypes(true) as $format_val => $format_data ){
                        ?>
                        <tr>
                            <td><?php echo $format_data['name']; ?></td>
                            <td>
                                <?php
                                $args = array(
                                    'show_option_all'    => '',
                                    'show_option_none'   => 'None',
                                    'option_none_value'  => '-1',
                                    'orderby'            => 'ID',
                                    'order'              => 'ASC',
                                    'show_count'         => 0,
                                    'hide_empty'         => 0,
                                    'child_of'           => 0,
                                    'exclude'            => '',
                                    'include'            => '',
                                    'echo'               => 1,
                                    'selected'           => ( isset($post_cats_array[ 'subtype_'.$format_val ]) ? $post_cats_array[ 'subtype_'.$format_val ] : 0 ),
                                    'hierarchical'       => 0,
                                    'name'               => 'zombify_post_categroies[subtype_'.$format_val.']',
                                    'id'                 => '',
                                    'class'              => 'postform',
                                    'depth'              => 0,
                                    'tab_index'          => 0,
                                    'taxonomy'           => 'category',
                                    'hide_if_empty'      => false,
                                    'value_field'	     => 'term_id',
                                );

                                wp_dropdown_categories( $args );
                                ?>
                            </td>
                            <td>
                                <input type="text" name="zombify_post_tags[<?php echo 'subtype_'.$format_val; ?>]" value="<?php echo isset( $post_tags_array[ 'subtype_'.$format_val ] ) ? $post_tags_array[ 'subtype_'.$format_val ] : '' ?>">
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </table>
            </td>
        </tr>

        </tbody>
    </table>

    <input type="hidden" name="action" value="update" />
    <input type="hidden" name="page_options" value="zombify_active_formats, zombify_post_categroies, zombify_post_tags, zombify_post_savetype" />

    <p class="submit">
        <input type="submit" class="button-primary" value="<?php esc_html_e( 'Save Changes', 'zombify' ); ?>" />
    </p>

</form>