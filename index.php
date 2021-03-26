<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');
session_start();
include_once('elems/db.php');
require_once('clases/users.php');
require_once('clases/mysql.php');
$db_users = new Mysql('users');
$title = 'Телепорт в аэропорт';
if (isset($_GET['logout'])) {
    $_SESSION['auth'] = null;
}
if (isset($_SESSION['auth'])) {
    if ($_SESSION['auth'] == 'true' && $_SESSION['access'] == 1) {
        $user = new User($_SESSION['id']);
        $header = '
        <ul>
        <li><a class="nav-bar-a-tech" href="?list=my_transfers">Мои трансферы</a></li>
        <li><a class="nav-bar-a-tech" href="?list=conditions">Условия перевозок</a></li>
        </ul>
        <div class="fr header_group"><a href="?list=order" class="btn-green fl">Заказать трансфер</a></div>
        <div class="balance_item">
            <div class="balance_content">
                Баланс
                <br>
                <span>-' . $user->getUserDebt() . ' <b style="font-size: 16px;">₽</b></span>
                <a href="?list=detalization" class="paysystem_item">Детализация</a>
            </div>
        </div>
        <div><a href="?list=profile">ЛК</a></div>
        <a href="?logout=true">Выход</a>
        ';
        if (isset($_GET['list'])) {
            $page = $_GET['list'];
            $path = "elems/$page.php";
            if (file_exists($path)) {
                $content = $path;
            }
            echo '<input type="hidden" value="?list=' . $page . '" id="prev_val">';
        } else {
            $content = 'elems/lk_main.php';
        }    
    } elseif ($_SESSION['auth'] == null) {
        $content = 'elems/autorization.php';
        $header = '
        <a class="main-head-a nav-bar-a-tech" href="?prev=register">Регистрация</a>
        <a class="main-head-a nav-bar-a-tech" href="?prev=firm_info">Карточка фирмы</a>
        <a class="main-head-a nav-bar-a-tech" style="margin-right: 600px;" href="index.php">Вход</a>
        ';
        if (isset($_GET['prev'])) {
            $prev = $_GET['prev'];
            $way = "elems/$prev.php";
            if (file_exists($way)) {
                $content = $way;
            }
            echo '<input type="hidden" value="?prev=' . $prev . '" id="prev_val">';
        }
    } elseif ($_SESSION['auth'] == 'true' && $_SESSION['access'] == 0) {
        $content = 'elems/autorization.php';
        $header = '
        <a class="main-head-a nav-bar-a-tech" href="?prev=register">Регистрация</a>
        <a class="main-head-a nav-bar-a-tech" href="?prev=firm_info">Карточка фирмы</a>
        <a class="main-head-a nav-bar-a-tech" style="margin-right: 600px;" href="index.php">Вход</a>
        ';
        if (isset($_GET['prev'])) {
            $prev = $_GET['prev'];
            $way = "elems/$prev.php";
            if (file_exists($way)) {
                $content = $way;
            }
            echo '<input type="hidden" value="?prev=' . $prev . '" id="prev_val">';
        }
    }
} else {
    $content = 'elems/autorization.php';
    $header = '
    <a class="main-head-a nav-bar-a-tech" href="?prev=register">Регистрация</a>
    <a class="main-head-a nav-bar-a-tech" href="?prev=firm_info">Карточка фирмы</a>
    <a class="main-head-a nav-bar-a-tech" style="margin-right: 600px;" href="index.php">Вход</a>
    ';
    if (isset($_GET['prev'])) {
        $prev = $_GET['prev'];
        $way = "elems/$prev.php";
        if (file_exists($way)) {
            $content = $way;
        }
        echo '<input type="hidden" value="?prev=' . $prev . '" id="prev_val">';
    }
}

if (isset($_POST['to_lk'])) {
    $login = preg_replace('#[\'<]#', '', $_POST['mail']);
    $pass = preg_replace('#[\'<]#', '', $_POST['pass']);
    
    $sql = mysqli_query($link, "SELECT * FROM `users` WHERE `mail` = '".$login."'");
    
    $userS = mysqli_fetch_assoc($sql);
    
    if (!empty($userS)) {
        $hash = $userS['pass'];
        if (password_verify($pass, $hash)){
            session_start();
            $_SESSION['auth'] = 'true';
            $_SESSION['id'] = $userS['ID'];
            $_SESSION['mail'] = $userS['mail'];
            $_SESSION['town'] = $userS['town'];
            $_SESSION['access'] = $userS['access'];
            header('Location: index.php');
//        echo $_SESSION['auth'];
        } else {
    //        echo 'Нах иди';
        }
    }
}


if (isset($_POST['get_company'])) {
    $sql = mysqli_query($link, "SELECT `mail` FROM `users`");
    
    $mails = [];
    
    while ($res = mysqli_fetch_array($sql)) {
        array_push($mails, $res['mail']);
    }
    
    if (in_array($_POST['mail'], $mails)) {
        echo 'e-mail уже зарегистрирован';
    } else {
        $names = ['mail', 'name', 'town', 'phone', 'site', 'dop_cont', 'company', 'company_full', 'director', 'order_by', 'ur_adr', 'mail_adr', 'real_adr', 'inn', 'kpp', 'ogrn', 'okpo', 'bik', 'bank', 'k_sch', 'r_sch'];
        $names_count = count($names);
        $names_2 = '';
        $values = '';
        for ($i = 0; $i < $names_count; $i++) {
            if ($i < $names_count - 1) {
                $names_2 .= '`'.$names[$i].'`, '; 
                $values .= "'" . preg_replace('#<.+?>|\'+#', '', $_POST[$names[$i]])."', ";
            } else {
                $names_2 .= '`'.$names[$i].'`';    
                $values .= "'" . preg_replace('#<.+?>|\'+#', '', $_POST[$names[$i]]) . "'";         
            }
        }
        
        $pass_hash = password_hash($_POST['pass'], PASSWORD_DEFAULT);
            mysqli_query($link, "INSERT INTO `users` ($names_2, `reg_date`, `pass`) VALUES ($values, '".date('Y-m-d H:i:s', time())."', '".$pass_hash."')");
    }
    
    
    
//    $sql = "INSERT INTO `users` ($names_2, `reg_date`) VALUES ($values, '".date('Y-m-d H:i:s', time())."')";
//    echo $sql . '<br>';
    
}

if (isset($_POST['red_company'])) {
    $names = ['name', 'town', 'phone', 'site', 'dop_cont', 'company', 'company_full', 'director', 'order_by', 'ur_adr', 'mail_adr', 'real_adr', 'inn', 'kpp', 'ogrn', 'okpo', 'bik', 'bank', 'k_sch', 'r_sch'];
    $names_count = count($names);
    $names_2 = '';
    $values = [];
    for ($i = 0; $i < $names_count; $i++) {
        $values[] = preg_replace('#<.+?>|\'+#', '', $_POST[$names[$i]]);
    }
    $db_users->dbOptFieldsUpdate('ID', $_SESSION['id'], $names, $values);

}
include_once 'elems/layout.php';