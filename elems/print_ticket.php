<?php
require_once('../clases/mysql.php');
require_once('../clases/users.php');
session_start();
if (isset($_SESSION['id'])) {
    $u_id = $_SESSION['id'];
    $user = new User($_SESSION['id']);
}
if (isset($_GET['print_id'])) {
    $printid = $_GET['print_id'];
}
$db_transfers = new Mysql('new_transfers');
$sql_transfer = $db_transfers->getSome2($u_id, 'u_id', $printid, 'ID');
$res_transfer = mysqli_fetch_assoc($sql_transfer);
$dest = $res_transfer['msk_point'];
if ($dest == 'adr') {
    $msk_point = $res_transfer['msk_adress'];
} else {
    $msk_point = strtoupper($res_transfer['msk_point']);
}
if ($res_transfer['direction'] == 'to_msk'){
    $from = date('d.m.Y в H:i', strtotime($res_transfer['depart_time'])) . '<br>' . $user->getTownRu() . ', ' . $res_transfer['adress'];
    $to = date('d.m.Y в H:i', strtotime($res_transfer['arrive_time'])) . '<br>Москва, ' . $msk_point;
} else {
    $from = date('d.m.Y в H:i', strtotime($res_transfer['depart_time'])) . '<br>Москва, ' . $msk_point;
    $to = date('d.m.Y в H:i', strtotime($res_transfer['arrive_time'])) . '<br>' . $user->getTownRu() . ', ' . $res_transfer['adress'];
}

