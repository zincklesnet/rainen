<h2><?php esc_html_e( 'General', 'zombify' ); ?></h2>

<form method="post" action="options.php">

    <?php wp_nonce_field('update-options'); ?>
    <?php settings_fields( 'zf-settings-group' ); ?>

    <table class="form-table">
        <tbody>

            <tr>
                <th scope="row"><?php esc_html_e( 'Frontend page', 'zombify' ); ?></th>
                <td>
                    <?php
                    $args = array(
                        'depth'                 => 0,
                        'child_of'              => 0,
                        'selected'              => zf_get_option('zombify_frontend_page'),
                        'echo'                  => 1,
                        'name'                  => 'zombify_frontend_page',
                        'id'                    => null, // string
                        'class'                 => 'regular-text',
                        'show_option_none'      => null, // string
                        'show_option_no_change' => null, // string
                        'option_none_value'     => null, // string
                    );

                    wp_dropdown_pages( $args );
                    ?>
                </td>
            </tr>

            <tr>
                <th scope="row"><?php esc_html_e( 'Post create/update page', 'zombify' ); ?></th>
                <td>
                    <?php
                    $args = array(
                        'depth'                 => 0,
                        'child_of'              => 0,
                        'selected'              => zf_get_option('zombify_post_create_page'),
                        'echo'                  => 1,
                        'name'                  => 'zombify_post_create_page',
                        'id'                    => null, // string
                        'class'                 => 'regular-text',
                        'show_option_none'      => null, // string
                        'show_option_no_change' => null, // string
                        'option_none_value'     => null, // string
                    );

                    wp_dropdown_pages( $args );
                    ?>
                </td>
            </tr>

            <tr>
                <th scope="row"><?php esc_html_e( 'Max. upload size', 'zombify' ); ?></th>
                <td>
                    <?php
                    $max_size = zombify()->get_file_upload_max_size();

                    $sizes = array(
                        "1048576" => "1MB",
                        "2097152" => "2MB",
                        "4194304" => "4MB",
                        "8388608" => "8MB",
                        "16777216" => "16MB",
                        "33554432" => "32MB",
                    );
