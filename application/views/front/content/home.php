<div class="home-tab">
    <div style="margin-top:10px; display:flex;">
        <label class="tab_home_item" style="margin-left:auto;" id='tab_feed' data-tab-index='0'>Feed</label>
        <div style="width:1px;background:#a8a8a8;"></div>
        <label class="tab_home_item" id='tab_featured' data-tab-index='1'>Featured</label>                
        <div style="width:1px;background:#a8a8a8;"></div>
        <label class="tab_home_item" style="margin-right:auto;" id='tab_funniest' data-tab-index='2'>Funniest</label>                            
    </div>
</div>
<div class="main-container">
    <div id='feedContainer'>
                    
    </div>
</div>
<script type="text/javascript">
    var baseUrl = '<?= base_url(); ?>';
    var memberId = '';
    var memberName = '';
    var isLogin = "<?php if ($this->session->userInfo == '') echo "0";else echo "1";?>";
    if (isLogin == '1')
    {
        memberId = "<?php  if ($this->session->userInfo == '') echo ""; else echo $this->session->userInfo->member_id;?>";
        memberName = "<?php  if ($this->session->userInfo == '') echo ""; else echo $this->session->userInfo->username;?>";
    }
</script>
<script src="<?php echo base_url();?>assets/tntl_js/feed.js"></script>
<script src="<?php echo base_url();?>assets/tntl_js/home.js"></script>