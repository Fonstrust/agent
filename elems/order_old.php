<?php
require_once './clases/users.php';
$user = new User($_SESSION['id']);
print_r($user->getIndTypes());
//echo date('d.m.Y / H:i', strtotime('2020-12-15 18:33'));
$group_dests = '';
$group_dests_sub_points = '';
$ind_dests_sub_points = '';
$user_conditions = '';
foreach ($user->getGroupDestsRu() as $dest) {
    $group_dests .= '<option value="' . $dest[0] . '_' . $dest[2] . '_' . $dest[3] . '">' . $dest[1] . '</option>';
    $group_dests_sub_points .= '<input type="hidden" id="sub_point-group-' . $dest[0] . '" value="' . $dest[4] . '">';
}
$ind_dests = '';
foreach ($user->getIndDestsRu() as $dest) {
    $ind_dests .= '<option value="' . $dest[0] . '_' . $dest[2] . '_' . $dest[3] . '">' . $dest[1] . '</option>';
    $ind_dests_sub_points .= '<input type="hidden" id="sub_point-ind-' . $dest[0] . '" value="' . $dest[4] . '">';
}
$obl_towns_opt = '<option value="off">Выберите город</option>';
foreach ($user->getOblTownsList() as $obl_town){
    $obl_towns_opt .= '<option value="' . $obl_town[0] .'_' . $obl_town[2] .'_' . $obl_town[3] .'">' . $obl_town[1] . '</option>';
}
$town_points = '';
foreach ($user->getTownPoints() as $value) {
    $town_points .= '<option value ="' . $value[0] . '_' . $value[2] . '">' . $value[1] . '</option>';
}
$ind_types_opt = '';
foreach ($user->getIndTypes() as $value=>$ind_type) {
    $ind_types_opt .= '<option value ="' . $value . '">' . $ind_type . '</option>';
}
$dest_types_messages = '';
foreach ($user->getDateTimeMessages() as $value) {
    $dest_time_plus = strtotime($value[2]) - strtotime('00:00:00');
    $dest_time_wait = strtotime($value[3]) - strtotime('00:00:00');
    $dest_types_messages .= '<p class="hide" data-time_plus="' . $dest_time_plus . '" data-time_wait="' . $dest_time_wait . '" id="dest_type_' . $value[0] . '">' . $value[1] . '</p>';
}
foreach ($user->getMaxPass() as $class=>$max_pass) {
    $user_conditions .= '<input type="hidden" id="min_pass_' . $class . '" value="' . $max_pass[0] . '">';
    $user_conditions .= '<input type="hidden" id="max_pass_' . $class . '" value="' . $max_pass[1] . '">';
}
$to_msk_schedule = '<input type="hidden" id="schedule-to_msk" value="';
$from_msk_schedule = '<input type="hidden" id="schedule-from_msk" value="';
$to_msk_str = '';
$from_msk_str = '';

foreach ($user->getSchedule() as $reis) {
    if ($reis[0] == 'to_msk') {
        $to_msk_str .= $reis[1] . ',';
    }
    if ($reis[0] == 'from_msk') {
        $from_msk_str .= $reis[1] . ',';
    }
}
$to_msk_str = trim($to_msk_str, ',');
$from_msk_str = trim($from_msk_str, ',');
    
$to_msk_schedule .= $to_msk_str . '">';
$from_msk_schedule .= $from_msk_str . '">';
$time_to = strtotime($user->getTimeToMsk()) - strtotime('00:00:00');
$user_conditions .= '<input type="hidden" id="order_int_id" value="' . $user->getIntId() . '">';
$user_conditions .= '<input type="hidden" id="order_time_to_msk" value="' . $time_to . '">';
$user_conditions .= $to_msk_schedule;
$user_conditions .= $from_msk_schedule;
?>
<h2>Заказ трансфера</h2>
<div class="step_by_step">
    <div class="order-step" id="order_step_1">
        <h3>Тип трансфера</h3>
        <select id="type_of_transfer">
            <option value="ind">Индивидуальный</option>
            <option value="group">Групповой</option>
        </select>
        <div id="ind_type">
            <h3>Выберите тариф</h3>
            <select id="ind_type_select">
                <?=$ind_types_opt?>
            </select>
        </div>
    </div>
    <div class="order-step" id="order_step_2">
        <h3>Направление</h3>
        <select id="transfer-direction">
            <option value="to_msk"><?=$user->getTownRu()?> - Москва</option>
            <option value="from_msk">Москва - <?=$user->getTownRu()?></option>
        </select>
        <h3 id="dests_h3">Точка высадки в Москве</h3>
        <select class="hide" id="transfer-group-destinations">
            <?=$group_dests?>
        </select>
        <select class="hide" id="transfer-group-destinations-sub_points">
            <?=$group_dests_sub_points?>
        </select>
        <select id="transfer-ind-destinations">
            <?=$ind_dests?>
        </select>
        <select class="hide" id="transfer-ind-destinations-sub_points">
            <?=$ind_dests_sub_points?>
        </select>
        <div id="reis_carret">
            <h3>№ авиарейса</h3>
            <input type="text" id="air_reis_input">
        </div>
    </div>
    <div class="order-step" id="order_step_3">
        <div id="ind_way_adress">
            <div id="adress_center">
                <h3>Адрес в г. <?=$user->getTownRu()?></h3>
                <p>Адрес не в черте города? <span id="obl_towns_button" style="text-decoration: underline;">Список областных городов</span></p>
            </div>
            <div id="obl_towns_list" class="hide">
                <h3>Областной город</h3>
                <p>Адрес в г. <?=$user->getTownRu()?>? <span id="center_button" style="text-decoration: underline;">Вернуться</span></p>
                <select id="obl_towns_list_select">
                    <?=$obl_towns_opt?>
                </select>
            </div>
            <div id="adr_place">
                <h3>Адрес</h3>
                <div id="adress_input_parent">
                    <input type="text" class="adress_input" id="ind-adress">
                </div>
                <button id="add_adress">Добавить адрес</button>
            </div>
        </div>
        <div class="hide" id="group_way_adress">
            <div id="group_depart_points">
                <h3>Выберите точку посадки</h3>
                <select id="town_points"><?=$town_points?></select>
            </div>
            <div class="hide" id="group_from_adress">
                <h3>Адрес в г. <?=$user->getTownRu()?></h3>
                <div id="adress_input_parent_group">
                    <input type="text" class="adress_input_group" id="group_adr_val">
                </div>
                <button id="add_adress_group">Добавить адрес</button>
            </div>
            <button id="group_adr">Забрать с адреса</button>
        </div>
    </div>
    <div class="order-step" id="order_step_4">
        <h3 id="date_time_head">Время прибытия</h3>
        <input type="date" id="date_set">
        <input type="time" id="time_set">
        <div class="flex-parent">
            <div class="flex-child">
                <p>Дата и время выезда</p>
                <p id="depart_date"></p>
            </div>
            <div class="flex-child">
                <p>Дата и время прибытия</p>
                <p id="arrive_date"></p>
            </div>
        </div>
    </div>
    <div class="order-step" id="order_step_5">
        <h3>Количество пассажиров</h3>
        <p>Взрослых</p>
        <input type="number" min="1" id="adlt_num_val">
        <p>Детей (до 12 лет)</p>
        <input type="number" min="0" value="0" id="chld_num_val">
        <p>Количество детских кресел</p>
        <input type="number" min="0" value="0" id="chld_seat_num_val">
        <p>Итого пассажиров:</p>
        <p id="all_pass_val"></p>
        <p>Комментарий к заказу</p>
        <textarea id="u_comment"></textarea>
    </div>
    <div class="order-step">
        <h3>Сумма заказа</h3>
        <p id="all_cost_view"></p>
        <h3>Заказ детально</h3>
        <div id="order_detail"></div>
    </div>
