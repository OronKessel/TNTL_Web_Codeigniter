<div class="main-container" style="padding-top:80px;">
    <div class="profile-info">
        <img src="<?php echo base_url(); ?>assets/images/avatar.png" class="profile-img"/>
        <div class="profile-account">        
            <h2 class="bold-text" style="font-size:23px;"><?php echo $userInfo->username;?></h2>
            <p><?php echo $userInfo->description;?></p>
            <?php
            if ($this->session->userInfo == '')
            {
                ?>
                <button id='btnFollow' data-follow='0' class="btn-follow" disabled>Follow</button>
                <?php
            }
            else if ($this->session->userInfo->member_id == $userInfo->member_id)
            {
                ?>
                <button id='btnFollow' data-follow='0' class="btn-follow" disabled>Follow</button>
                <?php
            }
            else{
                if ($isFollow)
                {
                    ?>
                    <button id='btnFollow' data-follow='1' class="btn-followed">Following</button>
                    <?php
                }
                else
                {
                    ?>
                    <button id='btnFollow' data-follow='0' class="btn-follow">Follow</button>
                    <?php
                }
            }
            ?>
        </div>
    </div>
    <div class="separate-line"></div>
    <div class="profile-info">
        <div class="profile-following">
            <h3 class="profile-count-title1"><?php echo count($videos);?></h3>
            <p class="profile-count-title">Videos</p>
        </div>
        <div class="profile-following">
            <h3 class="profile-count-title1" id='following_count'><?php echo $followings;?></h3>
            <p class="profile-count-title">Followers</p>
        </div>
        <div class="profile-following">
            <h3 class="profile-count-title1" id='follower_count'><?php echo $followers;?></h3>
            <p class="profile-count-title">Following</p>
        </div>
    </div>
    <div class="separate-line"></div>
    <div style="margin-top:10px; display:flex;" class="center">
        <label class="tab_home_item" id='tab_collection' data-tab-index='0' style="margin-left:auto;">Collection</label>                
        <div style="width:1px;background:#a8a8a8;"></div>
        <label class="tab_home_item" id='tab_feed' data-tab-index='1' style="margin-right:auto;min-width:70px;">Feed</label>                
    </div>
    <div style="margin-top:10px;" class="center">
        <p class="textcolor-standard">Order by:<span>Recent</span></p>
    </div>
    <div class="profile-collection-container" id='collection-container'>
        <?php
        foreach($videos as $video)
        {
            ?>
            <div class="profile-collection-item">
                <a href="<?php echo base_url()."index.php/Video/index/".$video->id;?>">
                    <video id='video_" + (currentFeedCount + key) + "' class="profile-collection-video">
                        <source src="<?php echo base_url().$video->file;?>" type="video/ogg">
                        Your browser does not support HTML5 video.\n\
                    </video>
                </a>
                <div class="profile-collection-item-info">
                    <p style="margin:5px 0px;"><?php echo $video->video_title;?></p>
                    <p style="margin:5px 0px;"><?php echo $video->view_count;?> views <?php echo $video->elapse;?></p>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
    <div class="profile-feed-container" id='feed-container'>
    <?php
        foreach($videos as $video)
        {
            ?>
            <div class="feed-container">
                <div class="side-feed-bar">
                    <a href="">
                        <img src="<?php echo base_url();?>assets/images/avatar.png" class="avatar-img"/></a>
                        <div class="left-info">
                            <img src="<?php echo base_url();?>assets/images/eye.png" class="img-eye"/>
                            <p class="lbl-standard"><?php echo $video->view_count;?></p>
                        </div>
                        <div class="left-info">
                            <img src="<?php echo base_url();?>assets/images/emoji.png" class="img-feed-left"/>
                            <p class="lbl-view">89%</p>
                        </div>
                        <div class="left-info">
                        <?php
                        if ($video->like)
                        {
                            ?>
                            <img onclick="likeVideo()" id='img_like_<?php echo $video->id;?>' data-like="1" data-video="<?php echo $video->id;?>" name="video-like" src="<?php echo base_url();?>assets/images/like_p.png" class="img-feed-left"/>
                            <?php
                        }
                        else
                        {
                            ?>
                            <img onclick="likeVideo(this)"  id='img_like_<?php echo $video->id;?>' data-like="0" data-video="<?php echo $video->id;?>" name="video-like" src="<?php echo base_url();?>assets/images/like.png" class="img-feed-left"/>
                            <?php
                        }
                        ?>
                        <p class="lbl-standard" id='like_<?php echo $video->id;?>'><?php echo $video->lk_count;?></p>
                        </div>
                        <div class="left-info">
                        <?php
                        if ($video->unlike)
                        {
                            ?>
                            <img onclick="unlikeVideo(this)" id='img_unlike_<?php echo $video->id;?>' data-unlike="1" data-video="<?php echo $video->id;?>" name="video-unlike" src="<?php echo base_url();?>assets/images/unlike_p.png" class="img-feed-left"/>
                            <?php
                        }
                        else
                        {
                            ?>
                            <img onclick="unlikeVideo(this)" id='img_unlike_<?php echo $video->id;?>' data-unlike="0" data-video="<?php echo $video->id;?>" name="video-unlike" src="<?php echo base_url();?>assets/images/unlike.png" class="img-feed-left"/>
                            <?php
                        }
                        ?>
                            <p class="lbl-standard" id='unlike_<?php echo $video->id;?>'><?php echo $video->ulk_count;?></p>
                        </div>
                </div>
                <div class="video-container">
                    <video id='video_" + (currentFeedCount + key) + "' class="feed-video">
                        <source src="<?php echo base_url().$video->file;?>" type="video/ogg">
                        Your browser does not support HTML5 video.
                    </video>
                    <p class="upload_time">Added <?php echo $video->elapse;?>&nbsp&nbsp</p>
                </div>
                <div class="side-feed-bar" style="margin:auto auto;">
                        <div class="right-info">
                            <img src="<?php echo base_url();?>assets/images/share.png" class="img-feed-right"/>
                        </div>
                        <div class="right-info">
                        <img src="<?php echo base_url();?>assets/images/plus.png" class="img-feed-right"/>
                    </div>
                    <div class="right-info">
                        <img src="<?php echo base_url();?>assets/images/warning.png" class="img-feed-right"/>
                    </div>
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
    var profileId = '<?php echo $userInfo->member_id;?>';
    var isLogin = "<?php if ($this->session->userInfo == '') echo "0";else echo "1";?>";
    var isFollow = "<?php if ($isFollow) echo "1"; else echo "0"; ?>";
    if (isLogin == '1')
    {
        memberId = "<?php  if ($this->session->userInfo == '') echo ""; else echo $this->session->userInfo->member_id;?>";
    }
</script>

<script src="<?php echo base_url();?>assets/tntl_js/feed.js"></script>
<script src="<?php echo base_url();?>assets/tntl_js/profile.js"></script>