	<?php
    if( isset($data["questions"]) && count($data["questions"]) > 0 ){
        ?>
        <h2><?php echo zf_array_values( $data["questions"] )[0]["question"] ?></h2>
    <?php
    }
    ?>

    <p class="zf-quiz zf-trivia_quiz zf-show-answer zf-numbered">
       <a class="zfBtnLg" href="<?php echo get_permalink(); ?>"><?php esc_html_e( 'Take a Quiz', 'zombify' ); ?></a>
    </p>

