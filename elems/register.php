<?php 
include_once('../elems/db.php');

$town_cols = ['name_ru', 'name_en', 'queue_index'];
$sql_towns = mysqli_query($link, "SELECT * FROM `towns` ORDER BY `queue_index` ASC");

$towns = '';
//$incr = 0;
while ($res = mysqli_fetch_array($sql_towns)) {
//    if ($incr == 0) {
//        $towns .= '<option value="' . $res[$town_cols[1]] . '" checked>' . $res[$town_cols[0]] . '</option>';
//    } else {
        $towns .= '<option value="' . $res[$town_cols[1]] . '">' . $res[$town_cols[0]] . '</option>';
//    }
//    $incr++;
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
        <input type="text" name='.$names_company[$i].' pattern="[\s\wА-Яа-яЁё.,(&#34)-]+" required>
        </label>
        ';
    } elseif ($i == $half) {
        $company .= '
        <label>
        <p>'.$paragraphs_company[$i].'</p>
        <input type="text" name='.$names_company[$i].' pattern="[\s\wА-Яа-яЁё.,-]+" required>
        </label>
        </div>
        <div class="second-column">
        ';
    } elseif ($i > $half) {
        $company .= '
        <label>
        <p>'.$paragraphs_company[$i].'</p>
        <input type="text" name='.$names_company[$i].' pattern="[\s\wА-Яа-яЁё.,-]+" required>
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
        <input type="text" name='.$names_bank[$i].' pattern="[\s\wА-Яа-яЁё(&#34).,-]+" required>
        </label>
        ';
    } elseif ($i == $half) {
        $bank .= '
        <label>
        <p>'.$paragraphs_bank[$i].'</p>
        <input type="text" name='.$names_bank[$i].' pattern="[\s\wА-Яа-яЁё(&#34).,-]+" required>
        </label>
        </div>
        <div class="second-column">
        ';
    } elseif ($i > $half) {
        $bank .= '
        <label>
        <p>'.$paragraphs_bank[$i].'</p>
        <input type="text" name='.$names_bank[$i].' pattern="[\s\wА-Яа-яЁё(&#34).,-]+" required>
        </label>
        ';
    }
}

$bank .= '</div></div>';


?>

<div class="container-mid registration-wrapper">
    <div class="block-header">
        <img src="/elems/img/key.png">
        <span class="block-head">Регистрация юридического лица</span>
    </div>
    <hr>
    <div class="registration-container">
        <form method="post" action="../index.php">
            <p class="form-header">Личные данные</p>
            <div class="registration-container-personal">
                <div class="first-column">
                    <label>
                        <p id="alarm">E-mail</p>
                        <input id="mail" type="text" name="mail" pattern="^[\w.-]+@\w+\.[a-z]{2,3}$" required>
                    </label>
                    <label>
                        <p>Пароль</p>
                        <input type="password" name="pass" pattern="[\w!]+" required>
                    </label>
                    <label>
                        <p>Город</p>
                        <select name="town">
                            <?php
                            echo $towns;
                            ?>
                        </select>
                    </label>
                    <label>
                        <p>Ваше имя</p>
                        <input type="text" name="name" pattern="[\s\wА-Яа-яЁё,.-]+" required>
                    </label>
                </div>
                <div class="second-column">
                    <label>
                        <p>Телефон для связи</p>
                        <input type="text" name="phone" pattern="[\s0-9()+-]{10,12}" required>
                    </label>
                    <label>
                        <p>Сайт</p>
                        <input type="text" name="site" pattern="[\s\w.-]+">
                    </label>
                    <label>
                        <p>Дополнительные контакты</p>
                        <textarea name="dop_cont" pattern="[\s\wА-Яа-яЁё\(	&#34).,-]+" ></textarea>
                    </label>
                </div>
            </div>
            <?php 
            echo $company;
            echo $bank;
            ?>
            <div class="form-submit">
                <input class="btn-green" type="submit" name="get_company" value="Зарегистрировать">
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    let mail = document.getElementById('mail');
    let alarm = document.getElementById('alarm');
    mail.addEventListener('input', function(){
        let seachParams = new URLSearchParams();
        seachParams.set('mail', this.value);
        let promise = fetch('ajax.php', {
            method: 'POST',
            body: seachParams,
        });
        promise.then(
            response => {
                return response.text();
            }
        ).then(
            text => {
                if (text == 1) {
                    alarm.innerHTML += ' <span class="alarm">этот e-mail занят</span>';
                } else {
                    alarm.innerHTML = 'E-mail   ';
                }
            }
        )
    });
</script>