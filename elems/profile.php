<?php 
// include_once('../elems/db.php');

$res_user = $user->getUserSql();

// Файлы phpmailer
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';
 
// Подключаем библиотеку PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
 
// Создаем письмо
$mail = new PHPMailer();
$mail->isSMTP();                   // Отправка через SMTP
$mail->Host   = 'smtp.yandex.ru';  // Адрес SMTP сервера
$mail->SMTPAuth   = true;          // Enable SMTP authentication
$mail->Username   = 'fonstrustex';       // ваше имя пользователя (без домена и @)
$mail->Password   = 'Piligrim1311';    // ваш пароль
$mail->SMTPSecure = 'ssl';         // шифрование ssl
$mail->Port   = 465;               // порт подключения
 
$mail->setFrom('fonstrustex@yandex.ru', 'Иван Иванов');    // от кого
$mail->addAddress('test-tpcyo1a1v@srv1.mail-tester.com', 'Вася Петров'); // кому
 
$mail->Subject = 'Тест';
$mail->msgHTML("<html><body>
                <h1>Здравствуйте!</h1>
                <p>Это тестовое письмо.</p>
                </html></body>");
// Отправляем
if ($mail->send()) {
  echo 'Письмо отправлено!';
} else {
  echo 'Ошибка: ' . $mail->ErrorInfo;
}



$paragraphs_company = ['Название компании (торговая марка)', 'Название компании (с формой собственности)', 'ФИО директора', 'Действует на основании', 'Юридический адрес', 'Почтовый адрес', 'Фактический адрес'];
$names_company = ['company', 'company_full', 'director', 'order_by', 'ur_adr', 'mail_adr', 'real_adr'];

$names_count = count($names_company);
$half = (ceil($names_count/2) - 1);


$company = '<p class="form-header">Данные компании</p> <div class="registration-container-company"> <div class="first-column">';

for ($i = 0; $i < $names_count; $i++) {
    if ($i < $half) {
        $company .= '
        <label>
        <p>'.$paragraphs_company[$i].'</p>
        <input type="text" value="' . $res_user[$names_company[$i]] . '" name='.$names_company[$i].' pattern="[\s\wА-Яа-яЁё.,(&#34)-]+" required>
        </label>
        ';
    } elseif ($i == $half) {
        $company .= '
        <label>
        <p>'.$paragraphs_company[$i].'</p>
        <input type="text" value="' . $res_user[$names_company[$i]] . '" name='.$names_company[$i].' pattern="[\s\wА-Яа-яЁё.,-]+" required>
        </label>
        </div>
        <div class="second-column">
        ';
    } elseif ($i > $half) {
        $company .= '
        <label>
        <p>'.$paragraphs_company[$i].'</p>
        <input type="text" value="' . $res_user[$names_company[$i]] . '" name='.$names_company[$i].' pattern="[\s\wА-Яа-яЁё.,-]+" required>
        </label>
        ';
    }
}

$company .= '</div></div>';




$paragraphs_bank = ['ИНН', 'КПП', 'ОГРН', 'ОКПО', 'БИК', 'Банк', 'К/счет', 'Расч счет'];

$names_bank = ['inn', 'kpp', 'ogrn', 'okpo', 'bik', 'bank', 'k_sch', 'r_sch'];

$count_bank = count($names_bank);

$half = ceil($count_bank / 2) - 1;

//$b = ['inn', 'kpp'];
//for ($i = 0 ; $i < $count_bank-1; $i++) {
//    mysqli_query($link, "ALTER TABLE `users` ADD COLUMN `$b[$i]` varchar(100)");
//}


$bank = '<p class="form-header">Реквизиты</p> <div class="registration-container-bank"> <div class="first-column">';

for ($i = 0; $i < $count_bank; $i++) {
    if ($i < $half) {
        $bank .= '
        <label>
        <p>'.$paragraphs_bank[$i].'</p>
        <input type="text" value="' . $res_user[$names_bank[$i]] . '" name='.$names_bank[$i].' pattern="[\s\wА-Яа-яЁё(&#34).,-]+" required>
        </label>
        ';
    } elseif ($i == $half) {
        $bank .= '
        <label>
        <p>'.$paragraphs_bank[$i].'</p>
        <input type="text" value="' . $res_user[$names_bank[$i]] . '" name='.$names_bank[$i].' pattern="[\s\wА-Яа-яЁё(&#34).,-]+" required>
        </label>
        </div>
        <div class="second-column">
        ';
    } elseif ($i > $half) {
        $bank .= '
        <label>
        <p>'.$paragraphs_bank[$i].'</p>
        <input type="text" value="' . $res_user[$names_bank[$i]] . '" name='.$names_bank[$i].' pattern="[\s\wА-Яа-яЁё(&#34).,-]+" required>
        </label>
        ';
    }
}

$bank .= '</div></div>';


?>

<div class="container-mid registration-wrapper">
    <div class="block-header">
        <img src="/elems/img/key.png">
        <span class="block-head">Личный кабинет</span>
    </div>
    <hr>
    <div class="registration-container">
        <form method="post" action="../index.php">
            <p class="form-header">Личные данные</p>
            <div class="registration-container-personal">
                <div class="first-column">
                    <label>
                        <p>Город</p>
                        <input type="text" readonly value="<?=$user->getTownRu()?>" name="town">
                    </label>
                    <label>
                        <p>Ваше имя</p>
                        <input type="text" name="name" value="<?=$res_user['director']?>" pattern="[\s\wА-Яа-яЁё,.-]+" required>
                    </label>
                </div>
                <div class="second-column">
                    <label>
                        <p>Телефон для связи</p>
                        <input type="text" name="phone" value="<?=$res_user['phone']?>" pattern="[\s0-9()+-]{10,12}" required>
                    </label>
                    <label>
                        <p>Сайт</p>
                        <input type="text" name="site" value="<?=$res_user['site']?>" pattern="[\s\w.-]+">
                    </label>
                    <label>
                        <p>Дополнительные контакты</p>
                        <textarea name="dop_cont" value="<?=$res_user['dop_cont']?>" pattern="[\s\wА-Яа-яЁё\(	&#34).,-]+" ></textarea>
                    </label>
                </div>
            </div>
            <?php 
            echo $company;
            echo $bank;
            ?>
            <div class="form-submit">
                <input class="btn-green" type="submit" name="red_company" value="Сохранить изменения">
            </div>
        </form>
    </div>
</div>