//
//                    foreach( $sizes as $size_index=>$size )
//                        if( $size_index > $max_size ) unset( $sizes[ $size_index ] );

                    $seled_size = zf_get_option('zombify_max_upload_size');

                    if( $seled_size > $max_size ){

                        if( !isset( $sizes[ $max_size ] ) ) {

                            $sizes[$max_size] = round(($max_size / 1024 / 1024), 2) . 'MB';

                        }

                        $seled_size = $max_size;

                    }
                    ?>
                    <select name="zombify_max_upload_size" id="zombify_max_upload_size" class="regular-text">
                        <?php
                        foreach( $sizes as $sizebits => $size ){
                            ?>
                            <option value="<?php echo $sizebits; ?>" <?php echo ( $seled_size == $sizebits ) ? 'selected' : '' ?> <?php echo $sizebits > $max_size ? 'disabled' : '' ?>><?php echo $size; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row"><?php esc_html_e( 'Disable video upload', 'zombify' ); ?></th>
                <td>
                    <?php
                    $seled_opt = zf_get_option('zombify_disable_mp4_upload', 0);
                    ?>
                    <input type="checkbox" name="zombify_disable_mp4_upload" id="zombify_disable_mp4_upload" value="1" <?php if( $seled_opt ) echo 'checked'; ?>>
                </td>
            </tr>

            <tr>
                <th scope="row"><?php esc_html_e( 'Video max. upload file size', 'zombify' ); ?></th>
                <td>
                    <?php
                    $sizes = array(
                        "2097152" => "2MB",
                        "4194304" => "4MB",
                        "8388608" => "8MB",
                        "16777216" => "16MB",
                        "33554432" => "32MB",
                        "67108864" => "64MB",
                        "134217728" => "128MB",
                    );
                    $sizes = apply_filters( 'zf_max_upload_mp4_size', $sizes );

                    $seled_size = zf_get_option('zombify_max_upload_mp4_size', 33554432);
                    ?>
                    <select name="zombify_max_upload_mp4_size" id="zombify_max_upload_mp4_size" class="regular-text">
                        <?php
                        foreach( $sizes as $sizebits => $size ){
                            ?>
                            <option value="<?php echo $sizebits; ?>" <?php echo ( $seled_size == $sizebits ) ? 'selected' : '' ?>><?php echo $size; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row"><?php esc_html_e( 'Disable audio upload', 'zombify' ); ?></th>
                <td>
                    <?php
                    $seled_opt = zf_get_option('zombify_disable_mp3_upload', 0);
                    ?>
                    <input type="checkbox" name="zombify_disable_mp3_upload" id="zombify_disable_mp3_upload" value="1" <?php if( $seled_opt ) echo 'checked'; ?>>
                </td>
            </tr>

            <tr>
                <th scope="row"><?php esc_html_e( 'Audio max. upload file size', 'zombify' ); ?></th>
                <td>
                    <?php
                    $sizes = array(
                        "2097152" => "2MB",
                        "4194304" => "4MB",
                        "8388608" => "8MB",
                        "16777216" => "16MB",
                        "33554432" => "32MB",
                    );

                    $seled_size = zf_get_option('zombify_max_upload_mp3_size', 8388608);
                    ?>
                    <select name="zombify_max_upload_mp3_size" id="zombify_max_upload_mp3_size" class="regular-text">
                        <?php
                        foreach( $sizes as $sizebits => $size ){
                            ?>
                            <option value="<?php echo $sizebits; ?>" <?php echo ( $seled_size == $sizebits ) ? 'selected' : '' ?>><?php echo $size; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row"><?php esc_html_e( 'Max tags count', 'zombify' ); ?></th>
                <td>
                    <input type="text" name="zf_tags_limit" value="<?php echo zf_get_option("zf_tags_limit", 3); ?>" id="zf_tags_limit" class="regular-text">
                </td>
            </tr>

			<tr>
                <th scope="row"><?php esc_html_e( 'Max categories count', 'zombify' ); ?></th>
                <td>
                    <input type="text" name="zf_categories_limit" value="<?php echo zf_get_option("zf_categories_limit", 3); ?>" id="zf_categories_limit" class="regular-text">
                </td>
            </tr>

			<tr>
                <th scope="row"><?php esc_html_e( 'Allowed categories', 'zombify' ); ?></th>
                <td>
                    <?php
                    $seled_cats = zf_get_option('zombify_allowed_cats', array(-1));

                    echo zf_get_categories_dropdown(array("name" => "zombify_allowed_cats[]", "multiple" => "multiple", "size" => 10, "class" => "regular-text zf_categories_dropdown"), $seled_cats, array(-1 => 'All'));

                    ?>
                </td>
            </tr>

			<tr>
                <th scope="row"><?php esc_html_e( 'Daily contributor can submit', 'zombify' ); ?></th>
                <td>
                    <?php
                    $post_counts = array(
                        "1" => __( "only 1 post", 'zombify' ),
                    );

                    for( $i=2; $i<=10; $i++ )
                        $post_counts[$i] = $i.' '.__( 'posts', 'zombify' );

                    $post_counts[0] = __( "unlimited posts", 'zombify' );

                    $seled_post_count = zf_get_option('zombify_contributor_can_submit', 0);
                    ?>
                    <select name="zombify_contributor_can_submit" id="zombify_contributor_can_submit" class="regular-text">
                        <?php
                        foreach( $post_counts as $count => $countlabel ){
                            ?>
                            <option value="<?php echo $count; ?>" <?php echo ( $seled_post_count == $count ) ? 'selected' : '' ?>><?php echo $countlabel; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </td>
            </tr>

			<tr>
                <th scope="row"><?php esc_html_e( 'Disable meme templates', 'zombify' ); ?></th>
                <td>
                    <?php
                    $seled_meme_temp = zf_get_option('zombify_disable_meme_templates', 0);
                    ?>
                    <input type="checkbox" name="zombify_disable_meme_templates" id="zombify_disable_meme_templates" value="1" <?php if( $seled_meme_temp ) echo 'checked'; ?>>
                </td>
            </tr>

			<tr>
				<th scope="row"><?php esc_html_e( 'Disable congratulations popup', 'zombify' ); ?></th>
				<td>
		            <?php
		            $selected_congratulations_popup = zf_get_option('zf_disable_congratulations_popup', 0);
		            ?>
					<input type="checkbox" name="zf_disable_congratulations_popup" id="zf_disable_congratulations_popup" value="1" <?php if( $selected_congratulations_popup ) echo 'checked'; ?>>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php esc_html_e( 'Automatically set media width equal to post width', 'zombify' ); ?></th>
				<?php
				$seled_media_width_equal_post_width = zf_get_option('zombify_media_width_equal_post_width', 'all');
				?>
				<td>
					<select name="zombify_media_width_equal_post_width" id="zombify_media_width_equal_post_width" class="regular-text">
						<option value="all" <?php selected( $seled_media_width_equal_post_width, 'all' ); ?>><?php esc_html_e( 'All', 'zombify' ); ?></option>
						<option value="images" <?php selected( $seled_media_width_equal_post_width, 'images' ); ?>><?php esc_html_e( 'Images Only', 'zombify' ); ?></option>
						<option value="gifs" <?php selected( $seled_media_width_equal_post_width, 'gifs' ); ?>><?php esc_html_e( 'GIFs Only', 'zombify' ); ?></option>
						<option value="none" <?php selected( $seled_media_width_equal_post_width, 'none' ); ?>><?php esc_html_e( 'None', 'zombify' ); ?></option>
					</select>
				</td>
			</tr>
        </tbody>
    </table>

    <input type="hidden" name="action" value="update" />
    <input type="hidden" name="page_options" value="zombify_frontend_page,zombify_post_create_page,zombify_max_upload_size,zombify_max_upload_mp4_size,zombify_max_upload_mp3_size,zombify_disable_mp3_upload,zombify_disable_mp4_upload,zf_tags_limit,zf_categories_limit,zombify_allowed_cats,zombify_contributor_can_submit,zombify_disable_meme_templates,zombify_media_width_equal_post_width" />

    <p class="submit">
        <input type="submit" class="button-primary" value="<?php esc_html_e( 'Save Changes', 'zombify' ); ?>" />
    </p>

</form>