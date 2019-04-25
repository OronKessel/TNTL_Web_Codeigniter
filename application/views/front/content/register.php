<div class="main-container">
    <div class="container">        
        <div class="row" style="margin-top: 50px;">
            <div class="col-md-offset-4 col-md-4"> 
                <div class="well-box align-middle">
                    <div class="row" style="text-align:center;">
                        <img src="<?php echo base_url(); ?>assets/images/logo.png">
                    </div>                    
                    <a href="#" class="btn btn-primary" style="width:100%;margin-top:20px;">Login in with Facebook</a>
                    <div style="text-align: center;margin-top:10px;">
                        <div class="col-md-4" style="height:1px;background-color:#efefef;margin-top:15px;"></div>
                        <div class="col-md-4"><label style="font-size:12px;color:#878787;">OR</label></div>
                        <div class="col-md-4" style="height:1px;background-color:#efefef;margin-top:15px;"></div>
                    </div>
                    <div class="row" style="margin-top:20px;">
                        <form method="POST" action="<?php echo base_url(); ?>index.php/Home/actionRegister">
                            <div class="form-group">                            
                                <input id="email" name="email" type="text" placeholder="Email" class="form-control input-md" required>
                            </div>  
                            <div class="form-group">                            
                                <input id="email" name="fullname" type="text" placeholder="Full Name" class="form-control input-md" required>
                            </div>  
                            <div class="form-group">                            
                                <input id="email" name="username" type="text" placeholder="Username" class="form-control input-md" required>
                            </div>                        
                            <div class="form-group">                            
                                <input id="password" name="password" type="password" placeholder="Password" class="form-control input-md" required>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary" style="width:100%;">Sign Up</button>                                
                            </div>
                        </form>
                    </div>
                </div>                        
                <div class="well-box social-login" style="text-align:center;padding:15px;"> 
                    <label style="font-weight:400;font-size:14px;color:#878787;">Don't have an account? <a href="<?php echo base_url(); ?>index.php/Home/login" style="color:#002166;font-size:16px;"><span class="">Sign up</span></a></label>
                </div>                
                <div style="text-align:center;display:block;"> 
                    <img src="<?php echo base_url(); ?>assets/images/appstore.png" style="width:40%;height:100%;"/>
                    <img src="<?php echo base_url(); ?>assets/images/googlestore.png" style="width:40%;height:100%;"/>
                </div>
            </div>
        </div>
    </div>
</div>