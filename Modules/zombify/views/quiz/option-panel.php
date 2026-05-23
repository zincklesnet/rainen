<div id="zf-options-section" class="zf-options-section">
	<div class="zf-head">
		<?php esc_html_e( "Additional Information", "zombify" ); ?>
		<i class="zf-icon zf-icon-settings"></i>
	</div>
	<div class="zf-body">
		<div class="zf-featured-image">
			<?php
			echo $this->renderField( [ 'image' ], '', 0, $this->data, array(), 'fields/feature_image' );
			?>
		</div>
		<div class="zf-option-item <?php if ( ! zf_validate_option( $this, 'category' ) ) {
			echo 'zf-error';
		} ?>">
			<?php
			$post_selected_categories = ( isset( $_GET['post_id'] ) && (int) $_GET['post_id'] > 0 ) ? wp_get_post_categories( (int) $_GET['post_id'] ) : array();

			$post_default_categories = zf_get_option( "zombify_post_categroies" );

			$include_categories = zf_get_option( "zombify_allowed_cats", array( - 1 ) );

			if ( in_array( '-1', $include_categories ) ) {
				$include_categories = '';
			} else {

				if ( is_array( $post_default_categories ) && count( $post_default_categories ) > 0 ) {

					foreach ( $include_categories as $incl_ind => $incl_val ) {
						if ( in_array( $incl_val, $post_default_categories ) ) {
							unset( $include_categories[ $incl_ind ] );
						}
					}

				}

			}

			if ( isset( $_POST["zombify_options"]["category"] ) && is_array( $_POST["zombify_options"]["category"] ) ) {
				$post_selected_categories = $_POST["zombify_options"]["category"];
			}
			?>
			<div class="zf-multiple-select">
				<div class="zf-select_header" data-label="<?php esc_attr_e( "Choose a category", "zombify" ); ?>">
					<span class="zf-selected"></span>
				</div>
				<ul class="zf-structure-list zf-select_dropdown">
					<?php
					$default_cat_id = 0;

					if ( isset( $post_default_categories[ $this->view_path ] ) && - 1 != $post_default_categories[ $this->view_path ] ) {
						$default_cat_id = $post_default_categories[ $this->view_path ];
					}

					$categories = get_categories( array(
						'orderby'    => 'name',
						'order'      => 'ASC',
						'hide_empty' => 0,
						'exclude'    => $default_cat_id,//Exclude default category in current post type
						'include'    => $include_categories
					) );

					echo walk_category_tree( $categories, 0, array(
						'walker'              => zf_get_category_walker(),
						'selected_categories' => $post_selected_categories
					) );
					?>
				</ul>
			</div>
			<?php if ( ! zf_validate_option( $this, 'category' ) ) {
				echo '<span class="zf-help">' . __( "The field is required", "zombify" ) . '</span>';
			} ?>
		</div>
		<div class="zf-option-item <?php if ( ! zf_validate_option( $this, 'tags' ) ) {
			echo 'zf-error';
		} ?>">
			<?php
			global $zf_tags_limit;

			$tags = array();

			if ( isset( $_POST["zombify_options"]["tags"] ) && $_POST["zombify_options"]["tags"] != '' ) {

				$tags_arr = explode( ",", $_POST["zombify_options"]["tags"] );

				if ( isset( $tags_arr ) ) {
					foreach ( $tags_arr as $tag ) {
						$tags[] = trim( $tag );
					}
				}

			} else {

				if ( isset( $_GET['post_id'] ) && (int) $_GET['post_id'] > 0 ) {

					$tag_terms = wp_get_post_tags( (int) $_GET['post_id'] );

					if ( isset( $tag_terms ) ) {
						foreach ( $tag_terms as $tterms ) {
							$tags[] = $tterms->name;
						}
					}

				}

			}

			$predef_post_tags = zf_get_option( "zombify_post_tags" );

			$tags = array_diff( $tags, $predef_post_tags );
			?>
			<textarea id="tag-editor" name="zombify_options[tags]" data-count-limit="<?php echo $zf_tags_limit; ?>" placeholder="<?php echo esc_attr_e( "Enter tags ...", "zombify" ); ?>"><?php echo implode( ", ", $tags ); ?></textarea>
			<span class="zf-tags-limit"><?php printf( esc_html__( " max. %s tags allowed", "zombify" ), $zf_tags_limit ); ?></span>
			<?php if ( ! zf_validate_option( $this, 'tags' ) ) {
				echo '<span class="zf-help">' . __( "The field is required", "zombify" ) . '</span>';
			} ?>
		</div>
		<?php
		if ( count( $this->pagination_path ) > 0 ) {
			?>
			<div class="zf-option-item <?php if ( ! zf_validate_option( $this, 'items_per_page' ) ) {
				echo 'zf-error';
			} ?>">
				<?php
				if ( isset( $_POST["zombify_options"]["items_per_page"] ) ) {
					$items_per_page = (int) $_POST["zombify_options"]["items_per_page"] > 0 ? (int) $_POST["zombify_options"]["items_per_page"] : '';
				} else {
					if ( isset( $_GET['post_id'] ) ) {
						$items_per_page = get_post_meta( (int) $_GET['post_id'], "zombify_items_per_page", true );
						if ( ! $items_per_page ) {
							$items_per_page = '';
						}
					} else {
						$items_per_page = '';
					}
				}
				?>
				<input type="number" value="<?php echo $items_per_page; ?>" name="zombify_options[items_per_page]"
					   placeholder="<?php echo esc_attr_e( "Items per page", "zombify" ); ?>">
				<?php if ( ! zf_validate_option( $this, 'items_per_page' ) ) {
					echo '<span class="zf-help">' . __( "The field is required", "zombify" ) . '</span>';
				} ?>
			</div>
			<?php
		}

		/* Date forms for scheduling the quiz publish date */
		if ( zf_user_can_publish( isset( $_GET['post_id'] ) ? (int) $_GET['post_id'] : 0 ) ) {
			$qz_id = isset( $_GET['post_id'] ) ? (int) $_GET['post_id'] : 0;
			echo $this->qiuzTouchTime( $qz_id, $this->data_options );
		}
		?>

		<?php
		$custom_option_panel = __DIR__ . DIRECTORY_SEPARATOR . $this->slug . DIRECTORY_SEPARATOR . 'option-panel.php';

		if ( is_file( $custom_option_panel ) ) {
			include( $custom_option_panel );
		}

		/* Add the ability to append other fields */
		$additional_fields_initial = array();

		$quiz_obj = $this;

		/**
		 * Filters the list of additional fields create post
		 *
		 * @param $additional_fields_initial  array Array of fields with following items
		 *                                    'field_type'            string  Field type, supported: text, hidden, checkbox, radio, also textarea and select
		 *                                    'field_name'            string  Field name
		 *                                    'field_label'           string  Field label
		 *                                    'is_required'           bool    Whether required the field or not
		 *                                    'field_value'           string  Field value
		 *                                    'field_default_value'   string  Field default value
		 *                                    'field_choices'         array   select->option/radio->input name and label as key=>value of assoc array
		 *                                    'field_placeholder'     string  Field placeholder
		 *                                    'field_class'           array   Field classes given as an numeric array
		 *                                    'field_id'              string  Field id
		 *                                    'extra_attr'            array   Extra attributes given as an associative array where
		 *                                    key is the attribute name, value is the value
		 * @param $quiz_obj                   Zombify_BaseQuiz Current quiz class object
		 * @param $post_id                    int Post id if editing post, 0 on creating a new one
		 */
		$post_id           = isset( $_GET['post_id'] ) ? (int) $_GET['post_id'] : 0;
		$additional_fields = apply_filters( 'zf_create_post_additional_fields', $additional_fields_initial, $quiz_obj, $post_id );

		$this->initiateQuizAdditionalFields();
		$this->quiz_additional_fields->drawFields( $additional_fields );

		?>

	</div>
</div>
