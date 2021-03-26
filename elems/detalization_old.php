<?php
require_once './clases/users.php';
require_once './clases/mysql.php';
require_once './functions/php_func.php';
$user = new User($_SESSION['id']);

?>
<div class="check_table">
    <ul class="sub_menu">
        <li id="unpayed_orders" class="selected">Неоплаченные брони</li>
        <li id="pay_history">История оплат</li>
    </ul>
</div>
<div class="tables">
    <div id="filter_place"></div>
    <table id="unpayed_table"></table>
    <table id="history_table"></table>
</div>
<script type="text/javascript">
        
    async function detAsync() {
        let unpayedBut = document.getElementById('unpayed_orders')
        let historyBut = document.getElementById('pay_history')
        let unpayedTable = document.getElementById('unpayed_table')
        let historyTable = document.getElementById('history_table')
        let tables = document.querySelector('.tables')
        let filterPlace = document.getElementById('filter_place')
        unpayedBut.addEventListener('click', await async function() {
            if (!unpayedBut.classList.contains('selected')){
                await showUnpayed()
                unpayedBut.classList.add('selected')
                historyBut.classList.remove('selected')
            }
        })
        historyBut.addEventListener('click', await async function() {
            if (!historyBut.classList.contains('selected')){
                await showHistory()
                historyBut.classList.add('selected')
                unpayedBut.classList.remove('selected')
            }
        })
        
        await showUnpayed();
        await unpayedJS();
        async function showHistory() {
            let pkoBut = document.getElementById('add_pko')
            let countBut = document.getElementById('add_count')
            tables.removeChild(pkoBut)
            tables.removeChild(countBut)
            unpayedTable.innerHTML = ''
            historyTable.innerHTML = ''
            filterPlace.innerHTML = ''
            let filterSelect = document.createElement('select')
            let optVals = ['all', 'pko', 'count', 'doc_id']
            let optInns = ['Все', 'ПКО', 'Счета', 'Поиск по номеру']
            let optLen = optVals.length
            let docLimit = 25
            for (let i = 0; i < optLen; i++) {
                let option = document.createElement('option')
                option.setAttribute('value', optVals[i])
                option.innerHTML = optInns[i]
                filterSelect.appendChild(option)
            }
            let inputFind = document.createElement('input')
            inputFind.setAttribute('type', 'number')
            inputFind.classList.add('hide')
            let butFind = document.createElement('button')
            butFind.classList.add('hide')
            filterPlace.appendChild(filterSelect)
            filterPlace.appendChild(inputFind)
            filterPlace.appendChild(butFind)
            filterBy()



            async function filterBy() {
                historyTable.innerHTML = ''
                let result
                if (filterSelect.value != 'doc_id') {
                    inputFind.classList.add('hide')
                    butFind.classList.add('hide')                
                    let searchParams = new URLSearchParams()
                    searchParams.set('filter_pay_docs', filterSelect.value)
                    searchParams.set('filter_pay_docs_limit', docLimit)
                    try {
                        result = await getAjaxPost(searchParams)
                    } catch(e) {
                        console.error(e)
                    }
                } else {
                    inputFind.classList.remove('hide')
                    butFind.classList.remove('hide')
                    if (inputFind.value) {
                        let searchParams = new URLSearchParams()
                        searchParams.set('filter_id_docs', inputFind.value)
                        searchParams.set('filter_id_docs_limit', docLimit)
                        try {
                            result = await getAjaxPost(searchParams)
                        } catch(e) {
                            console.error(e)
                        }
                    } else {
                        console.log('!!!!!!!!!!!!!!')
                        return
                    }

                }
                if (result == 'none') {
                    let caption = document.createElement('caption')
                    caption.innerHTML = '<nobr>Документы не найдены</nobr>'
                    historyTable.appendChild(caption)
                    return
                }
                console.log(result)
                let ths = ['#', 'Дата создания', 'Тип', 'Компания', 'Сумма', 'Оплата', 'Печать'];
                let colNum = ths.length
                let trHead = document.createElement('tr')
                for (let i = 0; i < colNum; i++) {
                    let th = document.createElement('th')
                    th.innerHTML = ths[i]
                    trHead.appendChild(th)
                }
                historyTable.appendChild(trHead)
                let trResLen = result.length
                for (let i = 0; i < trResLen; i++) {
                    let tr = document.createElement('tr')
                    tr.classList.add('to_inside')
                    tr.setAttribute('id', result[i].ID + '-' + result[i].type + '-inside')
                    let docType
                    let chkClass
                    if (result[i].type == 'pko'){
                        docType = 'ПКО'
                    } else if (result[i].type == 'count') {
                        docType = 'Счет'
                    }
                    let tdChk = document.createElement('td')
//                    let inputChk = document.createElement('input')
//                    inputChk.setAttribute('type', 'checkbox')
//                    inputChk.setAttribute('id', 'done-' + result[i].ID + '-' + result[i].type)

                    let tdPrint = document.createElement('td')
                    let aPrint = document.createElement('a')
                    aPrint.setAttribute('href', './elems/print_pko.php?doc_id=' + result[i].ID + '&&doc_type=' + result[i].type)
                    let butPrint = document.createElement('button')
                    butPrint.innerHTML = 'Печать'
                    if (result[i].done == 0) {
                        tdChk.innerHTML = 'Не оплачен'
//                        inputChk.classList.add('dones')
                    } else {
                        tdChk.innerHTML = 'Оплачен'
//                        inputChk.classList.add('dones_done')
//                        inputChk.checked = true
//                        inputChk.setAttribute('disabled', true)
                    }
//                    tdChk.appendChild(inputChk)
                    aPrint.appendChild(butPrint)
                    tdPrint.appendChild(aPrint)
                    let tdKeys = ['ID', 'create_time', 'type', 'user_id_debt', 'doc_sum', 'chk', 'print']
                    for (let j = 0; j < colNum; j++) {
                        if (tdKeys[j] == 'chk'){
                            tr.appendChild(tdChk)
                        } else if (tdKeys[j] == 'print') {
                            tr.appendChild(tdPrint)
                        } else if (tdKeys[j] == 'type') {
                            let td = document.createElement('td')
                            td.innerHTML = docType
                            tr.appendChild(td)
                        } else {
                            let td = document.createElement('td')
                            td.innerHTML = result[i][tdKeys[j]]
                            tr.appendChild(td)
                        }
                    }
                    tr.addEventListener('dblclick', await async function(){
                        let itId = this.id.split('-')[0]
                        let itType = this.id.split('-')[1]
                        window.location = '?list=pay_doc_inside&&pay_doc_type=' + itType + '&&pay_doc_id=' + itId
                    })
                    historyTable.appendChild(tr)
                }

            }
        }
        async function showUnpayed() {
            unpayedTable.innerHTML = ''
            historyTable.innerHTML = ''
            let result
            let searchParams = new URLSearchParams()
            searchParams.set('get_unpaed_orders', 1)
            try {
                result = await getAjaxPost(searchParams)
            } catch(e) {
                console.error(e)
            }
            if (result.length == 0) {
                let caption = document.createElement('caption')
                caption.innerHTML = '<nobr>Неоплаченных трансферов нет</nobr>'
                unpayedTable.appendChild(caption)
            } else {        
                console.log(result)
                let resLen = result.length
                let trHead = document.createElement('tr')
                let ths = ['#', 'Отправление', 'ФИО', 'Стоимость', 'Комиссия', 'К оплате', 'Выбрать']
                let colsNum = ths.length
                for (let i = 0; i < colsNum; i++) {
                    let th = document.createElement('th')
                    if (i == colsNum - 1) {
                        th.setAttribute('id', 'check_all')
                    }
                    th.innerHTML = ths[i]
                    trHead.appendChild(th)
                }
                unpayedTable.appendChild(trHead)
                for (let i = 0; i < resLen; i++) {
                    let tr = document.createElement('tr')
                    let keys = ['trans_id', 'pay_deadline', 'pass_id', 'cost', 'comision', 'for_pay']
                    for(let j = 0; j < colsNum; j++){
                        let td = document.createElement('td')
                        if (j == colsNum - 1) {
                            let input = document.createElement('input')
                            input.setAttribute('type', 'checkbox')
                            input.setAttribute('id', result[i]['trans_id'] + '-for_pay')
                            input.classList.add('checkboxes')
                            td.appendChild(input)
                        } else if (keys[j] == 'for_pay') {
                            td.setAttribute('id', result[i].trans_id + '-sent_sum')
                            if (result[i].add_pay == 0) {
                                td.innerHTML = result[i].for_pay
                            } else {
                                td.innerHTML = result[i].add_pay
                            }
                        } else {
                            td.innerHTML = result[i][keys[j]]
                        }
                        tr.appendChild(td)
                    }
                    unpayedTable.appendChild(tr)
                    
                    
                }
                let addPko = document.createElement('button')
                addPko.innerHTML = 'Создать ПКО'
                addPko.setAttribute('id', 'add_pko')
                addPko.disabled = true
                let addCount = document.createElement('button')
                addCount.innerHTML = 'Создать Счет'
                addCount.setAttribute('id', 'add_count')
                addCount.disabled = true
                tables.appendChild(addPko)
                tables.appendChild(addCount)
            }
            
            await unpayedJS();
        }
        async function unpayedJS() {
            let checkBoxes = document.getElementsByClassName('checkboxes')
            let checkBoxesNum = checkBoxes.length
            let checkAll = document.getElementById('check_all')
            let pkoBut = document.getElementById('add_pko')
            let countBut = document.getElementById('add_count')
            for (let i = 0; i < checkBoxesNum; i++) {            
                checkBoxes[i].addEventListener('change', unlockButtons)
            }
            checkAll.addEventListener('click', function(){
                let checkFlag = 0;
                for (let i = 0; i < checkBoxesNum; i++) {            
                    if (checkBoxes[i].checked) {
                        checkFlag++
                    }
                }
                if (checkFlag == checkBoxesNum) {
                    for (let i = 0; i < checkBoxesNum; i++) {
                        checkBoxes[i].checked = false
                    }
                } else {
                    for (let i = 0; i < checkBoxesNum; i++) {
                        checkBoxes[i].checked = true
                    }
                }
                unlockButtons()
            })


            function unlockButtons() {
                let checkFlag = 0;
                for (let i = 0; i < checkBoxesNum; i++) {            
                    if (checkBoxes[i].checked) {
                        checkFlag++
                    }
                }
                if (checkFlag > 0) {
                    pkoBut.removeAttribute('disabled')
                    countBut.removeAttribute('disabled')
                } else {
                    pkoBut.setAttribute('disabled', true)
                    countBut.setAttribute('disabled', true)
                }
            }
                pkoBut.addEventListener('click', await sentPay)
                countBut.addEventListener('click', await sentPay)

            async function sentPay() {
                let result
                let ajaxSent = {}
                for (let i = 0; i < checkBoxesNum; i++) {            
                    if (checkBoxes[i].checked) {
                        let sumElem = document.getElementById(checkBoxes[i].id.split('-')[0] + '-sent_sum')
                        let sum = sumElem.innerHTML
                        ajaxSent[checkBoxes[i].id.split('-')[0]] = sum
                    }
                }
                let searchParams = new URLSearchParams()
                searchParams.set('add_pay_doc', this.id)
                searchParams.set('add_pay_doc_vals', JSON.stringify(ajaxSent))
                try {
                    result = await getAjaxPost(searchParams)
                } catch(e) {
                    console.error(e)
                }
                console.log(result)
//                let pkoBut = document.getElementById('add_pko')
//                let countBut = document.getElementById('add_count')
//                tables.removeChild(pkoBut)
//                tables.removeChild(countBut)
                await showHistory()
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
    detAsync()
</script>