<?php
// Get plugin colors
$primary_color = zf_get_option('zombify_branding_color', zombify()->options_defaults["zombify_branding_color"]);
?>

/* icon fonts */
@font-face {
    font-family: 'icomoon';
    src: url('<?php echo zombify()->assets_url ?>amp/icomoon/icomoon.ttf?ta0zbz') format('truetype'),
    url('<?php echo zombify()->assets_url ?>amp/icomoon/icomoon.woff?ta0zbz') format('woff'),
    url('<?php echo zombify()->assets_url ?>amp/icomoon/icomoon.svg?ta0zbz#icomoon') format('svg');
    font-weight: normal;
    font-style: normal;
}

.zf-icon {
    font-family: 'icomoon';
    speak: none;
    font-style: normal;
    font-weight: normal;
    font-variant: normal;
    text-transform: none;
    line-height: 1;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

.zf-icon-check:before {content: "\f00c";}
.zf-icon-pencil:before {content: "\e900";}
.zf-icon-arrow-up:before {content: "\e902";}
.zf-icon-arrow-down:before {content: "\e901";}

/* General */
.zfClear:after {
    content: " ";
    display: table;
    clear: both;
}

#zfWrap {
	margin-bottom: 40px;
}

#zfWrap a,
#zfWrap a:hover {
    text-decoration: none;
}

/* button */
.zfBtn {
    display: inline-block;
    padding: 7px 10px;
    border: 1px solid #e2e2e2;
    border-radius: 3px;
    color: inherit;
    font-size: 12px;
    font-weight: 600;
    line-height: 1.5;
    text-transform: uppercase;
    text-decoration: none;
}

.zfBtn:hover {
    border-color: #000;
}

.zfBtn .zf-icon {
    margin-right: 10px;
    vertical-align: middle;
}

.zfBtnBuy {
    float: right;
    border-radius: 3px;
    color: #0f0d0e;
    font: 600 11px/1.5em sans-serif;
    text-transform: uppercase;
    padding: 3px 13px;
    background: #f2f2f2;
    margin: 5px 0;
}

.zfBtnBuy:hover {
    color: #fff;
    background: #000;
    text-decoration: none;
}

.zfBtnLg {
    display: block;
    padding: 10px 20px;
    border-radius: 7px;
    background: <?php echo $primary_color; ?>;
    color: #fff;
    text-align: center;
    text-transform: uppercase;
    text-decoration: none;

    -webkit-transition: all 100ms ease-out;
    -moz-transition: all 100ms ease-out;
    transition: all 100ms ease-out;
}

.zfBtnLg:hover {
    -webkit-box-shadow: 1px 2px 2px rgba(0,0,0,.18);
    -moz-box-shadow: 1px 2px 2px rgba(0,0,0,.18);
    box-shadow: 1px 2px 2px rgba(0,0,0,.18);
}

/* Titles */
.zfTitle a {
    text-decoration: none;
    color: inherit;
}

.zfTitle a:hover {
    text-decoration: none;
    color: <?php echo $primary_color; ?>;
}

.zfTitleNum {
    padding-left: 1.5em;
    position: relative;
}

.zfNum {
    width: 1.5em;
    color: <?php echo $primary_color; ?>;
    display: block;
    position: absolute;
    left: 0;
    top: 0;
}

/* media */
figure {
    /* todo change selector */
    margin: 0;
}

.zfMedia {
    position: relative;
    margin-bottom: 1.5em;
}

.zfMedia amp-img,
.zfMedia amp-anim,
.zfMedia amp-video {
    background: #f7f7f7;
}

.zfCredit {
    display: inline-block;
    padding: 5px 0;
    color: #757575;
    font: normal 400 12px/1.2em sans-serif;
}

.zfCredit a {
    color: inherit;
    text-decoration: none;
}

/* lists */
.zfList,
.zfListSet {
    margin: 0;
    padding: 0;
    list-style: none;
}

.zfListSet {
    margin-bottom:20px;
}

.zfSetItem {
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid #e2e2e2;
}

.zfListSet .zfSetItem:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: 0;
}

.zfList .zfItem:last-child {
    margin-bottom: 0;
}

/* edit */
.zfEdit { /* todo maybe rename zfFooter */
    margin: 40px 0 0 0;
}

/* Cols */
@media (min-width: 767px) {
    .zf2col,
    .zf3col {
        margin-left: -10px;
        margin-right: -10px;
    }

    .zf2col:after,
    .zf3col:after {
        content: "";
        display: block;
        clear: both;
    }

    .zf2col .zfItem,
    .zf3col .zfItem {
        float: left;
        padding-left: 10px;
        padding-right: 10px;
        box-sizing: border-box;
    }

    .zf2col .zfItem {
        width: 50%;
    }

    .zf3col .zfItem {
        width: 33.33%;
    }

    .zf2col .zfItem:nth-child(2n+1),
    .zf3col .zfItem:nth-child(3n+1) {
        clear: left;
    }

}

