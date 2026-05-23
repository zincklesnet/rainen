<?php if(!$question_voted){ ?>
<p id="zfTotalVotes_<?php echo $question["question_id"];?>" class="zfTotalVotes" zf-amp-text="(zfAmpTotalCount_<?php echo $question["question_id"];?> == null ? 'null' : zfAmpTotalCount_<?php echo $question["question_id"];?>+' <?php esc_html_e("votes", "zombify"); ?>')" hidden></p>
<h4 id="zfShareTitle_<?php echo $question["question_id"];?>" class="zfShareTitle" hidden><?php esc_html_e("Share Your Result", "zombify"); ?></h4>
<p id="zfShare_<?php echo $question["question_id"];?>" class="zfShare" hidden>
    <!--amp-social-share type="twitter"></amp-social-share-->
    <!--amp-social-share type="facebook"></amp-social-share-->
</p>
<?php } else { ?>
<p id="zfTotalVotes_<?php echo $question["question_id"];?>" class="zfTotalVotes"><?php echo $question_voted_count ?> <?php esc_html_e("votes", "zombify"); ?></p>
<h4 id="zfShareTitle_<?php echo $question["question_id"];?>" class="zfShareTitle"><?php esc_html_e("Share Your Result", "zombify"); ?></h4>
<p id="zfShare_<?php echo $question["question_id"];?>" class="zfShare">
	<!--amp-social-share type="twitter"></amp-social-share-->
	<!--amp-social-share type="facebook"></amp-social-share-->
</p-->
<?php } ?>