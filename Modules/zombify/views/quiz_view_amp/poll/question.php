<?php
$question_voted =  isset($_COOKIE["zf_poll_vote_".$question["question_id"]]) ? true : false;
$question_voted_count = isset($zombify_poll_results["groups"][$question["question_id"]]) ? $zombify_poll_results["groups"][$question["question_id"]] : 0;
?>
<li class="zfSetItem">
    <?php include zombify()->locate_template( zombify()->quiz_view_dir('poll/header.php'));?>
    <?php if( isset($question["answers"]) ) { ?>
        <form id="zfPoll_<?php echo $question["question_id"]; ?>"
			<?php if(!$question_voted) { ?>
				zf-amp-class="(zfAmpPollDone_<?php echo $question["question_id"]; ?> == null ? '' : zfAmpPollDone_<?php echo $question["question_id"]; ?>)"
			<?php } else { ?>
				class="zfPollDone"
			<?php } ?>
			method="post" target="_blank"
            action-xhr="<?php echo admin_url('admin-ajax.php') ?>?action=poll_vote">
            <ol class="zfList zfPoll <?php echo $answersType.' '.$answersCol;?>">
                <?php
                foreach( $question["answers"] as $answer ){

                    $answer_voted_count = isset( $zombify_poll_results["answers"][ $answer["answer_id"] ] ) ? $zombify_poll_results["answers"][ $answer["answer_id"] ] : 0;

                    $answer_percentage = $question_voted_count > 0 ? round(($answer_voted_count * 100)/$question_voted_count) : 0;

                    add_filter( 'zf_amp_poll_state', function ( $poll_state ) use ($question, $answer, $answer_voted_count) {

                        if( !isset( $poll_state [ $question["question_id"] ] ) ){
                            $poll_state [ $question["question_id"] ] = array();
                        }

                        $poll_state [ $question["question_id"] ][ $answer["answer_id"] ] = $answer_voted_count;

                        return $poll_state;
                    }, 10, 1 );
                    ?>
                    <li class="zfItem <?php if( isset($_COOKIE["zf_poll_vote_ans_".$answer["answer_id"]]) ) echo 'zfItemDone';?>">
                        <label class="zfItemChoice" for="zfRadio_<?php echo $answer["answer_id"]?>">
                            <?php
                            $answers_arr = array();
                            foreach( $question["answers"] as $temp_answer ){
                                $answers_arr[] = $temp_answer["answer_id"];
                            }
                            ?>
							<?php // poll type media
							if($answersType == "zfPollMedia") {
								$zf_media_html = '';
								if (isset(zf_array_values($answer["image"])[0]["attachment_id"])) {
									$zf_media_html = zombify_get_img_tag( zf_array_values($answer["image"])[0]["attachment_id"], 'zombify_small' );
								} ?>
								<div class="zfMedia">
									<?php
									echo $zf_media_html;

									if (isset($answer["image_credit"]) && $answer["image_credit"]) { ?>
										<cite class="zfCredit"><a href="<?php echo $answer["image_credit"]; ?>" target="_blank" rel="nofollow noopener"><?php echo (isset($answer["image_credit_text"]) && $answer["image_credit_text"]) ? $answer["image_credit_text"] : __('Credit', 'zombify'); ?></a></cite>
									<?php } ?>

									<?php if(!$question_voted){ ?>
									<span class="zfBtnCheck zfCheckT2"><span class="zf-icon zf-icon-check"></span><input name="id" value="<?php echo $answer["answer_id"]; ?>" type="radio" id="zfRadio_<?php echo $answer["answer_id"]?>" role="" tabindex=""
                                         <?php
                                         if(!$question_voted){
                                         ?>on="change:zfPoll_<?php echo $question["question_id"]; ?>.submit,AMP.setState({<?php
                                         $future_total_votes = $question_voted_count + 1;

                                         // set vote persent values
                                         foreach ($question["answers"] as $ans) {
                                             $future_votes = isset( $zombify_poll_results["answers"][ $ans["answer_id"] ] ) ? $zombify_poll_results["answers"][ $ans["answer_id"] ] : 0;
                                             if($answer["answer_id"] === $ans["answer_id"]){
                                                 $future_votes ++;
                                             }
                                             $future_persent = $future_total_votes > 0 ? round(( $future_votes * 100 )/$future_total_votes) : 0;

                                             echo "zfAmpVoteCount_".$ans["answer_id"].":"."'".$future_persent."', ";
                                             //echo "zfAmpVoteBar_".$ans["answer_id"].":"."'zf".$future_persent."', ";
                                         }

                                         // set total votes count
                                         echo "zfAmpTotalCount_".$question["question_id"].":"."'".$future_total_votes."', ";

                                         // set poll done class
                                         echo "zfAmpPollDone_".$question["question_id"].":'zfPollDone'";
                                         ?>}), <?php
                                         // show vote percent values
                                         foreach ($question["answers"] as $ans) {
                                             echo "zfVoteResult_".$ans["answer_id"].".show, ";
                                         }
                                         ?>zfTotalVotes_<?php echo $question["question_id"];?>.show, zfShareTitle_<?php echo $question["question_id"];?>.show, zfShare_<?php echo $question["question_id"];?>.show"<?php
                                        }?>></span>
									<span id="zfVoteResult_<?php echo $answer["answer_id"];?>" class="zfVoteResult" hidden>
										<span class="zfVoteCount" zf-amp-text="(zfAmpVoteCount_<?php echo $answer["answer_id"];?> == null ? '' : zfAmpVoteCount_<?php echo $answer["answer_id"];?>+'%')"></span>
										<span class="zfVoteBar" zf-amp-class="(zfAmpVoteCount_<?php echo $answer["answer_id"];?> == null ? 'zfVoteBar' : 'zfVoteBar zf'+zfAmpVoteCount_<?php echo $answer["answer_id"];?>)"></span>
									</span>
									<?php } else { ?>
									<span id="zfVoteResult_<?php echo $answer["answer_id"];?>" class="zfVoteResult">
										<span class="zfVoteCount"><?php echo $answer_percentage;?>%</span>
										<span class="zfVoteBar zf<?php echo $answer_percentage;?>"></span>
									</span>
									<?php } ?>
								</div>
								<!--todo:frontend -->
								<div class="zf-answer_text">
									<?php echo $answer["answer_text"]; ?>
								</div>
							<?php // poll type text
							} else { ?>
								<span class="zfItemText"><?php echo $answer["answer_text"]; ?></span>
								<?php if(!$question_voted){ ?>
									<span class="zfBtnCheck zfCheckT1"><span class="zf-icon zf-icon-check"></span><input name="id" value="<?php echo $answer["answer_id"]; ?>" type="radio" id="zfRadio_<?php echo $answer["answer_id"]?>" role="" tabindex=""
                                         <?php
                                         if(!$question_voted){
                                         ?>on="change:zfPoll_<?php echo $question["question_id"]; ?>.submit,AMP.setState({<?php
                                         $future_total_votes = $question_voted_count + 1;
                                         // set vote persent values

                                         foreach ($question["answers"] as $ans) {
                                             $future_votes = isset( $zombify_poll_results["answers"][ $ans["answer_id"] ] ) ? $zombify_poll_results["answers"][ $ans["answer_id"] ] : 0;
                                             if($answer["answer_id"] === $ans["answer_id"]){
                                                 $future_votes ++;
                                             }
                                             $future_persent = $future_total_votes > 0 ? round(( $future_votes * 100 )/$future_total_votes) : 0;

                                             echo "zfAmpVoteCount_".$ans["answer_id"].":"."'".$future_persent."', ";
                                             //echo "zfAmpVoteBar_".$ans["answer_id"].":"."'zf".$future_persent."', ";
                                         }

                                         // set total votes count
                                         echo "zfAmpTotalCount_".$question["question_id"].":"."'".$future_total_votes."', ";

                                         // set poll done class
                                         echo "zfAmpPollDone_".$question["question_id"].":'zfPollDone'";
                                         ?>}), <?php
                                         // show vote percent values
                                         foreach ($question["answers"] as $ans) {
                                             echo "zfVoteResult_".$ans["answer_id"].".show, ";
                                         }
                                         ?>zfTotalVotes_<?php echo $question["question_id"];?>.show, zfShareTitle_<?php echo $question["question_id"];?>.show, zfShare_<?php echo $question["question_id"];?>.show"<?php
                                        }?>></span>
									<span id="zfVoteResult_<?php echo $answer["answer_id"];?>" class="zfVoteResult" hidden>
										<span class="zfVoteCount" zf-amp-text="(zfAmpVoteCount_<?php echo $answer["answer_id"];?> == null ? '' : zfAmpVoteCount_<?php echo $answer["answer_id"];?>+'%')"></span>
										<span class="zfVoteBar" zf-amp-class="(zfAmpVoteCount_<?php echo $answer["answer_id"];?> == null ? 'zfVoteBar' : 'zfVoteBar zf'+zfAmpVoteCount_<?php echo $answer["answer_id"];?>)"></span>
									</span>
								<?php } else { ?>
									<span id="zfVoteResult_<?php echo $answer["answer_id"];?>" class="zfVoteResult">
										<span class="zfVoteCount"><?php echo $answer_percentage;?>%</span>
										<span class="zfVoteBar zf<?php echo $answer_percentage;?>"></span>
									</span>
								<?php } ?>
							<?php } ?>
                        </label>

                        <input type="hidden" name="amp" value="1">
                        <input type="hidden" name="action" value="zombify_poll_vote">
                        <input type="hidden" name="post_id" value="<?php echo get_the_ID() ?>">
                        <input type="hidden" name="group_id" value="<?php echo $question["question_id"]; ?>">
                        <input type="hidden" name="answers" value="<?php echo implode(",", $answers_arr); ?>">
                    </li>
                <?php
                }
                ?>
            </ol>
        </form>
    <?php } ?>
    <?php include zombify()->locate_template( zombify()->quiz_view_dir('poll/footer.php')); ?>
</li>