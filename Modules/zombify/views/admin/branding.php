<h2><?php esc_html_e( 'Branding', 'zombify' ); ?></h2>

<form method="post" action="options.php">

    <?php wp_nonce_field('update-options'); ?>
    <?php settings_fields( 'zf-settings-group-branding' ); ?>

    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row"><?php esc_html_e( 'Primary color', 'zombify' ); ?></th>
                <td>
                    <input type="text" name="zombify_branding_color" value="<?php echo zf_get_option('zombify_branding_color', zombify()->options_defaults["zombify_branding_color"]); ?>" class="zf-color-field">
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e( 'Color mode', 'zombify' ); ?></th>
                <td>
                    <?php
                    $colorModes = array(
                        "light" => "Light",
                        "dark" => "Dark",
                    );

                    $seled_color_mode = zf_get_option('zombify_color_mode');
                    ?>
                    <select name="zombify_color_mode" id="zombify_color_mode" class="regular-text">
                        <?php
                        foreach( $colorModes as $clr => $colorMode ){
                            ?>
                            <option value="<?php echo $clr; ?>" <?php echo ( $seled_color_mode == $clr ) ? 'selected' : '' ?>><?php echo $colorMode; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Logo', 'zombify'); ?></th>
                <td>
                    <?php
                    $logo_option = zf_get_option('zombify_logo', zombify()->options_defaults["zombify_logo"]);
                    ?>
                    <input class="regular-text zombify_logo_url" type="text" name="zombify_logo" value="<?php echo zf_get_option('zombify_logo', zombify()->options_defaults["zombify_logo"]); ?>">
                    <a href="#" class="zombify_logo_upload"><?php esc_html_e( 'Upload', 'zombify' ); ?></a>
                    <div><img class="zombify_logo" src="<?php echo $logo_option; ?>" height="50" style="margin-top:5px; <?php if( $logo_option == '' ) echo 'display:none;'; ?>"/></div>
                </td>
            </tr>
        </tbody>
    </table>


    <input type="hidden" name="action" value="update" />
    <input type="hidden" name="page_options" value="zombify_logo, zombify_branding_color, zombify_color_mode" />

    <p class="submit">
        <input type="submit" class="button-primary" value="<?php esc_html_e('Save Changes', 'zombify'); ?>" />
    </p>

</form>