/* check buttons */
.zfBtnCheck {
    border: 0;
    margin: 0;
    padding: 0;
    display: block;
    cursor: pointer;
    position: absolute;
    text-align: center;
    background: transparent;
    border-radius: 50%;
    z-index: 2;
    opacity: 0.5;
}

.zfBtnCheck:focus {
    outline: 0;
}

.zfBtnCheck .zf-icon {
    color: #7f6f7f;
    display: inline-block;
}

.zfBtnCheck input[type='radio']{
    display: none;
}

/* type 1 */
.zfCheckT1 {
    top: 8px;
    right: 12px;
    width: 35px;
    height: 35px;
    border: 1px solid #e4e0e4;
}

.zfCheckT1 .zf-icon {
    font-size: 24px;
    padding-top: 5px;
}

/* type 2 */
.zfCheckT2 {
    top: 50%;
    left: 50%;
    width: 70px;
    height: 70px;
    margin: -35px 0 0 -35px;
    background: rgba(255, 255, 255, 0.8);
}

.zfCheckT2 .zf-icon {
    font-size: 36px;
    padding-top: 14px;
}

/* choice label */
.zfItemChoice {
    display: block;
    padding: 14px;
    border-radius: 5px;
    border: 1px solid #e2e2e2;
    transition: box-shadow 100ms ease-out;
    cursor: pointer;
    position: relative;
    box-sizing: border-box;
}

.zfItemChoice:hover {
    box-shadow: 1px 1px 9px #ebebeb;
}

/* poll */
.zfVoteCount {
    top: 50%;
    right: 0;
    position: absolute;
    font-size: 24px;
    font-weight: 600;
    line-height: 1em;

    -webkit-transform: translate(0, -50%);
    -moz-transform: translate(0, -50%);
    transform: translate(0, -50%);
}

.zfVoteBar {
    left: 0;
    bottom: 0;
    height: 100%;
    opacity: 0.7;
    display: block;
    position: absolute;
    background: #49c793;
}

.zfPollDone {
    position: relative;
}

.zfPollDone:before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    z-index: 10;
}

.zfPollDone .zfBtnCheck {
    display: none;
}

/* poll text */
.zfPollText .zfItemChoice {
    line-height: 25px; /* todo - vertically align one line text */
    min-height: 55px;
    padding-right: 90px;
}

.zfPollText .zfItem {
    margin-bottom: 10px;
}

.zfPollText .zfItemText {
    position: relative;
}

.zfPollText .zfItemText,
.zfPollText .zfVoteCount {
    z-index: 1;
}

.zfPollText .zfVoteBar {
    z-index: 0;
}

.zfPollText .zfVoteCount {
    padding-right: 15px;
}

.zfPollText .zfVoteBar {
    border-radius: 5px 0 0 5px;
}

.zfPollText .zfVoteBar.zf100 {
    border-radius: 5px;
}

/* poll media */
.zfPollMedia .zfItem {
    margin-top: 20px; /* todo */
}

.zfPollMedia .zfMedia {
    background: #e2e2e2;
    padding: 100% 0 0 0;
    margin: 0 0 10px 0;
}

.zfPollMedia .zfVotetBar,
.zfPollMedia .zfVoteResult,
.zfPollMedia .zfMedia amp-img {
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: block;
    position: absolute;
}

.zfPollMedia .zfMedia amp-img {
    z-index: 1;
}

.zfPollMedia .zfCredit {
    right: 0;
    bottom: 0;
    position: absolute;
    padding: 3px 5px;
    background: rgba(0, 0, 0, .5);
    font-size: 12px;
    color: #fff;
    z-index: 4;
}

.zfPollMedia .zfVoteResult {
    background: rgba(0, 0, 0, 0.45);
    z-index: 3;
}

.zfPollMedia .zfVoteCount {
    width: 100%;
    color: #fff;
    text-align: center;
    z-index: 1;
}

.zfPollMedia .zfVoteBar {
    z-index: 0;
}

/* share */
.zfShareTitle {
    margin: 15px 0;
    padding: 10px 0 0 0;
    border-top: 1px solid #e2e2e2;
    font-size: 20px;
}

.zfShare {
    margin: 0;
}

/* total votes */
.zfTotalVotes {
    font-size: 13px;
    text-align: right;
    font-weight: 600;
    padding: 0px 12px;
    margin: 15px 0 0 0;
}