</div>
<div class="pass-place-1112">
    <div class="order-step w800" id="order_step_6">
        <h3>Данные о пассажирах</h3>
        <div id="passengers_place"></div>
    </div>

    <div class="order-step">
        <button id="sent_order">Заказать</button>
    </div>
</div>


<!--Технические элементы -->
<?=$dest_types_messages?>
<?=$user_conditions?>
<?=$group_dests_sub_points?>
<?=$ind_dests_sub_points?>
<!--------------------------------------------------------------->
<script type="text/javascript">
    let uComment = document.getElementById('u_comment')
    let chldSeatNumInput = document.getElementById('chld_seat_num_val')
    let airReisInput = document.getElementById('air_reis_input')
    let sentOrder = document.getElementById('sent_order')
    let allCostView = document.getElementById('all_cost_view')
    let orderDetail = document.getElementById('order_detail')
    let adressInputParent = document.getElementById('adress_input_parent')
    let addAdressBut = document.getElementById('add_adress')
    let adressInputParentGroup = document.getElementById('adress_input_parent_group')
    let addAdressButGroup = document.getElementById('add_adress_group')
    let adressCenter = document.getElementById('adress_center')
    let oblTownsButton = document.getElementById('obl_towns_button')
    let oblTownsList = document.getElementById('obl_towns_list')
    let adrPlace = document.getElementById('adr_place')
    let indAdress = document.getElementById('ind-adress')
    let oblTownsListSel = document.getElementById('obl_towns_list_select')
    let passengersPlace = document.getElementById('passengers_place')
    let adltNumValInput = document.getElementById('adlt_num_val')
    let chldNumValInput = document.getElementById('chld_num_val')
    let allPassValP = document.getElementById('all_pass_val')
    let typeOfTransfer = document.getElementById('type_of_transfer')
    let indWayAdress = document.getElementById('ind_way_adress')
    let groupWayAdress = document.getElementById('group_way_adress')
    let groupAdressBut = document.getElementById('group_adr')
    let groupAdressVal = document.getElementById('group_from_adress')
    let groupDepartPoints = document.getElementById('group_depart_points')
    let groupTownPoints = document.getElementById('town_points')
    let indType = document.getElementById('ind_type')
    let indTypeSelect = document.getElementById('ind_type_select')
    let dateTimeHead = document.getElementById('date_time_head')
    let dateSet = document.getElementById('date_set')
    let timeSet = document.getElementById('time_set')
    let departDateP = document.getElementById('depart_date')
    let arriveDateP = document.getElementById('arrive_date')
    let transferGroupDestinations = document.getElementById('transfer-group-destinations')
    let transferIndDestinations = document.getElementById('transfer-ind-destinations')
    let transferGroupDestinationSubPoints = document.getElementById('transfer-group-destinations-sub_points')
    let transferIndDestinationSubPoints = document.getElementById('transfer-ind-destinations-sub_points')
    let timeToMskVal = document.getElementById('order_time_to_msk').value
    let destsH3 = document.getElementById('dests_h3')
    let transferDirection = document.getElementById('transfer-direction')
    let groupCodeDests = transferGroupDestinations.children;
    let indCodeDests = transferIndDestinations.children;
    let toMskInput = document.getElementById('schedule-to_msk')
    let fromMskInput = document.getElementById('schedule-from_msk')
    let toMskSchedule = toMskInput.value.split(',')
    toMskSchedule.sort(sortTo)
    let fromMskSchedule = fromMskInput.value.split(',')
    fromMskSchedule.sort()
    console.log(fromMskSchedule)
    let groupCodeDestsArr = []
    let indCodeDestsArr = []
    let reisCarret = document.getElementById('reis_carret')
    let firstDestValInd = transferIndDestinations.value.split('_')
    let firstDestType = firstDestValInd[1]
    var groupAdressFlag = 0
    var oblTownFlag = 0
    if (firstDestType != 'air') {
        reisCarret.classList.add('hide')
    }
    for (let item of groupCodeDests){
        groupCodeDestsArr.push(item.value.split('_')[0])
    }
    console.log(groupCodeDestsArr)
    for (let item of indCodeDests){
        indCodeDestsArr.push(item.value.split('_')[0])
    }
    console.log(indCodeDestsArr)
    
    addPassData(0)
    
    adltNumValInput.addEventListener('input', countAllPass)
    chldNumValInput.addEventListener('input', countAllPass)
    
    addAdressButGroup.addEventListener('click', function(){
        let adressInputsLen = document.getElementsByClassName('adress_input_group').length
        if (adressInputsLen == 1) {
            let adressDelButton = document.createElement('button')
            adressDelButton.innerHTML = 'Убрать адрес'
            adressDelButton.addEventListener('click', delAdressInputGroup)
            adressDelButton.setAttribute('id', 'del_adress_but_group')
            groupAdressVal.appendChild(adressDelButton)
        }
        let input = document.createElement('input')
        input.classList.add('adress_input_group')
        input.setAttribute('type', 'text')
        adressInputParentGroup.appendChild(input)
    })
    function delAdressInputGroup() {
        let adressInputsLen = document.getElementsByClassName('adress_input_group').length
        let adressInputs = document.getElementsByClassName('adress_input_group')
        if (adressInputsLen > 2) {
            adressInputParentGroup.removeChild(adressInputs[adressInputsLen - 1])
        } else {
            adressInputParentGroup.removeChild(adressInputs[adressInputsLen - 1])
            groupAdressVal.removeChild(this)
        }
    }
    addAdressBut.addEventListener('click', function(){
        let adressInputsLen = document.getElementsByClassName('adress_input').length
        if (adressInputsLen == 1) {
            let adressDelButton = document.createElement('button')
            adressDelButton.innerHTML = 'Убрать адрес'
            adressDelButton.addEventListener('click', delAdressInput)
            adressDelButton.setAttribute('id', 'del_adress_but')
            adrPlace.appendChild(adressDelButton)
        }
        let input = document.createElement('input')
        input.classList.add('adress_input')
        input.setAttribute('type', 'text')
        adressInputParent.appendChild(input)
    })
    function delAdressInput() {
        let adressInputsLen = document.getElementsByClassName('adress_input').length
        let adressInputs = document.getElementsByClassName('adress_input')
        if (adressInputsLen > 2) {
            adressInputParent.removeChild(adressInputs[adressInputsLen - 1])
        } else {
            adressInputParent.removeChild(adressInputs[adressInputsLen - 1])
            adrPlace.removeChild(this)
        }
    }
    async function getAsync() {
        oblTownsListSel.addEventListener('change', await setDateTime)
        transferGroupDestinations.addEventListener('change', await setDateTime)
        transferIndDestinations.addEventListener('change', await setDateTime)
        typeOfTransfer.addEventListener('change', await setDateTime)
        dateSet.addEventListener('change', await setDateTime)
        timeSet.addEventListener('change', await setDateTime)
        async function setDateTime(){
            if (dateSet.value && timeSet.value) {                
                let destType = document.getElementById('transfer-' + typeOfTransfer.value + '-destinations').value.split('_')[1]
                let destElem = document.getElementById('dest_type_' + destType)
                let timePlusDestTypeVar = destElem.dataset.time_plus
                let timeWaitDestTypeVar = destElem.dataset.time_wait
                let timePlusMskVar = document.getElementById('transfer-' + typeOfTransfer.value + '-destinations').value.split('_')[2]
                let result = await getDepartTime(typeOfTransfer.value, transferDirection.value, timePlusMskVar, timePlusDestTypeVar, timeWaitDestTypeVar, groupTownPoints.value.split('_')[1], dateSet.value, timeSet.value, timeToMskVal, toMskSchedule, fromMskSchedule, oblTownsListSel.value)
                if (result[0] != 'fail'){
                    departDateP.innerHTML = addZero(result[0].getDate()) + '.' + addZero(result[0].getMonth() + 1) + '.' + addZero(result[0].getFullYear()) + ' / ' + addZero(result[0].getHours()) + ':' + addZero(result[0].getMinutes())
                    arriveDateP.innerHTML = addZero(result[1].getDate()) + '.' + addZero(result[1].getMonth() + 1) + '.' + addZero(result[1].getFullYear()) + ' / ' + addZero(result[1].getHours()) + ':' + addZero(result[1].getMinutes())
                } else {
                    departDateP.innerHTML = 'Некорректная дата'
                    arriveDateP.innerHTML = 'Некорректная дата'
                }
                return result
                
            }
        }
        async function getDepartTime(type, direction, timePlusMsk, timePlusDestType, timeWaitDestType, timePlusTown, dateVal, timeVal, timeToMsk, toMskSchedule, fromMskSchedule, oblTownPlus) {
            let dateTime = Date.parse(dateVal+'T'+timeVal+':00')
            let timePlus = 0
            if (oblTownPlus != 'off') {
                timePlus = parseInt(oblTownPlus.split('_')[1]) * 60 * 60 * 1000
            }
            let result = []
            if (direction == 'to_msk') {
                let allWayTime = timeToMsk * 1000 + timePlusMsk * 60 * 1000 + timePlusDestType * 1000 + timePlus
                if (type == 'ind') {
                    let departDate = new Date(dateTime - allWayTime)
                    let arriveDate = new Date(dateTime - timePlusDestType * 1000)
                    let now = new Date()
                    let nowMs = Date.parse(now)
                    if (departDate < now) {
                        let correct = new Date(nowMs + allWayTime)
                        console.log('correct:', correct)
                        dateSet.value = addZero(correct.getFullYear()) + '-' + addZero(correct.getMonth() + 1)  + '-' + addZero(correct.getDate())
                        timeSet.value = addZero(correct.getHours()) + ':' + addZero(correct.getMinutes())
                        result = ['fail', 'fail']                        
                        return result
                    }
                    result = [departDate, arriveDate]
                    return result
                } else if (type == 'group') {
                    let departDateMs = dateTime - allWayTime
                    let departDate = new Date(departDateMs)
                    let departHmi = addZero(departDate.getHours()) + ':' + addZero(departDate.getMinutes()) + ':' + addZero(departDate.getSeconds())
                    let scheduleLen = toMskSchedule.length
                    if (departHmi < toMskSchedule[scheduleLen-1]) {
                        departDate = new Date(departDateMs - 24*60*60*1000)
                    }
                    let departYmd = addZero(departDate.getFullYear()) + '-' + addZero(departDate.getMonth() + 1) + '-' + addZero(departDate.getDate())
                    
                    let scheduleDateMs
                    let scheduleDateArrive
//                    let now = new Date()
                    let now = new Date()
                    let nowMs = Date.parse(now)
                    for (let i = 0; i < scheduleLen; i++) {
                        scheduleDateMs = Date.parse(departYmd+'T'+toMskSchedule[i])
                        if (scheduleDateMs <= departDateMs) {
                            scheduleDateDepart = new Date(scheduleDateMs + timePlusTown * 60 * 1000)
                            scheduleDateArrive = new Date(scheduleDateMs + allWayTime - timePlusDestType * 1000)
                            
                            if (scheduleDateDepart < now) {
                                let nowHmi = addZero(now.getHours()) + ':' + addZero(now.getMinutes()) + ':' + addZero(now.getSeconds())
                                console.log('NNNNNOOOOWwwWW', nowHmi, toMskSchedule[0])
                                if (nowHmi > toMskSchedule[0]) {
                                    now = new Date(nowMs + 24*60*60*1000)
                                    
                                }
                                let nowYmd = addZero(now.getFullYear()) + '-' + addZero(now.getMonth() + 1) + '-' + addZero(now.getDate())
                                for (let j = scheduleLen - 1; j > -1 ; j--) {
                                    console.log(toMskSchedule[j])
                                    let testDate = Date.parse(nowYmd+'T'+toMskSchedule[j])
                                    console.log(new Date(testDate))
                                    console.log(new Date(nowMs))
                                    if (testDate > nowMs) {
                                        console.log('!!!!!!!!!!!!!!!', allWayTime / 1000 / 60 / 60)
                                        let correct = new Date(testDate + allWayTime - 1*60*1000)
                                        console.log('correct:', correct)
                                        dateSet.value = addZero(correct.getFullYear()) + '-' + addZero(correct.getMonth() + 1)  + '-' + addZero(correct.getDate())
                                        timeSet.value = addZero(correct.getHours()) + ':' + addZero(correct.getMinutes())
                                        result = ['fail', 'fail']                        
                                        return result
                                    }
                                }
                                
                                
                            }
                            result = [scheduleDateDepart, scheduleDateArrive]
                            return result
                            break
                        }
                    }
                }
            } else if (direction == 'from_msk'){                
                if (type == 'ind') {
                    let now = new Date()
                    let nowMs = Date.parse(now)
                    if (dateTime < nowMs) {
                        dateTime = nowMs;
                        dateSet.value = addZero(now.getFullYear()) + '-' + addZero(now.getMonth() + 1)  + '-' + addZero(now.getDate())
                        timeSet.value = addZero(now.getHours()) + ':' + addZero(now.getMinutes())
                    }
                    let backWayTime = timeToMsk * 1000 + timePlusMsk * 60 * 1000 + timeWaitDestType * 1000 + timePlus
                    let departDate = new Date(dateTime + timeWaitDestType * 1000)
                    let arriveDate = new Date(dateTime + backWayTime)
                    result = [departDate, arriveDate]
                    return result
                } else if (type == 'group') {
                    let now = new Date()
                    let nowMs = Date.parse(now)
                    if (dateTime < nowMs) {
                        dateTime = nowMs;
                        dateSet.value = addZero(now.getFullYear()) + '-' + addZero(now.getMonth() + 1)  + '-' + addZero(now.getDate())
                        timeSet.value = addZero(now.getHours()) + ':' + addZero(now.getMinutes())
                    }
                    let departDateMs = dateTime + timeWaitDestType * 1000
                    let departDate = new Date(departDateMs)
                    let backGroupWayTime = timeToMsk * 1000 - timePlusTown * 60 * 1000
                    let departHmi = addZero(departDate.getHours()) + ':' + addZero(departDate.getMinutes()) + ':' + addZero(departDate.getSeconds())
                    let scheduleLen = fromMskSchedule.length
                    if (departHmi > fromMskSchedule[scheduleLen-1]) {
                        departDate = new Date(departDateMs + 24*60*60*1000)
                    }
                    let departYmd = addZero(departDate.getFullYear()) + '-' + addZero(departDate.getMonth() + 1) + '-' + addZero(departDate.getDate())
                    let scheduleDateMs
                    let scheduleDateArrive
//                    nowMs += timeWaitDestType * 1000
                    for (let i = 0; i < scheduleLen; i++) {
                        scheduleDateMs = Date.parse(departYmd+'T'+fromMskSchedule[i])
                        testScheduleVar = scheduleDateMs - timePlusMsk * 60 * 1000
                        if (testScheduleVar >= departDateMs) {
                            scheduleDateDepart = new Date(scheduleDateMs - timePlusMsk * 60 * 1000)
                            scheduleDateArrive = new Date(scheduleDateMs + backGroupWayTime)
                            result = [scheduleDateDepart, scheduleDateArrive]
                            return result
                        }
                    }
                }
            }
            return result
        }
        sentOrder.addEventListener('click', await sentOrderAjax)
        
        async function sentOrderAjax() {
            let result
            let searchParams = new URLSearchParams()
            searchParams.set('add_transfer_order', typeOfTransfer.value)
            searchParams.set('add_transfer_order_direction', transferDirection.value)
            let passengersSent = [adltNumValInput.value, chldNumValInput.value, chldSeatNumInput.value]
            searchParams.set('add_transfer_order_passengers', passengersSent)
            searchParams.set('add_transfer_order_u_comment', uComment.value)
            if (typeOfTransfer.value == 'ind') {
//=====================================IND==================================================
                searchParams.set('add_transfer_order_ind_type', indTypeSelect.value)
                searchParams.set('add_transfer_order_ind_dest', transferIndDestinations.value.split('_')[0])
                let subPointVal = ''
                if (transferIndDestinationSubPoints.value) {
                    subPointVal = transferIndDestinationSubPoints.value
                } else {
                    subPointVal = 'no_val'
                }
                searchParams.set('add_transfer_order_ind_dest_sub_point', subPointVal)
                if (transferIndDestinations.value.split('_')[1] == 'air') {
                    if (airReisInput.value.length > 3){
                        searchParams.set('add_transfer_order_air_reis', airReisInput.value)
                    } else {
                        focusEmpty(airReisInput)
                        return
                    }
                } else {
                    searchParams.set('add_transfer_order_air_reis', 'noair')
                }
                if (oblTownFlag == 1) {
                    if (oblTownsListSel.value != 'off') {
                        searchParams.set('add_transfer_order_ind_type_obl_town', oblTownsListSel.value)
                    } else {
                        focusEmpty(oblTownsListSel)
                        return
                    }
                    
                } else {
                    searchParams.set('add_transfer_order_ind_type_obl_town', 'noobl')
                }
                let adressInputs = document.getElementsByClassName('adress_input')
                let adressCount = adressInputs.length
                
                let adressStrRes = ''
                for (let i = 0; i < adressCount; i++) {
                    if (adressInputs[i].value.length > 3) {
                        if (i == 0) {
                            adressStrRes += adressInputs[i].value
                        } else {
                            adressStrRes += ';;;' + adressInputs[i].value
                        }
                        
                    }
                }
                if (adressStrRes.length > 3) {
                    searchParams.set('add_transfer_order_ind_type_adress', adressStrRes)
                } else {
                    focusEmpty(adressInputs[0])
                    return
                }
                let depArriveDates = await setDateTime()
                if (depArriveDates != undefined) {
                    if (depArriveDates[0] != 'fail') {
                        let departForSent = addZero(depArriveDates[0].getFullYear()) + '-' + addZero(depArriveDates[0].getMonth() + 1)  + '-' + addZero(depArriveDates[0].getDate()) + ' ' + addZero(depArriveDates[0].getHours()) + ':' + addZero(depArriveDates[0].getMinutes())
                        let arriveForSent = addZero(depArriveDates[1].getFullYear()) + '-' + addZero(depArriveDates[1].getMonth() + 1)  + '-' + addZero(depArriveDates[1].getDate()) + ' ' + addZero(depArriveDates[1].getHours()) + ':' + addZero(depArriveDates[1].getMinutes())
                        let datesStrRes = dateSet.value + ' ' + timeSet.value + '_' + departForSent + '_' + arriveForSent
                        searchParams.set('add_transfer_order_ind_type_dep_arrive_dates', datesStrRes)
                    } else {
                        focusEmpty(dateSet)
                        focusEmpty(timeSet)
                        return
                    }
                } else {
                    focusEmpty(dateSet)
                    focusEmpty(timeSet)
                    return
                }
                let passDataSent = []
                let ids = ['fio', 'birth_date', 'phone', 'passport_num']
                let idsLen = ids.length
                
                for (let i = 0; i < idsLen; i++) {
                    let passInput = document.getElementById(ids[i] + '-0')
                    if (ids[i] == 'birth_date') {
                        if (passInput.value) {
                            passDataSent.push(passInput.value)
                        } else {
                            focusEmpty(passInput)
                            return
                        }
                    } else if (ids[i] == 'passport_num') {
                        if (passInput.value.length > 3){
                            passDataSent.push(passInput.value)
                        } else {
                            passDataSent.push('no_value')
                        }                        
                    } else {                        
                        if (passInput.value.length > 3) {
                            passDataSent.push(passInput.value)
                        } else {
                            focusEmpty(passInput)
                            return
                        }
                    }
                }
                searchParams.set('add_transfer_order_ind_type_pass_data', passDataSent)  
                console.log(passDataSent)
                console.log(depArriveDates)
            } else {
//==================================GROUP=================================================
                searchParams.set('add_transfer_order_group_dest', transferGroupDestinations.value.split('_')[0])
                let subPointVal = ''
                if (transferGroupDestinationSubPoints.value) {
                    subPointVal = transferGroupDestinationSubPoints.value
                } else {
                    subPointVal = 'no_val'
                }
                searchParams.set('add_transfer_order_group_dest_sub_point', subPointVal)
                if (transferGroupDestinations.value.split('_')[1] == 'air') {
                    if (airReisInput.value.length > 3){
                        searchParams.set('add_transfer_order_air_reis', airReisInput.value)
                    } else {
                        focusEmpty(airReisInput)
                        return
                    }
                } else {
                    searchParams.set('add_transfer_order_air_reis', 'noair')
                }
                if (groupAdressFlag == 0) {
                    let adressInputs = document.getElementsByClassName('adress_input_group')
                    let adressCount = adressInputs.length

                    let adressStrRes = ''
                    for (let i = 0; i < adressCount; i++) {
                        if (adressInputs[i].value.length > 3) {
                            if (i == 0) {
                                adressStrRes += adressInputs[i].value
                            } else {
                                adressStrRes += ';;;' + adressInputs[i].value
                            }
                        }
                    }
                    if (adressStrRes.length > 3) {
                        searchParams.set('add_transfer_order_group_type_adress', adressStrRes)
                        searchParams.set('add_transfer_order_group_type_town_point', 'noval')
                    } else {
                        focusEmpty(adressInputs[0])
                        return
                    }
                } else {
                    searchParams.set('add_transfer_order_group_type_adress', 'noval')
                    searchParams.set('add_transfer_order_group_type_town_point', groupTownPoints[groupTownPoints.selectedIndex].innerHTML)
                    
                }
                
                let depArriveDates = await setDateTime()
                if (depArriveDates != undefined) {
                    if (depArriveDates[0] != 'fail') {
                        let departForSent = addZero(depArriveDates[0].getFullYear()) + '-' + addZero(depArriveDates[0].getMonth() + 1)  + '-' + addZero(depArriveDates[0].getDate()) + ' ' + addZero(depArriveDates[0].getHours()) + ':' + addZero(depArriveDates[0].getMinutes())
                        let arriveForSent = addZero(depArriveDates[1].getFullYear()) + '-' + addZero(depArriveDates[1].getMonth() + 1)  + '-' + addZero(depArriveDates[1].getDate()) + ' ' + addZero(depArriveDates[1].getHours()) + ':' + addZero(depArriveDates[1].getMinutes())
                        let datesStrRes = dateSet.value + ' ' + timeSet.value + '_' + departForSent + '_' + arriveForSent
                        searchParams.set('add_transfer_order_group_type_dep_arrive_dates', datesStrRes)
                    } else {
                        focusEmpty(dateSet)
                        focusEmpty(timeSet)
                        return
                    }
                } else {
                    focusEmpty(dateSet)
                    focusEmpty(timeSet)
                    return
                }
                let passDataArrSent = []
                let passDivs = document.getElementsByClassName('passenger_item')
                let passDivsNum = passDivs.length
                let ids = ['fio', 'birth_date', 'phone', 'passport_num']
                let idsLen = ids.length
                let chld = false
                for (let j = 0; j < passDivsNum; j++) {
                    let passDiv = passDivs[j]
                    let itNum = passDiv.id.split('_')[0]
                    let passDataSent = {}
                    for (let i = 0; i < idsLen; i++) {
                        let passInput = document.getElementById(ids[i] + '-' + itNum)
                        if (ids[i] == 'birth_date') {
                            if (passInput.value) {
                                chld = isItChld(passInput)
                                passDataSent[ids[i]] = passInput.value
                            } else {
                                focusEmpty(passInput)
                                return
                            }
                        } else if (ids[i] == 'passport_num') {
                            if (passInput.value.length > 3){
                                passDataSent[ids[i]] = passInput.value
                            } else {
                                passDataSent[ids[i]] = 'no_value'
                            }                        
                        } else {                        
                            if (passInput.value.length > 3) {
                                passDataSent[ids[i]] = passInput.value
                            } else {
                                focusEmpty(passInput)
                                return
                            }
                        }
                    }
                    passDataSent['chld'] = chld
                    passDataArrSent.push(passDataSent)
                }
                    
                searchParams.set('add_transfer_order_group_type_pass_data', JSON.stringify(passDataArrSent))
            }
            try {
                result = await getAjaxPost(searchParams)
                window.location = '?list=my_transfers'
            } catch(e) {
                console.error(e)
            }
            console.log(result)
        }
        
        async function focusEmpty(item) {
            item.focus()
            item.style.background = '#fd7d7d'
            item.style.transition = 'all 1s ease'
            setTimeout(function(){
                item.style.background = 'white'
            }, 1000)
        }
        async function getPriceOfTransfer() {
            let result
            if (typeOfTransfer.value == 'ind') {
                let adressInputs = document.getElementsByClassName('adress_input')
                let adressInputNum = adressInputs.length
                let adressNum = 0
                for (let i = 0; i < adressInputNum; i++) {
                    if (adressInputs[i].value.length > 5) {
                        adressNum++
                    }
                }
                let allPass = parseInt(adltNumValInput.value) + parseInt(chldNumValInput.value) 
                let searchParams = new URLSearchParams()
                searchParams.set('get_ind_price', allPass)
                searchParams.set('get_ind_price_tarif', indTypeSelect.value)
                searchParams.set('get_ind_price_dest', transferIndDestinations.value.split('_')[0])
                searchParams.set('get_ind_price_obl_town', oblTownsListSel.value)
                searchParams.set('get_ind_price_adress_num', adressNum)
                
                let pricesSentArr = [allPass, indTypeSelect.value, transferIndDestinations.value.split('_')[0], oblTownsListSel.value, adressNum]
                console.log('GETPRICES', pricesSentArr)
                try {
                    result = await getAjaxPost(searchParams)
                } catch(e) {
                    console.error(e)
                }
                console.log('PRICES', result)
                
                return result
            }
            let adressInputs = document.getElementsByClassName('adress_input_group')
            let adressInputNum = adressInputs.length
            let adressNum = 0
            for (let i = 0; i < adressInputNum; i++) {
                if (adressInputs[i].value.length > 5) {
                    adressNum++
                }
            }
            if (groupAdressFlag == 1) {
                adressNum = 0
            }
            let searchParams = new URLSearchParams()
            searchParams.set('get_group_price', transferGroupDestinations.value.split('_')[0])
            searchParams.set('get_group_price_adlt', parseInt(adltNumValInput.value))
            searchParams.set('get_group_price_chld', parseInt(chldNumValInput.value))
            searchParams.set('get_group_price_adress_num', adressNum)
            try {
                result = await getAjaxPost(searchParams)
            } catch(e) {
                console.error(e)
            }
            return result
        }
        async function priceView() {
            let prices = await getPriceOfTransfer()
            let allPrice = 0
            let str = []
            let nums = []
            console.log(prices)
//            for (key in prices) {
//                allPrice += prices[key]
//                str.push(key)
//                nums.push(prices[key])
//            }
            prices[1].map(function(item){
                allPrice += item
            })
            allCostView.innerHTML = ''
            allCostView.innerHTML = allPrice
            orderDetail.innerHTML = ''
            if (typeOfTransfer.value == 'ind') {
                let category = document.createElement('p')
                category.innerHTML = prices[0][0] + ' - ' + prices[1][0] + ' руб.'
                orderDetail.appendChild(category)
                if (prices[0][1] != null) {
                    let oblTownPlus = document.createElement('p')
                    oblTownPlus.innerHTML = 'Областной город ' + oblTownsListSel.children[oblTownsListSel.selectedIndex].innerHTML + ' - ' + prices[1][1] + ' руб.'
                    orderDetail.appendChild(oblTownPlus)
                }
                let adressNum = prices[0][2]
                if (adressNum > 2) {
                    let addAdrPlace = document.createElement('p')
                    let addAdrNum = adressNum - 2
                    if (addAdrNum == 1) {
                        addAdrNum += ' дополнительный адрес'
                    } else if (addAdrNum > 1 && addAdrNum < 5){
                        addAdrNum += ' дополнительных адреса'
                    } else {
                        addAdrNum += ' дополнительных адресов'
                    }
                    addAdrPlace.innerHTML = addAdrNum + ' - ' + prices[1][2] + ' руб.'
                    orderDetail.appendChild(addAdrPlace)
                }
            } else {
                let adltP = document.createElement('p')
                adltP.innerHTML = 'Взрослых: ' + adltNumValInput.value + 'x' + prices[0][0] + ', сумма ' + prices[1][0] + ' руб.'
                orderDetail.appendChild(adltP)
                if (chldNumValInput.value > 0) {
                    let chldP = document.createElement('p')
                    chldP.innerHTML = 'Детей: ' + chldNumValInput.value + 'x' + prices[0][1] + ', сумма ' + prices[1][1] + ' руб.'
                    orderDetail.appendChild(chldP)
                }
                if (prices[1][2] > 0) {
                    let groupAdr = document.createElement('p')
                    groupAdr.innerHTML = prices[0][2] + ' - ' + prices[1][2] + ' руб.'
                    orderDetail.appendChild(groupAdr)
                }                
            }
//            console.log(str)
//            console.log(nums)
            
//            orderDetail
        }
        priceView()
        async function setPriceView() {
            priceView()
            let delsButs = document.getElementsByClassName('dels_buts')
            let birthInps = document.getElementsByClassName('birth_inps')
            let birthNum = birthInps.length
            let delsNum = delsButs.length
            for (let i = 0; i < delsNum; i++) {
                delsButs[i].addEventListener('click', await priceView)
                delsButs[i].addEventListener('click', countAllPass)
                
            }
            for (let i = 0; i < birthNum; i++) {
                birthInps[i].addEventListener('blur', await priceView)
            }
        }
        transferGroupDestinations.addEventListener('change', priceView)
        transferIndDestinations.addEventListener('change', priceView)
        chldNumValInput.addEventListener('input', await setPriceView)
        adltNumValInput.addEventListener('input', await setPriceView)
        typeOfTransfer.addEventListener('change', await priceView)
        transferDirection.addEventListener('change', await priceView)
        indTypeSelect.addEventListener('change', await priceView)
            let adressInputs2 = document.getElementsByClassName('adress_input')
            let adressCount2 = adressInputs2.length
            for (let i = 0; i < adressCount2; i++) {
                console.log(adressInputs2[i])
                adressInputs2[i].addEventListener('blur', await priceView)
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
    getAsync()
    
    
    
    function countAllPass() {
        let allPass = parseInt(adltNumValInput.value) + parseInt(chldNumValInput.value)
        let maxPass = adltNumValInput.dataset.max
        let minPass = adltNumValInput.getAttribute('min')
        if (allPass > maxPass) {
            if (this == adltNumValInput) {
                adltNumValInput.value = maxPass - chldNumValInput.value
            } else if (this == chldNumValInput){
                chldNumValInput.value = maxPass - adltNumValInput.value
            }
        } else if (allPass < minPass) {
            if (this == adltNumValInput) {
                adltNumValInput.value = minPass - chldNumValInput.value
            } else if (this == chldNumValInput){
                chldNumValInput.value = minPass - adltNumValInput.value
            }
//            this.value = parseInt(this.value)
            
        }
        allPass = parseInt(adltNumValInput.value) + parseInt(chldNumValInput.value)
        allPassValP.innerHTML = allPass
        if (typeOfTransfer.value == 'group') {
            let passItems = document.getElementsByClassName('passenger_item')
            let passItemsCount = passItems.length
            if (passItemsCount < allPass) {
                for (let i = passItemsCount; i < allPass; i++) {
                    addPassData(i)
                }
            } else if (passItemsCount > allPass) {
                for (let i = passItemsCount-1; i > allPass-1; i--) {
                    console.log(i, passItems[i])
                    passengersPlace.removeChild(passItems[i])
                }
            }
              correctAdltChld()  
        }
    }
    
    indTypeSelect.addEventListener('change', setMaxPass)
    
    function addPassData(num) {
        let div = document.createElement('div')
        div.style.display = 'flex'
        div.style.border = '1px solid #00000026'
        div.classList.add('passenger_item')
        div.setAttribute('id', num + '_divpass')
        let heads = ['ФИО пассажира', 'Дата рождения', 'Номер телефона', 'Серия и номер паспорта']
        let ids = ['fio', 'birth_date', 'phone', 'passport_num']
        let countChld = ids.length
        for (let i = 0; i < countChld; i++) {
            let divChld = document.createElement('div')
            let pHead = document.createElement('p')
            pHead.innerHTML = heads[i]
            let input = document.createElement('input')
            if (num == 0) {
                input.setAttribute('id', ids[i] + '-' + num)
            } else {
                let trueNum = parseInt(passengersPlace.lastChild.firstChild.children[1].id.split('-')[1]) + 1
                input.setAttribute('id', ids[i] + '-' + trueNum)
            }
            
            if (ids[i] == 'birth_date') {
                input.classList.add('birth_inps')
                input.setAttribute('type', 'date')
                input.addEventListener('blur', correctAdltChld)
            } else {
                input.setAttribute('type', 'text')
            }
            if (ids[i] == 'phone') {
                input.addEventListener('input', function(){        
                    this.value = this.value.replace(/[^\+\d]/g, '');
                    if(this.value[1] == 9 || this.value[1] == 4){
                        this.value = '+7(' + this.value.slice(1).replace(/[+]/g, '');
                    }else if(this.value[0] == 8 || this.value[0] == '+' || this.value[0] == 7){
                        this.value = '+7(' + this.value.slice(2).replace(/[+]/g, '');            
                    }else{
                        this.value = '';
                    }
                    if(this.value[3] == 7){
                        this.value = '+7(';
                    }
                    if(this.value[6]){
                        this.value = this.value.slice(0,6) + ')' + this.value.slice(6);
                    }
                    if(this.value[10]){
                        this.value = this.value.slice(0,10) + '-' + this.value.slice(10);
                    }
                    if(this.value[13]){
                        this.value = this.value.slice(0,13) + '-' + this.value.slice(13);
                    }
                    if(this.value[16]){
                        this.value = this.value.slice(0,16);
                    }

                });                
            }
            divChld.appendChild(pHead)
            divChld.appendChild(input)
            div.appendChild(divChld)
        }
        if (num != 0) {
            let delPass = document.createElement('div');
            delPass.style.width = '20px'
            delPass.style.height = '20px'
            delPass.style.position = 'relative'
            delPass.setAttribute('title', 'Удалить пассажира')
            delPass.classList.add('dels_buts')
            delPass.addEventListener('click', delDataPass)
            let leftPart = document.createElement('div')
            leftPart.style.borderBottom = '2px solid red'
            leftPart.style.borderLeft = '2px solid red'
            leftPart.style.width = '10px'
            leftPart.style.height = '10px'
            leftPart.style.position = 'absolute'
            leftPart.style.transform = 'rotate(-135deg)'
            let rightPart = document.createElement('div')
            rightPart.style.borderTop = '2px solid red'
            rightPart.style.borderRight = '2px solid red'
            rightPart.style.width = '10px'
            rightPart.style.height = '10px'
            rightPart.style.position = 'absolute'
            rightPart.style.left = '14px'
            rightPart.style.transform = 'rotate(-135deg)'
            delPass.appendChild(leftPart)
            delPass.appendChild(rightPart)
            div.appendChild(delPass)

        }
        passengersPlace.appendChild(div)
    }
    function delDataPass() {
        let passItemsRem = this.parentNode
        passengersPlace.removeChild(passItemsRem)
        correctAdltChld()
    }
    function correctAdltChld() {
        if (typeOfTransfer.value == 'group') {
            let passItems = document.getElementsByClassName('passenger_item')
            let passItemsCount = passItems.length
            let chld = 0
            let adlt = 0
            let noVal = 0
            for (let i = 0; i < passItemsCount; i++) {
                let birthDateIn = document.getElementById('birth_date-' + passItems[i].id.split('_')[0])
                if (birthDateIn.value) {
                    if (isItChld(birthDateIn)) {
                        chld++
                    } else {
                        adlt++
                    }
                } else {
                    noVal++
                }
            }
            if (noVal == 0) {
                adltNumValInput.value = adlt
                chldNumValInput.value = chld
            } else {
                let allPass = parseInt(adltNumValInput.value) + parseInt(chldNumValInput.value)
                console.log('allPASS', allPass)
                console.log('passItemsCount', passItemsCount)
                if (allPass > passItemsCount) {
                    if (parseInt(chldNumValInput.value) > chld) {
                        chldNumValInput.value = chldNumValInput.value - 1
                    } else {
                        adltNumValInput.value = adltNumValInput.value - 1
                    }
                }
            }
        }
    }
    
    function isItChld(elem) {
        let birth = new Date(Date.parse(elem.value))
        let now = new Date()
        let nowDate = addZero(now.getFullYear()) + '-' + addZero(now.getMonth() + 1) + '-' + addZero(now.getDate())
        let nowBirthDate = addZero(now.getFullYear()) + '-' + addZero(birth.getMonth() + 1) + '-' + addZero(birth.getDate())
        let age = now.getFullYear() - birth.getFullYear()
        if (nowDate <= nowBirthDate) {
            age -= 1
        }
        if (age < 13) {
            return true
        }
        return false
    }
    
    function setMaxPass() {
        if (typeOfTransfer.value == 'ind') {
            let tarif = indTypeSelect.value
            let maxPassSelected = document.getElementById('max_pass_' + tarif)
            let minPassSelected = document.getElementById('min_pass_' + tarif)
            let maxPass = maxPassSelected.value
            let minPass = minPassSelected.value
            adltNumValInput.setAttribute('data-max', maxPass)
            chldNumValInput.setAttribute('data-max', maxPass)
            adltNumValInput.setAttribute('min', minPass)
            adltNumValInput.value = minPass
            chldNumValInput.value = 0
        } else {
            adltNumValInput.setAttribute('data-max', 20)
            chldNumValInput.setAttribute('data-max', 20)
            adltNumValInput.setAttribute('min', 1)
        }
        countAllPass()
    }
    setMaxPass()
    countAllPass()
    function sortTo(num1, num2) {
        if (num1 < num2) {
            return 1;
        }

        if (num1 > num2) {
            return -1;
        }

        return 0;
    }
    function addZero(num) {
        if (num >= 0 && num <= 9) {
            return '0' + num;
        } else {
            return num;
        }
    }
    
    typeOfTransfer.addEventListener('change', function(){
        console.log('TOWNPointss', groupTownPoints[groupTownPoints.selectedIndex].innerHTML)
        if (this.value == 'ind') {
            groupAdressFlag = 0
            indWayAdress.classList.remove('hide')
            groupWayAdress.classList.add('hide')
            indType.classList.remove('hide')
            transferIndDestinations.value = transferGroupDestinations.value
            transferGroupDestinationSubPoints.classList.add('hide')
            let itVal = transferIndDestinations.value
            let itArr = itVal.split('_')
            transferIndDestinationSubPoints.innerHTML = ''
            createSubPointsOpt(transferIndDestinationSubPoints, 'ind', itArr[0])
            transferIndDestinationSubPoints.value = transferGroupDestinationSubPoints.value
            
            let firstDestValInd2 = transferIndDestinations.value.split('_')
            transferIndDestinations.classList.remove('hide')
            transferGroupDestinations.classList.add('hide')
            let firstDestType2 = firstDestValInd2[1]
            if (firstDestType2 != 'air') {
                reisCarret.classList.add('hide')
            } else {
                reisCarret.classList.remove('hide')
            }
            
        } else if (this.value == 'group') {
            groupAdressFlag = 1
            indWayAdress.classList.add('hide')
            groupWayAdress.classList.remove('hide')
            indType.classList.add('hide')
            transferGroupDestinations.value = transferIndDestinations.value            
            transferIndDestinationSubPoints.classList.add('hide')
            let itVal = transferGroupDestinations.value
            let itArr = itVal.split('_')
            transferGroupDestinationSubPoints.innerHTML = ''
            createSubPointsOpt(transferGroupDestinationSubPoints, 'group', itArr[0])
            transferGroupDestinationSubPoints.value = transferIndDestinationSubPoints.value
            let firstDestValGroup = transferGroupDestinations.value.split('_')
            transferGroupDestinations.classList.remove('hide')
            transferIndDestinations.classList.add('hide')
            let firstDestType3 = firstDestValGroup[1]
            console.log(firstDestType3)
            if (firstDestType3 != 'air') {
                reisCarret.classList.add('hide')
            } else {
                reisCarret.classList.remove('hide')
            }
            countAllPass()
        }
    })
    
    groupAdressBut.addEventListener('click', function(){
        if (groupAdressVal.classList.contains('hide')) {
            groupAdressFlag = 0
            console.log(groupAdressFlag)
            groupAdressVal.classList.remove('hide')
            groupDepartPoints.classList.add('hide')
            this.innerHTML = 'Забрать с точки посадки'
        } else {
            groupAdressFlag = 1
            console.log(groupAdressFlag)
            groupAdressVal.classList.add('hide')
            groupDepartPoints.classList.remove('hide')
            this.innerHTML = 'Забрать с адреса'            
        }
    })
   
    transferDirection.addEventListener('change', function(){
        if (transferGroupDestinations.value.split('_')[0] == 'mte') {
            transferGroupDestinationSubPoints.innerHTML = ''
            createSubPointsOpt(transferGroupDestinationSubPoints, 'group','mte')
        }
        if (transferIndDestinations.value.split('_')[0] == 'mte') {
            transferIndDestinationSubPoints.innerHTML = ''
            createSubPointsOpt(transferIndDestinationSubPoints, 'ind', 'mte')
        }
        if (this.value == 'to_msk') {
            destsH3.innerHTML = 'Точка высадки в Москве'
        } else if (this.value) {
            destsH3.innerHTML = 'Точка посадки в Москве'
           
        }
    })
    
    transferGroupDestinations.addEventListener('change', function(){
        if (typeOfTransfer.value == 'group') {
            let itVal = this.value
            let itArr = itVal.split('_')
            transferGroupDestinationSubPoints.innerHTML = ''
            createSubPointsOpt(transferGroupDestinationSubPoints, 'group', itArr[0])
            let itType = itArr[1]
            if (itType == 'air') {
                reisCarret.classList.remove('hide')
            } else {
                reisCarret.classList.add('hide')
            }
            let destTypeP = document.getElementById('dest_type_' + itType)
            dateTimeHead.innerHTML = destTypeP.innerHTML
        }
            
    })
    transferIndDestinations.addEventListener('change', function(){
        if (typeOfTransfer.value == 'ind') {
            let itVal = this.value
            let itArr = itVal.split('_')
            transferIndDestinationSubPoints.innerHTML = ''
            createSubPointsOpt(transferIndDestinationSubPoints, 'ind', itArr[0])
            let itType = itArr[1]
            if (itType == 'air') {
                reisCarret.classList.remove('hide')
            } else {
                reisCarret.classList.add('hide')
            }
            let destTypeP = document.getElementById('dest_type_' + itType)
            dateTimeHead.innerHTML = destTypeP.innerHTML
        }
    })
    function createSubPointsOpt(parent, type, val) {
        let subPointsElem = document.getElementById('sub_point-' + type + '-' + val)
        if (subPointsElem.value) {
            if (subPointsElem.value != 0) {
                let subPointsArr = subPointsElem.value.split(', ')
                if (val == 'mte') {
                    if (transferDirection.value == 'to_msk') {
                        let option = document.createElement('option')
                        option.setAttribute('value', subPointsArr[0])
                        option.innerHTML = subPointsArr[0]
                        parent.appendChild(option)
                    } else if (transferDirection.value == 'from_msk') {
                        let option = document.createElement('option')
                        option.setAttribute('value', subPointsArr[1])
                        option.innerHTML = subPointsArr[1]
                        parent.appendChild(option)
                    }                        
                } else {
                    subPointsArr.map(function(item){
                        let option = document.createElement('option')
                        option.setAttribute('value', item)
                        option.innerHTML = item
                        parent.appendChild(option)
                    })
                }
                parent.classList.remove('hide')
            } else {
                parent.classList.add('hide')
            }
        } else {
            parent.classList.add('hide')
        }
    }
    console.log('destVal', firstDestType)
    
    
    oblTownsButton.addEventListener('click', function(){
        oblTownFlag = 1
        adressCenter.classList.add('hide')
        oblTownsList.classList.remove('hide')
        if (oblTownsListSel.value == 'off') {
            adrPlace.classList.add('hide')
        }
        
    })
    let centerButton = document.getElementById('center_button')
    centerButton.addEventListener('click', function(){
        oblTownFlag = 0
        adressCenter.classList.remove('hide')
        oblTownsList.classList.add('hide')
        adrPlace.classList.remove('hide')
    })
    oblTownsListSel.addEventListener('change', function(){
        if (this.value == 'off'){
            adrPlace.classList.add('hide')
        } else {
            adrPlace.classList.remove('hide')            
        }
    })
    console.log(passengersPlace.children[0].children)
</script>
