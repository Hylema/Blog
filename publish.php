<?php

use blog\db\Db;
require_once ("Db.php");

session_start();
$error = '';
$imageUploaded = false;

if(array_key_exists("post-image",$_FILES)) {
    $target_dir = '\images\\';
    $target_file = __DIR__ . $target_dir . basename($_FILES['post-image']['name']);
    $imageType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    if($imageType != 'jpeg' && $imageType != 'jpg' && $imageType != 'png' && $imageType != 'gif' && $imageType != 'svg') {
        $error .= 'Uploaded file should be an image (.jpeg or .png or .svg or .gif)';
    }

    if($error) {
        $_SESSION['error'] = $error;
        header('Location: new-post.php');
        exit;
    } else {
        if(!move_uploaded_file($_FILES['post-image']['tmp_name'], $target_file)) {
            $_SESSION['error'] = 'Failed to upload the image';
            header('Location: new-post.php');
            exit;
        } else {
            unset($_SESSION['error']);
            $imageUploaded = true;
        }
    }
}

if($_POST) {
        $db = Db::getInstance();
        $date = new DateTime();
        if($imageUploaded) {
            $imagePath = 'images/' . basename($_FILES['post-image']['name']);
            $sql = "INSERT INTO post (title, content, published, image) VALUES('".$_POST["post-title"]."', '".$_POST["post-content"]."', now(), '".$imagePath."')";
        }
        else {
            $sql = "INSERT INTO post (title, content, published) VALUES('".$_POST["post-title"]."', '".$_POST["post-content"]."', now())";
        }

        $db->sqlQuery($sql);
        header('Location: index.php');
        exit;
    }