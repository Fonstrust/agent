<?php
$user = new User($_SESSION['id']);
?>
<div class="flex-wrapper ctext">
    <div class="left-bar">
        <div class="calendar" id="calendar_order">
        </div>
        <div class="news">
            <div class="block-header">
                <img src="/elems/img/news_t.png">
                <span class="block-head">Новости</span>
            </div>
            <hr>
        </div>
    </div>
    <div class="right-field">
        <div class="nearest-transfers">
            <div class="block-header">
                <img src="/elems/img/mytransfers_icon.png">
                <span class="block-head">Ближайшие трансферы</span>
            </div>
            <hr>
            <table id="nearest_table">
                
            <?php
                $sql_my_transfers = $user->getUserTransfersASC5();
                $i = 0;
                while ($res_my_transfers = mysqli_fetch_array($sql_my_transfers)) {
                    if ($i == 0) {
                        echo '
                        <tr>
                            <td>ФИО</td>
                            <td>Откуда - куда</td>
                            <td>Дата</td>
                            <td>Время</td>
                        </tr>';
                    }
                    $depart_timestamp = strtotime($res_my_transfers['depart_time']);
                    if ($res_my_transfers['direction'] == 'to_msk') {
                        $direction = $user->getTownRu() . ' - Москва';
                    } elseif ($res_my_transfers['direction'] == 'from_msk') {
                        $direction = 'Москва - '. $user->getTownRu();
                    }
                    $passenger = $user->getUserPassenger($res_my_transfers['passenger']);
                    echo '<tr class="transfers_list" id="transfer_' . $res_my_transfers['ID'] . '">';                    
                    echo '<td>' . $passenger['fio'] . '</td>';
                    echo '<td>' . $direction . '</td>';
                    echo '<td>' . date('d.m.Y', $depart_timestamp) . '</td>';
                    echo '<td>' . date('H:i', $depart_timestamp) . '</td>';
                    echo '</tr>';
                    $i++;
                }
                if ($i == 0) {
                    echo 'На данный момент трансферов нет';
                }
                
            ?>
            </table>
        </div>
        <hr>
        <div class="shedule">
            <div class="block-header">
                <img src="/elems/img/calendar2.png">
                <span class="block-head">Расписание</span>
                <hr>
            </div>
            <hr>
            <div class="flex-beetwen">
                <div class="half-width green-t-18">
                    <p><?=$user->getTownRu()?> - Москва</p>
                </div>
                <div class="half-width green-t-18">
                    <p>Москва - <?=$user->getTownRu()?></p>
                </div>                
            </div>
            <hr>
            <table class="schedule-table">
                <tr>
                    <th>Отправление</th>
                    <th>Прибытие</th>
                    <th>Отправление</th>
                    <th>Прибытие</th>
                </tr>
                <?php
                $schedule = $user->getSchedule();
                $to_msk = [];
                $from_msk = [];
                foreach($schedule as $reis) {
                    if ($reis[0] == 'to_msk') {
                        $to_msk[] = $reis[1];
                    } else {
                        $from_msk[] = $reis[1];
                    }
                }
                $tr_len = max(count($to_msk), count($from_msk));
                $time_to_msk = $user->getTimeToMsk();
                for ($i = 0; $i < $tr_len; $i++) {
                    $arrive_to = date('H:i', strtotime($to_msk[$i]) + strtotime($time_to_msk) - strtotime('00:00:00'));
                    $arrive_from = date('H:i', strtotime($from_msk[$i]) + strtotime($time_to_msk) - strtotime('00:00:00'));
                    echo '<tr>';
                    echo '<td>' . date('H:i', strtotime($to_msk[$i])) . '</td>';
                    echo '<td>' . $arrive_to . '</td>';
                    echo '<td>' . date('H:i', strtotime($from_msk[$i])) . '</td>';
                    echo '<td>' . $arrive_from . '</td>';
                    echo '</tr>';
                }
                ?>
            </table>
        </div>
    </div>    
