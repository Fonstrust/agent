<?php
include_once('db.php');
require_once 'clases/mysql.php';
require_once 'clases/users.php';
require_once 'functions/php_func.php';
$pay_control = new Mysql('pay_control');
$passengers = new Mysql('passengers_data');
$db_pko = new Mysql('pko');
$db_count = new Mysql('count');
if (isset($_GET['pay_doc_type']) && isset($_GET['pay_doc_id'])) {
    $type = $_GET['pay_doc_type'];
    $doc_id = $_GET['pay_doc_id'];
    if ($type == 'pko') {
        $doc_type = $db_pko;
    } elseif ($type == 'count') {
        $doc_type = $db_count;
    }
    $arr_trans = getPayDocTransfers($doc_type, $doc_id, $pay_control, $passengers, $_SESSION['id']);
    if ($arr_trans === false) {
        header('Location: index.php');
    }
    echo '<table id="pko_id" data-id="' . $doc_id . '-' . $type . '">';
    echo '<tr>
            <th>#</th>
            <th>Отправление</th>
            <th>ФИО</th>
            <th>Стоимость</th>
            <th>Комиссия</th>
            <th>К оплате</th>
            <th id="check_to_del">Выбрать</th>
        </tr>
    ';
    foreach ($arr_trans as $trans) {
        $trans_len = count($trans);
        if ($trans[$trans_len - 1] == 0) {
            $class = 'non_payed';
        } else {
            $class = 'payed';
        }
        echo '<tr class="' . $class . '">';
        
        for ($i = 0; $i < $trans_len; $i++) {
            if ($i != $trans_len - 1) {
                echo '<td>' . $trans[$i] . '</td>';
            } else {
                echo '<td><input class="dels" type="checkbox" id="' . $trans[0] . '-check_for_del"</td>';
            }
        }
        
        echo '</tr>';
        
    }
    echo '</table>';
    echo '<button id="del_but" class="hide">Удалить выбранные</button>';
} else {
    header('Location: ?page=pay_history');
}
?>
<script type="text/javascript">
    async function asyncInside() {
        let pkoIdElem = document.getElementById('pko_id')
        let pkoId = pkoIdElem.dataset.id
        let delBut = document.getElementById('del_but')
        let dels = document.getElementsByClassName('dels')
        let delNum = dels.length
        for (let i = 0; i < delNum; i++) {
            dels[i].addEventListener('click', await delControl)
        }
        delBut.addEventListener('click', await delChecked)
        
        async function delChecked() {
            let checkedItems = await delControl()
            let result
            let searchParams = new URLSearchParams()
            searchParams.set('del_pko', checkedItems)
            searchParams.set('del_pko_id', pkoId)
            try {
                result = await getAjaxPost(searchParams)
            } catch(e) {
                console.error(e)
            }
            if (result == 'all') {
                window.location = '?page=pay_history'
            } else {
                window.location = '?page=pay_doc_inside&&pay_doc_type=' + pkoId.split('-')[1] + '&&pay_doc_id=' + pkoId.split('-')[0]
            }
            console.log(result)
        }
        async function delControl() {
            let delsArr = []
            for (let i = 0; i < delNum; i++) {
                if (dels[i].checked) {
                    delsArr.push(dels[i].id.split('-')[0])
                }
            }
            if (delsArr.length > 0) {
                delBut.classList.remove('hide')
                return delsArr
            } else {
                delBut.classList.add('hide')
            }
        }
        async function getAjaxPost(seachParams) {
            let result;
            try {
                const promise = await fetch('ajax/ajax_int_opt.php', {
                    method: 'POST',
                    body: seachParams,
                })
                const data = await promise.json()
                return data
                console.log(data)
            } catch (e) {
                console.error(e)
            } 
        }
        
    }
    asyncInside()
        
</script>