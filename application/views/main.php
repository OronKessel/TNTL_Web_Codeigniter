<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">        
        <title>TNTL</title>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/style_tntl.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/preloader.css"/>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/sweetalert.css">
        <script src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script> 
        <script src="<?php echo base_url(); ?>assets/js/sweetalert.min.js"></script>
    </head>
    <body>
        <script>
        window.fbAsyncInit = function() {
            FB.init({
            appId            : '337066630095835',
            autoLogAppEvents : true,
            xfbml            : true,
            version          : 'v3.3'
            });
        };
        </script>
        <script async defer src="https://connect.facebook.net/en_US/sdk.js"></script>
        <?php
        if ($pageName != "Login" && $pageName != "Register") {
            include('front/header.php');
        }
        include('front/content.php');
        ?>              
        <script src="<?php echo base_url(); ?>assets/js/jquery.preloader.min.js"></script>
        <script src="<?php echo base_url();?>assets/tntl_js/header.js"></script>
        <script>
            <?php
            if (isset($message)) {
                ?>
                if ('<?php echo $message; ?>' != '')
                {
                    swal('<?php echo $message; ?>');
                }
                <?php
            }
            if (isset($error)) {
                ?>
                if ('<?php echo $error; ?>' != '')
                {
                    swal('<?php echo $error; ?>', '', "error");
                }
                <?php
            }
            ?>
        </script>

    </body>
</html>