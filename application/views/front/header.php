<div class="navbar">
    <div class="nav-logo">
        <a href="<?php echo base_url(); ?>index.php"><img src="<?php echo base_url(); ?>assets/images/logo.png" class="img-responsive"></a>
    </div>
    <div class="nav-search">
        <input class="input-search" style="width: 100%;margin-top:15px;" placeholder="Search..." id='input-search' oninput="changeSearch(this.value);" onkeypress="enterSearch(this,event)">
        <div class="nav-search-content" id="search-content">
            <!-- <div class="single-video-account">
                <img src="<?php echo base_url(); ?>assets/images/notification.png" class="img-top-icon"/>
                <div class="search-account-info">
                    <div class="single-video-account">
                        <p class="search-account-name">Kitty</p>
                        <p class="search-account-follow" style="text-align:right;">1k followers</p>
                    </div>
                    <p class="search-account-desc">Best cat on the internet</p>
                </div>
            </div>
            <div class="separate-line" style="margin-top:0px;"></div>
            <div class="search-video-info">
                <p class="search-account-desc">Funny cat videos</p>
            </div>
            <div class="separate-line" style="margin-top:0px;"></div>
            <div class="search-video-info">
                <p class="search-account-desc">Funny cat videos</p>
            </div>
            <div class="separate-line" style="margin-top:0px;"></div>
            <div class="search-video-info">
                <p class="search-account-desc">Funny cat videos</p>
            </div>
            <div class="separate-line" style="margin-top:0px;"></div> -->
        </div>
    </div>
    <div style="min-width:264px;">
        <?php
        if ($this->session->userInfo == '') {
        ?>
            <div class="nav-menu" style="margin-top:20px;">
                <a href="<?php echo base_url(); ?>index.php/Home/login" class="link-menu">Login</a>
                <a href="<?php echo base_url(); ?>index.php/Home/register" class="link-menu">Register</a>
            </div>
        <?php
        } else {
        ?>
            <div class="nav-menu">
                <div style="margin-left:auto;">
                    <a id='btnHeaderNotification'>
                        <img src="<?php echo base_url(); ?>assets/images/notification.png" class="img-top-icon"/>

                    </a>
                </div>
                <a href='<?php echo base_url(); ?>index.php/Profile/index/<?php echo $this->session->userInfo->username;?>'>
                    <img src="<?php echo base_url(); ?>assets/images/avatar.png" class="img-top-icon"/>
                </a>
                <a href='<?php echo base_url(); ?>index.php/Home/actionLogout'>
                    <img src="<?php echo base_url(); ?>assets/images/logout.png" class="img-top-icon"/>
                </a>
            </div>
        <?php
        }
        ?>
        
        <div class="nav-nontification-content" id="notification-content">
            <div class="notification-no-info">
                <p class="search-account-desc">No notifications available</p>
            </div>
        </div>
    </div>
    
</div>