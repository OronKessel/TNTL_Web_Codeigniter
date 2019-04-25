<div class="main-container">
    <div class="container">        
        <div class="col-md-offset-4 col-md-4"> 
            <div class="well-box">
                <div class="row">
                    <div class="col-md-4">        
                        <div style="text-align:center;">
                            <a href="<?php echo base_url(); ?>index.php/Profile/index"><img src="<?php echo base_url(); ?>assets/images/avatar.png" style="width:40%;height:100%;"/></a>
                        </div>                            
                    </div>
                    <div class="col-md-8">        
                        <h2 class="bold-text" style="font-size:23px;">Kittycat</h2>
                        <p>The best cat on the internet  Description will go here.</p>
                        <label class="follow_button" style="margin-top: 10px;">Follow</label>
                    </div>
                </div>
            </div>            
            <div class="tab-content ">
                <div role="tabpanel" class="tab-pane active vendor-login" id="home">


                    <form >

                        <!-- Text input-->
                        <div class="form-group">
                            <label class="control-label" for="email">E-mail<span class="required">*</span></label>
                            <input id="email" name="email" type="text" placeholder="E-Mail" class="form-control input-md" required>
                        </div>

                        <!-- Text input-->
                        <div class="form-group">
                            <label class="control-label" for="password">Password<span class="required">*</span></label>
                            <input id="password" name="password" type="text" placeholder="Password" class="form-control input-md" required>
                        </div>

                        <!-- Button -->
                        <div class="form-group">
                            <button id="submit" name="submit" class="btn tp-btn-primary tp-btn-lg">Login</button>
                            <a href="forget-password.html" class="pull-right"> <small>Forgot Password ?</small></a> </div>
                    </form>
                </div>
                <div role="tabpanel" class="tab-pane couple-login" id="profile"><!-- Text input-->
                    <form >

                        <!-- Text input-->
                        <div class="form-group">
                            <label class="control-label" for="email-one">E-mail<span class="required">*</span></label>
                            <input id="email-one" name="email-one" type="text" placeholder="E-Mail" class="form-control input-md" required>
                        </div>

                        <!-- Text input-->
                        <div class="form-group">
                            <label class="control-label" for="password-one">Password<span class="required">*</span></label>
                            <input id="password-one" name="password-one" type="text" placeholder="Password" class="form-control input-md" required>
                        </div>

                        <!-- Button -->
                        <div class="form-group">
                            <button name="submit" class="btn tp-btn-primary tp-btn-lg">Login</button>
                            <a href="forget-password.html" class="pull-right"> <small>Forgot Password ?</small></a> </div>
                    </form>
                </div>
            </div>
            <div class="well-box social-login"> <a href="#" class="btn facebook-btn"><i class="fa fa-facebook-square"></i>Facebook</a> <a href="#" class="btn twitter-btn"><i class="fa fa-twitter-square"></i>Twitter</a> <a href="#" class="btn google-btn"><i class="fa fa-google-plus-square"></i>Google+</a> </div>
        </div>
    </div>
</div>