/* percents */
.zf1{width:1%}.zf2{width:2%}.zf3{width:3%}.zf4{width:4%}.zf5{width:5%}.zf6{width:6%}.zf7{width:7%}.zf8{width:8%}.zf9{width:9%}.zf10{width:10%}.zf11{width:11%}.zf12{width:12%}.zf13{width:13%}.zf14{width:14%}.zf15{width:15%}.zf16{width:16%}.zf17{width:17%}.zf18{width:18%}.zf19{width:19%}.zf20{width:20%}.zf21{width:21%}.zf22{width:22%}.zf23{width:23%}.zf24{width:24%}.zf25{width:25%}.zf26{width:26%}.zf27{width:27%}.zf28{width:28%}.zf29{width:29%}.zf30{width:30%}.zf31{width:31%}.zf32{width:32%}.zf33{width:33%}.zf34{width:34%}.zf35{width:35%}.zf36{width:36%}.zf37{width:37%}.zf38{width:38%}.zf39{width:39%}.zf40{width:40%}.zf41{width:41%}.zf42{width:42%}.zf43{width:43%}.zf44{width:44%}.zf45{width:45%}.zf46{width:46%}.zf47{width:47%}.zf48{width:48%}.zf49{width:49%}.zf50{width:50%}.zf51{width:51%}.zf52{width:52%}.zf53{width:53%}.zf54{width:54%}.zf55{width:55%}.zf56{width:56%}.zf57{width:57%}.zf58{width:58%}.zf59{width:59%}.zf60{width:60%}.zf61{width:61%}.zf62{width:62%}.zf63{width:63%}.zf64{width:64%}.zf65{width:65%}.zf66{width:66%}.zf67{width:67%}.zf68{width:68%}.zf69{width:69%}.zf70{width:70%}.zf71{width:71%}.zf72{width:72%}.zf73{width:73%}.zf74{width:74%}.zf75{width:75%}.zf76{width:76%}.zf77{width:77%}.zf78{width:78%}.zf79{width:79%}.zf80{width:80%}.zf81{width:81%}.zf82{width:82%}.zf83{width:83%}.zf84{width:84%}.zf85{width:85%}.zf86{width:86%}.zf87{width:87%}.zf88{width:88%}.zf89{width:89%}.zf90{width:90%}.zf91{width:91%}.zf92{width:92%}.zf93{width:93%}.zf94{width:94%}.zf95{width:95%}.zf96{width:96%}.zf97{width:97%}.zf98{width:98%}.zf99{width:99%}.zf100{width:100%}

/* author */
.zfUser {
    display: inline-block;
    position: relative;
    margin: 5px 0;
    padding: 5px 0 0 50px;
    min-height: 40px;
    line-height: 1;
}

.zfUserImg {
    width: 40px;
    display: inline-block;
    position: absolute;
    left: 0;
    top: 0;
}

.zfUserImg amp-img {
    border-radius: 50%;
}

.zfUserName {
    display: block;
    font: 500 16px/1em sans-serif;
    color: #0f0d0e;
}

.zfDate {
    color: #989898;
    font: 400 12px/1em sans-serif;
}

/* Vote Box */
.zf-item_meta {
    padding: 10px 0;
}

.zfUser,
.zf-item-vote-box {
    float: left;
    width: 50%;
}

.zf-item-vote-box {
    text-align: right;
    min-height: 35px;
}

.zf-item-vote {
    position: relative;
    display: inline-block;
    padding: 0 35px;
    text-align: center;
}

.zf-vote_btn {
    position: absolute;
    top: 0;
    width: 35px;
    height: 35px;
    color: #757575;
    font-size: 17px;
    vertical-align: middle;
    text-align: center;
    border-radius: 50%;
    background-color: transparent;
    padding: 3px 0 0 0;
    margin: 0;
    border: 2px solid #e4e0e4;
    z-index: 2;
}

.zf-vote_up {
    left: 0;
}

.zf-vote_down {
    right: 0;
}

.zf-vote_count,
.zf-vote_number {
    display: block;
    min-width: 50px;
    font: 400 30px/35px sans-serif;
    color: #0f0d0e;
    text-align: center;
}

.zf-vote-form .zf-vote_number {
    position: absolute;
    top: 0;
    left: 35px;
    right: 35px;
    z-index: 0;
}

.zf-ripple {
    position: absolute;
    left: 50%;
    top: 50%;
    margin-top: -17px;
    margin-left: -17px;
    visibility: hidden;
}

.zf-item-vote-box.zf-voted .zf-vote_number,
.zf-item-vote-box.zf-voting .zf-vote_number { visibility: hidden; }
.zf-item-vote-box.zf-voting .zf-ripple { visibility: visible; }
.zf-item-vote-box.zf-voted-up .zf-vote_number_up,
.zf-item-vote-box.zf-voted-down .zf-vote_number_down { visibility: visible; }

.zf-vote_down:hover,
.zf-vote_down:focus,
.zf-voted-down .zf-vote_down,
.zf-voting-down .zf-vote_down {
    border-color: #d9534f;
}

.zf-vote_up:hover,
.zf-vote_up:focus,
.zf-voted-up .zf-vote_up,
.zf-voting-up .zf-vote_up {
    border-color: #49c793;
}

/* Link */
.zfLink {
    clear: both;
    margin: 20px auto;
    width: 90%;
    padding: 5%;
    border: 1px solid #e2e2e2;
}