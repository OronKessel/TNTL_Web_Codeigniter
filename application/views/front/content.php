<?php

switch ($pageName) {
    case "Home":
        include("content/home.php");
        break;
    case "Profile":
        include("content/profile.php");
        break;
    case "Video":
        include("content/video.php");
        break;
    case "Search":
        include("content/search.php");
        break;
    case "Login":
        include("content/login.php");
        break;
    case "Register":
        include("content/register.php");
        break;
}
?>