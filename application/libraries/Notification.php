<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Notification {
    
    public function sendEmail($email,$title,$text) {
        mail($email, $title, $text);
    }
    
    
}