<div class="main-container" style="padding-top:80px;">
    <div>
    <?php
    foreach($members as $member)
    {
        ?>
        <a style="text-decoration:none;" href='<?php echo base_url()."index.php/Profile/index/".$member->member_id;?>'>
            <div class="single-video-search2">
                <img src="<?php echo base_url(); ?>assets/images/notification.png" class="img-top-icon"/>
                <div class="search-account-info">
                    <div class="single-video-account">
                        <p class="search-account-name"><?php echo $member->username;?></p>
                        <p class="search-account-follow" style="text-align:right;"><?php echo $member->followings;?> followers</p>
                    </div>
                    <p class="search-account-desc"><?php echo $member->description;?></p>
                </div>
            </div>
        </a>
        <?php
    }
    ?>
    </div>
    <div>
        <?php
        foreach($videos as $video)
        {
            ?>
            <a href="<?php echo base_url()."index.php/Video/index/".$video->id;?>" style="text-decoration:none;">
            <div class="search-video-item">
                <video class="video-search-container">
                    <source src="<?php echo base_url().$video->file;?>" type="video/ogg">
                    Your browser does not support HTML5 video.
                </video>
                <div class="video-extra-item-info">
                    <p class="search-video-title"><?php echo $video->video_title;?></p>
                    <p class="search-video-detail-info"><?php echo $video->view_count;?> views <?php echo $video->elapse;?></p>
                    <div style="display:flex;margin-top:20px;">
                        <img src="<?php echo base_url(); ?>assets/images/notification.png" class="img-search-icon"/>
                        <div style="margin-left:10px;">
                            <p class="search-video-detail-info"><?php echo $video->memberInfo->username;?></p>
                            <p class="search-video-detail-info"><?php echo $video->memberInfo->followings;?> followers</p>
                        </div>
                    </div>
                </div>
            </div>
            </a>
            <?php
        }
        ?>
    </div>
</div>
<script type="text/javascript">
    var baseUrl = '<?= base_url(); ?>';
    var memberId = '';
    var isLogin = "<?php if ($this->session->userInfo == '') echo "0";else echo "1";?>";
    if (isLogin == '1')
    {
        memberId = "<?php  if ($this->session->userInfo == '') echo ""; else echo $this->session->userInfo->member_id;?>";
    }
</script>

<script src="<?php echo base_url();?>assets/tntl_js/feed.js"></script>
<script src="<?php echo base_url();?>assets/tntl_js/profile.js"></script>