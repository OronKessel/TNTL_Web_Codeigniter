<div class="tp-nav" id="headersticky">
    <div class="container">
        <nav class="navbar navbar-default navbar-static-top" style='display: flex;'> 
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
                <a class="navbar-brand" href="<?php echo base_url(); ?>index.php"><img src="<?php echo base_url(); ?>assets/images/logo.png" alt="Wedding Vendors" class="img-responsive"></a>
            </div>
            <div style="width:350px;margin: 0 auto;margin-top: 20px;" >
                <input class="form-control" style="width: 100%;" placeholder="Search...">
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1" style="min-height: 94px;">
                <ul class="nav navbar-nav navbar-right">
                    <?php
                    if ($this->session->userInfo == '') {
                        ?>
                        <li>
                            <a href="<?php echo base_url(); ?>index.php/Home/login">Login</a>                        
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>index.php/Home/register">Register</a>                        
                        </li>                   
                        <?php
                    } else {
                        ?>
                        <li>
                            <a href="index.html"><img src="<?php echo base_url(); ?>assets/images/notification.png" class="top-menu_icon"/></a>                        
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>index.php/Profile/index"><img src="<?php echo base_url(); ?>assets/images/avatar.png" class="top-menu_icon"/></a>                        
                        </li>                   
                        <?php
                    }
                    ?>

                </ul>
            </div>


            <!-- /.navbar-collapse --> 
        </nav>
    </div>
    <!-- /.container-fluid --> 

</div>