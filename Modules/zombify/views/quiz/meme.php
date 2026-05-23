<?php $disable_meme_templates = zf_get_option('zombify_disable_meme_templates', 0); ?>
<div id="zombify-main-section" class="zombify-main-section zf-meme zombify-screen">
    <?php
    $zf_config = zombify()->get_config();
    global $zf_excerpt_characters_limit;

    switch ($action) {
        case "create":
            ?><h2 class="zf-global-title"><?php printf( esc_html__("Create %s", "zombify"), $zf_config['zf_post_types']['meme']['name'] ); ?></h2><?php
            break;
        case "update":
            ?><h2 class="zf-global-title"><?php printf( esc_html__("Update %s", "zombify"), $zf_config['zf_post_types']['meme']['name'] ); ?></h2><?php
            break;
    }

    ?>
    <div class="zf-container">
        <form id="zombify-form" method="post" action="" enctype="multipart/form-data">

            <?php include zombify()->locate_template('quiz/option-panel.php'); ?>

            <div id="zf-main" class="zf-main">
                <?php if($action == 'create' && !isset($_POST["zombify"]) && !$this->virtual ) {
                    ?>
                    <div class="zf-start zf-open">
                        <div class="zf-uploader">
                            <label class="zf-image-label" for="meme_image">
                                <div class="zf-extensions">
                                    <span>png</span>
                                    <span>jpg</span>
                                </div>

                                <div class="zf-label">
                                    <i class="zf-icon zf-icon-type-meme"></i>
                                    <span class="zf-label_text"><?php esc_html_e( 'Browse Image', 'zombify' ); ?></span>
                                    <?php if( !$disable_meme_templates ) { ?>
                                        <span class="zf_or "><?php esc_html_e( 'or', 'zombify' ); ?></span>
                                        <a class="zf-meme-popup-btn" href="#"><?php esc_html_e( 'Choose a popular meme template', 'zombify' ); ?></a>
                                    <?php } ?>
                                </div>
                            </label>
                        </div>
                    </div>
                    <?php
                } ?>

                <div class="zf-after-start">
                    <?php echo $this->PostIDHiddenInput() ?>
                    <?php echo $this->QuizTypeHiddenInput() ?>

                    <div class="zf-info-wrapper">
                        <?php
                        echo $this->renderField(['title'], '', 0, $this->data, array(), '', array('showPlaceholder'=>true, 'showLabel'=>false));
                        echo $this->renderField(['description'], '', 0, $this->data, array(), '', array('showPlaceholder'=>true, 'showLabel'=>false));
                        echo $this->renderField(['meme_template'], '', 0, $this->data, array('class' => 'meme-template'), '', array('showPlaceholder'=>false, 'showLabel'=>false));
                        ?>

                        <div class="zf-preface-excerpt-cont">
                            <?php
                                if( $zf_config['zf_post_types']['meme']['excerpt'] === 1 ) {
                                    echo $this->renderField(['use_excerpt'], '', 0, $this->data, array(), '', array('showPlaceholder' => false, 'showLabel' => true));
                                }
                                if( $zf_config['zf_post_types']['meme']['preface'] === 1 ) {
                                    echo $this->renderField(['use_preface'], '', 0, $this->data, array(), '', array('showPlaceholder' => false, 'showLabel' => true));
                                }
                            ?>
                        </div>
                        <div class="zf-excerpt <?php if( ( isset( $this->data["use_excerpt"] ) && $this->data["use_excerpt"] ) || $zf_config['zf_post_types']['meme']['excerpt'] === 0 ) echo 'zf-open'; ?>">
                            <div class="zf-excerpt_inner">
                                <?php echo $this->renderField(['excerpt_description'], '', 0, $this->data, array('maxlength' => $zf_excerpt_characters_limit), '', array('showPlaceholder' => true, 'showLabel' => false)); ?>
                            </div>
                        </div>
                        <div class="zf-preface <?php if( ( isset( $this->data["use_preface"] ) && $this->data["use_preface"] ) || $zf_config['zf_post_types']['meme']['preface'] === 0 ) echo 'zf-open'; ?>">
                            <div class="zf-preface_inner">
                                <?php echo $this->renderField(['preface_description'], '', 0, $this->data, array('class' => 'zf-wysiwyg-advanced'), '', array('showPlaceholder' => true, 'showLabel' => false)); ?>
                            </div>
                        </div>
                    </div>

                    <div id="zf-meme" class="<?php echo ($action == 'create' && !isset($_POST["zombify"]) && !$this->virtual ) ? 'zf-create' : ''; ?> zf-form-group zf-form-group_media">
                        <div id="zf-memecontainer" class="zf-memecontainer">
                            <?php
                            if( isset($this->data["image_image"]) && is_array($this->data["image_image"]) && count($this->data["image_image"])>0 ){

                                $img_url = wp_get_attachment_url(zf_array_values($this->data["image_image"])[0]["attachment_id"]);

                            } elseif( isset($_GET['post_id']) && isset($this->data["meme_template"]) ) {

                                $post_id            = $_GET['post_id'];
                                $image_path         = zf_modify_meme_template_url( $this->data['meme_template'] );
                                $image_url_hash     = md5( $image_path );
                                $downloaded_images  = zf_get_downloaded_attachment( $post_id );

                                if( isset( $downloaded_images[$image_url_hash] ) ) {
                                    $img_url = wp_get_attachment_url($downloaded_images[$image_url_hash]);
                                } else {
                                    $img_url = '';
                                }

                            } else {
                                $img_url = '';
                            }
                            ?>
                            <img id="zf-meme-img" crossOrigin="anonymous" src="<?php echo $img_url; ?>" alt="">

                            <div class="zf-drag-area zf-top-drag" data-rel="1">
                                <div id="zf-drag-1" class="zf-drag"></div>
                                <textarea id="zf-top-text" class="zf-write" data-rel="1"
                                          placeholder="<?php esc_attr_e( 'Top Text', 'zombify' ); ?>" autocomplete="off"></textarea>

                                <div class="zf-options">
                                    <a class="zf-options_toggle" href="#"><i class="zf-icon zf-icon-options"></i></a>
                                    <ul class="zf-structure-list">
                                        <li class="zf-optColor">
                                            <span class="zf-label"><?php esc_html_e( 'Color', 'zombify' ); ?></span>

                                            <div class="zf-option">
                                                <div class="zf-colorPicker">
                                                    <input class="zf-color_input zf-opt_fontColor" data-rel="1"
                                                           type="hidden" value="">
                                                    <span class="zf-current_color"
                                                          style="background-color: #ffffff"></span>

                                                    <div class="zf-color_popup">
                                                        <span class="zf-color" style="background-color: #ffffff"
                                                              rel="#ffffff"></span>
                                                        <span class="zf-color" style="background-color: #000000"
                                                              rel="#000000"></span>
                                                        <span class="zf-color" style="background-color: #727272"
                                                              rel="#727272"></span>
                                                        <span class="zf-color" style="background-color: #CC0000"
                                                              rel="#CC0000"></span>
                                                        <span class="zf-color" style="background-color: #FF5252"
                                                              rel="#FF5252"></span>
                                                        <span class="zf-color" style="background-color: #FF00FF"
                                                              rel="#FF00FF"></span>
                                                        <span class="zf-color" style="background-color: #FF6D00"
                                                              rel="#FF6D00"></span>
                                                        <span class="zf-color" style="background-color: #0000CC"
                                                              rel="#0000CC"></span>
                                                        <span class="zf-color" style="background-color: #536DFE"
                                                              rel="#536DFE"></span>
                                                        <span class="zf-color" style="background-color: #18FFFF"
                                                              rel="#18FFFF"></span>
                                                        <span class="zf-color" style="background-color: #00E676"
                                                              rel="#00E676"></span>
                                                        <span class="zf-color" style="background-color: #FFEA00"
                                                              rel="#FFEA00"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="zf-optFont">
                                            <span class="zf-label"><?php esc_html_e( 'Font Size', 'zombify' ); ?></span>

                                            <div class="zf-option">
                                                <select class="zf-opt_fontSize" data-rel="1">
                                                    <option value="auto"><?php esc_html_e( 'Auto', 'zombify' ); ?></option>
                                                    <option value="15">15px</option>
                                                    <option value="25">25px</option>
                                                    <option value="35">35px</option>
                                                    <option value="45">45px</option>
                                                    <option value="55">55px</option>
                                                    <option value="65">65px</option>
                                                    <option value="80">80px</option>
                                                    <option value="100">100px</option>
                                                </select>
                                            </div>
                                        </li>
                                        <li class="zf-optFontFamily">
                                            <span class="zf-label"><?php esc_html_e( 'Font Family', 'zombify' ); ?></span>

                                            <div class="zf-option">
                                                <select class="zf-opt_fontFamily" data-rel="1">
                                                    <option value="Arial">Arial</option>
                                                    <option value="Georgia">Georgia</option>
                                                    <option value="Cambria">Cambria</option>
                                                    <option value="Constantia">Constantia</option>
                                                    <option value="Palatino Linotype">Palatino Linotype</option>
                                                    <option value="Times New Roman">Times New Roman</option>
                                                    <option value="Times">Times</option>
                                                    <option value="Arial Black">Arial Black</option>
                                                    <option value="Arial Narrow ">Arial Narrow </option>
                                                    <option value="Calibri">Calibri</option>
                                                    <option value="Candara">Candara</option>
                                                    <option value="Corbel">Corbel</option>
                                                    <option value="Droid Sans">Droid Sans</option>
                                                    <option value="Impact" selected="selected">Impact</option>
                                                    <option value="Microsoft Sans Serif">Microsoft Sans Serif</option>
                                                    <option value="Tahoma ">Tahoma </option>
                                                    <option value="Trebuchet MS">Trebuchet MS</option>
                                                    <option value="Verdana">Verdana</option>
                                                    <option value="Comic Sans MS">Comic Sans MS</option>
                                                    <option value="Webdings">Webdings</option>
                                                    <option value="Wingdings">Wingdings</option>
                                                    <option value="Courier New">Courier New</option>
                                                    <option value="Consolas">Consolas</option>
                                                    <option value="Courier">Courier</option>
                                                    <option value="Lucida Console">Lucida Console</option>
                                                </select>
                                            </div>
                                        </li>
                                        <li class="zf-optStroke">
                                            <span class="zf-label"><?php esc_html_e( 'Stroke', 'zombify' ); ?></span>

                                            <div class="zf-option">
                                                <div class="zf-colorPicker">
                                                    <input class="zf-color_input zf-opt_strokeColor" data-rel="1"
                                                           type="hidden" value="">
                                                    <span class="zf-current_color"
                                                          style="background-color: #000000"></span>

                                                    <div class="zf-color_popup">
                                                        <span class="zf-color" style="background-color: #ffffff"
                                                              rel="#ffffff"></span>
                                                        <span class="zf-color" style="background-color: #000000"
                                                              rel="#000000"></span>
                                                        <span class="zf-color" style="background-color: #727272"
                                                              rel="#727272"></span>
                                                        <span class="zf-color" style="background-color: #CC0000"
                                                              rel="#CC0000"></span>
                                                        <span class="zf-color" style="background-color: #FF5252"
                                                              rel="#FF5252"></span>
                                                        <span class="zf-color" style="background-color: #FF00FF"
                                                              rel="#FF00FF"></span>
                                                        <span class="zf-color" style="background-color: #FF6D00"
                                                              rel="#FF6D00"></span>
                                                        <span class="zf-color" style="background-color: #0000CC"
                                                              rel="#0000CC"></span>
                                                        <span class="zf-color" style="background-color: #536DFE"
                                                              rel="#536DFE"></span>
                                                        <span class="zf-color" style="background-color: #18FFFF"
                                                              rel="#18FFFF"></span>
                                                        <span class="zf-color" style="background-color: #00E676"
                                                              rel="#00E676"></span>
                                                        <span class="zf-color" style="background-color: #FFEA00"
                                                              rel="#FFEA00"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="zf-optWidth">
                                            <span class="zf-label"><?php esc_html_e( 'Width', 'zombify' ); ?></span>

                                            <div class="zf-option">
                                                <select class="zf-opt_strokeWidth" data-rel="1">
                                                    <option value="0"><?php esc_html_e( 'None', 'zombify' ); ?></option>
                                                    <option value="1">1px</option>
                                                    <option value="2" selected>2px</option>
                                                    <option value="3">3px</option>
                                                    <option value="4">4px</option>
                                                    <option value="5">5px</option>
                                                    <option value="6">6px</option>
                                                    <option value="7">7px</option>
                                                    <option value="8">8px</option>
                                                    <option value="9">9px</option>
                                                </select>
                                            </div>
                                        </li>
                                        <li class="zf-optTop">
                                            <span class="zf-label"><?php esc_html_e( 'Top', 'zombify' ); ?></span>

                                            <div class="zf-option">
                                                <input class="zf-opt_top" type="number" value="" data-rel="1">
                                            </div>
                                        </li>
                                        <li class="zf-optLeft">
                                            <span class="zf-label"><?php esc_html_e( 'Left', 'zombify' ); ?></span>

                                            <div class="zf-option">
                                                <input class="zf-opt_left" type="number" value="" data-rel="1">
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <canvas id="zf-memecanvas">
                                <?php esc_html_e( 'Sorry, but your browser not support', 'zombify' ); ?>
                            </canvas>
                            <div class="zf-drag-area zf-bottom-drag" data-rel="2">
                                <div class="zf-drag" id="zf-drag-2"></div>
                                <div class="zf-options">
                                    <a class="zf-options_toggle" href="#"><i class="zf-icon zf-icon-options"></i></a>
                                    <ul class="zf-structure-list">
                                        <li class="zf-optColor">
                                            <span class="zf-label"><?php esc_html_e( 'Color', 'zombify' ); ?></span>

                                            <div class="zf-option">
                                                <div class="zf-colorPicker">
                                                    <input class="zf-color_input zf-opt_fontColor" data-rel="2"
                                                           type="hidden" value="">
                                                    <span class="zf-current_color"
                                                          style="background-color: #ffffff"></span>

                                                    <div class="zf-color_popup">
                                                        <span class="zf-color" style="background-color: #ffffff"
                                                              rel="#ffffff"></span>
                                                        <span class="zf-color" style="background-color: #000000"
                                                              rel="#000000"></span>
                                                        <span class="zf-color" style="background-color: #727272"
                                                              rel="#727272"></span>
                                                        <span class="zf-color" style="background-color: #CC0000"
                                                              rel="#CC0000"></span>
                                                        <span class="zf-color" style="background-color: #FF5252"
                                                              rel="#FF5252"></span>
                                                        <span class="zf-color" style="background-color: #FF00FF"
                                                              rel="#FF00FF"></span>
                                                        <span class="zf-color" style="background-color: #FF6D00"
                                                              rel="#FF6D00"></span>
                                                        <span class="zf-color" style="background-color: #0000CC"
                                                              rel="#0000CC"></span>
                                                        <span class="zf-color" style="background-color: #536DFE"
                                                              rel="#536DFE"></span>
                                                        <span class="zf-color" style="background-color: #18FFFF"
                                                              rel="#18FFFF"></span>
                                                        <span class="zf-color" style="background-color: #00E676"
                                                              rel="#00E676"></span>
                                                        <span class="zf-color" style="background-color: #FFEA00"
                                                              rel="#FFEA00"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="zf-optFont">
                                            <span class="zf-label"><?php esc_html_e( 'Font Size', 'zombify' ); ?></span>

                                            <div class="zf-option">
                                                <select class="zf-opt_fontSize" data-rel="2">
                                                    <option value="auto"><?php esc_html_e( 'Auto', 'zombify' ); ?></option>
                                                    <option value="15">15px</option>
                                                    <option value="25">25px</option>
                                                    <option value="35">35px</option>
                                                    <option value="45">45px</option>
                                                    <option value="55">55px</option>
                                                    <option value="65">65px</option>
                                                    <option value="80">80px</option>
                                                    <option value="100">100px</option>
                                                </select>
                                            </div>
                                        </li>
                                        <li class="zf-optFontFamily">
                                            <span class="zf-label"><?php esc_html_e( 'Font Family', 'zombify' ); ?></span>

                                            <div class="zf-option">
                                                <select class="zf-opt_fontFamily" data-rel="2">
                                                    <option value="Arial">Arial</option>
                                                    <option value="Georgia">Georgia</option>
                                                    <option value="Cambria">Cambria</option>
                                                    <option value="Constantia">Constantia</option>
                                                    <option value="Palatino Linotype">Palatino Linotype</option>
                                                    <option value="Times New Roman">Times New Roman</option>
                                                    <option value="Times">Times</option>
                                                    <option value="Arial Black">Arial Black</option>
                                                    <option value="Arial Narrow ">Arial Narrow </option>
                                                    <option value="Calibri">Calibri</option>
                                                    <option value="Candara">Candara</option>
                                                    <option value="Corbel">Corbel</option>
                                                    <option value="Droid Sans">Droid Sans</option>
                                                    <option value="Impact" selected="selected">Impact</option>
                                                    <option value="Microsoft Sans Serif">Microsoft Sans Serif</option>
                                                    <option value="Tahoma ">Tahoma </option>
                                                    <option value="Trebuchet MS">Trebuchet MS</option>
                                                    <option value="Verdana">Verdana</option>
                                                    <option value="Comic Sans MS">Comic Sans MS</option>
                                                    <option value="Webdings">Webdings</option>
                                                    <option value="Wingdings">Wingdings</option>
                                                    <option value="Courier New">Courier New</option>
                                                    <option value="Consolas">Consolas</option>
                                                    <option value="Courier">Courier</option>
                                                    <option value="Lucida Console">Lucida Console</option>
                                                </select>
                                            </div>
                                        </li>
                                        <li class="zf-optStroke">
                                            <span class="zf-label"><?php esc_html_e( 'Stroke', 'zombify' ); ?></span>

                                            <div class="zf-option">
                                                <div class="zf-colorPicker">
                                                    <input class="zf-color_input zf-opt_strokeColor" data-rel="2"
                                                           type="hidden" value="">
                                                    <span class="zf-current_color"
                                                          style="background-color: #000000"></span>

                                                    <div class="zf-color_popup">
                                                        <span class="zf-color" style="background-color: #ffffff"
                                                              rel="#ffffff"></span>
                                                        <span class="zf-color" style="background-color: #000000"
                                                              rel="#000000"></span>
                                                        <span class="zf-color" style="background-color: #727272"
                                                              rel="#727272"></span>
                                                        <span class="zf-color" style="background-color: #CC0000"
                                                              rel="#CC0000"></span>
                                                        <span class="zf-color" style="background-color: #FF5252"
                                                              rel="#FF5252"></span>
                                                        <span class="zf-color" style="background-color: #FF00FF"
                                                              rel="#FF00FF"></span>
                                                        <span class="zf-color" style="background-color: #FF6D00"
                                                              rel="#FF6D00"></span>
                                                        <span class="zf-color" style="background-color: #0000CC"
                                                              rel="#0000CC"></span>
                                                        <span class="zf-color" style="background-color: #536DFE"
                                                              rel="#536DFE"></span>
                                                        <span class="zf-color" style="background-color: #18FFFF"
                                                              rel="#18FFFF"></span>
                                                        <span class="zf-color" style="background-color: #00E676"
                                                              rel="#00E676"></span>
                                                        <span class="zf-color" style="background-color: #FFEA00"
                                                              rel="#FFEA00"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="zf-optWidth">
                                            <span class="zf-label"><?php esc_html_e( 'Width', 'zombify' ); ?></span>

                                            <div class="zf-option">
                                                <select class="zf-opt_strokeWidth" data-rel="2">
                                                    <option value="0"><?php esc_html_e( 'None', 'zombify' ); ?></option>
                                                    <option value="1">1px</option>
                                                    <option value="2" selected>2px</option>
                                                    <option value="3">3px</option>
                                                    <option value="4">4px</option>
                                                    <option value="5">5px</option>
                                                    <option value="6">6px</option>
                                                    <option value="7">7px</option>
                                                    <option value="8">8px</option>
                                                    <option value="9">9px</option>
                                                </select>
                                            </div>
                                        </li>
                                        <li class="zf-optTop">
                                            <span class="zf-label"><?php esc_html_e( 'Top', 'zombify' ); ?></span>

                                            <div class="zf-option">
                                                <input class="zf-opt_top" type="number" value="" data-rel="2">
                                            </div>
                                        </li>
                                        <li class="zf-optLeft">
                                            <span class="zf-label"><?php esc_html_e( 'Left', 'zombify' ); ?></span>

                                            <div class="zf-option">
                                                <input class="zf-opt_left" type="number" value="" data-rel="2">
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <textarea id="zf-bottom-text" class="zf-write" data-rel="2"
                                          placeholder="<?php esc_attr_e( 'Bottom Text', 'zombify' ); ?>" autocomplete="off"></textarea>
                            </div>

                            <?php echo $this->renderField(['readyimage'], '', 0, $this->data, array("id" => "zf-readyImage"), '', array('showPlaceholder'=>false, 'showLabel'=>true)); ?>
                        </div>

                        <div class="zf-media-uploader" data-format="image">
                            <div class="">
                                <div class="zombify_medatype_image">
                                    <?php
                                    echo $this->renderField(['image_image'], '', 0, $this->data,array('id'=>'meme_image'));

                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    echo $this->renderField(['original_source'], '', 0, $this->data, array(), '', array('showPlaceholder'=>false, 'showLabel'=>true));
                    echo $this->renderField(['image_credit'], '', 0, $this->data, array("placeholder"=> esc_attr( __("http://example.com", "zombify")), "rel"=>"nofollow"), '', array('showPlaceholder'=>false, 'showLabel'=>true));
                    echo $this->renderField(['image_credit_text'], '', 0, $this->data, array(), '', array('showPlaceholder'=>false, 'showLabel'=>true));
                    echo $this->renderField(['image_description'], '', 0, $this->data, array('class' => 'zf-wysiwyg-light'), '', array('showPlaceholder'=>false, 'showLabel'=>true));
                    echo $this->renderField(['settings'], '', 0, $this->data, array("id" => "meme_settings"), '', array('showPlaceholder'=>false, 'showLabel'=>true));
                    ?>

                </div>

                <?php include zombify()->locate_template('quiz/save_buttons.php'); ?>

            </div>

        </form>
    </div>
</div>