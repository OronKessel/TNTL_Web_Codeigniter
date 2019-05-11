<div class="login-container" style="margin-bottom:10px;">
    <div class="center">
        <a href="<?php echo base_url() ?>index.php">
            <img src="<?php echo base_url(); ?>assets/images/logo.png">
        </a>
    </div>
    <div class="margin-hor20 center" style="margin-bottom:20px;margin-top:20px;">
            <button class="login-button" style="width:342px;" id="btnFbLogin">Login in with facebook</button>
    </div>
    <div style="margin-bottom:20px;">
        <form method="POST" action="<?php echo base_url(); ?>index.php/Home/actionRegister">
            <div class="margin-hor20 center">
                <input id="email" name="email" type="text" placeholder="E-Mail" class="input-search login-input-width" required>
                <input id="fullname" style="margin-top:10px;" name="fullname" type="text" placeholder="Full Name" class="input-search login-input-width" required>
                <input id="email" style="margin-top:10px;" name="username" type="text" placeholder="Username" class="input-search login-input-width" required>
                <input id="password" name="password" style="margin-top:10px;" type="password" placeholder="Password" class="input-search login-input-width" required>
            </div>
            <div class="margin-hor20 center">
                <button class="login-button" style="margin-top:20px; width:342px;">Sign Up</button>
            </div>
        </form>
    </div>
</div>
<div class="login-container" style="margin-top:10px;text-align:center;padding-bottom:10px;padding-top:10px;">
    <label style="color:#878787">Already have an account? </label><a href='<?php echo base_url(); ?>index.php/Home/login' style="text-decoration:none;color:#337ab7">Sign in</a>
</div>
<div style="text-align:center;display:block;"> 
    <img src="<?php echo base_url(); ?>assets/images/appstore.png" style="width:150px"/>
    <img src="<?php echo base_url(); ?>assets/images/googlestore.png" style="width:150px;"/>
</div>
<script>
    $(document).ready(function() {
        $('#btnFbLogin').click(function(){
            FB.login(function(response) {
                if (response.authResponse) {
                    console.log('Welcome!  Fetching your information.... ');
                FB.api('/me', function(response) {
                    console.log('Good to see you, ' + response.name + '.');
                });
                } else {
                    console.log('User cancelled login or did not fully authorize.');
                }
            });
        });
    });
    
</script>