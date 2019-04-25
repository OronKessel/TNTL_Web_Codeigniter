<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">        
        <title>TNTL</title>
        <link href="<?php echo base_url(); ?>assets/css/bootstrap.min.css" rel="stylesheet">        
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/style.css">
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/style_customize.css">
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/bootstrap-select.min.css">
        <link href='https://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
        <link href='https://fonts.googleapis.com/css?family=Istok+Web:400,400italic,700,700italic' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/preloader.css"/>

        <script src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script> 
    </head>
    <body>
        <?php
        if ($pageName != "Login" && $pageName != "Register") {
            include('front/header.php');
        }
        include('front/content.php');
        ?>              
        <!-- Include all compiled plugins (below), or include individual files as needed --> 
        <script src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script> 
        <script src="<?php echo base_url(); ?>assets/js/nav.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/jquery.sticky.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/header-sticky.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/jquery.preloader.min.js"></script>
    </body>
</html>