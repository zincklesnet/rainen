<div id="zombify-main-section" class="zombify-main-section zf-story zombify-screen">
    <?php
    $zf_config = zombify()->get_config();
    global $zf_excerpt_characters_limit;

    switch ($action) {
        case "create":
            ?><h2 class="zf-global-title"><?php printf( esc_html__("Create %s", "zombify"), $zf_config['post_sub_types'][$this->subtype]['name'] ); ?></h2><?php
            break;
        case "update":
            ?><h2 class="zf-global-title"><?php printf( esc_html__("Update %s", "zombify"), $zf_config['post_sub_types'][$this->subtype]['name'] ); ?></h2><?php
            break;
    }

    if( $action == 'create' && !$this->virtual ){
        ?>
        <script>
            var story_first_group = '<?= $zf_config['post_sub_types'][$this->subtype]["first_group"] ?>';
        </script>
        <?php
    }
    ?>
    <div class="zf-container">

        <form id="zombify-form" method="post" action="" enctype="multipart/form-data">
            <?php include zombify()->locate_template('quiz/option-panel.php'); ?>

            <div id="zf-main" class="zf-main">
                <div class="zf-after-start">
                    <?php echo $this->PostIDHiddenInput() ?>
                    <?php echo $this->QuizTypeHiddenInput() ?>

                    <div class="zf-info-wrapper">
                        <?php
                        echo $this->renderField(['title'], '', 0, $this->data, array(), '', array('showPlaceholder' => true, 'showLabel' => false));
                        echo $this->renderField(['description'], '', 0, $this->data, array(), '', array('showPlaceholder' => true, 'showLabel' => false));
                        ?>
                        <div class="zf-preface-excerpt-cont">
                            <?php
                                if( $zf_config['post_sub_types'][$this->subtype]['excerpt'] === 1 ) {
                                    echo $this->renderField(['use_excerpt'], '', 0, $this->data, array(), '', array('showPlaceholder' => false, 'showLabel' => true));
                                }
                                if( $zf_config['post_sub_types'][$this->subtype]['preface'] === 1 ) {
                                    echo $this->renderField(['use_preface'], '', 0, $this->data, array(), '', array('showPlaceholder' => false, 'showLabel' => true));
                                }
                            ?>
                        </div>
                        <div class="zf-excerpt <?php if( ( isset( $this->data["use_excerpt"] ) && $this->data["use_excerpt"] ) || $zf_config['post_sub_types'][$this->subtype]['excerpt'] === 0 ) echo 'zf-open'; ?>">
                            <div class="zf-excerpt_inner">
                                <?php echo $this->renderField(['excerpt_description'], '', 0, $this->data, array('maxlength' => $zf_excerpt_characters_limit), '', array('showPlaceholder' => true, 'showLabel' => false)); ?>
                            </div>
                        </div>
                        <div class="zf-preface <?php if( ( isset( $this->data["use_preface"] ) && $this->data["use_preface"] ) || $zf_config['post_sub_types'][$this->subtype]['preface'] === 0 ) echo 'zf-open'; ?>">
                            <div class="zf-preface_inner">
                                <?php echo $this->renderField(['preface_description'], '', 0, $this->data, array('class' => 'zf-wysiwyg-advanced'), '', array('showPlaceholder' => true, 'showLabel' => false)); ?>
                            </div>
                        </div>
                    </div>
                    <?php
                    $aliasGroups = $this->getAliasGroups();
                    if( !is_null( $aliasGroups ) && count($aliasGroups) > 0 ) {
                        ?>
                        <div class="zf-components">
                            <button class="zf-components_plus zf-js-components_toggle">
                                <i class="zf-icon-delete"></i>
                                <i class="zf-icon-add"></i>
                            </button>
                            <div class="zf-components_wrapper">
                                <?php
                                foreach ($aliasGroups as $alias_slug => $alias_group) {

                                    if (!in_array($alias_slug, $zf_config["post_sub_types"][$this->subtype]["formats"]) && count($zf_config["post_sub_types"][$this->subtype]["formats"]) > 0)
                                        continue;

                                    ?>
                                    <a class="zf-add-component zombify_add_group" href="#" data-zf-not-arrange-group="1"
                                       data-zombify-position="first"
                                       data-zombify-group="story" data-zombify-group-path="story___story"
                                       data-include-group="<?php echo $alias_slug; ?>">
                                        <i class="<?php echo $alias_group["icon"]; ?>"></i><span
                                            class="zf-text"><?php echo $alias_group["label"]; ?></span>
                                    </a>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    <?php
                    }
                    ?>

                    <div class="zf-item-wrapper">

                        <div class="zf-erase-before-save" style="display:none;">
                            <?php
                            $data = $this->data;
                            $this->data = array();
                            echo $this->renderGroups(['story'], '', array(), $action, array(), '', '', '', true, 9999);
                            ?>
                        </div>

                        <div class="zf-story_container zf-must-delete">

                            <?php
                            $this->data = $data;
                            echo $this->renderGroups(['story'], '', $this->data, $action, array(), '', '', '', false);
                            ?>
                        </div>
                    </div>


                </div>

                <?php include zombify()->locate_template('quiz/save_buttons.php'); ?>
            </div>
        </form>
    </div>
</div>