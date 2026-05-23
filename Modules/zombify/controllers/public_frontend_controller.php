<?php
/**
 * Zombify Public Frontend Page Controller
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

if( !class_exists("Zombify_Public_Frontend_Controller") ){

    class Zombify_Public_Frontend_Controller extends Zombify_BaseController{

        /**
         * The view path
         */
        protected $view_path = 'public';

        public function actionIndex(){

            return $this->render("frontend_page/index");

        }

        public function actionPermissiondenied(){

            do_action( 'zf_before_exception/permission_denied' );
            throw new Exception(esc_html__("You are not allowed to create posts.", "zombify"));

        }

        public function actionCreate(){

            if( zf_user_exceeded_daily_limit() ){

                return $this->render("frontend_page/limit");

            }

            $zf_config = zombify()->get_config();

            $active_formats = zombify()->get_active_formats();
            $active_formats = is_array( $active_formats ) ? $active_formats : array();

            $type = isset($_GET["type"]) ? $_GET["type"] : 'story';
            $subtype = isset($_GET["subtype"]) ? $_GET["subtype"] : 'main';

            if( is_user_logged_in() ) {

                if( $type!='story' || ( $type=='story' && isset( $zf_config["post_sub_types"][ $subtype ] ) ) ) {

                    if (in_array(strtolower($type), $active_formats) || in_array('subtype_'.strtolower($subtype), $active_formats)) {
                        $QuizClass = "Zombify_" . ucfirst(strtolower($type)) . "Quiz";

                        if (class_exists($QuizClass)) {
                            $quiz = new $QuizClass();
                        } else
                            throw new Exception(esc_html__("Invalid Quiz type. Type not found.", "zombify"));
                    } else
                        throw new Exception(esc_html__("Invalid Quiz type. Type not found.", "zombify"));

                } else
                    throw new Exception(esc_html__("Invalid Quiz type. Type not found.", "zombify"));

            } else
                throw new Exception(esc_html__("Only registered users can upload posts.", "zombify"));

            $quiz->subtype = $subtype;

            // Check if form was submitted
            if( isset($_POST["save_zombify_builder"]) ){

                $data = $_POST['zombify'];
                $existing_data = isset( $_POST['zombify_existing_data'] ) ? $_POST['zombify_existing_data'] : array();
                $files_data = isset( $_FILES['zombify'] ) ? $_FILES['zombify'] : array();

                // Load data
                $quiz->load( $data, $files_data, $existing_data );

                // Validate loaded data
                if( $quiz->validate() ) {

                    // Get zombify post title
                    $post_title = (isset($_POST['zombify']['title']) && $_POST['zombify']['title']) ? htmlspecialchars($_POST['zombify']['title']) : 'Zombify Post';

                    // Create new post for quiz
                    $post_args = array(
                        'post_author' => get_current_user_id(),
                        'post_content' => zf_get_shortcode(),
                        'post_content_filtered' => '',
                        'post_title' => $post_title,
                        'post_excerpt' => '',
                        'post_status' => 'publish',
                        'post_type' => 'post',
                        'post_parent' => 0,
                        'comment_status' => zf_get_option("default_comment_status"),
                    );
                    $new_post_id = wp_insert_post($post_args);

                    // Add submitted data to post as a meta tag in serialized format
                    add_post_meta($new_post_id, "zombify_data", zf_encode_data( $quiz->data ), true);

                    // Add quiz type as meta tag
                    add_post_meta($new_post_id, "zombify_data_type", $_GET["type"], true);

                    // Get zombify post url
                    $post_url = get_permalink( $new_post_id );

                    // Redirect to newly created zombify post
                    wp_redirect( $post_url );

                }


            } else {

                if( $vpost_id = zombify_get_virtual_post_id( $type, $quiz->subtype, false ) ){

                    $vpost_post_id = get_post_meta($vpost_id, "zombify_virtual_post_id", true);

                    if ($vpost_post_id == 0) {

                        $vdata = get_post_meta($vpost_id, 'zombify_virtual_data', true);

                        $vdata = zf_decode_data($vdata);

                        $quiz->data = $vdata;

                        $quiz->virtual = 1;

                    }


                }

            }

            $quizHtml = $quiz->renderBuilder("create");

            return $this->render("frontend_page/create", array(
                "quizHtml" => $quizHtml,
            ));

        }

        public function actionUpdate(){

            $active_formats = zombify()->get_active_formats();
            $active_formats = is_array( $active_formats ) ? $active_formats : array();

            if( is_user_logged_in() ) {

                if( isset($_GET['post_id']) )
                {
                    $postID = (int)$_GET['post_id'];

                    $postinfo = get_post( $postID );

                    if( zf_user_can_edit($postID)  ) {

                        $post_data = zf_decode_data(get_post_meta($postID, 'zombify_data', true));

                        $post_type = get_post_meta($postID, 'zombify_data_type', true);
                        $post_subtype = get_post_meta($postID, 'zombify_data_subtype', true);

                        if( !$post_subtype ) $post_subtype = 'main';

                        if (in_array(strtolower($post_type), $active_formats) || in_array('subtype_'.strtolower($post_subtype), $active_formats)) {

                            $QuizClass = "Zombify_" . ucfirst(strtolower($post_type)) . "Quiz";

                            if (class_exists($QuizClass)) {
                                $quiz = new $QuizClass();

                                if( !isset($post_data["excerpt_description"]) || $postinfo->post_excerpt != $post_data["excerpt_description"] ){

                                    $post_data["excerpt_description"] = $postinfo->post_excerpt;

                                }

                                if( isset( $post_data["preface_description"] ) ){

                                    $post_data["preface_description"] = zf_remove_shortcode( $post_data["preface_description"] );

                                }

                                $quiz->load($post_data);
                            } else
                                throw new Exception(esc_html__("Invalid Quiz type. Type not found.", "zombify"));

                        } else
                            throw new Exception(esc_html__("Invalid Quiz type. Type not found.", "zombify"));

                    } else
                        throw new Exception(esc_html__("Access denied.", "zombify"));
                }
                else
                    throw new Exception(esc_html__("Invalid post ID.", "zombify"));

            }
            else
                throw new Exception(esc_html__("Access denied.", "zombify"));

            $post_subtype = get_post_meta($postID, 'zombify_data_subtype', true);

            $quiz->subtype = $post_subtype ? $post_subtype : 'main';

            // Check if form was submitted
            if( isset($_POST["save_zombify_builder"]) ){

                $data = $_POST['zombify'];
                $existing_data = isset( $_POST['zombify_existing_data'] ) ? $_POST['zombify_existing_data'] : array();
                $files_data = isset( $_FILES['zombify'] ) ? $_FILES['zombify'] : array();

                // Load data
                $quiz->load( $data, $files_data, $existing_data );

                // Validate loaded data
                if( $quiz->validate() ) {

                    // Get zombify post title
                    $post_title = (isset($_POST['zombify']['title']) && $_POST['zombify']['title']) ? htmlspecialchars($_POST['zombify']['title']) : 'Zombify Post';

                    // Update post title
                    $post_args = array(
                        'ID' => $postID,
                        'post_title' => $post_title,
                    );
                    wp_update_post($post_args);

                    // Add submitted data to post as a meta tag in JSON format
                    update_post_meta($postID, "zombify_data", zf_encode_data( $quiz->data ) );

                    // Get zombify post url
                    $post_url = get_permalink( $postID );

                    // Redirect to updated zombify post
                    wp_redirect( $post_url );

                }


            } else {

                if( $vpost_id = zombify_get_virtual_post_id( $post_type, false, false ) ){

                    $vpost_post_id = get_post_meta($vpost_id, "zombify_virtual_post_id", true);

                    if( $vpost_post_id == (int)$_GET['post_id'] ){

                        $vdata = get_post_meta($vpost_id, 'zombify_virtual_data', true);

                        $vdata = zf_decode_data($vdata);

                        $quiz->data = $vdata;

                        $quiz->virtual = 1;

                    }

                }


            }

            $quiz->removeInvalidData = false;

            $quizHtml = $quiz->renderBuilder("update");

            return $this->render("frontend_page/create", array(
                "quizHtml" => $quizHtml
            ));

        }

        public function actionSave(){

            add_action("shutdown", "zf_save_shutdown", 1);

            $active_formats = zombify()->get_active_formats();
            $active_formats = is_array( $active_formats ) ? $active_formats : array();

            if( is_user_logged_in() ) {

                if( isset( $_POST["zombify_quiz_type"] ) )
                {
                    if( in_array( strtolower( $_POST["zombify_quiz_type"] ), $active_formats ) || in_array('subtype_'.strtolower($_POST["zombify_quiz_subtype"]), $active_formats) )
                    {
                        $QuizClass = "Zombify_".ucfirst( strtolower( $_POST["zombify_quiz_type"] ) )."Quiz";

                        if( class_exists( $QuizClass ) )
                        {
                            $quiz = new $QuizClass();
                        }
                        else
                            throw new Exception(esc_html__("Invalid Quiz type. Type not found.", "zombify"));
                    }
                    else
                        throw new Exception(esc_html__("Invalid Quiz type. Type not found.", "zombify"));
                }
                else
                    throw new Exception(esc_html__("Invalid Quiz type. Type not specified.", "zombify"));

            }
            else
                throw new Exception(esc_html__("Access denied.", "zombify"));

            $quiz->subtype = isset($_POST['zombify_quiz_subtype']) ? $_POST['zombify_quiz_subtype'] : 'main';

            $old_data = array();

            $data = $_POST['zombify'];
            $data_options = isset( $_POST['zombify_options'] ) ? $_POST['zombify_options'] : array();
            $existing_data = isset( $_POST['zombify_existing_data'] ) ? $_POST['zombify_existing_data'] : array();
            $files_data = isset( $_FILES['zombify'] ) ? $_FILES['zombify'] : array();

            $quiz->data_options = $data_options;

            // Load data
            $quiz->load( $data, $files_data, $existing_data );

            // Validate loaded data
            if( $quiz->validate() ) {

                if( isset($_POST["zombify_post_id"]) && (int)$_POST["zombify_post_id"] > 0 ){

                    $postinfo = get_post( (int)$_POST["zombify_post_id"] );

                    if( zf_user_can_edit( (int)$_POST["zombify_post_id"] ) ) {

                        $old_data = zf_decode_data( get_post_meta( (int)$_POST["zombify_post_id"], 'zombify_data', true ) );

                        if( isset($_POST['zombify_publish_post']) ){
                            $post_status = zf_user_can_publish() || $postinfo->post_status == 'publish' ? 'publish' : 'pending';
                        } else {
                            $post_status = null;
                        }
                        $saved_post_id = $quiz->save((int)$_POST["zombify_post_id"], $post_status, $data_options, $old_data);

                    } else
                        return false;

                } else {

                    if( zf_user_can_create() ) {

                        if (isset($_POST['zombify_publish_post'])) {
                            $post_status = zf_user_can_publish() ? 'publish' : 'pending';
                        } else {
                            $post_status = null;
                        }

                        $saved_post_id = $quiz->save(null, $post_status, $data_options);

                    } else
                        return false;

                }

                //set post parent for new attachments and delete deleted attachments
                zf_set_attachments_parent_id( $quiz->data, $saved_post_id, $old_data );
                //if we will delete an attachment, that was post thumbnail, wordpress will remove post thumbnail
                //so, we can sync zombify post data just in case, this will sync post thumbnail too
                zombify_post_update($saved_post_id);

                // Get zombify post url
                $post_url = get_permalink( $saved_post_id );
                setcookie('zombify_zfps', get_post_status( $saved_post_id ), time()+60*60, '/');

                $_GET['post_id'] = $saved_post_id;

                //todo: we have to reload post data from DB, to be able to show updated featuerd image
                if( isset($_POST["zombify_post_id"]) && (int)$_POST["zombify_post_id"] > 0 ){
                    $post_data = zf_decode_data(get_post_meta($_POST["zombify_post_id"], 'zombify_data', true));
                    $quiz->load($post_data);
                }

                $quizHtml = $quiz->renderBuilder("update");

                $output = $this->render("frontend_page/create", array(
                    "quizHtml" => $quizHtml
                ));

                if( $vpost_id = zombify_get_virtual_post_id( $_POST["zombify_quiz_type"], $_POST["zombify_quiz_subtype"], false ) ){

                    wp_delete_post( $vpost_id, true );

                }

                return json_encode( array(
                    "result" => 1,
                    "post_url" => $post_url,
                    "post_id" => $saved_post_id,
                    "output" => $output
                ) );

            } else {

                $quizHtml = $quiz->renderBuilder( ( isset($_POST["zombify_post_id"]) && (int)$_POST["zombify_post_id"] > 0 ) ? 'update' : 'create' );

                $output = $this->render("frontend_page/create", array(
                    "quizHtml" => $quizHtml
                ));

                return json_encode( array(
                    "result" => 0,
                    "output" => $output
                ) );

            }

        }

        public function actionVirtual_save(){

          if (ob_get_level() > 0) {
            ob_clean();
          }

            $active_formats = zombify()->get_active_formats();
            $active_formats = is_array( $active_formats ) ? $active_formats : array();

            if( is_user_logged_in() ) {

                if( isset( $_POST["zombify_quiz_type"] ) )
                {
                    if( in_array( strtolower( $_POST["zombify_quiz_type"] ), $active_formats ) || in_array('subtype_'.strtolower($_POST["zombify_quiz_subtype"]), $active_formats) )
                    {
                        $QuizClass = "Zombify_".ucfirst( strtolower( $_POST["zombify_quiz_type"] ) )."Quiz";

                        if( class_exists( $QuizClass ) )
                        {
                            $quiz = new $QuizClass();
                        }
                        else
                            throw new Exception(esc_html__("Invalid Quiz type. Type not found.", "zombify"));
                    }
                    else
                        throw new Exception(esc_html__("Invalid Quiz type. Type not found.", "zombify"));
                }
                else
                    throw new Exception(esc_html__("Invalid Quiz type. Type not specified.", "zombify"));

            }
            else
                throw new Exception(esc_html__("Access denied.", "zombify"));

            $quiz->subtype = isset($_POST['zombify_quiz_subtype']) ? $_POST['zombify_quiz_subtype'] : 'main';

            $data = $_POST['zombify'];
            $data_options = isset( $_POST['zombify_options'] ) ? $_POST['zombify_options'] : array();
            $existing_data = isset( $_POST['zombify_existing_data'] ) ? $_POST['zombify_existing_data'] : array();
            $files_data = isset( $_FILES['zombify'] ) ? $_FILES['zombify'] : array();

            // Load data
            $quiz->load( $data, $files_data, $existing_data );

            $quiz->virtual = 1;

            if( $quiz->validate() ) {

                $vpost_id = zombify_get_virtual_post_id( $_POST["zombify_quiz_type"], $_POST["zombify_quiz_subtype"] );

                update_post_meta($vpost_id, "zombify_virtual_post_id", (isset($_POST["zombify_post_id"]) && (int)$_POST["zombify_post_id"] > 0) ? (int)$_POST["zombify_post_id"] : 0);
                update_post_meta($vpost_id, "zombify_virtual_data", zf_encode_data($quiz->data));

                return json_encode(array(
                    "result" => 1,
                    "data" => $quiz->data,
                ));

            } else {

                return json_encode(array(
                    "result" => 0,
                    "errors" => $quiz->incorrectFiles
                ));

            }

        }


        public function actionPoll_vote(){

            if( isset($_POST["id"]) && isset($_POST["post_id"]) && isset($_POST["group_id"]) && !isset($_COOKIE["zf_poll_vote_".$_POST["group_id"]]) ){

                if(!$this->zf_check_data_exist()){
                    return json_encode([0]);
                }


                if( !$vote_arr = get_post_meta( (int)$_POST["post_id"], 'zombify_poll_results', true ) )
                    $vote_arr = array(
                        "total" => 0,
                        "answers" => array(),
                        "groups" => array(),
                    );

                if( !isset($vote_arr[ "answers" ][ $_POST["id"] ]) )
                    $vote_arr[ "answers" ][ $_POST["id"] ] = 0;

                if( !isset($vote_arr[ "groups" ][ $_POST["group_id"] ]) )
                    $vote_arr[ "groups" ][ $_POST["group_id"] ] = 0;

                $vote_arr["total"]++;

                $vote_arr["answers"][ $_POST["id"] ]++;
                $vote_arr["groups"][ $_POST["group_id"] ]++;

                setcookie('zf_poll_vote_ans_'.$_POST["id"], $_POST["id"], time()+60*60*24*30, "/");
                setcookie('zf_poll_vote_'.$_POST["group_id"], $_POST["group_id"], time()+60*60*24*30, "/");

                update_post_meta((int)$_POST["post_id"], 'zombify_poll_results', $vote_arr);

            }

            if( !isset($_POST["amp"]) ){

                return json_encode([1]);

            } else {

                $response = array();

                if( isset($_POST["answers"]) && isset($vote_arr) ){

                    $answers_arr = explode(",", $_POST["answers"]);

                    foreach( $answers_arr as $answer_id ){

                        $response["votes".$_POST["group_id"].$answer_id] = isset($vote_arr["answers"][$answer_id]) ? round(($vote_arr["answers"][$answer_id]*100)/$vote_arr["groups"][$_POST["group_id"]]) : 0;

                    }

                }

                header("Content-type: application/json");
                header("Access-Control-Allow-Credentials: true");
                header("Access-Control-Allow-Origin: *.ampproject.org");
                header("AMP-Access-Control-Allow-Source-Origin: ".(isset($_SERVER['HTTPS']) ? "https" : "http") . "://".$_SERVER["HTTP_HOST"]);
                header("Access-Control-Expose-Headers: AMP-Access-Control-Allow-Source-Origin");

                return json_encode($response);

            }

        }

        private function zf_check_data_exist(){
            $zombify_data_json = base64_decode(get_post_meta( (int)$_POST["post_id"], 'zombify_data', true ));
            $group_id_exist = false;
            $answer_id_exist = false;

            preg_match_all('/'.$_POST["group_id"].'/', $zombify_data_json,$group_id_match );

            if(!empty($group_id_match)){
                $group_id_exist = true;
                preg_match_all('/'.$_POST["id"].'/', $zombify_data_json,$answer_id_match );
                if(!empty($answer_id_match)){
                    $answer_id_exist = true;
                }
            }

            return ( $group_id_exist && $answer_id_exist );
        }


        public function actionSubpost(){

            global $post;

            if( is_admin() ) return false;

            $output = '';

            $parent_post_data = zf_decode_data( get_post_meta( $post->post_parent, 'zombify_data', true ) );

            $parent_post_type = get_post_meta( $post->post_parent, 'zombify_data_type', true );

            $data = '';
            $prev_data = '';
            $next_data = '';
            $prev_data_temp = '';
            $first_data = '';
            $last_data = '';
            $data_num = 0;
            $total_sub_count = 0;

            $i=0;
            foreach( $parent_post_data["list"] as $pdata ){

                if(!zf_user_can_edit($pdata["post_id"])) {
                    $postObj = get_post($pdata["post_id"]);

                    if( $postObj->post_status != 'publish' ) continue;
                }

                $total_sub_count++;

                $i++;
                if( $pdata["post_id"] == $post->ID ){
                    $data = $pdata;
                    $prev_data = $prev_data_temp;
                    $data_num = $i;
                }

                if( $prev_data_temp!='' && $prev_data_temp["post_id"] == $post->ID ){
                    $next_data = $pdata;
                }

                $prev_data_temp = $pdata;

                if( $first_data == '' ){
                    $first_data = $pdata;
                }

                $last_data = $pdata;

            }

            if( zombify()->sub_posts_loop ){

                if( $next_data == '' ) $next_data = $first_data;
                if( $prev_data == '' ) $prev_data = $last_data;

            }


            $template_file_path = zombify()->locate_template( zombify()->quiz_view_dir( $parent_post_type.'/subpost.php' ) );

            if( file_exists($template_file_path) ){

                ob_start();
                include $template_file_path;
                $output = ob_get_contents();
                ob_end_clean();

            }

            return $output;

        }

        public function actionZombify_video_upload(){

            try {

                $up_dir = wp_upload_dir();

                $temp_dir = $up_dir["basedir"].'/zombify_temp/';

                if (!file_exists($temp_dir))
                    mkdir($temp_dir, 0755, true);

                if ($_SERVER['REQUEST_METHOD'] === 'GET') {

                    if( isset($_GET["cancel"]) && isset($_GET["uniqueIdentifier"]) ){

                        $up_dir = wp_upload_dir();

                        $unid = str_replace(array("/","\\", "."), "", $_GET["uniqueIdentifier"]);

                        if( file_exists($up_dir["basedir"].'/zombify_temp/'.$unid) )
                            zf_remove_temp( $up_dir["basedir"].'/zombify_temp/'.$unid );

                    } else {

                        if(!(isset($_GET['resumableIdentifier']) && trim($_GET['resumableIdentifier'])!='')){
                            $_GET['resumableIdentifier']='';
                        }
                        $temp_dir = $temp_dir.$_GET['resumableIdentifier'].'/';
                        if(!(isset($_GET['resumableFilename']) && trim($_GET['resumableFilename'])!='')){
                            $_GET['resumableFilename']='';
                        }
                        if(!(isset($_GET['resumableChunkNumber']) && trim($_GET['resumableChunkNumber'])!='')){
                            $_GET['resumableChunkNumber']='';
                        }
                        $chunk_file = $temp_dir.$_GET['resumableFilename'].'.part'.$_GET['resumableChunkNumber'];
                        if (file_exists($chunk_file)) {
                            header("HTTP/1.0 200 Ok");
                        } else {
                            header("HTTP/1.0 404 Not Found");
                        }

                    }
                }

                if (!empty($_FILES))
                    foreach ($_FILES as $file) {

	                    $file_ext = strtolower( pathinfo($file['name'], PATHINFO_EXTENSION) );
						$allowed_mimes = array_merge(
							zombify()->get_allowed_video_extensions(),
							zombify()->get_allowed_audio_extensions()
						);

						$allowed_mimes = array_reduce(
							$allowed_mimes,
							function($allowed_mimes_arr, $mime) {
								switch ($mime) {
									case 'mp3':
										$allowed_mimes_arr['mp3'] = 'audio/mpeg';
										break;
									case 'mp4':
										$allowed_mimes_arr['mp4'] = 'video/mp4';
										break;
									case 'webm':
										$allowed_mimes_arr['webm'] = 'video/webm';
										break;
								}

								return $allowed_mimes_arr;
							},
							array()
						);

	                    $file_type_ext = wp_check_filetype_and_ext( $file['name'], $file['name'], $allowed_mimes );
						if (! $file_type_ext['ext'] || ! $file_type_ext['type'] ) {
							throw new Exception('Uploaded file must be video.');
						}

                        // check the error status
                        if ($file['error'] != 0) {
                            throw new Exception('error '.$file['error'].' in file '.$_POST['resumableFilename']);
                            continue;
                        }

                        // init the destination file (format <filename.ext>.part<#chunk>
                        // the file is stored in a temporary directory
                        if(isset($_POST['resumableIdentifier']) && trim($_POST['resumableIdentifier'])!=''){
                            $temp_dir = $temp_dir.$_POST['resumableIdentifier'].'/';
                        }

                        $dest_file = $temp_dir.sanitize_file_name($_POST['resumableFilename']  ).'.part'.$_POST['resumableChunkNumber'];

                        // create the temporary directory
                        if (!is_dir($temp_dir)) {
                            mkdir($temp_dir, 0755, true);
                        }

                        // move the temporary file
                        if (!move_uploaded_file($file['tmp_name'], $dest_file)) {
                            throw new Exception('Error saving (move_uploaded_file) chunk '.$_POST['resumableChunkNumber'].' for file '.$_POST['resumableFilename']);
                        } else {
                            // check if all the parts present, and create the final destination file
                            $res = zf_createFileFromChunks($temp_dir, sanitize_file_name($_POST['resumableFilename']),$_POST['resumableChunkSize'], $_POST['resumableTotalSize'],$_POST['resumableTotalChunks']);

                            if( $res ){

                                $file_ext = strtolower( pathinfo($res, PATHINFO_EXTENSION) );

                                $is_video = in_array( $file_ext, zombify()->get_allowed_video_extensions() );
                                $is_audio = in_array( $file_ext, zombify()->get_allowed_audio_extensions() );

                                if( $is_video || $is_audio ) {

                                    if( ( filesize($res) <= zombify()->get_video_upload_max_size() && $is_video ) || ( filesize($res) <= zombify()->get_audio_upload_max_size() && $is_audio ) ) {

                                        $file_type_ext = wp_check_filetype_and_ext($res, basename($res) );

                                        $attach_data = zf_insert_attachment( 0, $res, $file_type_ext["type"] );
                                        $attach_id = $attach_data["id"];


                                        return json_encode([
                                            "result" => 1,
                                            "field_path" => htmlspecialchars($_GET["field_path"]),
                                            "unid" => htmlspecialchars($_GET["unid"]),
                                            "file_url" => wp_get_attachment_url($attach_id),
                                            "attachment_id" => $attach_id,
                                        ]);

                                    } else {

                                        return json_encode([
                                            "result" => 0,
                                            "error" => __("File size is too big.", "zombify"),
                                            "errorMessage" => __("File size is too big.", "zombify"),
                                        ]);

                                    }

                                } else {

                                    return json_encode([
                                        "result" => 0,
                                        "error" => __("Incorrect audio/video format.", "zombify"),
                                        "errorMessage" => __("Incorrect audio/video format.", "zombify"),
                                    ]);

                                }

                            }
                        }
                    }


            } catch (Exception $e) {

                error_log( $e );

                return json_encode([
                    "result" => 0,
                    "error" => $e->getMessage()
                ]);
            }

            return json_encode([
                "result" => 0,
                "error" => "",
            ]);

        }

    }

    function zombify_public_frontend_controller( $action = '', $default_action = '' )
    {
        return Zombify_Public_Frontend_Controller::get_instance()->action( $action, $default_action );
    }

}