</div>
<div class="full-field">
    <div class="prices ctext">
        <div class="block-header">
            <img src="/elems/img/money_icon.png">
            <span class="block-head">Цены</span>
            <div class="green-type w930">
                <p>Групповые</p>
            </div>
        </div>
        <hr>
        <!-- <div class="green-type">
            <p>Групповые</p>
        </div> -->
        <div id="gr_prices_table">
        </div>
        <hr>
        <div class="green-type">
            <p style="margin: 28px;">Индивидуальные</p>
        </div>
        <hr>
        <div id="ind_prices_table" style="display: flex;">
        </div>
        
    </div>    
    <div class="dop-info">
            <hr>
        <div class="block-header">
            <img src="/elems/img/dinfo.png">
            <span class="block-head">Дополнительная информация</span>
        </div>
            <hr>
        <ul class="dop_dan">
            <li><span>Пассажиры имеют право на бесплатный провоз одного места ручной клади размером не более 55х40х20 см, весом не более 5 кг и одно место багажа размером не более 100х50х30 см, весом не более 30 кг.</span></li>
            <li><span>Дополнительное место багажа на групповом трансфере — 500 руб.</span></li>
            <li><span>При перевозке детей до 12 лет на индивидуальном трансфере дополнительно </span></li>
            <li><span>оплачивается детское кресло, цена — 100 руб.</span></li>
            <li><span>Дополнительный адрес посадки (более двух) на индивидуальном трансфере – 300 руб.</span></li>
            <li><span>Дополнительное место высадки г. Москва — 500 руб.</span></li>
            <li><span>Перевозка мелких домашних животных, собак и птиц допускается сверх установленной нормы провоза ручной клади и багажа за отдельную плату и только на индивидуальном трансфере – 500 руб.</span></li>
            <li><span>Заезд в город по пути следования 500 руб., для микроавтобуса 1000 руб.</span></li>
        </ul>
    </div>
