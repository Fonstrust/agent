
<div class="container-mini restoration">
<div class="block-header">
    <img src="/elems/img/key.png">
    <span class="block-head">Восстановление пароля</span>
</div>
<hr>
<?if(isset($_POST['set_pass'])) :?>
    <?php
    $pass_hash = password_hash($_POST['rest_pass1'], PASSWORD_DEFAULT);
    $db_users->dbUpdateOne('pass', $pass_hash, 'ID', $_COOKIE['us_id']);
    header('location: index.php');
    ?>
    
<?elseif(isset($_POST['set_code'])) :?>
    <?php
    $rest_c = $_COOKIE['rest_c'];
    ?>
    <?if ($_POST['rest_code'] == $rest_c) :?>
        <form action="#" method="post">
            <input id="pass1" type="password" placeholder="Пароль" name="rest_pass1" pattern="[0-9a-zA-Z]+" required>
            <input id="pass2" type="password" placeholder="Пароль еще раз" name="rest_pass2" pattern="[0-9a-zA-Z]+" required>
            <input id="subm" class="btn-green mt40 t18" type="submit" name="set_pass" value="Установить пароль" disabled>
        </form>
        <script type="text/javascript">
        let pass1 = document.getElementById('pass1')
        let pass2 = document.getElementById('pass2')
        let subm = document.getElementById('subm')
        function checkPass() {
            if (pass1.value == pass2.value) {
                subm.disabled = false
            } else {
                subm.disabled = true
            }
        }
        pass1.addEventListener('input', checkPass)
        pass2.addEventListener('input', checkPass)
        </script>
    <?else :?>
        <h3>Код не верен</h3>
    <?endif?>


<?elseif (isset($_POST['set_mail'])):?>
<!-- Почта получена -->
    <?php
    $mail = $_POST['rest_mail'];
    $sql_mail_check = $db_users->getSome($mail, 'mail');
    $res_mail_check = mysqli_fetch_assoc($sql_mail_check);
    ?>
    <?if (empty($res_mail_check)) :?>
        <h3>Введенный адрес не зарегистрирован</h3>
    <?else :?>
        <h3>Проверьте почту (папка "спам") и введите полученный код</h3>
        <?php
        $to = $mail;
        $subject = 'Восстановление пароля G-line';
        $restore_code = '';
        for ($i = 0; $i < 4; $i ++) {
            $restore_code .= random_int(0, 9);
        }
        $message = 'Ваш код: <b>' . $restore_code . '<b>';
        $headers = 'From: g-line32@mail.ru' . "\r\n" .
        'Reply-To: g-line32@mail.ru' . "\r\n" .
        'X-Mailer: PHP/' . phpversion() . "\r\n" .
        'MIME-Version: 1.0' . "\r\n" . 
        'Content-type: text/html; charset=utf-8' . "\r\n";

        mail($to, $subject, $message, $headers);
        setcookie('rest_c', $restore_code, time() + 600);
        setcookie('us_id', $res_mail_check['ID'], time() + 600);
        ?>
        <form action="#" method="post">
            <input type="text" name="rest_code" required>
            <input type="submit" class="btn-green mt40 t18" name="set_code" value="Восстановить пароль">
        </form>
    <?endif?>
<!-- <h3>SSSSSSSSS</h3> -->
<?else:?>
    <form action="#" method="post">
        <input type="text" name="rest_mail" placeholder="Введите Ваш E-mail" pattern="^[\w.-]+@\w+\.[a-z]{2,3}$" required>
        <input type="submit" class="btn-green mt40 t18" name="set_mail" value="Восстановить пароль">
    </form>
<?endif?>
</div>