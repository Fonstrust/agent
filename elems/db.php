<?php
error_reporting(-1);
$host = '';
$user = '';
$password = '';
$dbname = '';
$link = mysqli_connect($host, $user, $password, $dbname) or die ('Ошибка : ('. mysqli_connect_error($link) . ')');
?>