</div>
<script type="text/javascript">
    let myTransfers = document.getElementsByClassName('transfers_list')
    let transferLen = myTransfers.length
    for (let i = 0; i < transferLen; i++) {
        myTransfers[i].addEventListener('click', toRedact)
    }
    function toRedact() {
        let orderId = this.id.split('_')[1]
        window.location = '?list=order&&id=' + orderId
    }
    let calendarOrder = document.getElementById('calendar_order')
    createOrderCalendar(calendarOrder)
    function createOrderCalendar(parent) {
        let nowDate = new Date()
        let chosenMonth = [nowDate.getFullYear(), nowDate.getMonth()]
        let controls = document.createElement('div')
        let controlsIn = document.createElement('div')
        let calendarHeader = document.createElement('div')
        let calendarImg = document.createElement('img')
        let calendarHead = document.createElement('span')
        calendarImg.setAttribute('src', 'img/calendar.png')
        calendarImg.style.marginRight = '20px'
        calendarHead.innerHTML = 'Календарь'
        calendarHead.classList.add('block-head')
        calendarHeader.style.display = 'flex'
        calendarHeader.appendChild(calendarImg)
        calendarHeader.appendChild(calendarHead)
        let past = document.createElement('div')
        let monthYear = document.createElement('div')
        let future = document.createElement('div')
        let monthRu = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь']
        monthYear.innerHTML = monthRu[chosenMonth[1]] + ' ' + chosenMonth[0]
        let prev = document.createElement('i')
        prev.classList.add('fa')
        prev.classList.add('fa-angle-left')
        prev.classList.add('arrows')
        let next = document.createElement('i')
        next.classList.add('fa')
        next.classList.add('fa-angle-right')
        next.classList.add('arrows')
        past.appendChild(prev)
        future.appendChild(next)
        past.style.marginRight = '5px'
        future.style.marginLeft = '5px'
        monthYear.style.width = '120px'
        monthYear.style.fontSize = '18px'
        controls.style.display = 'flex'
        controls.style.justifyContent = 'space-between'
        controlsIn.style.alignItems = 'center'
        controlsIn.style.display = 'flex'
        controls.style.padding = '25px 20px 20px 35px'
        future.addEventListener('click', function() {
            let calendTable = document.getElementById('calend_table')
            if (chosenMonth[1] == 11) {
                chosenMonth = [chosenMonth[0]+1, 0]
            } else {
                chosenMonth = [chosenMonth[0], chosenMonth[1]+1]
            }
            parent.removeChild(calendTable)
            createTable(parent, chosenMonth[0], chosenMonth[1])
            monthYear.innerHTML = monthRu[chosenMonth[1]] + ' ' + chosenMonth[0]
        })
        past.addEventListener('click', function() {
            let calendTable = document.getElementById('calend_table')
            if (chosenMonth[1] == 0) {
                chosenMonth = [chosenMonth[0]-1, 11]
            } else {
                chosenMonth = [chosenMonth[0], chosenMonth[1]-1]
            }
            parent.removeChild(calendTable)
            createTable(parent, chosenMonth[0], chosenMonth[1])
            monthYear.innerHTML = monthRu[chosenMonth[1]] + ' ' + chosenMonth[0]
        })
        let hr = document.createElement('hr')
        hr.style.opacity = '.1'
        controls.appendChild(calendarHeader)
        controlsIn.appendChild(past)
        controlsIn.appendChild(monthYear)
        controlsIn.appendChild(future)
        controls.appendChild(controlsIn)
        parent.appendChild(controls)
        parent.appendChild(hr)
        
        createTable(parent, nowDate.getFullYear(), nowDate.getMonth())
        function createTable(parent, year, month) {
            let now = new Date()
            let table = document.createElement('table')
            table.setAttribute('id', 'calend_table')
            table.setAttribute('cellspacing', 10)
            table.classList.add('calend-center')
            let thTr = document.createElement('tr')
            let ths = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Cб', 'Вс']
            for (let i = 0; i < 7; i++) {
                let td = document.createElement('td')
                td.innerHTML = ths[i]
                thTr.appendChild(td)
            }
            table.appendChild(thTr)
            
            let daysArr = getWeeks(year, month)
            let k = 0
            let flag = 0
            for (let i = 0; i < 6; i++) {
                let tr = document.createElement('tr')
                for (let j = 0; j < 7; j++) {
                    let td = document.createElement('td')
                    td.setAttribute('title', 'Выберите дату вылета/прилета')
                    td.innerHTML = daysArr[k]
                    if (daysArr[k] == 1 && flag == 0) {
                        flag = 1
                    } else if (daysArr[k] == 1 && flag == 1) {
                        flag = 2
                    }
                    if (daysArr[k] == now.getDate() && month == now.getMonth() && year == now.getFullYear() && flag == 1) {
                        td.style.border = '2px solid #fb661d'
                        td.style.borderRadius = '50%'
                    }
                    if (flag == 0) {
                        td.style.color = '#d4d4d4'
                        td.addEventListener('click', function() {
                            window.location ='?list=order&or_date=' + year + '-' + addZero(month) + '-' + addZero(this.innerHTML)
                        })
                    } else if (flag == 2) {
                        td.style.color = '#d4d4d4'
                        td.addEventListener('click', function() {
                            window.location ='?list=order&or_date=' + year + '-' + addZero(month+2) + '-' + addZero(this.innerHTML)
                        })
                    } else {
                        td.addEventListener('click', function() {
                            window.location ='?list=order&or_date=' + year + '-' + addZero(month+1) + '-' + addZero(this.innerHTML)
                        })
                    }
                    k++
                    tr.appendChild(td)
                }
                table.appendChild(tr)
            }
            console.log('Calendar', daysArr)
            parent.appendChild(table)
        }
        function getWeeks(year, month) {
            let firstDay = getFirstDay(year, month)
            let daysNum = getDaysNum(year, month)
            let prevMonthDaysNum = getDaysNum(year, month - 1)
            let firstWeekDate
            let firstNums
            if (firstDay == 0) {
                firstWeekDate = prevMonthDaysNum - 5
                firstNums = 6
            } else if (firstDay == 1) {
                firstWeekDate = prevMonthDaysNum - 6
                firstNums = 7
            } else {
                let prevDays = firstDay - 2
                firstWeekDate = prevMonthDaysNum - prevDays
                firstNums = firstDay - 1
            }
            let resultArr = []
            for (let i = firstWeekDate; i <= prevMonthDaysNum; i++) {
                resultArr.push(i)
            }
            for (let i = 1; i <= daysNum; i++) {
                resultArr.push(i)
            }
            let finishNum = 42 - (daysNum + firstNums)
            for (let i = 1; i <= finishNum; i++) {
                resultArr.push(i)                
            }
            return resultArr
        }
        function getFirstDay(year, month) {
            let firstDate = new Date (year, month, 1)
            return firstDate.getDay()
        }
        function getDaysNum(year, month) {
            let nextMonth = new Date(year, month + 1, 0)
            return nextMonth.getDate()
        }
        function addZero(num) {
            if (num >= 0 && num <= 9) {
                return '0' + num;
            } else {
                return num;
            }
        }
        
    }
    ////////////////////////////////////////////////////////////_____________ASYNC
    async function run() {
        let modalParent = document.querySelector('.flex-wrapper')
        let grpriceTable = document.getElementById('gr_prices_table')
        let indPriceTable = document.getElementById('ind_prices_table')
        let qIndex = await getUserQIndex()
        let intID = await getIntId()
        let grDests = await getOldDestParams('group')
        let indDests = await getOldDestParams('ind')
        console.log('Qindex', qIndex)
        console.log('intID', intID)
        console.log('grDests', grDests)
        createGroupPriceTable(grDests, grpriceTable)
        createIndPriceTable(indDests, indPriceTable)
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        async function createIndPriceTable(destsI, parent) {
            let oldIndPrices = await getOldIndPrices(destsI)
            let pricesLen = oldIndPrices.length
            let firstTds = ['Категория', 'Тариф'];
            let mainLen = destsI.length
            let firstLen = firstTds.length
            let destsIRu = await getRuDests(destsI)
            
            let firstTable = document.createElement('table')
            let overDiv = document.createElement('div')
            overDiv.style.overflow = 'auto'
            let mainTable = document.createElement('table')
            let tdWirth = 150
            let mainWidth = mainLen * tdWirth
            let firstWidth = tdWirth * firstLen
            mainTable.style.width = mainWidth + 'px'
            firstTable.style.width = firstWidth + 'px'
            firstTable.classList.add('price-table')
            mainTable.classList.add('price-table')
            // parent.style.width = mainWidth + firstWidth + 'px'
            // if (mainLen < 7) {
            //     parent.style.margin = '0 auto'
            // } 
            if (pricesLen > 0) {
                let thTr1 = document.createElement('tr')
                let thTr2 = document.createElement('tr')
                for (let i = 0; i < firstLen; i++) {
                    let td = document.createElement('td')
                    td.style.width = tdWirth + 'px'
                    td.innerHTML = firstTds[i]
                    td.classList.add('little-grey')
                    thTr1.appendChild(td)
                }
                firstTable.appendChild(thTr1)
                for (let i = 0; i < mainLen; i++) {
                    let td = document.createElement('td')
                    td.style.width = tdWirth + 'px'
                    td.innerHTML = destsIRu[i]
                    td.classList.add('little-grey')
                    thTr2.appendChild(td)
                }
                mainTable.appendChild(thTr2)
                parent.appendChild(firstTable)
                overDiv.appendChild(mainTable)
                parent.appendChild(overDiv)
                for (let i = 0; i < pricesLen; i++) {
                    let tr1 = document.createElement('tr')
                    let tr2 = document.createElement('tr')
                    
                    let td1 = document.createElement('td')
                    let td2 = document.createElement('td')
//                    td2.classList.add('little-grey')
                    if (i == 0) {
                        td1.innerHTML = oldIndPrices[i][1] + ' - ' + oldIndPrices[i][2] + '<br><span class="little-grey">человек</span>'
                    } else {
                        td1.innerHTML = oldIndPrices[i][1] + ' - ' + oldIndPrices[i][2]
                    }
                    
                    
                    td2.innerHTML = await getRuTarif(oldIndPrices[i][3])
                    tr1.appendChild(td1)
                    tr1.appendChild(td2)
                    firstTable.appendChild(tr1)
                    for (let j = 5; j < mainLen + 5; j++) {
                        let td = document.createElement('td')
                        td.innerHTML = oldIndPrices[i][j] + ' <i style="font-size: 20px" class="fa fa-rub" aria-hidden="true"></i>'
                        tr2.appendChild(td)
                    }
                    td1.style.cursor = 'pointer'
                    td1.setAttribute('title', 'Нажмите, чтобы развернуть эту категорию.')
                    td1.addEventListener('click', function(){
                        let div = document.createElement('div')
                        let closeCross = document.createElement('img')
                        closeCross.setAttribute('src', 'elems/img/cross.png')
                        closeCross.classList.add('close-cross')
                        div.classList.add('modal-price')
                        let table = document.createElement('table')
                        let caption = document.createElement('caption')
                        caption.innerHTML = this.innerHTML + '<br>' + td2.innerHTML
                        let cloneThTr = document.createElement('tr')
                        let cloneTr2 = document.createElement('tr')
                        cloneThTr.innerHTML = thTr2.innerHTML
                        cloneTr2.innerHTML = tr2.innerHTML
                        table.appendChild(caption)
                        table.appendChild(cloneThTr)
                        table.appendChild(cloneTr2)
                        table.style.width = '1200px'
                        div.appendChild(closeCross)
                        div.appendChild(table)
                        modalParent.appendChild(div)
                        closeCross.addEventListener('click', function() {
                            modalParent.removeChild(div)
                        })
//                        console.log(tr2)
                    })
                    
                    mainTable.appendChild(tr2)
                }
            }
            
            
            
            console.log('IndPrices', oldIndPrices)            
        }
        async function getOldIndPrices(points) {
            let result
            let seachParams = new URLSearchParams()
            seachParams.set('get_old_ind_prices', 1);
            seachParams.set('get_old_ind_prices_points', points);
            seachParams.set('get_old_ind_prices_int_id', intID);
            seachParams.set('get_old_ind_prices_town', qIndex);
            try {
                 result = await getAjaxPost(seachParams)
            }catch(e){
                console.error(e)
            }
            return result
        }
        async function getRuTarif(tarif) {
            let result
            let searchParams = new URLSearchParams()
            searchParams.set('get_ru_tarif', tarif)
            try {
                result = await getAjaxPost(searchParams)
            } catch(e) {
                console.log(e)
            }
            return result
        }
        async function createGroupPriceTable(destsG, parent) {
            let firstTable = document.createElement('table')
            let mainTable = document.createElement('table')
            let overDiv = document.createElement('div')
            overDiv.style.overflow = 'auto'
            let trHead1 = document.createElement('tr')
            let trAdlt1 = document.createElement('tr')
            let trChld1 = document.createElement('tr')
            let trHead = document.createElement('tr')
            let trAdlt = document.createElement('tr')
            let trChld = document.createElement('tr')
            let tdCountG = destsG.length
            let th1 = document.createElement('td')
            let tdA1 = document.createElement('td')
            let tdC1 = document.createElement('td')
            let len
            let destOn
            th1.innerHTML = 'Тип'
            tdA1.innerHTML = 'Взрослый'
            tdC1.innerHTML = 'Детский'
            th1.classList.add('little-grey')
//            tdA1.classList.add('little-grey')
//            tdC1.classList.add('little-grey')
            
            let destRu = await getRuDests(destsG)
            let tdWidth = 150
            let mainWidth = tdCountG * tdWidth
            let firstWidth = 150
            
            trHead1.appendChild(th1)
            trAdlt1.appendChild(tdA1)
            trChld1.appendChild(tdC1)
            len = tdCountG
            destOn = destsG       
            let oldPriceA = await getOldPrices('groupadlt', destOn)
            let oldPriceC = await getOldPrices('groupchld', destOn)
             console.log('test:', oldPriceA)
             for (let i = 0; i < len; i++) {
                let th = document.createElement('td')
                th.classList.add('little-grey')
                let tdA = document.createElement('td')
                let tdC = document.createElement('td')
                th.innerHTML = destRu[i]
                tdA.innerHTML = oldPriceA[destOn[i]] + '<br><span class="little-grey">рублей</span>'
                tdC.innerHTML = oldPriceC[destOn[i]] + '<br><span class="little-grey">рублей</span>'
                th.style.width = tdWidth + 'px'
                trHead.appendChild(th)
                trAdlt.appendChild(tdA)
                trChld.appendChild(tdC)
            }
            firstTable.appendChild(trHead1)
            firstTable.appendChild(trAdlt1)
            firstTable.appendChild(trChld1)
            
            mainTable.style.width = mainWidth + 'px'
            firstTable.style.width = firstWidth + 'px'
            parent.style.width = mainWidth + firstWidth + 'px'
            parent.style.margin = '0 auto'
            firstTable.classList.add('price-table')
            mainTable.classList.add('price-table')
            mainTable.appendChild(trHead)
            mainTable.appendChild(trAdlt)
            mainTable.appendChild(trChld)
            overDiv.appendChild(mainTable)
            parent.appendChild(firstTable)
            parent.appendChild(overDiv)
            parent.style.display = 'flex'
            tdA1.style.cursor = 'pointer'
            tdC1.style.cursor = 'pointer'
            tdA1.setAttribute('title', 'Нажмите, чтобы развернуть эту категорию.')
            tdC1.setAttribute('title', 'Нажмите, чтобы развернуть эту категорию.')
            tdA1.addEventListener('click', function(){
                let div = document.createElement('div')
                let closeCross = document.createElement('img')
                closeCross.setAttribute('src', 'elems/img/cross.png')
                closeCross.classList.add('close-cross')
                div.classList.add('modal-price')
                let table = document.createElement('table')
                let caption = document.createElement('caption')
                caption.innerHTML = this.innerHTML
                let cloneThTr = document.createElement('tr')
                let cloneTr2 = document.createElement('tr')
                cloneThTr.innerHTML = trHead.innerHTML
                cloneTr2.innerHTML = trAdlt.innerHTML
                table.appendChild(caption)
                table.appendChild(cloneThTr)
                table.appendChild(cloneTr2)
                table.style.width = '1200px'
                div.appendChild(closeCross)
                div.appendChild(table)
                modalParent.appendChild(div)
                closeCross.addEventListener('click', function() {
                    modalParent.removeChild(div)
                })
//                        console.log(tr2)
            })
            
            tdC1.addEventListener('click', function(){
                let div = document.createElement('div')
                let closeCross = document.createElement('img')
                closeCross.setAttribute('src', 'elems/img/cross.png')
                closeCross.classList.add('close-cross')
                div.classList.add('modal-price')
                let table = document.createElement('table')
                let caption = document.createElement('caption')
                caption.innerHTML = this.innerHTML
                let cloneThTr = document.createElement('tr')
                let cloneTr2 = document.createElement('tr')
                cloneThTr.innerHTML = trHead.innerHTML
                cloneTr2.innerHTML = trChld.innerHTML
                table.appendChild(caption)
                table.appendChild(cloneThTr)
                table.appendChild(cloneTr2)
                table.style.width = '1200px'
                div.appendChild(closeCross)
                div.appendChild(table)
                modalParent.appendChild(div)
                closeCross.addEventListener('click', function() {
                    modalParent.removeChild(div)
                })
//                        console.log(tr2)
            })
        }
        async function getOldPrices(type, points) {
            let result
            let seachParams = new URLSearchParams()
            seachParams.set('get_old_gr_prices', type);
            seachParams.set('get_old_gr_prices_points', points);
            seachParams.set('get_old_gr_prices_int_id', intID);
            seachParams.set('get_old_gr_prices_town', qIndex);
            try {
                 result = await getAjaxPost(seachParams)
            }catch(e){
                console.error(e)
            }
            return result
        }
        async function getIntId(){
            let result;
            let seachParams = new URLSearchParams()
            seachParams.set('now_int_id_request', 'get_id')
            try {                
                result = await getAjaxPost(seachParams)
//                    result = res;
                console.log(result);
            } catch (e) {
                console.error(e)
            }
            return result;
        }
        async function getOldDestParams(type) {
            let result;
            let seachParams = new URLSearchParams()
            seachParams.set('get_old_dest_params', type);
            seachParams.set('int_id_old', intID);
            seachParams.set('town_index', qIndex);
            try {                
                result = await getAjaxPost(seachParams)
            } catch (e) {
                console.error(e)
            } 
            return result;
        }
        async function getUserQIndex() {
            let result
            let seachParams = new URLSearchParams()
            seachParams.set('get_user_q_index', 1)
            try {
                result = await getAjaxPost(seachParams)
                return result
            } catch (e) {
                console.log(e)
            }
        }
        async function getRuDests(dests) {
            let result
            let searchParams = new URLSearchParams()
            searchParams.set('get_ru_dests', dests)
            try {
                result = await getAjaxPost(searchParams)
            } catch(e) {
                console.error(e)
            }
            return result
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
        run()
    
</script>