$type = $res_transfer['type'];
$bag_abr = $res_transfer['add_bag'];
$pass_data = $user->getUserPassenger($res_transfer['passenger']);
$fio = $pass_data['fio'];
$birth = date('d.m.Y г.', strtotime($pass_data['birth_date']));
$passport = $pass_data['passport'];
if ($passport == 'no_value') {
    $passport = 'не указан';
}
$phone = $pass_data['phone'];
$price = $user->getPriceOfTransfer($printid);
$img;
if ($dest == 'vko' || $dest == 'dme') {
    $img = '<img src="img/' . strtoupper($dest) . '.jpg" style="width: 470px; float: right; margin-top: 50px;">';
} elseif (explode('-', $dest)[0] == 'svo') {
    $dest_arr = explode('-', $dest);
    $img = '<img src="img/' . strtoupper($dest_arr[0]) . '-' . strtoupper($dest_arr[1]) . '.jpg" style="width: 470px; float: right; margin-top: 50px;">';
    
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Печать документа</title>
</head>
    <body onload="window.print()">

        <div class="print">
            <div class="wrap_title clear">
                <img src="img/1.jpg" alt="" style="
                width: 260px;
                float: left;
                margin-top: 25px;
                ">
                <div style="
                float: left;
                width: 600px;
                ">
                <div style="
                font-size: 30px;
                font-weight: bold;
                margin-top: 27px;
                "> МАРШРУТ-КВИТАНЦИЯ ЭЛЕКТРОННОГО БИЛЕТА</div>
                <span>ООО”ДЖИ-ЛАЙН” Россия, Брянск, ул. Бежицкая, д.70, оф.3 <strong>(4832) 72-08-68, <br> 8-962-130-58-88</strong>, www.g-line32.ru, e-mail:g-line32@mail.ru	</span>
            </div>
        </div>
        <div style="    float: left; width: 500px;">
            <ul class="list clear" style="
            padding: 0;
            margin-top: 50px;
            list-style: none;
            ">

            <li><span>Билет:</span> № 000<?echo $printid?>	</li>
            <li><span>ФИО: </span><?=$fio?></li>
            <li><span>Дата рождения: </span><?=$birth?></li>
            <li><span>Паспорт: </span><?=$passport?></li>
            <li><span>Тел.: </span> <?=$phone?></li>
        </ul>
        <p style="
        width: 400px;
        font-size: 18px;
        line-height: 27px;
        ">ООО «ДЖИ-ЛАЙН» гарантирует конфиденциальность и защиту Ваших личных от не санкционированного использования, согласно ФЗ № 152 от 27.07.2006 г. «О персональных данных».</p>
    </div>
    <?=$img?>
    <div class="clear"></div>

    <table style="
    margin-top: 20px;
    border: 1px solid #000;
    border-collapse: collapse;
    width: 100%;
    ">
    <tbody><tr style="
    border-bottom: 1px solid #000;
    ">
    <th>Отправление</th>
    <th>Прибытие</th>
    <th>Вид</th>
    <th>Багаж</th>
    <th>Тариф</th>
    </tr>
    <tr>
        <td><?echo $from;?></td>
        <td><?echo $to?></td>
        <td><?echo $type;?></td>
        <td><?echo $bag_abr?> </td>
        <td><?=$price?></td>
    </tr>
    </tbody></table>

    <p style="
    margin-top: 20px;
    font-size: 23px;
    text-align: center;
    font-weight: bold;
    ">Памятка пассажиру:</p>

    <?
    if($rowT["typetrans"] == "solo"){
        echo '
        <div class="text_wrap">
        <p style="line-height: 1.1;">Пассажир обязан предварительно и во время перевозки обеспечить постоянный прием информации на указанный в посадочном талоне телефонный номер. По прилету в аэропорт пассажиру следует уведомить диспетчера ООО «ДЖИ-ЛАЙН» о своем прибытии с помощью телефонного звонка на телефонные номера, указанные в проездных документах. В случае переноса авиарейса пассажиру следует заблаговременно уведомить ООО «ДЖИ-ЛАЙН». В случае невозможности проинформировать диспетчера о переносе авиарейса, автомобиль компании ожидает своих клиентов в течении 5 часов от времени выезда(указанном в маршрутной квитанции) после чего автомобиль уезжает, <strong>деньги за данный трансфер не возвращаются.</strong>  Если пассажиры проинформировали ООО «ДЖИ-ЛАЙН» о задержке авиарейса, автомобиль ожидает своих клиентов на протяжении 5 часов, по <strong>100руб. за час,  после чего за каждый час по 300руб.</strong>, деньги туристы обязаны будут оплатить водителю на руки при посадке в т/с.  Компания вправе отказать в принятии багажа и ручной клади для перевозки, если превышены нормы, допустимые к перевозке, а дополнительные багажные места заранее не оплачены. В ТС компании запрещается курение, употребление спиртных напитков, нахождение в состоянии алкогольного либо наркотического опьянения, действия, создающие помехи для управления ТС, порча и загрязнение салона ТС. В случае нарушения данного запрета пассажиром, сотрудники компании имеют право высадить данного пассажира на ближайшем полицейском посту или передать представителям полиции. В данной ситуации услуга трансфера считается оказанной – денежные средства, уплаченные за услугу, возврату не подлежат. В случае нанесения ущерба, в том числе загрязнении ТС, по вине пассажира, пассажир обязан возместить компании средства необходимые для устранения данного ущерба.</p>
        Размер денежных средств, подлежащих возврату, определяется из условий заблаговременности отказа от услуги:
        <ul style="
        list-style-type: disc;
        line-height: 1.1;
        ">
        <li>при отказе от трансфера более чем за 24 часа возврату подлежит полная стоимость;</li>
        <li>при отказе от трансфера менее чем за 24 часа, но не позднее, чем за 12 часов до отправления трансфера, выплачивается 75% от стоимости услуги;</li>
        <li>при отказе от трансфера менее чем за 12 часов, но не позднее, чем за 4 часа до отправления трансфера, выплачивается 50% от стоимости услуги;</li>
        <li>
        при отказе от трансфера менее чем за 4 часа до отправления трансфера, выплачивается 25% от стоимости услуги.
        </li></ul>

        В случае отказа пассажира от услуги по независящим от него причинам, подтвержденным документально, вне зависимости от заблаговременности отказа, пассажиру возвращается 75% от стоимости трансфера.

        </div>
        ';
    }
    else{

        echo '
        <div class="text_wrap">
        <p style="line-height: 1.1;">Пассажир обязан предварительно и во время перевозки обеспечить постоянный прием информации на указанный в посадочном талоне телефонный номер. По прилету в аэропорт пассажиру следует уведомить диспетчера ООО «ДЖИ-ЛАЙН» о своем прибытии с помощью телефонного звонка на телефонные номера, указанные в проездных документах. Перевозка детей любого возраста осуществляется на отдельном посадочном месте, которое подлежит оплате. В случае переноса авиарейса пассажиру следует заблаговременно уведомить ООО «ДЖИ-ЛАЙН». При опоздании авиарейса или прибытии ранее запланированного, ООО «ДЖИ-ЛАЙН» по своему усмотрению может предоставить пассажиру место на ближайший рейс группового трансфера. Пассажиры имеют право на бесплатный провоз одного места ручной клади размером не более 55х40х20 см, весом не более 5 кг и одно место багажа размером не более 100х50х30 см, весом не более 30 кг. Компания вправе отказать в принятии багажа и ручной клади для перевозки, если превышены нормы, допустимые к перевозке, а дополнительные багажные места заранее не оплачены. В ТС компании запрещается курение, употребление спиртных напитков, нахождение в состоянии алкогольного либо наркотического опьянения, поведение, влекущее неудобства для других пассажиров, действия, создающие помехи для управления ТС, порча и загрязнение салона ТС. В случае нарушения данного запрета пассажиром, сотрудники компании имеют право высадить данного пассажира на ближайшем полицейском посту или передать представителям полиции. В данной ситуации услуга трансфера считается оказанной – денежные средства, уплаченные за услугу, возврату не подлежат. В случае нанесения ущерба, в том числе загрязнении ТС, по вине пассажира, пассажир обязан возместить компании средства необходимые для устранения данного ущерба.
        </p>
        Размер денежных средств, подлежащих возврату, определяется из условий заблаговременности отказа от услуги:
        <ul style="
        list-style-type: disc;
        line-height: 1.1;
        ">
        <li>при отказе от трансфера более чем за 24 часа возврату подлежит полная стоимость;</li>
        <li>при отказе от трансфера менее чем за 24 часа, но не позднее, чем за 12 часов до отправления трансфера, выплачивается 75% от стоимости услуги;</li>
        <li>при отказе от трансфера менее чем за 12 часов, но не позднее, чем за 4 часа до отправления трансфера, выплачивается 50% от стоимости услуги;</li>
        <li>
        при отказе от трансфера менее чем за 4 часа до отправления трансфера, выплачивается 25% от стоимости услуги.
        </li></ul>

        В случае отказа пассажира от услуги по независящим от него причинам, подтвержденным документально, вне зависимости от заблаговременности отказа, пассажиру возвращается 75% от стоимости трансфера.

        <p class="bolded_print">Компания ООО "ДЖИ-ЛАЙН" оставляет за собой право корректировать время отправления +/- 1 час.</p>
        </div>
        ';

    }
    ?>

    <div class="clear"></div>

    <div class="podp_wrap" style="
    font-size: 22px;
    margin-top: 30px;
    ">
    <p style="
    width: 50%;
    float: left;
    line-height: 30px;
    ">С условиями перевозки ознакомлен, личная информация указана верно</p>
    <p style="
    width: 50%;
    float: right;
    text-align: right;
    margin-top: 12px;
    ">____________/ <?echo $nowFio?></p>
    </div>
    <div class="clear"></div>





    <div style="display:flex">
    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="180" height="180" viewBox="0 0 250 250">
            <defs>
              <clipPath id="main-mask-8531513">
                <rect x="0" y="0" width="250" height="250" rx="10"></rect>
              </clipPath>
              <clipPath id="avatar-mask-8531513">
                <circle cx="125" cy="60" r="28" fill="#000"></circle>
              </clipPath>


              <g id="qr-8531513">
      <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" viewBox="0 0 3267 3267" width="190px" height="190px" class="qr-code" xml:space="preserve">
        <defs>
          <rect id="rect-8531513" width="100" height="100" fill="#000000"></rect>
          <path id="empty-8531513" d="M0,28.6v42.9C0,87.3,12.8,100,28.6,100h42.9c15.9,0,28.6-12.8,28.6-28.6V28.6C100,12.7,87.2,0,71.4,0H28.6 C12.8,0,0,12.8,0,28.6z" fill="#000000"></path>
          <path id="b-8531513" d="M0,0 L66,0 C84.7776815,-3.44940413e-15 100,15.2223185 100,34 L100,66 C100,84.7776815 84.7776815,100 66,100 L0,100 L0,0 Z" transform="rotate(-90 50 50)" fill="#000000"></path>
          <path id="r-8531513" d="M0,0 L66,0 C84.7776815,-3.44940413e-15 100,15.2223185 100,34 L100,66 C100,84.7776815 84.7776815,100 66,100 L0,100 L0,0 Z" transform="rotate(-180 50 50)" fill="#000000"></path>
          <path id="l-8531513" d="M0,0 L66,0 C84.7776815,-3.44940413e-15 100,15.2223185 100,34 L100,66 C100,84.7776815 84.7776815,100 66,100 L0,100 L0,0 Z" fill="#000000"></path>
          <path id="t-8531513" d="M0,0 L66,0 C84.7776815,-3.44940413e-15 100,15.2223185 100,34 L100,66 C100,84.7776815 84.7776815,100 66,100 L0,100 L0,0 Z" transform="rotate(90 50 50)" fill="#000000"></path>
          <path id="l-8531513" d="M0,0 L100,0 L100,66 C100,84.7776815 84.7776815,100 66,100 L0,100 L0,0 Z" transform="rotate(-90 50 50)" fill="#000000"></path>
          <path id="lt-8531513" d="M0,0 L100,0 L100,66 C100,84.7776815 84.7776815,100 66,100 L0,100 L0,0 Z" fill="#000000"></path>
          <path id="lb-8531513" d="M0,0 L100,0 L100,66 C100,84.7776815 84.7776815,100 66,100 L0,100 L0,0 Z" transform="rotate(-90 50 50)" fill="#000000"></path>
          <path id="rb-8531513" d="M0,0 L100,0 L100,66 C100,84.7776815 84.7776815,100 66,100 L0,100 L0,0 Z" transform="rotate(-180 50 50)" fill="#000000"></path>
          <path id="rt-8531513" d="M0,0 L100,0 L100,66 C100,84.7776815 84.7776815,100 66,100 L0,100 L0,0 Z" transform="rotate(90 50 50)" fill="#000000"></path>
          <path id="n_lt-8531513" d="M30.5,2V0H0v30.5h2C2,14.7,14.8,2,30.5,2z" fill="#000000"></path>
          <path id="n_lb-8531513" d="M2,69.5H0V100h30.5v-2C14.7,98,2,85.2,2,69.5z" fill="#000000"></path>
          <path id="n_rt-8531513" d="M98,30.5h2V0H69.5v2C85.3,2,98,14.8,98,30.5z" fill="#000000"></path>
          <path id="n_rb-8531513" d="M69.5,98v2H100V69.5h-2C98,85.3,85.2,98,69.5,98z" fill="#000000"></path>
          <path id="point-8531513" fill="#000000" d="M600.001786,457.329333 L600.001786,242.658167 C600.001786,147.372368 587.039517,124.122784 581.464617,118.535383 C575.877216,112.960483 552.627632,99.9982143 457.329333,99.9982143 L242.670667,99.9982143 C147.372368,99.9982143 124.122784,112.960483 118.547883,118.535383 C112.972983,124.122784 99.9982143,147.372368 99.9982143,242.658167 L99.9982143,457.329333 C99.9982143,552.627632 112.972983,575.877216 118.547883,581.464617 C124.122784,587.027017 147.372368,600.001786 242.670667,600.001786 L457.329333,600.001786 C552.627632,600.001786 575.877216,587.027017 581.464617,581.464617 C587.039517,575.877216 600.001786,552.627632 600.001786,457.329333 Z M457.329333,0 C653.338333,0 700,46.6616668 700,242.658167 C700,438.667167 700,261.332833 700,457.329333 C700,653.338333 653.338333,700 457.329333,700 C261.332833,700 438.667167,700 242.670667,700 C46.6616668,700 0,653.338333 0,457.329333 C0,261.332833 0,352.118712 0,242.658167 C0,46.6616668 46.6616668,0 242.670667,0 C438.667167,0 261.332833,0 457.329333,0 Z M395.996667,200 C480.004166,200 500,220.008332 500,303.990835 C500,387.998334 500,312.001666 500,395.996667 C500,479.991668 480.004166,500 395.996667,500 C312.001666,500 387.998334,500 304.003333,500 C220.008332,500 200,479.991668 200,395.996667 C200,312.001666 200,350.906061 200,303.990835 C200,220.008332 220.008332,200 304.003333,200 C387.998334,200 312.001666,200 395.996667,200 Z"></path>
          <g id="vk_logo-8531513">
            <path fill="#000000" d="M253.066667,0 C457.466667,0 272.533333,0 476.933333,0 C681.333333,0 730,48.6666667 730,253.066667 C730,457.466667 730,272.533333 730,476.933333 C730,681.333333 681.333333,730 476.933333,730 C272.533333,730 457.466667,730 253.066667,730 C48.6666667,730 0,681.333333 0,476.933333 C0,272.533333 0,367.206459 0,253.066667 C0,48.6666667 48.6666667,0 253.066667,0 Z"></path><path fill="#FFF" d="M597.816744,251.493445 C601.198942,240.214758 597.816746,231.927083 581.719678,231.927083 L528.490512,231.927083 C514.956087,231.927083 508.716524,239.08642 505.332448,246.981031 C505.332448,246.981031 478.263599,312.960647 439.917002,355.818719 C427.510915,368.224806 421.871102,372.172112 415.10389,372.172112 C411.720753,372.172112 406.822917,368.224806 406.822917,356.947057 L406.822917,251.493445 C406.822917,237.95902 402.895137,231.927083 391.615512,231.927083 L307.969678,231.927083 C299.511836,231.927083 294.425223,238.208719 294.425223,244.162063 C294.425223,256.99245 313.597583,259.951287 315.573845,296.043086 L315.573845,374.428788 C315.573845,391.614583 312.470184,394.730425 305.702972,394.730425 C287.658011,394.730425 243.763595,328.456052 217.730151,252.620844 C212.628223,237.881107 207.511068,231.927083 193.907178,231.927083 L140.678012,231.927083 C125.469678,231.927083 122.427826,239.08642 122.427826,246.981031 C122.427826,261.079625 140.473725,331.006546 206.452402,423.489903 C250.437874,486.648674 312.410515,520.885417 368.803012,520.885417 C402.638134,520.885417 406.823845,513.28125 406.823845,500.183098 L406.823845,452.447917 C406.823845,437.239583 410.029185,434.204421 420.743703,434.204421 C428.638315,434.204421 442.172739,438.151727 473.753063,468.603713 C509.843923,504.694573 515.79398,520.885417 536.094678,520.885417 L589.323845,520.885417 C604.532178,520.885417 612.136345,513.28125 607.749619,498.274853 C602.949226,483.318593 585.717788,461.619053 562.853283,435.89599 C550.446258,421.234166 531.837128,405.444943 526.197316,397.548454 C518.302704,387.399043 520.558441,382.88663 526.197316,373.864619 C526.197316,373.864619 591.049532,282.508661 597.816744,251.493445 Z"></path>
          </g>
          <clipPath id="logo-mask-8531513">
            <rect x="0" y="0" width="750" height="750"></rect>
          </clipPath>
        </defs>



        <g transform="translate(0,0)">
        <g transform="translate(776,0)"><use xlink:href="#empty-8531513"></use></g>
        <g transform="translate(873,0)"><use xlink:href="#n_rb-8531513"></use></g>
        <g transform="translate(970,0)"><use xlink:href="#b-8531513"></use></g>
        <g transform="translate(1067,0)"><use xlink:href="#n_rb-8531513"></use></g>
        <g transform="translate(1164,0)"><use xlink:href="#b-8531513"></use></g>
        <g transform="translate(1358,0)"><use xlink:href="#empty-8531513"></use></g>
        <g transform="translate(1649,0)"><use xlink:href="#empty-8531513"></use></g>
        <g transform="translate(1843,0)"><use xlink:href="#r-8531513"></use></g>
        <g transform="translate(1940,0)"><use xlink:href="#lb-8531513"></use></g>
        <g transform="translate(2134,0)"><use xlink:href="#rb-8531513"></use></g>
        <g transform="translate(2231,0)"><use xlink:href="#lb-8531513"></use></g>
        <g transform="translate(776,97)"><use xlink:href="#n_rb-8531513"></use></g>
        <g transform="translate(873,97)"><use xlink:href="#rb-8531513"></use></g>
        <g transform="translate(970,97)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1067,97)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1164,97)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1746,97)"><use xlink:href="#b-8531513"></use></g>
        <g transform="translate(1940,97)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2134,97)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2231,97)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2328,97)"><use xlink:href="#l-8531513"></use></g>
        <g transform="translate(776,194)"><use xlink:href="#rb-8531513"></use></g>
        <g transform="translate(873,194)"><use xlink:href="#lt-8531513"></use></g>
        <g transform="translate(1067,194)"><use xlink:href="#rt-8531513"></use></g>
        <g transform="translate(1164,194)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1261,194)"><use xlink:href="#l-8531513"></use></g>
        <g transform="translate(1649,194)"><use xlink:href="#n_rb-8531513"></use></g>
        <g transform="translate(1746,194)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1940,194)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2134,194)"><use xlink:href="#t-8531513"></use></g>
        <g transform="translate(776,291)"><use xlink:href="#t-8531513"></use></g>
        <g transform="translate(873,291)"><use xlink:href="#n_rb-8531513"></use></g>
        <g transform="translate(970,291)"><use xlink:href="#b-8531513"></use></g>
        <g transform="translate(1067,291)"><use xlink:href="#n_rb-8531513"></use></g>
        <g transform="translate(1164,291)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1358,291)"><use xlink:href="#r-8531513"></use></g>
        <g transform="translate(1455,291)"><use xlink:href="#l-8531513"></use></g>
        <g transform="translate(1649,291)"><use xlink:href="#rb-8531513"></use></g>
        <g transform="translate(1746,291)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1843,291)"><use xlink:href="#n_rb-8531513"></use></g>
        <g transform="translate(1940,291)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2231,291)"><use xlink:href="#n_rb-8531513"></use></g>
        <g transform="translate(2328,291)"><use xlink:href="#b-8531513"></use></g>
        <g transform="translate(873,388)"><use xlink:href="#r-8531513"></use></g>
        <g transform="translate(970,388)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1067,388)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1164,388)"><use xlink:href="#lt-8531513"></use></g>
        <g transform="translate(1649,388)"><use xlink:href="#rt-8531513"></use></g>
        <g transform="translate(1746,388)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1843,388)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1940,388)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2037,388)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2134,388)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2231,388)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2328,388)"><use xlink:href="#lt-8531513"></use></g>
        <g transform="translate(776,485)"><use xlink:href="#b-8531513"></use></g>
        <g transform="translate(970,485)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1067,485)"><use xlink:href="#lt-8531513"></use></g>
        <g transform="translate(1358,485)"><use xlink:href="#rb-8531513"></use></g>
        <g transform="translate(1455,485)"><use xlink:href="#l-8531513"></use></g>
        <g transform="translate(1843,485)"><use xlink:href="#t-8531513"></use></g>
        <g transform="translate(2037,485)"><use xlink:href="#rt-8531513"></use></g>
        <g transform="translate(2134,485)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(776,582)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(873,582)"><use xlink:href="#n_rb-8531513"></use></g>
        <g transform="translate(970,582)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1164,582)"><use xlink:href="#empty-8531513"></use></g>
        <g transform="translate(1358,582)"><use xlink:href="#t-8531513"></use></g>
        <g transform="translate(1552,582)"><use xlink:href="#b-8531513"></use></g>
        <g transform="translate(1649,582)"><use xlink:href="#n_rb-8531513"></use></g>
        <g transform="translate(1746,582)"><use xlink:href="#b-8531513"></use></g>
        <g transform="translate(1940,582)"><use xlink:href="#empty-8531513"></use></g>
        <g transform="translate(2134,582)"><use xlink:href="#t-8531513"></use></g>
        <g transform="translate(2328,582)"><use xlink:href="#b-8531513"></use></g>
        <g transform="translate(679,679)"><use xlink:href="#n_rb-8531513"></use></g>
        <g transform="translate(776,679)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(873,679)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(970,679)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1261,679)"><use xlink:href="#empty-8531513"></use></g>
        <g transform="translate(1552,679)"><use xlink:href="#rt-8531513"></use></g>
        <g transform="translate(1649,679)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1746,679)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2037,679)"><use xlink:href="#b-8531513"></use></g>
        <g transform="translate(2231,679)"><use xlink:href="#n_rb-8531513"></use></g>
        <g transform="translate(2328,679)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(97,776)"><use xlink:href="#empty-8531513"></use></g>
        <g transform="translate(291,776)"><use xlink:href="#empty-8531513"></use></g>
        <g transform="translate(485,776)"><use xlink:href="#r-8531513"></use></g>
        <g transform="translate(582,776)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(679,776)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(776,776)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(970,776)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1746,776)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2037,776)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2134,776)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2231,776)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2328,776)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2425,776)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2522,776)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2619,776)"><use xlink:href="#lb-8531513"></use></g>
        <g transform="translate(2813,776)"><use xlink:href="#r-8531513"></use></g>
        <g transform="translate(2910,776)"><use xlink:href="#l-8531513"></use></g>
        <g transform="translate(3104,776)"><use xlink:href="#b-8531513"></use></g>
        <g transform="translate(194,873)"><use xlink:href="#b-8531513"></use></g>
        <g transform="translate(388,873)"><use xlink:href="#empty-8531513"></use></g>
        <g transform="translate(679,873)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(776,873)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(873,873)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(970,873)"><use xlink:href="#lt-8531513"></use></g>
        <g transform="translate(1067,873)"><use xlink:href="#n_rb-8531513"></use></g>
        <g transform="translate(1164,873)"><use xlink:href="#rb-8531513"></use></g>
        <g transform="translate(1261,873)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1358,873)"><use xlink:href="#lb-8531513"></use></g>
        <g transform="translate(1649,873)"><use xlink:href="#r-8531513"></use></g>
        <g transform="translate(1746,873)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2037,873)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2328,873)"><use xlink:href="#rt-8531513"></use></g>
        <g transform="translate(2425,873)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2522,873)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2619,873)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(3007,873)"><use xlink:href="#n_rb-8531513"></use></g>
        <g transform="translate(3104,873)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(0,970)"><use xlink:href="#b-8531513"></use></g>
        <g transform="translate(194,970)"><use xlink:href="#t-8531513"></use></g>
        <g transform="translate(582,970)"><use xlink:href="#r-8531513"></use></g>
        <g transform="translate(679,970)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(776,970)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(873,970)"><use xlink:href="#lt-8531513"></use></g>
        <g transform="translate(1067,970)"><use xlink:href="#r-8531513"></use></g>
        <g transform="translate(1164,970)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1261,970)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1358,970)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1746,970)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1843,970)"><use xlink:href="#l-8531513"></use></g>
        <g transform="translate(1940,970)"><use xlink:href="#n_rb-8531513"></use></g>
        <g transform="translate(2037,970)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2425,970)"><use xlink:href="#rt-8531513"></use></g>
        <g transform="translate(2522,970)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2619,970)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2716,970)"><use xlink:href="#lb-8531513"></use></g>
        <g transform="translate(2910,970)"><use xlink:href="#r-8531513"></use></g>
        <g transform="translate(3007,970)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(3104,970)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(0,1067)"><use xlink:href="#rt-8531513"></use></g>
        <g transform="translate(97,1067)"><use xlink:href="#lb-8531513"></use></g>
        <g transform="translate(291,1067)"><use xlink:href="#empty-8531513"></use></g>
        <g transform="translate(388,1067)"><use xlink:href="#n_rb-8531513"></use></g>
        <g transform="translate(485,1067)"><use xlink:href="#b-8531513"></use></g>
        <g transform="translate(679,1067)"><use xlink:href="#rt-8531513"></use></g>
        <g transform="translate(776,1067)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(970,1067)"><use xlink:href="#empty-8531513"></use></g>
        <g transform="translate(1164,1067)"><use xlink:href="#t-8531513"></use></g>
        <g transform="translate(1358,1067)"><use xlink:href="#rt-8531513"></use></g>
        <g transform="translate(1455,1067)"><use xlink:href="#l-8531513"></use></g>
        <g transform="translate(1746,1067)"><use xlink:href="#t-8531513"></use></g>
        <g transform="translate(1940,1067)"><use xlink:href="#r-8531513"></use></g>
        <g transform="translate(2037,1067)"><use xlink:href="#lt-8531513"></use></g>
        <g transform="translate(2231,1067)"><use xlink:href="#empty-8531513"></use></g>
        <g transform="translate(2716,1067)"><use xlink:href="#rt-8531513"></use></g>
        <g transform="translate(2813,1067)"><use xlink:href="#lb-8531513"></use></g>
        <g transform="translate(3007,1067)"><use xlink:href="#rt-8531513"></use></g>
        <g transform="translate(3104,1067)"><use xlink:href="#lt-8531513"></use></g>
        <g transform="translate(97,1164)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(388,1164)"><use xlink:href="#rb-8531513"></use></g>
        <g transform="translate(485,1164)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(582,1164)"><use xlink:href="#l-8531513"></use></g>
        <g transform="translate(776,1164)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(873,1164)"><use xlink:href="#l-8531513"></use></g>
        <g transform="translate(2134,1164)"><use xlink:href="#b-8531513"></use></g>
        <g transform="translate(2425,1164)"><use xlink:href="#n_rb-8531513"></use></g>
        <g transform="translate(2522,1164)"><use xlink:href="#rb-8531513"></use></g>
        <g transform="translate(2619,1164)"><use xlink:href="#lb-8531513"></use></g>
        <g transform="translate(2813,1164)"><use xlink:href="#t-8531513"></use></g>
        <g transform="translate(0,1261)"><use xlink:href="#n_rb-8531513"></use></g>
        <g transform="translate(97,1261)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(388,1261)"><use xlink:href="#t-8531513"></use></g>
        <g transform="translate(679,1261)"><use xlink:href="#n_rb-8531513"></use></g>
        <g transform="translate(776,1261)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2134,1261)"><use xlink:href="#t-8531513"></use></g>
        <g transform="translate(2425,1261)"><use xlink:href="#rb-8531513"></use></g>
        <g transform="translate(2522,1261)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2619,1261)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2716,1261)"><use xlink:href="#l-8531513"></use></g>
        <g transform="translate(2910,1261)"><use xlink:href="#n_rb-8531513"></use></g>
        <g transform="translate(3007,1261)"><use xlink:href="#rb-8531513"></use></g>
        <g transform="translate(3104,1261)"><use xlink:href="#l-8531513"></use></g>
        <g transform="translate(0,1358)"><use xlink:href="#r-8531513"></use></g>
        <g transform="translate(97,1358)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(194,1358)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(291,1358)"><use xlink:href="#l-8531513"></use></g>
        <g transform="translate(485,1358)"><use xlink:href="#r-8531513"></use></g>
        <g transform="translate(582,1358)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(679,1358)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(776,1358)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2037,1358)"><use xlink:href="#b-8531513"></use></g>
        <g transform="translate(2231,1358)"><use xlink:href="#empty-8531513"></use></g>
        <g transform="translate(2425,1358)"><use xlink:href="#rt-8531513"></use></g>
        <g transform="translate(2522,1358)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2619,1358)"><use xlink:href="#lt-8531513"></use></g>
        <g transform="translate(2910,1358)"><use xlink:href="#r-8531513"></use></g>
        <g transform="translate(3007,1358)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(97,1455)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(776,1455)"><use xlink:href="#rt-8531513"></use></g>
        <g transform="translate(873,1455)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(970,1455)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1067,1455)"><use xlink:href="#lb-8531513"></use></g>
        <g transform="translate(2037,1455)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2134,1455)"><use xlink:href="#lb-8531513"></use></g>
        <g transform="translate(2328,1455)"><use xlink:href="#b-8531513"></use></g>
        <g transform="translate(2522,1455)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2716,1455)"><use xlink:href="#rb-8531513"></use></g>
        <g transform="translate(2813,1455)"><use xlink:href="#lb-8531513"></use></g>
        <g transform="translate(3007,1455)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(0,1552)"><use xlink:href="#r-8531513"></use></g>
        <g transform="translate(97,1552)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(388,1552)"><use xlink:href="#n_rb-8531513"></use></g>
        <g transform="translate(485,1552)"><use xlink:href="#rb-8531513"></use></g>
        <g transform="translate(582,1552)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(679,1552)"><use xlink:href="#lb-8531513"></use></g>
        <g transform="translate(1067,1552)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2037,1552)"><use xlink:href="#rt-8531513"></use></g>
        <g transform="translate(2134,1552)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2231,1552)"><use xlink:href="#n_rb-8531513"></use></g>
        <g transform="translate(2328,1552)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2522,1552)"><use xlink:href="#t-8531513"></use></g>
        <g transform="translate(2716,1552)"><use xlink:href="#rt-8531513"></use></g>
        <g transform="translate(2813,1552)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(3007,1552)"><use xlink:href="#t-8531513"></use></g>
        <g transform="translate(97,1649)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(194,1649)"><use xlink:href="#lb-8531513"></use></g>
        <g transform="translate(291,1649)"><use xlink:href="#n_rb-8531513"></use></g>
        <g transform="translate(388,1649)"><use xlink:href="#rb-8531513"></use></g>
        <g transform="translate(485,1649)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(679,1649)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(873,1649)"><use xlink:href="#r-8531513"></use></g>
        <g transform="translate(970,1649)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1067,1649)"><use xlink:href="#lt-8531513"></use></g>
        <g transform="translate(2134,1649)"><use xlink:href="#rt-8531513"></use></g>
        <g transform="translate(2231,1649)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2328,1649)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2619,1649)"><use xlink:href="#empty-8531513"></use></g>
        <g transform="translate(2813,1649)"><use xlink:href="#t-8531513"></use></g>
        <g transform="translate(0,1746)"><use xlink:href="#n_rb-8531513"></use></g>
        <g transform="translate(97,1746)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(194,1746)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(291,1746)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(388,1746)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(485,1746)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(582,1746)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(679,1746)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(776,1746)"><use xlink:href="#l-8531513"></use></g>
        <g transform="translate(2037,1746)"><use xlink:href="#empty-8531513"></use></g>
        <g transform="translate(2328,1746)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2910,1746)"><use xlink:href="#empty-8531513"></use></g>
        <g transform="translate(3007,1746)"><use xlink:href="#n_rb-8531513"></use></g>
        <g transform="translate(3104,1746)"><use xlink:href="#b-8531513"></use></g>
        <g transform="translate(0,1843)"><use xlink:href="#r-8531513"></use></g>
        <g transform="translate(97,1843)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(194,1843)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(291,1843)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(679,1843)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(970,1843)"><use xlink:href="#b-8531513"></use></g>
        <g transform="translate(2134,1843)"><use xlink:href="#empty-8531513"></use></g>
        <g transform="translate(2328,1843)"><use xlink:href="#rt-8531513"></use></g>
        <g transform="translate(2425,1843)"><use xlink:href="#l-8531513"></use></g>
        <g transform="translate(2619,1843)"><use xlink:href="#b-8531513"></use></g>
        <g transform="translate(3007,1843)"><use xlink:href="#r-8531513"></use></g>
        <g transform="translate(3104,1843)"><use xlink:href="#lt-8531513"></use></g>
        <g transform="translate(97,1940)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(194,1940)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(291,1940)"><use xlink:href="#lt-8531513"></use></g>
        <g transform="translate(388,1940)"><use xlink:href="#n_rb-8531513"></use></g>
        <g transform="translate(485,1940)"><use xlink:href="#rb-8531513"></use></g>
        <g transform="translate(582,1940)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(679,1940)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(776,1940)"><use xlink:href="#l-8531513"></use></g>
        <g transform="translate(970,1940)"><use xlink:href="#rt-8531513"></use></g>
        <g transform="translate(1067,1940)"><use xlink:href="#l-8531513"></use></g>
        <g transform="translate(2619,1940)"><use xlink:href="#rt-8531513"></use></g>
        <g transform="translate(2716,1940)"><use xlink:href="#l-8531513"></use></g>
        <g transform="translate(0,2037)"><use xlink:href="#n_rb-8531513"></use></g>
        <g transform="translate(97,2037)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(388,2037)"><use xlink:href="#rb-8531513"></use></g>
        <g transform="translate(485,2037)"><use xlink:href="#lt-8531513"></use></g>
        <g transform="translate(776,2037)"><use xlink:href="#n_rb-8531513"></use></g>
        <g transform="translate(873,2037)"><use xlink:href="#b-8531513"></use></g>
        <g transform="translate(1067,2037)"><use xlink:href="#n_rb-8531513"></use></g>
        <g transform="translate(1164,2037)"><use xlink:href="#b-8531513"></use></g>
        <g transform="translate(1358,2037)"><use xlink:href="#empty-8531513"></use></g>
        <g transform="translate(1455,2037)"><use xlink:href="#n_rb-8531513"></use></g>
        <g transform="translate(1552,2037)"><use xlink:href="#b-8531513"></use></g>
        <g transform="translate(1746,2037)"><use xlink:href="#r-8531513"></use></g>
        <g transform="translate(1843,2037)"><use xlink:href="#lb-8531513"></use></g>
        <g transform="translate(2037,2037)"><use xlink:href="#empty-8531513"></use></g>
        <g transform="translate(2522,2037)"><use xlink:href="#b-8531513"></use></g>
        <g transform="translate(2813,2037)"><use xlink:href="#empty-8531513"></use></g>
        <g transform="translate(3007,2037)"><use xlink:href="#r-8531513"></use></g>
        <g transform="translate(3104,2037)"><use xlink:href="#lb-8531513"></use></g>
        <g transform="translate(0,2134)"><use xlink:href="#r-8531513"></use></g>
        <g transform="translate(97,2134)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(291,2134)"><use xlink:href="#n_rb-8531513"></use></g>
        <g transform="translate(388,2134)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(582,2134)"><use xlink:href="#r-8531513"></use></g>
        <g transform="translate(679,2134)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(776,2134)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(873,2134)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1067,2134)"><use xlink:href="#r-8531513"></use></g>
        <g transform="translate(1164,2134)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1261,2134)"><use xlink:href="#l-8531513"></use></g>
        <g transform="translate(1455,2134)"><use xlink:href="#r-8531513"></use></g>
        <g transform="translate(1552,2134)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1649,2134)"><use xlink:href="#lb-8531513"></use></g>
        <g transform="translate(1843,2134)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2134,2134)"><use xlink:href="#empty-8531513"></use></g>
        <g transform="translate(2328,2134)"><use xlink:href="#empty-8531513"></use></g>
        <g transform="translate(2522,2134)"><use xlink:href="#t-8531513"></use></g>
        <g transform="translate(3104,2134)"><use xlink:href="#t-8531513"></use></g>
        <g transform="translate(97,2231)"><use xlink:href="#rt-8531513"></use></g>
        <g transform="translate(194,2231)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(291,2231)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(388,2231)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(679,2231)"><use xlink:href="#t-8531513"></use></g>
        <g transform="translate(873,2231)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1358,2231)"><use xlink:href="#b-8531513"></use></g>
        <g transform="translate(1455,2231)"><use xlink:href="#n_rb-8531513"></use></g>
        <g transform="translate(1552,2231)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1649,2231)"><use xlink:href="#lt-8531513"></use></g>
        <g transform="translate(1843,2231)"><use xlink:href="#rt-8531513"></use></g>
        <g transform="translate(1940,2231)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2037,2231)"><use xlink:href="#lb-8531513"></use></g>
        <g transform="translate(2231,2231)"><use xlink:href="#empty-8531513"></use></g>
        <g transform="translate(2328,2231)"><use xlink:href="#n_rb-8531513"></use></g>
        <g transform="translate(2425,2231)"><use xlink:href="#b-8531513"></use></g>
        <g transform="translate(3007,2231)"><use xlink:href="#b-8531513"></use></g>
        <g transform="translate(0,2328)"><use xlink:href="#empty-8531513"></use></g>
        <g transform="translate(291,2328)"><use xlink:href="#rt-8531513"></use></g>
        <g transform="translate(388,2328)"><use xlink:href="#lt-8531513"></use></g>
        <g transform="translate(582,2328)"><use xlink:href="#empty-8531513"></use></g>
        <g transform="translate(776,2328)"><use xlink:href="#rb-8531513"></use></g>
        <g transform="translate(873,2328)"><use xlink:href="#lt-8531513"></use></g>
        <g transform="translate(1067,2328)"><use xlink:href="#r-8531513"></use></g>
        <g transform="translate(1164,2328)"><use xlink:href="#l-8531513"></use></g>
        <g transform="translate(1358,2328)"><use xlink:href="#rt-8531513"></use></g>
        <g transform="translate(1455,2328)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1552,2328)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1940,2328)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2037,2328)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2231,2328)"><use xlink:href="#n_rb-8531513"></use></g>
        <g transform="translate(2328,2328)"><use xlink:href="#rb-8531513"></use></g>
        <g transform="translate(2425,2328)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2522,2328)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2619,2328)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2716,2328)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2813,2328)"><use xlink:href="#lb-8531513"></use></g>
        <g transform="translate(3007,2328)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(3104,2328)"><use xlink:href="#lb-8531513"></use></g>
        <g transform="translate(776,2425)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1164,2425)"><use xlink:href="#n_rb-8531513"></use></g>
        <g transform="translate(1261,2425)"><use xlink:href="#b-8531513"></use></g>
        <g transform="translate(1552,2425)"><use xlink:href="#rt-8531513"></use></g>
        <g transform="translate(1649,2425)"><use xlink:href="#lb-8531513"></use></g>
        <g transform="translate(1843,2425)"><use xlink:href="#r-8531513"></use></g>
        <g transform="translate(1940,2425)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2037,2425)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2134,2425)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2231,2425)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2328,2425)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2716,2425)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2813,2425)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(3007,2425)"><use xlink:href="#rt-8531513"></use></g>
        <g transform="translate(3104,2425)"><use xlink:href="#lt-8531513"></use></g>
        <g transform="translate(776,2522)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(873,2522)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(970,2522)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1067,2522)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1164,2522)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1261,2522)"><use xlink:href="#lt-8531513"></use></g>
        <g transform="translate(1358,2522)"><use xlink:href="#n_rb-8531513"></use></g>
        <g transform="translate(1455,2522)"><use xlink:href="#b-8531513"></use></g>
        <g transform="translate(1649,2522)"><use xlink:href="#rt-8531513"></use></g>
        <g transform="translate(1746,2522)"><use xlink:href="#lb-8531513"></use></g>
        <g transform="translate(2134,2522)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2328,2522)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2522,2522)"><use xlink:href="#empty-8531513"></use></g>
        <g transform="translate(2716,2522)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2813,2522)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2910,2522)"><use xlink:href="#l-8531513"></use></g>
        <g transform="translate(776,2619)"><use xlink:href="#t-8531513"></use></g>
        <g transform="translate(970,2619)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1164,2619)"><use xlink:href="#t-8531513"></use></g>
        <g transform="translate(1358,2619)"><use xlink:href="#r-8531513"></use></g>
        <g transform="translate(1455,2619)"><use xlink:href="#lt-8531513"></use></g>
        <g transform="translate(1746,2619)"><use xlink:href="#rt-8531513"></use></g>
        <g transform="translate(1843,2619)"><use xlink:href="#lb-8531513"></use></g>
        <g transform="translate(2037,2619)"><use xlink:href="#r-8531513"></use></g>
        <g transform="translate(2134,2619)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2231,2619)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2328,2619)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2619,2619)"><use xlink:href="#n_rb-8531513"></use></g>
        <g transform="translate(2716,2619)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(3007,2619)"><use xlink:href="#rb-8531513"></use></g>
        <g transform="translate(3104,2619)"><use xlink:href="#lb-8531513"></use></g>
        <g transform="translate(873,2716)"><use xlink:href="#n_rb-8531513"></use></g>
        <g transform="translate(970,2716)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1067,2716)"><use xlink:href="#l-8531513"></use></g>
        <g transform="translate(1164,2716)"><use xlink:href="#n_rb-8531513"></use></g>
        <g transform="translate(1261,2716)"><use xlink:href="#b-8531513"></use></g>
        <g transform="translate(1552,2716)"><use xlink:href="#rb-8531513"></use></g>
        <g transform="translate(1649,2716)"><use xlink:href="#lb-8531513"></use></g>
        <g transform="translate(1746,2716)"><use xlink:href="#n_rb-8531513"></use></g>
        <g transform="translate(1843,2716)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1940,2716)"><use xlink:href="#l-8531513"></use></g>
        <g transform="translate(2328,2716)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2425,2716)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2522,2716)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2619,2716)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2716,2716)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2813,2716)"><use xlink:href="#lb-8531513"></use></g>
        <g transform="translate(3007,2716)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(3104,2716)"><use xlink:href="#lt-8531513"></use></g>
        <g transform="translate(776,2813)"><use xlink:href="#r-8531513"></use></g>
        <g transform="translate(873,2813)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(970,2813)"><use xlink:href="#lt-8531513"></use></g>
        <g transform="translate(1164,2813)"><use xlink:href="#r-8531513"></use></g>
        <g transform="translate(1261,2813)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1552,2813)"><use xlink:href="#rt-8531513"></use></g>
        <g transform="translate(1649,2813)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1746,2813)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1843,2813)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2134,2813)"><use xlink:href="#r-8531513"></use></g>
        <g transform="translate(2231,2813)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2328,2813)"><use xlink:href="#lt-8531513"></use></g>
        <g transform="translate(2522,2813)"><use xlink:href="#t-8531513"></use></g>
        <g transform="translate(2716,2813)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2813,2813)"><use xlink:href="#lt-8531513"></use></g>
        <g transform="translate(3007,2813)"><use xlink:href="#t-8531513"></use></g>
        <g transform="translate(873,2910)"><use xlink:href="#t-8531513"></use></g>
        <g transform="translate(1067,2910)"><use xlink:href="#b-8531513"></use></g>
        <g transform="translate(1164,2910)"><use xlink:href="#n_rb-8531513"></use></g>
        <g transform="translate(1261,2910)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1455,2910)"><use xlink:href="#b-8531513"></use></g>
        <g transform="translate(1746,2910)"><use xlink:href="#rt-8531513"></use></g>
        <g transform="translate(1843,2910)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1940,2910)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2037,2910)"><use xlink:href="#lb-8531513"></use></g>
        <g transform="translate(2716,2910)"><use xlink:href="#t-8531513"></use></g>
        <g transform="translate(2910,2910)"><use xlink:href="#empty-8531513"></use></g>
        <g transform="translate(3104,2910)"><use xlink:href="#empty-8531513"></use></g>
        <g transform="translate(776,3007)"><use xlink:href="#empty-8531513"></use></g>
        <g transform="translate(1067,3007)"><use xlink:href="#rt-8531513"></use></g>
        <g transform="translate(1164,3007)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1261,3007)"><use xlink:href="#lt-8531513"></use></g>
        <g transform="translate(1455,3007)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1552,3007)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1649,3007)"><use xlink:href="#lb-8531513"></use></g>
        <g transform="translate(1746,3007)"><use xlink:href="#n_rb-8531513"></use></g>
        <g transform="translate(1843,3007)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1940,3007)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2037,3007)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2231,3007)"><use xlink:href="#n_rb-8531513"></use></g>
        <g transform="translate(2328,3007)"><use xlink:href="#rb-8531513"></use></g>
        <g transform="translate(2425,3007)"><use xlink:href="#lb-8531513"></use></g>
        <g transform="translate(2813,3007)"><use xlink:href="#empty-8531513"></use></g>
        <g transform="translate(873,3104)"><use xlink:href="#empty-8531513"></use></g>
        <g transform="translate(1455,3104)"><use xlink:href="#rt-8531513"></use></g>
        <g transform="translate(1552,3104)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1649,3104)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1746,3104)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1843,3104)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(1940,3104)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2037,3104)"><use xlink:href="#lt-8531513"></use></g>
        <g transform="translate(2231,3104)"><use xlink:href="#r-8531513"></use></g>
        <g transform="translate(2328,3104)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2425,3104)"><use xlink:href="#rect-8531513"></use></g>
        <g transform="translate(2522,3104)"><use xlink:href="#l-8531513"></use></g>
        <g transform="translate(2716,3104)"><use xlink:href="#empty-8531513"></use></g>
        <g transform="translate(3007,3104)"><use xlink:href="#empty-8531513"></use></g>
        <use fill-rule="evenodd" transform="translate(0,0)" xlink:href="#point-8531513"></use>
        <use fill-rule="evenodd" transform="translate(2496,0)" xlink:href="#point-8531513"></use>
        <use fill-rule="evenodd" transform="translate(0,2496)" xlink:href="#point-8531513"></use>

                <use style="width: 750px; height: 750px;" width="750" height="750" fill="none" fill-rule="evenodd" transform="translate(1238,1238) " xlink:href="#vk_logo-8531513"></use>

            </g>
          </svg></g>



                </defs>
                <g clip-path="url(#main-mask-8531513)">
                    <rect width="250" height="250" style="fill:#ffffff"></rect>
                    <use xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="30" y="30" xlink:href="#qr-8531513" transform="scale(1)"></use>
                </g>
              </svg>
                     <p style="margin-top: 66px;font-size: 26px;">Понравилось - напиши. Не понравилось - <b>обязательно</b> напиши.</p>
        </div>

        <div class="clear"></div>        


        <a href="#" data-city="<?=$cityLat?>" data-print="<?=$printid?>" class="print-send">Получить ссылку на билет</a>
    </div>

    <style>
        body {
            background-color: #e2edff;            
            line-height: 1.6;
            font-family: "OsR", sans-serif;
        }
        .clear {
            clear: both;
        }
        .clear:after {
            content: "";
            display: block;
            clear: both;
        }
        .print {
            width: 1000px;
            padding: 10px 15px;
            margin: 0 auto;
            background-color: #fff;
            min-height: 600px;
            position: relative;
        }
        .list li{
            font-size: 25px;
        }

        .list li span{
            font-weight: bold;
        }

        th {
            border: 1px solid #000;
            text-align: center;
            font-size: 21px;
        }

        td {
            border: 1px solid #000;
            text-align: center;
            padding: 0 10px;
            font-size: 20px;
            line-height: 25px;
        }

        .bolded_print {
            font-weight: bold;
            font-size: 18px;
        }
    </style>
    </body>
</html>