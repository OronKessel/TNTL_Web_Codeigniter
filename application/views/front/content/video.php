<div class="main-container" style="padding-top:80px;">
    <div class="video-info">
        <video class="single-video" controls autoplay="on">
            <source src="<?php echo base_url() . $videoInfo->file; ?>" type="video/ogg">
            Your browser does not support HTML5 video.
        </video>
        <p class="video_date_label">
            <?php
            $date = new DateTime($videoInfo->created);
            echo $date->format('M d, Y');
            ?>
        </p>
        <div class="video-account-info">
            <div>
                <p class="video_item_name"><?php echo $videoInfo->video_title; ?></p>
                <div class="single-video-account">
                    <a style="margin-top:auto;margin-bottom:auto;" href="<?php echo base_url(); ?>index.php/Profile/index/<?php echo $videoInfo->memberInfo->username; ?>"><img src="<?php echo base_url(); ?>assets/images/avatar.png" class="video-img"/></a>
                    <div class="single-video-info">
                        <h2 class="bold-text" style="font-size:14px;color:#D8D8D8;"><?php echo $videoInfo->memberInfo->username; ?></h2>                    
                        <?php
                        if ($this->session->userInfo == '') {
                            ?>
                            <button id='btnFollow' data-follow='0' class="btn-follow-small" disabled>Follow</button>
                            <?php
                        } else if ($this->session->userInfo->member_id == $videoInfo->member_id) {
                            ?>
                            <button id='btnFollow' data-follow='0' class="btn-follow-small" disabled>Follow</button>
                            <?php
                        } else {
                            if ($isFollow) {
                                ?>
                                <button id='btnFollow' data-follow='0' class="btn-followed-small">Following</button>
                                <?php
                            } else {
                                ?>
                                <button id='btnFollow' data-follow='0' class="btn-follow-small">Follow</button>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div style="text-align:right;width:100%;">
                <img src="<?php echo base_url(); ?>assets/images/eye.png" style="width:24px;height:15px;"/><span>
                    <label class="video_item_name"><?php echo $videoInfo->view_count; ?></label>
                </span>
                <div class="single-video-control">
                    <div class="single-video-single-control" style="margin-left: auto;">
                        <img src="<?php echo base_url(); ?>assets/images/emoji.png" style="width:17px;height:17px;"/>
                        <p class="video-control-text" style="color:#40a14a;">89%</p>
                        <img src="<?php echo base_url(); ?>assets/images/share.png" style="width:17px;height:17px;margin-top:5px;"/>
                    </div>
                    <div class="single-video-single-control" style="margin-left: 15px;">
                        <?php
                        if ($videoInfo->like) {
                            ?>
                            <img onclick="likeVideo(this)" id='img_like_<?php echo $videoInfo->id; ?>' data-like="1" data-video="<?php echo $videoInfo->id; ?>" src="<?php echo base_url(); ?>assets/images/like_p.png" style="width:17px;height:17px;"/>
                            <?php
                        } else {
                            ?>
                            <img onclick="likeVideo(this)" id='img_like_<?php echo $videoInfo->id; ?>' data-like="0" data-video="<?php echo $videoInfo->id; ?>" src="<?php echo base_url(); ?>assets/images/like.png" style="width:17px;height:17px;"/>
                            <?php
                        }
                        ?>
                        <p class="video-control-text" id='like_<?php echo $videoInfo->id; ?>'><?php echo $videoInfo->lk_count; ?></p>
                        <img src="<?php echo base_url(); ?>assets/images/plus.png" style="width:17px;height:17px;margin-top:5px;"/>
                    </div>
                    <div class="single-video-single-control" style="margin-left: 15px;">
                        <?php
                        if ($videoInfo->unlike) {
                            ?>
                            <img onclick="unlikeVideo(this)" id='img_unlike_<?php echo $videoInfo->id; ?>' data-unlike="1" data-video="<?php echo $videoInfo->id; ?>" name="video-unlike" src="<?php echo base_url(); ?>assets/images/unlike_p.png" style="width:17px;height:17px;"/>
                            <?php
                        } else {
                            ?>
                            <img onclick="unlikeVideo(this)" id='img_unlike_<?php echo $videoInfo->id; ?>' data-unlike="0" data-video="<?php echo $videoInfo->id; ?>" name="video-unlike" src="<?php echo base_url(); ?>assets/images/unlike.png" style="width:17px;height:17px;"/>
                            <?php
                        }
                        ?>
                        <p class="video-control-text" id='unlike_<?php echo $videoInfo->id; ?>'><?php echo $videoInfo->ulk_count; ?></p>
                        <img src="<?php echo base_url(); ?>assets/images/warning.png" style="width:17px;height:17px;margin-top:5px;"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="video-extra-info">
        <div class="video-extra-comment">
            <div class="video-self-comment">
                <img src="<?php echo base_url(); ?>assets/images/avatar.png" class="video-img-small"/>
                <form method="POST" action="<?php echo base_url(); ?>index.php/Video/actionComment">
                    <?php
                    $disable = "disabled";
                    if ($this->session->userInfo != '') {
                        $disable = "";
                    }
                    ?>
                    <div style="display:flex;">
                        <input type="hidden" name="memberId" value="<?php if ($this->session->userInfo != '')
                        echo $this->session->userInfo->member_id;
                    else
                        echo "";
                    ?>"/>
                        <input type="hidden" name="postId" value="<?php echo $videoInfo->id; ?>"/>
                        <input type="text" class="video-comment" name="content" placeholder="Comment here" <?php echo $disable; ?> required/>
                        <button type="submit" class="button-video-comment" <?php echo $disable; ?>>Send</button>
                    </div>
                </form>
            </div>
            <div class="separate-line"></div>
            <div style="position:relative">
                <p class="sort-text" id="sort_button" style="margin-right:10px;">Most Recent</p>
                <div class="sort-content" id="sort-content" style="position:absolute;left:240px"> 
                    <p class="sort-item" id="sort-recent">Most Recent</p>
                    <div class="separate-line" style="margin-top:0px;"></div>
                    <p class="sort-item" id="sort-old">Oldest</p>
                </div>
            </div>
            <div id="video-comments">

            </div>

        </div>
        <div class="video-extra-video">
<?php
foreach ($videos as $video) {
    ?>
                <div class="video-extra-item">
                    <a href="<?php echo base_url() . "index.php/Video/index/" . $video->id; ?>">
                        <video class="video-extra">
                            <source src="<?php echo base_url() . $video->file; ?>" type="video/ogg">
                            Your browser does not support HTML5 video.
                        </video>
                    </a>
                    <div class="video-extra-item-info">
                        <p class="video-extra-item-title"><?php echo $video->video_title; ?></p>
                        <p class="video-extra-item-info1"><?php echo $video->view_count; ?> views <?php echo $video->elapse; ?></p>
                    </div>
                </div>
    <?php
}
?>
        </div>
    </div>
</div>
<script type="text/javascript">
    var baseUrl = '<?= base_url(); ?>';
    var memberId = '';
    var videoId = '<?php echo $videoInfo->id; ?>';
    var profileId = '<?php echo $videoInfo->member_id; ?>';
    var isLogin = "<?php if ($this->session->userInfo == '')
    echo "0";
else
    echo "1";
?>";
    var isFollow = "<?php if ($isFollow)
    echo "1";
else
    echo "0";
?>";
    if (isLogin == '1')
    {
        memberId = "<?php if ($this->session->userInfo == '')
    echo "";
else
    echo $this->session->userInfo->member_id;
?>";
    }
</script>
<script src="<?php echo base_url(); ?>assets/tntl_js/feed.js"></script>
<script src="<?php echo base_url(); ?>assets/tntl_js/video.js"></script>