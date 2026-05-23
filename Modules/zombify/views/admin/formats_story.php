<h2><?php esc_html_e( 'Story format', 'zombify' ); ?></h2>

<form method="post" action="options.php">

    <?php wp_nonce_field('update-options'); ?>
    <?php settings_fields( 'zf-settings-group-formats-story' ); ?>

    <table class="form-table zf-table">
        <tbody>
        <tr>
            <th scope="row">
                <?php esc_html_e( 'Active Components', 'zombify' ); ?>
                <br>
                <span style="font-weight: normal">(<?php esc_html_e( 'drag to reorder', 'zombify' ); ?>)</span>
            </th>
            <td>
                <div id="sortable">
                    <?php
                    $story_obj = new Zombify_StoryQuiz();

                    $alias_groups = $story_obj->getAliasGroups(false);

                    $default_formats = array();

                    if( isset($alias_groups) ) {
                        foreach( $alias_groups as $format_value => $format_data )
                            $default_formats[ $format_value ] = $format_value;
                    }

                    $zombify_story_formats = zf_get_option("zombify_story_formats", $default_formats);

                    if( isset($alias_groups) ) {
                        foreach( $alias_groups as $format_value => $format_data ){
                            ?>
                            <p>
                                <label>
                                    <input type="hidden" name="zombify_story_formats[<?php echo $format_value; ?>]" value="">
                                    <input type="checkbox" class="story_format_checkbox" name="zombify_story_formats[<?php echo $format_value; ?>]" value="<?php echo $format_value; ?>" <?php echo ( is_array( $zombify_story_formats ) && in_array($format_value, $zombify_story_formats) ) ? 'checked' : '' ?>> <?php echo $format_data['label']; ?>
                                </label>
                            </p>
                        <?php
                        }
                    } ?>
                </div>
                <input type="hidden" name="zombify_story_format_order" class="zombify_story_format_order" value='<?php echo zf_get_option('zombify_story_format_order'); ?>'>
            </td>
        </tr>


        </tbody>
    </table>

    <input type="hidden" name="action" value="update" />
    <input type="hidden" name="page_options" value="zombify_story_formats" />

    <p class="submit">
        <input type="submit" class="button-primary" value="<?php esc_html_e( 'Save Changes', 'zombify' ); ?>" />
    </p>

</form>