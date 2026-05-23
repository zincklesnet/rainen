<?php
/**
 * Zombify Base Quiz
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

if( !class_exists("Zombify_BaseQuiz") ) {

    /**
     * Class Zombify_BaseQuiz
     */
    abstract class Zombify_BaseQuiz
    {

        /**
         * Loaded data
         *
         * @var Array
         */
        public $data = array();

        /**
         * @var ZF_QuizAdditionalFields
         */
        public $quiz_additional_fields;

        /**
         * Zombify Quiz Structure
         *
         * @return mixed
         */
        abstract function structure();

        public $pagination_path = array();

        public $virtual = 0;

        public $errors = array();

        public $incorrectFiles = array();

		public $data_options = false;

        public $alias_groups = array();

        public $subtype = 'main';

        private $structure_temp = false;

        public $removeInvalidData = true;

        /**
         * Render builder html
         *
         * @return string
         */
        public function renderBuilder( $action = 'create' ){

            $structure = $this->getStructure();

            $template_file = zombify()->locate_template( 'quiz/'.$this->view_path.'.php' );

            ob_start();

            echo '<div class="zombify_quiz">';

            include $template_file;

            echo '</div>';

            $output = ob_get_contents();
            ob_end_clean();

            return $output;

        }

        /**
         * Render field html
         *
         * @param $field_name
         * @param array $options
         * @param string $template
         * @return string
         */
        public function renderField( $field_name, $name_prefix='', $name_index = 0, $data = array(), $options = array(), $template = '', $args = array(), $path_prefix = array() ){

            $field = $this->findField( $field_name );

            $options["data-zombify-field-type"] = $field["field_type"];

            $attributes = $this->optionsToAttributes( $options );

	        $field_value = '';
	        if( isset($data[ end($field_name) ]) ) {
		        $field_value = $data[ end($field_name) ];
		        if( is_string( $field_value ) ) {
			        $field_value = str_replace( '&amp;', '&', $field_value );
		        }
	        };

            if( $this->data && $this->virtual == 0 ) {
	            $error = $this->validateField( $field, $field_value );
            }

            $field_template_file = $template == '' ? zombify()->locate_template( 'quiz/fields/'.$field["field_type"].'.php') : zombify()->locate_template( 'quiz/'.$template.'.php' );

            ob_start();
            include $field_template_file;
            $output = ob_get_contents();
            ob_end_clean();

            return $output;

        }

        public function renderAddGroupButton( $group_name, $label, $template = '', $args = array() ){

            $field_template_file = $template == '' ? zombify()->locate_template( 'quiz/fields/add_button.php' ) : zombify()->locate_template( 'quiz/'.$template.'.php' );

            ob_start();
            include $field_template_file;
            $output = ob_get_contents();
            ob_end_clean();

            return $output;

        }

        /**
         * Render all groups html
         *
         * @param $group_name
         * @return string
         */
        public function renderGroups( $group_name, $name_prefix = '', $data = array(), $action = 'create', $path_prefix = array(), $aliased_group_path = '', $group_before_html = '', $group_after_html = '', $renderEmpty = true, $default_name_index = 0 ){
            $output = '';

            if( $this->data ){

                if( isset( $data[ end( $group_name ) ] ) && count( $data[ end( $group_name ) ] ) > 0 ){

                    $group_num = 0;

                    foreach( $data[ end( $group_name ) ] as $name_index => $data_item ){

                        $output .= $this->renderGroup( $group_name, $name_prefix, $name_index, $data_item, ( $group_num > 0 ? true : false ), $action, $group_num, $path_prefix, $aliased_group_path, $group_before_html, $group_after_html, count($data[ end( $group_name ) ]), $data );

                        $group_num++;

                    }

                }

            } else {

                if( $renderEmpty ) {

                    $name_index = $default_name_index;

                    $output .= $this->renderGroup($group_name, $name_prefix, $name_index, array(), false, $action, 0, $path_prefix, $aliased_group_path, $group_before_html, $group_after_html, 1, $data);

                }

            }

            return $output;

        }

        /**
         * Render group html
         *
         * @param $group_name
         * @return string
         */
        public function renderGroup( $group_name, $name_prefix = '', $name_index = 0, $data = array(), $clone = false, $action = 'create', $group_num = 0, $path_prefix = array(), $aliased_group_path = '', $group_before_html = '', $group_after_html = '', $groups_count = 1, $groupData = array() ){

            $group = $this->findGroup( $group_name );
            $fullgroup = $this->findGroupFull( $group_name );

            $alias_class_path = '';
            $alias_group_path = '';

            if( isset($fullgroup["alias_class"]) ){
                $alias_class_path = strtolower( $fullgroup["alias_class"] );
            }

            if( isset($fullgroup["alias_group"]) ){
                $alias_group_arr = explode("/", $fullgroup["alias_group"]);
                $alias_group_path = strtolower( end($alias_group_arr) );
            }

            $field_name_prefix = $name_prefix.'['.end($group_name).']';
            $group_name_prefix = $name_prefix.'['.end($group_name).']['.$name_index.']';

            $group_template_file = zombify()->locate_template( 'quiz/'.( $alias_class_path ? $alias_class_path : $this->slug).'/'.( $alias_group_path ? $alias_group_path : end($group_name) ).'_group.php' );

            ob_start();

            echo '<div class="zombify_group '.( $clone ? "zombify_clone" : "" ).'" data-zombify-group-name="'.end($group_name).'">';
                echo $group_before_html;

                include $group_template_file;

                echo $group_after_html;
            echo '</div>';

            $output = ob_get_contents();
            ob_end_clean();

            return $output;

        }

        public function fieldPath( $path, $alias = '', $path_prefix = array() ){

            $newpath = $path_prefix;

            if( $alias != '' )
                $path[0] = $alias;

            if( count($path_prefix) > 0 ){

                foreach( $path as $p ){
                    $newpath[] = $p;
                }

            } else {

                $newpath = $path;

            }

            return $newpath;

        }

        /**
         * Print out HTML form date elements for editing post or comment publish date.
         *
         * @global WP_Locale  $wp_locale
         *
         * @param int $post_id The id of the post
         * @param bool|array $data_options The $_POST values of the option fields in case the form is submitted
         *
         * @return string
         */
        public function qiuzTouchTime( $post_id = 0, $data_options = false ) {
            global $wp_locale;

            $edit = false;
            if( $post_id && $post = get_post( $post_id ) ) {
				//check edit mode as it done in WordPress
				$edit = ! ( in_array($post->post_status, array('draft', 'pending') ) && (!$post->post_date_gmt || '0000-00-00 00:00:00' == $post->post_date_gmt ) );
			}

            $html = '';
            $time_adj = current_time('timestamp');
            $post_date = ( $edit ) ? $post->post_date : $time_adj;

            if( ! $data_options ) {
                $jj = ( $edit ) ? mysql2date( 'd', $post_date, false ) : gmdate( 'd', $time_adj );
                $mm = ( $edit ) ? mysql2date( 'm', $post_date, false ) : gmdate( 'm', $time_adj );
                $aa = ( $edit ) ? mysql2date( 'Y', $post_date, false ) : gmdate( 'Y', $time_adj );
                $hh = ( $edit ) ? mysql2date( 'H', $post_date, false ) : gmdate( 'H', $time_adj );
                $mn = ( $edit ) ? mysql2date( 'i', $post_date, false ) : gmdate( 'i', $time_adj );
                $ss = ( $edit ) ? mysql2date( 's', $post_date, false ) : gmdate( 's', $time_adj );
            } else {
                $jj = $data_options['jj'];
                $mm = $data_options['mm'];
                $aa = $data_options['aa'];
                $hh = $data_options['hh'];
                $mn = $data_options['mn'];
                $ss = $data_options['ss'];
            }

            $cur_jj = gmdate( 'd', $time_adj );
            $cur_mm = gmdate( 'm', $time_adj );
            $cur_aa = gmdate( 'Y', $time_adj );
            $cur_hh = gmdate( 'H', $time_adj );
            $cur_mn = gmdate( 'i', $time_adj );

            $datef = __( 'M j, Y @ H:i' );

            if( ! empty( $data_options['stamp_text'] ) ) {
                $stamp_text =  wp_kses( $data_options['stamp_text'], array( 'i' => array() ) );
            } elseif ( $edit ) {
                if ( 'future' == $post->post_status ) {
                    /* translators: Post date information. 1: Date on which the post is currently scheduled to be published */
                    $stamp = __( "Schedule for:", "zombify" ) . ' <i>%1$s</i>';
                } elseif ( 'publish' == $post->post_status ) { // already published
                    /* translators: Post date information. 1: Date on which the post was published */
                    $stamp = __( "Published on:", "zombify" ) . ' <i>%1$s</i>';
                } elseif ( '0000-00-00 00:00:00' == $post->post_date_gmt ) { // draft, 1 or more saves, no date specified
	                $stamp = __( 'Publish', 'zombify' ) . ' <i>'.__( "immediately", "zombify" ).'</i>';
                } elseif ( time() < strtotime( $post->post_date_gmt . ' +0000' ) ) { // draft, 1 or more saves, future date specified
                    /* translators: Post date information. 1: Date on which the post is to be published */
                    $stamp = __( "Schedule for:", "zombify" ) . ' <i>%1$s</i>';
                } else { // draft, 1 or more saves, date specified
	                $stamp = __( "Publish on:", "zombify" ) . ' <i>%1$s</i>';
                }
                $date = date_i18n( $datef, strtotime( $post->post_date ) );
                $stamp_text = sprintf( $stamp, $date );
            } else {
                $stamp = __( 'Publish', 'zombify' ) . ' <i>'.__( "immediately", "zombify" ).'</i>';
                $date = date_i18n( $datef, strtotime( current_time('mysql') ) );
                $stamp_text = sprintf( $stamp, $date );
            }

            /* Whether display date fields expanded or not */
            $fieldset_style     = '';
            $edit_btn_style     = '';
            $cancel_btn_style   = '';
            if( ! empty( $data_options['display_date_fields'] ) && 'block' === $data_options['display_date_fields'] ) {
                $fieldset_style     = 'style="display: block"';
                $edit_btn_style     = 'style="display: none"';
                $cancel_btn_style   = 'style="display: inline"';
            }

            $month = '<label><span class="screen-reader-text">' . __( 'Month' ) . '</span><select id="mm" name="zombify_options[mm]"' . ">\n";
            for ( $i = 1; $i < 13; $i = $i +1 ) {
                $monthnum = zeroise( $i, 2 );
                $monthtext = $wp_locale->get_month_abbrev( $wp_locale->get_month( $i ) );
                $month .= "\t\t\t" . '<option value="' . $monthnum . '" data-text="' . $monthtext . '" ' . selected( $monthnum, $mm, false ) . '>';
                /* translators: 1: month number (01, 02, etc.), 2: month abbreviation */
                $month .= sprintf( __( '%1$s-%2$s' ), $monthnum, $monthtext ) . "</option>\n";
            }
            $month .= '</select></label>';

            $day    = '<label><span class="screen-reader-text">'
                . __( 'Day' )
                . '</span><input type="text" id="jj" name="zombify_options[jj]" value="'
                . $jj
                . '" size="2" maxlength="2" autocomplete="off" /></label>';

            $year = '<label><span class="screen-reader-text">'
                . __( 'Year' )
                . '</span><input type="text" id="aa" name="zombify_options[aa]" value="'
                . $aa
                . '" size="4" maxlength="4" autocomplete="off" /></label>';

            $hour = '<label><span class="screen-reader-text">'
                . __( 'Hour' )
                . '</span><input type="text" id="hh" name="zombify_options[hh]" value="'
                . $hh
                . '" size="2" maxlength="2" autocomplete="off" /></label>';

            $minute = '<label><span class="screen-reader-text">'
                . __( 'Minute' )
                . '</span><input type="text" id="mn" name="zombify_options[mn]" value="'
                . $mn
                . '" size="2" maxlength="2" autocomplete="off" /></label>';

            $html .= '<div class="zf-option-item zf-post-schedule">'
                . '<span id="timestamp">' . $stamp_text . '</span>'
                . '<a id="zf-edit-timestamp" href="#" class="zf-edit-timestamp" role="button" ' . $edit_btn_style . '>' . __( 'Edit', 'zombify' ) .'</a>'
                . '<a id="zf-cancel-timestamp" href="#" class="zf-cancel-timestamp" ' . $cancel_btn_style . '>' . __( 'Cancel', 'zombify' ) . '</a>'
                . '<fieldset id="zf-timestampdiv" class="" ' . $fieldset_style . '>'
                . '<div class="zf-timestamp-wrap zf-float-left">';
            /* translators: 1: month, 2: day, 3: year, 4: hour, 5: minute */
            $html .= sprintf( __( '%1$s %2$s, %3$s @ %4$s:%5$s' ), $month, $day, $year, $hour, $minute );

            $html .= '</div><input type="hidden" id="ss" name="zombify_options[ss]" value="' . $ss . '" />';

            $map = array(
                'mm' => array( $mm, $cur_mm ),
                'jj' => array( $jj, $cur_jj ),
                'aa' => array( $aa, $cur_aa ),
                'hh' => array( $hh, $cur_hh ),
                'mn' => array( $mn, $cur_mn ),
            );

            foreach ( $map as $timeunit => $value ) {
                list( $unit, $curr ) = $value;

                $html .= '<input type="hidden" id="hidden_' . $timeunit . '" name="zombify_options[hidden_' . $timeunit . ']" value="' . $unit . '" />';

                $cur_timeunit = 'cur_' . $timeunit;
                $html .= '<input type="hidden" id="' . $cur_timeunit . '" name="zombify_options[' . $cur_timeunit . ']" value="' . $curr . '" />';
            }

            $html .= '<input type="hidden" id="stamp_text" name="zombify_options[stamp_text]" value="' . $stamp_text . '" />';
            $html .= '<input type="hidden" id="display_date_fields" name="zombify_options[display_date_fields]" value="'
                . ( ( ! empty( $data_options['display_date_fields'] ) && 'block' === $data_options['display_date_fields'] )
                    ? 'block' : 'none' ) . '" />';
			$html .= '<input type="hidden" id="zombify_original_post_status" name="zf_original_post_status" value="' . (!empty($post->post_status) ? $post->post_status : '') . '" />';
            $html .= '<div class="zf-timestamp-actions zf-float-right">'
                . '<a id="zf-save-timestamp" href="#" class="zf-button zf-save-timestamp">' . __( 'Ok', 'zombify' ) . '</a>'
                . '</div></fieldset></div>';

            return $html;
        }

        /**
         * Convert array to html attributes string
         *
         * @param $options
         * @return string
         */
        private function optionsToAttributes( $options ){

            $attrs = [];

            foreach( $options as $opt_name => $opt_val ){

                $attrs[] = $opt_name.'="'.$opt_val.'"';

            }

            return implode(" ", $attrs);

        }

        /**
         * Find field in quiz structure
         *
         * @param $fieldName
         * @return mixed
         */
        private function findField( $fieldName ){

            $structure = $this->getStructure();

            foreach( $fieldName as $fname ){

                if( !isset($structure[ $fname ]["type"]) ){
                    return false;
                }

                $structure = $structure[ $fname ]["type"] == 'field' ? $structure[ $fname ] : $structure[ $fname ]["fields"];

            }

            return $structure;

        }

        /**
         * Find group in quiz structure
         *
         * @param $groupName
         * @return mixed
         */
        private function findGroup( $groupName ){

            $structure = $this->getStructure();

            foreach( $groupName as $gname ){

                $structure = $structure[ $gname ]["type"] == 'field' ? $structure[ $gname ] : $structure[ $gname ]["fields"];

            }

            return $structure;

        }

        private function findGroupFull( $groupName ){

            $structure = $this->getStructure();

            foreach( $groupName as $gname ){

                if( $structure[ $gname ]["type"] == 'field' ){

                    $structure = $structure[ $gname ];

                } else {

                    $grp = $structure[ $gname ];

                    $structure = $structure[ $gname ]["fields"];

                }

            }

            return $grp;

        }

        /**
         * Generate hidden input for quiz type
         *
         * @return string
         */
        public function QuizTypeHiddenInput(){

            return '<input type="hidden" name="zombify_quiz_type" value="'.$this->slug.'" class="zombify_quiz_type">
                    <input type="hidden" name="zombify_quiz_subtype" value="'.$this->subtype.'" class="zombify_quiz_subtype">';

        }

        /**
         * Generate hidden input for quiz type
         *
         * @return string
         */
        public function PostIDHiddenInput(){

            if( isset($_POST["zombify_post_id"]) && (int)$_POST['zombify_post_id'] > 0 ){

                $post_id = (int)$_POST['zombify_post_id'];

            } else {

                if( isset( $_GET['post_id'] ) && (int)$_GET['post_id'] > 0 ){

                    $post_id = (int)$_GET['post_id'];

                } else {

                    $post_id = '';

                }

            }

            return '<input type="hidden" name="zombify_post_id" value="'.$post_id.'" class="zombify_post_id">';

        }

        /**
         * Load data in object
         *
         * @param $data
         */
        public function load( $data, $files_data = array(), $existing_files = array() ){

            $this->errors = array();

            $this->array_rec($data);

            array_walk_recursive( $data, function( &$item, $key ){ $item = zf_purify_kses( $item ); } );

            $this->data = $data;

            if( isset( $files_data["tmp_name"] ) )
                $this->getLoadedFiles( $files_data, $files_data["tmp_name"] );

            $this->loadExistingFiles( $existing_files );

        }

        private function loadExistingFiles( $existing_files ){

            $this->loadExistingFilesToData( $this->data, $existing_files );

        }

        private function loadExistingFilesToData( &$data, $existing_files ){

            if( isset( $existing_files["existingfile"] ) ){

                foreach($existing_files as $index=>$value){

                    if( $index == 'existingfile' ) continue;

                    $data[$index] = $value;

                }

            } else {

                foreach( $existing_files as $index=>$value ){

                    if( !isset( $data[ $index ] ) )
                        $data[ $index ] = array();

                    $this->loadExistingFilesToData( $data[ $index ], $existing_files[ $index ] );

                }

            }

        }

        /**
         * Load files into data variable
         *
         * @param $main_files_data
         * @param $files_data
         * @param array $parents
         * @return bool
         */
        private function getLoadedFiles( $main_files_data, $files_data, $parents = array() ){

            foreach( $files_data as $findex => $fdata ){

                $tmp_parents = $parents;
                $tmp_parents[] = $findex;

                if( is_array( $fdata ) ){

                    $this->getLoadedFiles( $main_files_data, $fdata, $tmp_parents );

                } else {

                    $post_data = isset($_POST["zombify"]["file_url"]) ? $_POST["zombify"]["file_url"] : '';

                    foreach( $parents as $par )
                        $post_data = isset( $post_data[$par] ) ? $post_data[$par] : '';

                    $file_data_url = $post_data!='' ? $post_data : '';

                    if( $fdata == '' && $file_data_url == '' ) continue;

                    $file_name = $main_files_data["name"];
                    $file_type = $main_files_data["type"];
                    $file_size = $main_files_data["size"];

                    foreach( $tmp_parents as $tparent ){

                        $file_name = $file_name[ $tparent ];
                        $file_type = $file_type[ $tparent ];
                        $file_size = $file_size[ $tparent ];

                    }

                    $data = &$this->data;

                    foreach( $tmp_parents as $p ){

                        $data = &$data[ $p ];

                    }

                    if( !is_numeric( $p ) ){
                        $data = &$data[0];
                    }



                    $f = array(
                        "name" => $file_name,
                        "type" => $file_type,
                        "size" => $file_size,
                        "tmp_name" => $fdata,
                    );

                    $max_file_size = zf_get_option("zombify_max_upload_size");

                    if( $fdata != '' ){

                        if( $max_file_size >= $f["size"] ) {

                            $f = zf_get_file_upload($f);

                            if (isset($f["uploaded"]["error"]) && $f["uploaded"]["error"] != '') {
                                unset($f);
                            }

                        } else {
                            unset($f);
                        }

                    } else {
                        try {
                            $url_file_data = zf_get_file_by_url($file_data_url);

                            $f["name"] = $url_file_data["name"];
                            $f["size"] = $url_file_data["size"];
                            $f["type"] = $url_file_data["type"];
                            $f["uploaded"] = $url_file_data["uploaded"];
                            $f["attachment_id"] = $url_file_data["attachment_id"];
                        } catch (Exception $e) {
                            unset($f);
                            $data = $e->getMessage();
                            continue;
                        }
                    }

                    unset( $f["tmp_name"] );

					if (empty($f['attachment_id'])) {
	                    $attach_data = zf_insert_attachment( 0, $f["uploaded"]["file"], $f['type'] );
	                    $attach_id = $attach_data["id"];
					} else {
						$attach_id = $f['attachment_id'];
					}

                    $data = ["attachment_id" => $attach_id];
                }
            }

            return true;
        }

        /**
         * Validate loaded data
         *
         * @return bool
         */
        public function validate(){

            if( count( $this->data ) > 0 )
            {

                if( $this->virtual == 0 ){

                    $required_options = zombify()->getRequiredOptions( $this->slug );

                    foreach( $required_options as $req_opt ){

                        if( !isset( $this->data_options[$req_opt] ) || empty($this->data_options[$req_opt]) ){
                            return false;
                        }

                    }

                }

                $structure = $this->getStructure();

                foreach( $structure as $item )
                {
                    if( $item["type"] == "field" )
                    {
                        $field_validate = $this->validateField($item, isset($this->data[ $item["name"] ]) ? $this->data[ $item["name"] ] : '');

                        if( $field_validate["error"] == true )
                            return false;

                    }
                    else
                    {
                        $group_validate = $this->validateGroup( array($item["name"]) );

                        if( $group_validate == false )
                            return false;
                    }
                }

                return true;

            }
            else
                return true;

        }

        private function validateGroup( $group_name ){

            if( count( $this->data ) > 0 )
            {
                $group = $this->findGroup( $group_name );

                foreach( $group as $item )
                {
                    if( $item["type"] == "field" )
                    {

                        $field_items = $this->getFieldItemsFromData( $group_name, $item, $this->data );

                        foreach( $field_items as $field_val )
                        {
                            $field_validate = $this->validateField($item, $field_val);

                            if ($field_validate["error"] == true)
                                return false;
                        }

                    }
                    else
                    {
                        $group_name_temp = $group_name;
                        $group_name_temp[] = $item["name"];

                        $group_validate = $this->validateGroup( $group_name_temp );

                        if( $group_validate == false )
                            return false;
                    }
                }

                return true;

            }
            else
                return true;

        }

        private function getFieldItemsFromData( $group_name, $field, $data ){

            $items = array();

            if( count($group_name) > 0 ) {

                $keys = array_keys($group_name);

                if (isset($data[$group_name[$keys[0]]]) && is_array($data[$group_name[$keys[0]]])) {

                    foreach ($data[$group_name[$keys[0]]] as $group) {

                        $itms = $this->getFieldItemsFromData(array_slice($group_name, 1), $field, $group);

                        $items = array_merge( $items, $itms );

                    }

                }

            } else {

                if( isset( $data[ $field["name"] ] ) )
                    $items[] = $data[ $field["name"] ];

            }

            return $items;

        }

        /**
         * Validate field for error messages
         *
         * @param $field
         * @param $value
         * @return array|mixed
         */
        private function validateField( $field, $value ){

            $return = array("error" => false, "errorMessage" => "");

            if( isset($field["rules"]) )
            {
                foreach ($field["rules"] as $rule_index => $rule)
                {
                    if (is_callable($rule))
                    {
                        $return = $this->virtual == 0 ? call_user_func($rule, $value) : array("error" => false, "errorMessage" => "");
                    }
                    else
                    {
                        if( !is_numeric( $rule_index ) )
                            $r = $rule_index;
                        else
                            $r = $rule;

                        switch ( $r )
                        {
                            case "required":
                                if ( ( ( is_array($value) && count($value) == 0 ) || ( is_string($value) && $value == '' ) ) && $this->virtual == 0 )
                                {
                                    $return = array("error" => true, "errorMessage" => !is_numeric( $rule_index ) ? $rule : __("The field is required", "zombify"));
                                }
                                break;

                            case "maxSize":

                                if( is_array( $value ) ){
                                    foreach( $value as $val ){

                                        $attachment_metadata = wp_get_attachment_metadata( $val["attachment_id"] );
                                        $attachment_path = get_attached_file( $val["attachment_id"] );
                                        $attachment_metadata["size"] = filesize( $attachment_path );

                                        if( $attachment_metadata["size"] > ($rule * 1024) ){

                                            if( $this->removeInvalidData ){
                                                wp_delete_attachment( $val["attachment_id"], true );
                                            }

                                            $return = array("error" => true, "errorMessage" => __("File is too large. Maximum size is ", "zombify").round( ( $rule > 0 ? $rule / 1024 : 0 ) , 2).__("MB", "zombify"));

                                            $temp_val = $attachment_metadata;
                                            $temp_val["error"] = $return;

                                            $this->incorrectFiles[] = $temp_val;
                                        }
                                    }
                                }
                                break;

                            case 'url':

                                if( ( $value!='' && filter_var($value, FILTER_VALIDATE_URL) === false ) && $this->virtual == 0 ){

                                    $return = array("error" => true, "errorMessage" => __("Invalid URL", "zombify"));

                                }

                                break;

                            case "extensions":
                                $extensions = array_map('trim', explode( ",", $rule ) );

                                if( is_array( $value ) ){

                                    foreach( $value as $val ){

                                        if( is_array($val) ) {

                                            $attachment_metadata = wp_get_attachment_metadata( $val["attachment_id"] );
                                            $attachment_path = get_attached_file( $val["attachment_id"] );

                                            $ext = strtolower(pathinfo( $attachment_path, PATHINFO_EXTENSION ));

                                            if( !in_array($ext, $extensions) ){

                                                if( $this->removeInvalidData ){
                                                    wp_delete_attachment( $val["attachment_id"], true );
                                                }

                                                $return = array("error" => true, "errorMessage" => __("Incorrect file extension. It can be only:", "zombify")." ".$rule);

                                                $temp_val = $attachment_metadata;
                                                $temp_val["error"] = $return;

                                                    $temp_val = $val;
                                                    $temp_val["error"] = $return;

                                                    $this->incorrectFiles[] = $temp_val;
                                            }

                                        } else {

                                            if( is_string($val) ){

                                                $return = array("error" => true, "errorMessage" => $val);

                                                $temp_val = $val;
                                                $temp_val["error"] = $return;

                                                $this->incorrectFiles[] = $temp_val;

                                            }

                                        }

                                    }
                                }
                                break;

                        }
                    }

                    if( $return["error"] == true ){
                        break;
                    }
                }
            }

            if( $return["error"] == true ){
                $this->errors[] = $return;
            }

            return $return;

        }

        public function save( $post_id = null, $post_status = null, $options = array(), $old_data = array() ){

            global $wpdb;
            global $zf_tags_limit;

            if( $post_id ){
                clean_post_cache( $post_id );
            }

            if( ! zf_user_can_create() ) {
	            return false;
            }

            // Get zombify post title
            $post_title = (isset($this->data['title']) && $this->data['title']) ? $this->data['title'] : 'Zombify Post';

            $seled_pst = zombify()->postsave_types;

            $post_content = isset( $this->data['preface_description'] ) ? zf_remove_shortcode( $this->data['preface_description'] ) : '';
            $post_content = zf_append_shortcode( $post_content );

            if( ! $post_id ) {
	            $post_action = 'create';

                if( isset( $seled_pst[ $this->slug ] ) ){

                    switch( $seled_pst[ $this->slug ] ){

                        case 'editor':

                            $data = $this->data;

                            $template_file = zombify()->locate_template( zombify()->quiz_view_dir( strtolower($this->slug) . '.php' ) );

                            ob_start();
                            include $template_file;
	                        $post_content = ob_get_clean();

                            break;
                        case 'meta':
                            $post_content = '';
                            break;

                    }

                }

	            // Create new post for quiz
	            $post_args = array(
		            'post_author'               => get_current_user_id(),
		            'post_content_filtered'     => '',
		            'post_title'                => $post_title,
		            'post_type'                 => 'post',
		            'post_parent'               => 0,
		            'post_status'               => 'draft',
		            'post_excerpt'              => isset($this->data['excerpt_description']) ? $this->data['excerpt_description'] : '',
		            'post_content'              => $post_content,
		            'comment_status'            => zf_get_option("default_comment_status"),
		            'meta_input'                => array()
	            );

            } else {
	
	            $post_action = 'update';
            	
	            // Update post title
	            $post_args = array(
		            'ID' => $post_id,
		            'post_title' => $post_title,
		            'post_excerpt' => isset($this->data['excerpt_description']) ? $this->data['excerpt_description'] : '',
		            'post_content' => $post_content,
		            'meta_input'   => array()
	            );

            }

            /* Maybe schedule for future publish */
            if( zf_user_can_publish() ) {

            	/* Figure out if the user changed post date, and if so, save `post_date_gmt` for 'draft' post */
	            foreach ( array('aa', 'mm', 'jj', 'hh', 'mn') as $timeunit ) {
		            if ( ! empty( $options['hidden_' . $timeunit] ) && $options['hidden_' . $timeunit] != $options[$timeunit] ) {
			            $post_args['edit_date'] = '1';
			            break;
		            }
	            }

	            if ( ! empty( $post_args['edit_date'] ) ) {
		            $options     = $this->load_date( $options );
		            $post_status = $this->adjustPostStatus( $post_status, $options, $post_args );
	            }
            }

            /* Insert post status as provided, draft as default */
            if( $post_status ) {
                $post_args[ 'post_status' ] = $post_status;
            }

	        /************* Prepare post categories */
	        $post_categories = $this->prepare_post_categories_for_save( $options );
	        /************* /end prepare post categories */

            /************* Prepare post tags */
	        $post_tags = $this->prepare_post_tag_for_save( $options );
	        /************* /end prepare post tags */
	
	        /************* Insert sub posts with temp parent id of 0 */
            $sub_post_ids = $this->saveSubPosts( $this->data, 0 );

            $post_data = $this->data;

            if( in_array( $this->slug, array( 'openlist', 'rankedlist' ) ) ) {
            	
                foreach( $post_data['list'] as $list_index => $list_item ){
                    $post_data['list'][ $list_index ][ 'temp_item_rateing' ] = (int)get_post_meta( $list_item['post_id'], 'zombify_post_rateing', true);
                }

                usort($post_data['list'],function($a, $b) {
                    return $b['temp_item_rateing'] != $a['temp_item_rateing'] ? $b['temp_item_rateing'] - $a['temp_item_rateing'] : $a['post_id'] - $b['post_id'];
                });

                foreach( $post_data['list'] as $list_index => $list_item ){
                    unset( $post_data['list'][ $list_index ]['temp_item_rateing'] );
                }

            }
	
	        /************* Prepare metadata */
	        $post_args['meta_input']['openlist_close_submission'] = isset( $options['openlist_close_submission'] ) ? 1 : 0;
	        $post_args['meta_input']['openlist_close_voting'] = isset( $options['openlist_close_voting'] ) ? 1 : 0;
	        $post_args['meta_input']['openlist_hide_comments'] = isset( $options['openlist_hide_comments'] ) ? 1 : 0;
	        $post_args['meta_input']['zombify_data_type'] = $this->slug;
	        $post_args['meta_input']['zombify_data_subtype'] = $this->subtype;
	        $post_args['meta_input']['zombify_postsave_type'] = $seled_pst[ $this->slug ];
	        $post_args['meta_input']['zombify_data'] = zf_encode_data( $post_data );
	        if( count( $post_categories ) > 0 ) {
		        $post_args['post_category'] = $post_categories;
	        }
	        $post_args['tags_input'] = $post_tags;
	        if( isset( $options["items_per_page"] ) ) {
		        $post_args['meta_input'][ 'zombify_items_per_page' ] = (int)$options['items_per_page'];
	        }
	        /************* /end prepare metadata */

	        if( $post_action == 'create' ) {
	            $post_id = wp_insert_post( $post_args );
	        } else {
	            $post_id = wp_update_post( $post_args );
	        }

	        if( $post_id ) {

		        /************* Update sub posts and set correct parent ID */
		        if ( is_array($sub_post_ids) && !empty($sub_post_ids) ) {

					$sub_post_args = array('post_parent' => $post_id);
					
					if( $post_status ) {
						if($post_status == "future") {
							$sub_post_args['edit_date'] = '1';
							$sub_post_args['post_date_gmt'] = $post_args['post_date_gmt'];
							$sub_post_args['post_date'] = $post_args['post_date'];
						}
						$sub_post_args['post_status'] = $post_status;
					}
					foreach($sub_post_ids as $sub_post_id) {
						$sub_post_args['ID'] = $sub_post_id;
						wp_update_post($sub_post_args);
					}
		        }

				$this->deleteAttachments( $old_data );

		        $this->setFeaturedImage( $post_id );

		        $this->updateFeaturedMedia( $this->data, $post_id );
		
		        $this->getDependencies();
		        
		        $additional_fields = array();
		        if( isset( $_POST[ ZF_QuizAdditionalFields::NAME_PREFIX ] ) ) {
			        $additional_fields = $_POST[ ZF_QuizAdditionalFields::NAME_PREFIX ];
		        }
		        /**
		         * Fires when `Zombify` post is saved, for saving additional fields
		         *
		         * @param $post_id              int     Saved post id
		         * @param $post_action          string  Action name
		         * @param $additional_fields    array   Appended fields to `create post`
		         */
		        do_action( 'zf_save_post_additional_fields', $post_id, $post_action, $additional_fields );
		
                do_action( 'zf_post_saved', $post_id, $post_action, $this->data );
		
	        } else {
	        
	        	// remove sub posts on fail
		        foreach( $sub_post_ids as $sub_post_id ) {
		        	wp_delete_post( $sub_post_id, true );
		        }
	        
	        }

            return $post_id;

        }
		
		protected function deleteAttachments( $old_data ) {
			$attachment_diff = zf_compare_attachments($this->data, $old_data);
			
			if( ! empty( $attachment_diff['removed'] ) ) {
				foreach( $attachment_diff['removed'] as $attach_id ){
					if( (int)$attach_id <= 0 ) continue;
					
					if( $post = get_post( (int)$attach_id ) ) {
						$zf_attachment = get_post_meta($post->ID, 'zf_attachment', true);
						if ($zf_attachment && $post->post_type == 'attachment' && zf_user_can_edit($post->ID)) {
							wp_delete_attachment($post->ID, true);
						}
					}
				}
			}
		}

        /**
         * If post date is future, set post status to 'future'
         *
         * @param string $post_status
         * @param array $options
         * @param array &$post_args
         *
         * @return string
         */
        private function adjustPostStatus( $post_status, $options, &$post_args ) {
            if( ! empty( $options["post_date"] ) ) {
                $post_args['post_date_gmt'] = get_gmt_from_date( $options['post_date'] );
                $post_args["post_date"]     = $options["post_date"];

                if( $post_status == 'publish' ) {

                    $d1 = new DateTime( $options[ 'post_date' ] );
                    $d2 = new DateTime( current_time( 'Y-m-d H:i:s' ) );

                    if( $d1 > $d2 ) {
                        $post_status = 'future';
                    }
                }
            }

            return $post_status;
        }
	
	    /**
	     * Get post categories for post save
	     * @param $options
	     *
	     * @return array
	     */
        private function prepare_post_categories_for_save( $options ) {
	        $post_categories = array();
	        $predef_post_categories = zf_get_option('zombify_post_categroies');

	        if( $this->slug == 'story' ){
		        if( isset( $predef_post_categories[ 'subtype_'.$this->subtype ] ) ) {
			        $post_categories[] = $predef_post_categories[ 'subtype_' . $this->subtype ];
		        } else {
			        if( isset( $predef_post_categories[ $this->slug ] ) ) {
				        $post_categories[] = $predef_post_categories[ $this->slug ];
			        }
		        }
	        } else {
		        if( isset( $predef_post_categories[ $this->slug ] ) ) {
			        $post_categories[] = $predef_post_categories[ $this->slug ];
		        }
	        }

	        if( isset( $options['category'] ) && is_array( $options['category'] ) && count( $options[ 'category' ] ) > 0 ) {
		
		        $cat_count = 0;
		
		        $allowed_categories = zf_get_option('zombify_allowed_cats', array( -1 ) );
		        $cat_max_limit = zf_get_option('zf_categories_limit', 3);
		
		        foreach( $options['category'] as $optcat ){
			
			        if( (int)$optcat <= 0 ) {
				        continue;
			        }
			
			        if( ! in_array('-1', $allowed_categories ) && ! in_array( (int)$optcat, $allowed_categories) ){
				        continue;
			        }
			
			        $post_categories[] = (int)$optcat;
			
			        $cat_count++;
			
			        if( $cat_count >= $cat_max_limit ) {
				        break;
			        }
			
		        }

		        if( count( $post_categories ) > 1 && in_array( '-1', $post_categories ) ) {
			        if ( ( $key = array_search( '-1', $post_categories ) ) !== false ) {
				        unset( $post_categories[ $key ] );
						$post_categories = array_values( $post_categories );
			        }
		        }
		
	        }

	        return $post_categories;
        }

	    /**
	     * Get "must to use" tags
	     * @return array
	     */
	    private function get_mu_tags() {
		    $predefined_tags = zf_get_option( 'zombify_post_tags' );
		    $tags_key = $this->slug;
		    if( 'story' == $this->slug && 'main' != $this->subtype ){
			    $tags_key = 'subtype_' . $this->subtype;
		    }

		    $tags = array();
		    if( isset( $predefined_tags[ $tags_key ] ) ) {
			    foreach ( explode( ',', $predefined_tags[ $tags_key ] ) as $tag ) {
				    $tags[] = trim( $tag );
			    }
		    }

		    return apply_filters( 'zombify_mu_tags', $tags, $this );
	    }

	    /**
	     * Get post tags for post save
	     * @param $options
	     *
	     * @return array
	     */
	    private function prepare_post_tag_for_save( $options ) {

	    	/** First we will add configured "must to use" tags */
		    $post_tags = $this->get_mu_tags();

		    if( isset( $options[ 'tags' ]) && $options[ 'tags' ] != '' ) {

			    $post_tags_arr = explode( ',', $options[ 'tags' ] );
			    $zf_tags_limit = zf_get_option( 'zf_tags_limit', 3 );

			    if( count( $post_tags_arr ) > $zf_tags_limit ) {
				    $post_tags_arr = array_slice( $post_tags_arr, 0, $zf_tags_limit );
			    }

			    foreach ($post_tags_arr as $tag) {
				    $post_tags[] = trim( $tag );
			    }

		    }

		    return array_unique( $post_tags );
	    }

        public function updateFeaturedMedia( $data, $post_id, $attachment_id = null, $media_data = array() ){

			//overwrite necessary parameters
			if(isset($attachment_id)) {

				$media_data["media_id"]         = $attachment_id;
				$media_data["media_mime_type"]  = get_post_mime_type( $attachment_id );
				$media_data["media_url"]        = wp_get_attachment_url( $attachment_id );

				if ($jpeg_id = get_post_meta($attachment_id, "zombify_jpeg_id", true)) {
					$media_data["zombify_jpeg_id"] = $jpeg_id;

					if ($jpeg_url = get_post_meta($attachment_id, "zombify_jpeg_url", true)) {
						$media_data["zombify_jpeg_url"] = $jpeg_url;
					}
				}

				if ($mp4_id = get_post_meta($attachment_id, "zombify_mp4_id", true)) {
					$media_data["zombify_mp4_id"] = $mp4_id;

					if ($mp4_url = get_post_meta($attachment_id, "zombify_mp4_url", true)) {
						$media_data["zombify_mp4_url"] = $mp4_url;
					}
				}
			}

			if( empty( $media_data ) ) { // That is: doesn't uploaded or isn't previously uploaded
				return delete_post_meta ( $post_id, "zombify_featured_media" );
			}
			else {
				return update_post_meta( $post_id, "zombify_featured_media", $media_data );
			}
        }

        /**
         * Set featured image of post
         *
         * @param int $post_id
         *
         * @return bool
         */
		public function setFeaturedImage( $post_id ) {

			$thumbnail_id = 0;
			$featured =  $this->getFeaturedImageByPriority();
			 if( !empty($featured["attachment_id"]) ){
				 $thumbnail_id = $featured["attachment_id"];
			 } else {
				 //todo:no sure if we need this case at all
				 $thumbnail_id = $this->handleFeaturedImage( $featured, $post_id );
			 }

			$this->setPostThumbnail( $thumbnail_id, $post_id );

            return true;

        }

        protected function getFeaturedImageByPriority() {
			
			$fimages = array();

            $this->getFeaturedImages( $this->data, $fimages );

            $fimages = $this->sortByPriority( $fimages );
			while($featured = array_shift($fimages)) {
				if(empty($featured["attachment_id"])) {
					//maybe it's trying to fetch by URL
					break;					
				} else {
					$post = get_post( (int)$featured["attachment_id"] );
					if(!empty( $post->post_type ) &&  $post->post_type == 'attachment') {
						break;
					}
				}
			}
			return $featured;	
		}
		/**
         * Save thumbnail
         *
         * @param int $thumbnail_id
         * @param int $post_id
         *
         * @return void
         */
        protected function setPostThumbnail( $thumbnail_id, $post_id ) {
            if(!empty($thumbnail_id)) {
                zf_set_post_thumbnail($post_id, $thumbnail_id);                
            }
			//todo: we had to update featured image, before, we had zombify_update_featured function,
			//but it contained code duplication, so, i have removed it
			//the problem here is that zombify_post_update can do more than only featured image update
			zombify_post_update($post_id);
        }

        /**
         * Sort array of images
         *
         * @param array $fimages
         *
         * @return array
         */
        protected function sortByPriority( $fimages ) {
            usort($fimages, function($a, $b) {
                return $a['priority'] == $b['priority'] ? 1 : $a['priority'] - $b['priority'];
            });

            return $fimages;
        }

        /**
         * Get featured image id of a post, if exist
         *
         * @param array $featured
         * @param int $post_id
         *
         * @return int
         */
        protected function handleFeaturedImage( $featured, $post_id ) {

            //todo: do we really need this function, when it will work?
			$thumbnail_id = 0;
			if(!empty($featured["url"])) {
				$image_url_hash = md5( $featured["url"] );
				$downloaded_images = zf_get_downloaded_attachment( $post_id );

				if( isset($downloaded_images[ $image_url_hash ]) ){
					$thumbnail_id = $downloaded_images[ $image_url_hash ];
				} else {
					$file_data_url = $featured["url"];
					try {
						$url_file_data = zf_get_file_by_url($file_data_url);

						$file_data_up_path = $url_file_data["uploaded"]["file"];
						$mime_type = $url_file_data["type"];

						if (empty($url_file_data['attachment_id'])) {
							$attach_data = zf_insert_attachment( $post_id, $file_data_up_path, $mime_type, true );
							$attach_id = $attach_data["id"];
						} else {
							$attach_id = $url_file_data['attachment_id'];
						}

						if( !isset( $this->data["downloaded_images"] ) )
							$this->data["downloaded_images"] = array();

						$this->data["downloaded_images"][ $image_url_hash ] = $attach_id;

						zf_save_downloaded_attachment( $post_id, $attach_id, $file_data_url );

					} catch (Exception $e) {
					}

				}
			}
            return $thumbnail_id;
        }

        public function getFeaturedImages( $data, &$fimages, $parent_path = array(), $priority = 1 ){

            foreach( $data as $index=>$value ){

                $fieldName = $parent_path;
                $fieldName[] = $index;

                if( $fieldInfo = $this->findField( $fieldName ) ){

                    if( isset($fieldInfo["type"]) ) {

                        if ($fieldInfo["type"] == "field") {

                            if (isset($fieldInfo["use_as_featured"]) && $fieldInfo["use_as_featured"]) {

                                switch ($fieldInfo["field_type"]) {

                                    case "file":

                                        if (count($value) > 0) {

                                            $img = reset($value);

                                            if ($img["attachment_id"] > 0) {

                                                $fimages[] = array(
                                                    "attachment_id" => $img["attachment_id"],
                                                    "url" => "",
                                                    "priority" => ($priority * 1000) + count($fimages),
                                                    "path" =>  $fieldName,
                                                );

                                            }

                                        }

                                        break;

                                    case "video":

                                        if ( $value > 0) {

                                            $fimages[] = array(
                                                "attachment_id" => $value,
                                                "url" => "",
                                                "priority" => ($priority * 1000) + count($fimages),
												"path" =>  $fieldName,
                                            );

                                        }

                                        break;

                                    case "text":
                                    case "hidden":

                                        if ($value != '') {

                                            $fimages[] = array(
                                                "attachment_id" => 0,
                                                "url" => $value,
                                                "priority" => ($priority * 1000) + count($fimages),
												"path" =>  $fieldName,
                                            );
                                        }

                                        break;

                                }

                            }

                        } else {

                            foreach ($data[$index] as $dt) {

                                $this->getFeaturedImages($dt, $fimages, $fieldName, $priority + 1);

                            }

                        }

                    } else {

                        foreach ($data[$index] as $dt) {

                            $this->getFeaturedImages($dt, $fimages, $fieldName, $priority + 1);

                        }

                    }

                }

            }

        }


        private function findFieldFromData( $data, $path ){

            $field_data = array();

            $index = array_shift( $path );

            if( count( $path ) == 0 ){

                if( is_numeric( key( $data[ $index ] ) ) ) {

                    foreach( $data[ $index ] as $val ){

                        $field_data[] = $val;

                    }

                } else {

                    $field_data[] = $data[$index];

                }

            } else {

                foreach( $data[ $index ] as $s_data ){

                    $s_field_data = $this->findFieldFromData( $s_data, $path );

                    foreach( $s_field_data as $sdata )
                        $field_data[] = $sdata;

                }

            }

            return $field_data;

        }

        /**
         * Check provided data and format in the options
         *
         * @param array $options Quiz option fields
         *
         * @return array
         */
        private function load_date( $options ) {
            $aa = $options['aa'];
            $mm = $options['mm'];
            $jj = $options['jj'];
            $hh = $options['hh'];
            $mn = $options['mn'];
            $ss = $options['ss'];
            $aa = ( $aa <= 0 ) ? date( 'Y' ) : $aa;
            $mm = ( $mm <= 0 ) ? date( 'n' ) : $mm;
            $jj = ( $jj > 31 ) ? 31 : $jj;
            $jj = ( $jj <= 0 ) ? date( 'j' ) : $jj;
            $hh = ( $hh > 23 ) ? $hh -24 : $hh;
            $mn = ( $mn > 59 ) ? $mn -60 : $mn;
            $ss = ( $ss > 59 ) ? $ss -60 : $ss;

            $post_date = sprintf( "%04d-%02d-%02d %02d:%02d:%02d", $aa, $mm, $jj, $hh, $mn, $ss );
            $valid_date = wp_checkdate( $mm, $jj, $aa, $post_date );
            if ( $valid_date ) {
                $options['post_date'] = $post_date;
            }

            return $options;
        }

        public function array_rec(&$data){

            foreach( $data as $index=>$value ){

                if( is_string($value) ){

                    $data[ $index ] = stripslashes( $data[ $index ] );

                }

                if( is_array( $value ) ){

                    $this->array_rec( $data[ $index ] );

                }

            }

        }

        private function saveSubPosts( &$data, $parent_post_id ){

            $post_ids = $this->insertSubPosts( $data, $parent_post_id );

            $query = new WP_Query( array(
                'posts_per_page'   => -1,
                'offset'           => 0,
                'post_type'        => 'list_item',
                'post_parent'      => $parent_post_id,
                'post__not_in'     => $post_ids,
                'post_status'      => array('draft', 'publish', 'pending'),
            ) );
            foreach( $query->get_posts() as $post ) {
	            wp_delete_post( $post->ID, true );
            }
            
            return $post_ids;
        }

        private function insertSubPosts(&$data, $parent_post_id){

            $post_ids = array();

            foreach( $data as $index=>$value ){

                if( is_string($value) ){

                    if( $index == 'post_id' ){

                        if( $value == '' || $value==0 ){

                            // Create new post
                            $post_args = array(
                                'post_author' => get_current_user_id(),
                                'post_content' => '',
                                'post_content_filtered' => '',
                                'post_title' => isset( $data["title"] ) ? $data["title"] : '',
                                'post_excerpt' => '',
                                'post_status' => 'draft',
                                'post_type' => 'list_item',
                                'post_parent' => $parent_post_id,
                                'comment_status' => zf_get_option("default_comment_status"),
                            );
                            $post_id = wp_insert_post($post_args);

                            $data[$index] = $post_id;

                            $post_ids[] = $post_id;

                            if( isset($data["image"][0]["attachment_id"]) && $data["image"][0]["attachment_id"] ){

                                zf_set_post_thumbnail($post_id, $data["image"][0]["attachment_id"]);

                            } else {

                                if( $data["embed_thumb"] != '' ){

                                    $image_url_hash = md5( $data["embed_thumb"] );

                                    $downloaded_images = zf_get_downloaded_attachment( $post_id );

                                    if( isset($downloaded_images[ $image_url_hash ]) ) {

                                        zf_set_post_thumbnail($post_id, $downloaded_images[$image_url_hash]);

                                    } else {

                                        $file_data_url = $data["embed_thumb"];

                                        try {

                                            $url_file_data = zf_get_file_by_url($file_data_url);

                                            $file_data_up_path = $url_file_data["uploaded"]["file"];
                                            $mime_type = $url_file_data["type"];

	                                        if (empty($url_file_data['attachment_id'])) {
		                                        $attach_data = zf_insert_attachment( $post_id, $file_data_up_path, $mime_type, true );
		                                        $attach_id = $attach_data["id"];
	                                        } else {
		                                        $attach_id = $url_file_data['attachment_id'];
	                                        }

                                            zf_save_downloaded_attachment( $post_id, $attach_id, $file_data_url );

                                        } catch (Exception $e) {

                                        }

                                    }

                                }

                            }

                        } else {

                            $post_id = (int)$value;

                            // Update post title
                            $post_args = array(
                                'ID' => (int)$value,
                                'post_title' => isset( $data["title"] ) ? $data["title"] : '',
                            );
                            wp_update_post($post_args);

                            $post_ids[] = (int)$value;

                            if( isset($data["image"][0]["attachment_id"]) && $data["image"][0]["attachment_id"] ){

                                zf_set_post_thumbnail((int)$value, $data["image"][0]["attachment_id"]);

                            } else {

                                if( $data["embed_thumb"] != '' ){

                                    $image_url_hash = md5( $data["embed_thumb"] );

                                    $downloaded_images = zf_get_downloaded_attachment( $post_id );

                                    if( isset($downloaded_images[ $image_url_hash ]) ) {

                                        zf_set_post_thumbnail($post_id, $downloaded_images[$image_url_hash]);

                                    } else {

                                        $file_data_url = $data["embed_thumb"];

                                        try {

                                            $url_file_data = zf_get_file_by_url($file_data_url);

                                            $file_data_up_path = $url_file_data["uploaded"]["file"];
                                            $mime_type = $url_file_data["type"];

	                                        if (empty($url_file_data['attachment_id'])) {
		                                        $attach_data = zf_insert_attachment( $post_id, $file_data_up_path, $mime_type, true );
		                                        $attach_id = $attach_data["id"];
	                                        } else {
		                                        $attach_id = $url_file_data['attachment_id'];
	                                        }

                                            zf_save_downloaded_attachment( $post_id, $attach_id, $file_data_url );

                                        } catch (Exception $e) {

                                        }



                                    }

                                }

                            }

                        }

                    }

                }

                if( is_array( $value ) ){

                    $sub_post_ids = $this->insertSubPosts( $data[ $index ], $parent_post_id );

                    $post_ids = array_merge( $post_ids, $sub_post_ids );

                }

            }

            return $post_ids;

        }

        public function getStructure(){

            if( !$this->structure_temp ) {

                $structure = $this->structure();

                $this->structure_alias($structure);

                $required_fields = apply_filters('zombify_structure_' . $this->slug, array() );

                foreach ($required_fields as $req_field => $req) {

                    $struct = &$structure;

                    $path = explode("/", $req_field);

                    foreach ($path as $pt) {

                        $struct = &$struct[$pt];

                        if ($struct["type"] == "group") $struct = &$struct["fields"];

                    }

                    if ($req) {

                        if (!isset($struct["rules"])) $struct["rules"] = array();

                        if (!in_array("required", $struct["rules"])) {
                            $struct["rules"][] = "required";
                        }

                    } else {

                        if (isset($struct["rules"])) {

                            $keys = array_search("required", $struct["rules"]);

                            if (is_array($keys)) {
                                foreach ($keys as $key) {
                                    unset($struct["rules"][$key]);
                                }
                            } else {
                                unset($struct["rules"][$keys]);
                            }

                        }

                    }


                }

                $disable_extensions = apply_filters('zombify_structure_disable_exts', array() );

                if( is_array($disable_extensions) && count($disable_extensions) > 0 ) {

                    array_walk_recursive($structure, function (&$item, $key) use ($disable_extensions) {

                        if ($key == 'extensions') {

                            $exts_arr = array_map('trim', explode(",", $item));

                            $new_exts_arr = array_diff( $exts_arr, $disable_extensions );

                            $item = implode(",", $new_exts_arr);

                        }

                    });

                }

                $this->structure_temp = $structure;

                return $structure;

            } else
                return $this->structure_temp;

        }

        private function structure_alias(&$structure){

            if( !is_array( $structure ) ) return false;

            foreach( $structure as $item_index => &$item ){

                if( isset($item["type"]) && $item["type"] == 'group' && isset($item["alias_class"]) && $item["alias_class"] != '' ){

                    $QuizClass = "Zombify_" . ucfirst(strtolower($item["alias_class"])) . "Quiz";

                    $classObj = new $QuizClass();

                    $alias_arr = $classObj->getStructure();

                    if( isset($item["alias_group"]) && $item["alias_group"] != '' ){

                        $alias_group_arr = explode("/", $item["alias_group"]);

                        for( $ai=0; $ai<count($alias_group_arr); $ai++ ){

                            $aga = $alias_group_arr[ $ai ];

                            $grp = $alias_arr[ $aga ];

                            $alias_arr = $alias_arr[ $aga ]["fields"];

                            if( $ai == count($alias_group_arr)-1 ){

                                $alias_arr = $this->setSubAliases( $alias_arr, $item["alias_class"], $item["alias_group"] );

                                $structure[ $item_index ]["fields"] = $alias_arr;

                            }

                        }

                    }

                }

                $this->structure_alias($item);

            }

        }

        private function setSubAliases( $alias_arr, $alias_class, $alias_group ){

            foreach( $alias_arr as $al_index=>$al_arr ){

                if( $al_arr["type"] == "group" ){

                    $alias_arr[$al_index]["alias_class"] = $alias_class;
                    $alias_arr[$al_index]["alias_group"] = $alias_group.'/'.$al_arr["name"];

                    $alias_arr[$al_index]["fields"] = $this->setSubAliases( $al_arr["fields"], $alias_class, $alias_group.'/'.$al_arr["name"] );

                }

            }

            return $alias_arr;

        }

        public function getAliasGroups( $filtered = true ){
	
	        $zf_config = zombify()->get_config();

            $groups = $this->alias_groups;

            if( $filtered ){

                $alias_groups = $this->getAliasGroups(false);

                $default_formats = array();

                foreach( $alias_groups as $format_value => $format_data )
                    $default_formats[ $format_value ] = $format_value;

                $zombify_story_formats = zf_get_option("zombify_story_formats", $default_formats);

                foreach( $groups as $group_slug=>$group ){

                    if( $this->slug == 'story' ){

                        if( $group_slug != $zf_config["post_sub_types"][$this->subtype]["first_group"] ){

                            if( !isset($zombify_story_formats[$group_slug]) || $zombify_story_formats[$group_slug] == '' ){
                                unset( $groups[ $group_slug ] );
                            }

                        }

                    } else {

                        if( !isset($zombify_story_formats[$group_slug]) || $zombify_story_formats[$group_slug] == '' ){
                            unset( $groups[ $group_slug ] );
                        }

                    }

                }

                $groups =  apply_filters( 'zombify_alias_groups_'.$this->slug, $groups );

            }

            return $groups;

        }

        public static function renderEmbed( $data, $view )
        {
            if( isset( $data['embed_url'] ) && $data['embed_url'] !== '' ) {

                return Zombify_Embed::getEmbedCode( '', '', false, $data, $view );

            }
        }

        /**
         * Initiate "ZF_QuizAdditionalFields" class as tight coupling
         *
         * @return void
         */
        public function initiateQuizAdditionalFields() {
            $this->getDependencies();
            $this->quiz_additional_fields = new ZF_QuizAdditionalFields;
        }

        /**
         * Require Dependencies
         *
         * @return void
         */
        private function getDependencies(){
            require_once 'QuizAdditionalFields.php';
        }

    }

}