<div id="zombify-main-section" class="zombify-main-section zf-quiz zombify-trivia-quiz zombify-screen">
    <?php
    $zf_config = zombify()->get_config();
    global $zf_excerpt_characters_limit;

    switch( $action ){
        case "create":
            ?><h2 class="zf-global-title"><?php printf( esc_html__("Create %s", "zombify"), $zf_config['zf_post_types']['trivia']['name'] ); ?></h2><?php
            break;
        case "update":
            ?><h2 class="zf-global-title"><?php printf( esc_html__("Update %s", "zombify"), $zf_config['zf_post_types']['trivia']['name'] ); ?></h2><?php
            break;
    }


    ?>

    <div class="zf-container">
        <form id="zombify-form" method="post" action="" enctype="multipart/form-data">
            <?php include zombify()->locate_template('quiz/option-panel.php'); ?>
            <div id="zf-main" class="zf-main">

                <?php echo $this->PostIDHiddenInput() ?>
                <?php echo $this->QuizTypeHiddenInput() ?>

                <div class="zf-info-wrapper">
                    <?php
                    echo $this->renderField(['title'], '', 0, $this->data, array(), '', array('showPlaceholder'=>true, 'showLabel'=>false));
                    echo $this->renderField(['description'], '', 0, $this->data, array(), '', array('showPlaceholder'=>true, 'showLabel'=>false)); ?>
                    <div class="zf-preface-excerpt-cont">
                        <?php
                            if( $zf_config['zf_post_types']['trivia']['excerpt'] === 1 ) {
                                echo $this->renderField(['use_excerpt'], '', 0, $this->data, array(), '', array('showPlaceholder' => false, 'showLabel' => true));
                            }
                            if( $zf_config['zf_post_types']['trivia']['preface'] === 1 ) {
                                echo $this->renderField(['use_preface'], '', 0, $this->data, array(), '', array('showPlaceholder' => false, 'showLabel' => true));
                            }
                        ?>
                    </div>
                    <div class="zf-excerpt <?php if( ( isset( $this->data["use_excerpt"] ) && $this->data["use_excerpt"] ) || $zf_config['zf_post_types']['trivia']['excerpt'] === 0 ) echo 'zf-open'; ?>">
                        <div class="zf-excerpt_inner">
                            <?php echo $this->renderField(['excerpt_description'], '', 0, $this->data, array('maxlength' => $zf_excerpt_characters_limit), '', array('showPlaceholder' => true, 'showLabel' => false)); ?>
                        </div>
                    </div>
                    <div class="zf-preface <?php if( ( isset( $this->data["use_preface"] ) && $this->data["use_preface"] ) || $zf_config['zf_post_types']['trivia']['preface'] === 0 ) echo 'zf-open'; ?>">
                        <div class="zf-preface_inner">
                            <?php echo $this->renderField(['preface_description'], '', 0, $this->data, array('class' => 'zf-wysiwyg-advanced'), '', array('showPlaceholder' => true, 'showLabel' => false)); ?>
                        </div>
                    </div>
                </div>
                <div class="zf-questions-container zf-item-wrapper">
                    <div class="zf-type-wrapper"><i class="zf-icon zf-icon-question"></i></div>
                    <h3><?php esc_html_e("Add Question",  "zombify"); ?></h3>
                    <div class="zf-questions_container">
                        <?php
                        echo $this->renderGroups(['questions'], '', $this->data);
                        ?>
                    </div>
                    <?php echo $this->renderAddGroupButton(['questions'], esc_html__('Add question', 'zombify')) ?>
                </div>
                <div class="zf-results-container zf-item-wrapper">
                    <div class="zf-type-wrapper"><i class="zf-icon zf-icon-result"></i></div>
                    <h3><?php esc_html_e("Add Results",  "zombify"); ?></h3>

                    <div class="zf-results_container">
                        <?php
                        echo $this->renderGroups(['results'], '', $this->data);
                        ?>
                    </div>
                    <?php echo $this->renderAddGroupButton(['results'], esc_html__('Add Result', 'zombify')) ?>
                </div>


                <?php include zombify()->locate_template('quiz/save_buttons.php'); ?>

            </div>
        </form>
    </div>
</div>