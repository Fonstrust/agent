<?php
require_once './clases/users.php';
require_once './clases/mysql.php';
$user = new User($_SESSION['id']);
?>
<div class="container">
    <div class="block-header">
        <img src="/elems/img/mytransfers_icon.png">
        <span class="block-head">Ближайшие трансферы</span>
    </div>
    <hr>
    <table class="my_transfers-table" id="nearest_table">
        <tr>
            <th>#</th>
            <th>Дата</th>
            <th>Время</th>
            <th>Направление</th>
            <th>Точка в МСК</th>
            <th>Тип трансфера</th>
            <th>ФИО</th>
            <th>Телефон</th>  
            <th>Оплата</th>    
            <th>Статус</th>     
            <th>Печать</th>            
        </tr>
    <?php
        $sql_my_transfers = $user->getUserTransfersASC5();
        $i = 0;

        while ($res_my_transfers = mysqli_fetch_array($sql_my_transfers)) {
            $depart_timestamp = strtotime($res_my_transfers['depart_time']);
            if ($res_my_transfers['direction'] == 'to_msk') {
                $direction = 'В Москву';
            }elseif ($res_my_transfers['direction'] == 'from_msk') {
                $direction = 'Из Москвы';
            }
            if ($res_my_transfers['type'] == 'ind') {
                $trans_type = 'Индивидуальный';
            } elseif ($res_my_transfers['type'] == 'group') {
                $trans_type = 'Групповой';
            }
            $pay_status = $user->getTransferPayStatus($res_my_transfers['ID']);
            if ($pay_status == 0) {
                $pay = '<i style="color: red" class="fa fa-times" aria-hidden="true"></i>';
            } else {
                $pay = '<i style="color: green" class="fa fa-check" aria-hidden="true"></i>';
            }
            if ($res_my_transfers['deleted'] == 0) {
                $status = 'Подтвержден';
            } elseif ($res_my_transfers['deleted'] == 1) {
                $status = 'Не подтвержден';
            } else {
                $status = 'Отменен';
            }
            $passenger = $user->getUserPassenger($res_my_transfers['passenger']);
            
            
            echo '<tr class="transfers_list" id="transfer_' . $res_my_transfers['ID'] . '">';
            echo '<td>' . $res_my_transfers['ID'] . '</td>';
            echo '<td>' . date('d.m.Y', $depart_timestamp) . '</td>';
            echo '<td>' . date('H:i', $depart_timestamp) . '</td>';
            echo '<td>' . $direction . '</td>';
            echo '<td>' . strtoupper($res_my_transfers['msk_point']) . '</td>';
            echo '<td>' . $trans_type . '</td>';
            echo '<td>' . str_replace(' ', '<br>', $passenger['fio']) . '</td>';
            echo '<td><nobr>' . $passenger['phone'] . '</nobr></td>';
            echo '<td>' . $pay . '</td>';
            echo '<td>' . $status . '</td>';
            echo '<td><a onclick="event.stopPropagation()" class="print_but" href="elems/print_ticket.php?print_id=' . $res_my_transfers['ID'] . '" target="_blank"><i class="fa fa-print" aria-hidden="true"></i></a></td>';
            echo '</tr>';
            $i++;
        }
        if ($i == 0) {
            echo 'На данный момент трансферов нет';
        }

    ?>
    </table>
</div>
<div class="container">
    <div class="block-header">
        <img src="/elems/img/mytransfers_icon.png">
        <span class="block-head">Мои трансферы</span>
    </div>
    <hr>
    <table class="my_transfers-table">
        <tr>
            <th>#</th>
            <th>Дата</th>
            <th>Время</th>
            <th>Направление</th>
            <th>Точка в МСК</th>
            <th>Тип трансфера</th>
            <th>ФИО</th>
            <th>Телефон</th>  
            <th>Оплата</th>    
            <th>Статус</th>     
            <th>Печать</th>            
        </tr>
        <?php
        $sql_my_transfers = $user->getUserTransfers();

        while ($res_my_transfers = mysqli_fetch_array($sql_my_transfers)) {
            $depart_timestamp = strtotime($res_my_transfers['depart_time']);
            if ($res_my_transfers['direction'] == 'to_msk') {
                $direction = 'В Москву';
            }elseif ($res_my_transfers['direction'] == 'from_msk') {
                $direction = 'Из Москвы';
            }
            if ($res_my_transfers['type'] == 'ind') {
                $trans_type = 'Индивидуальный';
            } elseif ($res_my_transfers['type'] == 'group') {
                $trans_type = 'Групповой';
            }
            $pay_status = $user->getTransferPayStatus($res_my_transfers['ID']);
            if ($pay_status == 0) {
                $pay = '<i style="color: red" class="fa fa-times" aria-hidden="true"></i>';
            } else {
                $pay = '<i style="color: green" class="fa fa-check" aria-hidden="true"></i>';
            }
            if ($res_my_transfers['deleted'] == 0) {
                $status = 'Подтвержден';
            } elseif ($res_my_transfers['deleted'] == 1) {
                $status = 'Не подтвержден';
            } else {
                $status = 'Отменен';
            }
            $passenger = $user->getUserPassenger($res_my_transfers['passenger']);
            
            
            echo '<tr class="transfers_list" id="transfer_' . $res_my_transfers['ID'] . '">';
            echo '<td>' . $res_my_transfers['ID'] . '</td>';
            echo '<td>' . date('d.m.Y', $depart_timestamp) . '</td>';
            echo '<td>' . date('H:i', $depart_timestamp) . '</td>';
            echo '<td>' . $direction . '</td>';
            echo '<td>' . strtoupper($res_my_transfers['msk_point']) . '</td>';
            echo '<td>' . $trans_type . '</td>';
            echo '<td>' . str_replace(' ', '<br>', $passenger['fio']) . '</td>';
            echo '<td><nobr>' . $passenger['phone'] . '</nobr></td>';
            echo '<td>' . $pay . '</td>';
            echo '<td>' . $status . '</td>';
            echo '<td><a onclick="event.stopPropagation()" class="print_but" href="elems/print_ticket.php?print_id=' . $res_my_transfers['ID'] . '" target="_blank"><i class="fa fa-print" aria-hidden="true"></i></a></td>';
            echo '</tr>';
        }
        ?>
    </table>
</div>
<script type="text/javascript">
//    let printButs = document.getElementsByClassName('print_but')
//    let printNum = printButs.length
//    for (let i = 0; i < printNum; i++) {
//        printButs[i].addEventListener('click', function(e){
//            e.stopPropagation()
//        })
//    }
    let myTransfers = document.getElementsByClassName('transfers_list')
    let transferLen = myTransfers.length
    for (let i = 0; i < transferLen; i++) {
        myTransfers[i].addEventListener('click', toRedact)
    }
    function toRedact() {
        let orderId = this.id.split('_')[1]
        window.location = '?list=order&&id=' + orderId
    }
</script>
<?php

