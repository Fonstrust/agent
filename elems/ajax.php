<?php
include_once('db.php');
if (isset($_POST['mail'])) {
//    echo $_POST['mail'] . '!!!!!!!';
    $sql = mysqli_query($link, "SELECT `mail` FROM `users`");
    
    $mails = [];
    
    while ($res = mysqli_fetch_array($sql)) {
        array_push($mails, $res['mail']);
    }
    
    if (in_array($_POST['mail'], $mails)) {
        $match = 1;
    } else {
        $match = 0;
    }
    
    echo $match;
    
}