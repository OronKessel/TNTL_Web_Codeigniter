<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Utils
{
    public function redirectPage($pageName)
    {
        redirect(base_url() . $pageName);
    }

    public function isEmptyPost($fields)
    {
        $error = false;
        foreach ($fields as $field) {
            if (empty($_POST[$field])) {
                $error = true;
            }
        }
        return $error;
    }

    public function inflatePost($fields)
    {
        $result = array();
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                $result[$field] = $_POST[$field];
            } else {
                $result[$field] = array();
            }
        }
        return $result;
    }

    public function uploadImage($m_image, $index, $width = 320, $height = 420)
    {
        $upload_file = $m_image['tmp_name'];
        $path_parts = pathinfo($m_image['name']);
        if (($path_parts['basename'] == "") || ((strtolower($path_parts['extension']) != 'jpg') and (strtolower($path_parts['extension']) != 'png'))) {
            return "";
        }
        $date = new DateTime();
        $name = $date->getTimestamp();
        $filename = $name . "_" . $index . "." . $path_parts["extension"];
        $m_imgPath = BASEPATH . '../upload/' . $filename;        
        if (is_uploaded_file($upload_file)) {
            move_uploaded_file($upload_file, $m_imgPath);
        }
        $thumb_filename = '/upload/' . $filename;
        $this->resizeThumbnailImage($m_imgPath, $m_imgPath, $width, $height);
        return $thumb_filename;
    }
    public function uploadFile($m_file)
    {
        $upload_file = $m_file['tmp_name'];
        $path_parts = pathinfo($m_file['name']);
        $date = new DateTime();
        $name = $date->getTimestamp();
        $filename = $name . "." . $path_parts["extension"];
        $m_imgPath = BASEPATH . '../video/' . $filename;
        if (is_uploaded_file($upload_file)) {
            move_uploaded_file($upload_file, $m_imgPath);
        }
        $thumb_filename = '/video/' . $filename;
        return $thumb_filename;
    }
    public function resizeThumbnailImage($thumb_path, $org_image_path, $width = 320, $height = 420)
    {
        list($imagewidth, $imageheight, $imageType) = getimagesize($org_image_path);
        $imageType = image_type_to_mime_type($imageType);

        $newImageWidth = $width;
        $newImageHeight = $height;
        $newImage = imagecreatetruecolor($newImageWidth, $newImageHeight);
        switch ($imageType) {
            case "image/gif":
                $source = imagecreatefromgif($org_image_path);
                break;
            case "image/pjpeg":
            case "image/jpeg":
            case "image/jpg":
                $source = imagecreatefromjpeg($org_image_path);
                break;
            case "image/png":
            case "image/x-png":
                $source = imagecreatefrompng($org_image_path);
                break;
        }
        imagecopyresampled($newImage, $source, 0, 0, 0, 0, $newImageWidth, $newImageHeight, $imagewidth, $imageheight);
        switch ($imageType) {
            case "image/gif":
                imagegif($newImage, $thumb_path);
                break;
            case "image/pjpeg":
            case "image/jpeg":
            case "image/jpg":
                imagejpeg($newImage, $thumb_path, 90);
                break;
            case "image/png":
            case "image/x-png":
                imagepng($newImage, $thumb_path);
                break;
        }
        chmod($thumb_path, 0777);
        return $thumb_path;
    }

    public function distance($lat1, $lon1, $lat2, $lon2, $unit="K")
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } elseif ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }
    public function generateRandomString($length = 7)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
