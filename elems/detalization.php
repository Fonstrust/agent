<?php
require_once './clases/users.php';
require_once './clases/mysql.php';
require_once './functions/php_func.php';
$user = new User($_SESSION['id']);

?>
<div class="check_table container">
    <div class="block-header">
        <img src="/elems/img/mytransfers_icon.png">
        <span class="block-head">
            <ul class="sub_menu">
                <li id="unpayed_orders" class="selected-tr">Неоплаченные брони</li>
                <li id="pay_history">История оплат</li>
            </ul>
        </span>
    </div>
    <hr>
    <div id="tables">
    </div>
</div>

<script type="text/javascript">
    async function getAsync() {
        let unpayedBut = document.getElementById('unpayed_orders')
        let historyBut = document.getElementById('pay_history')
        let tables = document.getElementById('tables')
        writeUnpayed()
        unpayedBut.addEventListener('click', await writeUnpayed)
        historyBut.addEventListener('click', await writeHistory)
        
        async function writeUnpayed() {
            unpayedBut.classList.add('selected-tr')
            historyBut.classList.remove('selected-tr')
            tables.innerHTML = ''
            let userUnpayedTransfers = await getUserUnpayedTransfers()
            let transLen = userUnpayedTransfers.length
            let table = document.createElement('table')
            table.classList.add('my_transfers-table')
            let pkoBut = document.createElement('button')
            let countBut = document.createElement('button')
            pkoBut.innerHTML = 'Создать ПКО'
            countBut.innerHTML = 'Создать счет'
            pkoBut.disabled = true
            countBut.disabled = true
            pkoBut.setAttribute('id', 'add_pko')
            countBut.setAttribute('id', 'add_count')
            pkoBut.addEventListener('click', await sentPay)
            countBut.addEventListener('click', await sentPay)
            
            let ths = ['ID', 'ФИО', 'Дата/время создания', 'Дата/время отправления', 'Сумма', 'Комиссия', 'К оплате', 'Выбрать']
            let thTr = document.createElement('tr')
            let colNum = ths.length
            for (let i = 0; i < colNum; i++) {
                let th = document.createElement('th')
                th.innerHTML = ths[i]
                thTr.appendChild(th)
            }
            table.appendChild(thTr)
            
            let tds = ['trans_id', 'pass_id', 'create_time', 'pay_deadline', 'cost', 'comision', 'for_pay', 'check']
            
            for (let i = 0; i < transLen; i++) {
                let tr = document.createElement('tr')
                for (let j = 0; j < colNum; j++) {
                    let td = document.createElement('td')
                    if (j == 6) {
                        td.classList.add('tac')
                    }
                    if (tds[j] == 'check') {
                        let checkbox = document.createElement('input')
                        checkbox.setAttribute('type', 'checkbox')
                        checkbox.setAttribute('data-sum', userUnpayedTransfers[i]['for_pay'])
                        checkbox.setAttribute('id', 'check-' + userUnpayedTransfers[i]['trans_id'])
                        checkbox.classList.add('check-list')
                        td.appendChild(checkbox)
                        checkbox.addEventListener('click', await async function(){
                            
                            if (checkbox.checked == true) {
                                checkbox.checked = false;
                                tr.classList.remove('selected')
                                await unlockButs()
                                return
                            }
                            checkbox.checked = true
                            tr.classList.add('selected')
                            await unlockButs()
                        })
                        tr.addEventListener('click', await async function(){
                            
                            if (checkbox.checked == true) {
                                checkbox.checked = false;
                                tr.classList.remove('selected')
                                await unlockButs()
                                return
                            }
                            checkbox.checked = true
                            tr.classList.add('selected')
                            await unlockButs()
                        })
                    } else {
                        td.innerHTML = userUnpayedTransfers[i][tds[j]]
                    }
                    tr.appendChild(td)
                } 
                
                table.appendChild(tr)
            }
            tables.appendChild(table)
            tables.appendChild(pkoBut)
            tables.appendChild(countBut)
            
            async function unlockButs() {
                let checking = await checkChecked()
                if (checking.length == 0) {
                    pkoBut.disabled = true
                    countBut.disabled = true
                } else {
                    pkoBut.disabled = false
                    countBut.disabled = false
                }
            }
            async function sentPay() {
                let result
                let ajaxSent = {}
                let checkedTransfers = await checkChecked()
                let checkedNum = checkedTransfers.length
                for (let i = 0; i < checkedNum; i++) {
                        let sumElem = document.getElementById('check-' + checkedTransfers[i])
                        let sum = sumElem.dataset.sum
                        ajaxSent[checkedTransfers[i]] = sum
                }
                let searchParams = new URLSearchParams()
                searchParams.set('add_pay_doc', this.id)
                searchParams.set('add_pay_doc_vals', JSON.stringify(ajaxSent))
                try {
                    result = await getAjaxPost(searchParams)
                } catch(e) {
                    console.error(e)
                }
                await writeHistory()
            }
        }
            
        async function checkChecked() {
            let checkList = document.getElementsByClassName('check-list')
            let checkNum = checkList.length
            let checkedArr = []
            for (let i = 0; i < checkNum; i++) {
                if (checkList[i].checked == true) {
                    checkedArr.push(checkList[i].id.split('-')[1])
                }
            }
            return checkedArr
        }
        
        async function writeHistory() {
            unpayedBut.classList.remove('selected-tr')
            historyBut.classList.add('selected-tr')
            tables.innerHTML = ''
            let table = document.createElement('table')
            table.classList.add('my_transfers-table')
            let ths = ['№', 'Тип', 'Дата/время создания', 'Сумма', 'Проведен', 'Печать']
            let thTr = document.createElement('tr')
            let colNum = ths.length
            for (let i = 0; i < colNum; i++) {
                let th = document.createElement('th')
                th.innerHTML = ths[i]
                thTr.appendChild(th)
            }
            table.appendChild(thTr)
            
            let payDocs = await getPayDocs()

            let docsLen = payDocs.length
            let tds = ['ID', 'type', 'create_time', 'doc_sum', 'done', 'print']
            for (let i = 0; i < docsLen; i++) {
                let tr = document.createElement('tr')
                for (let j = 0; j < colNum; j++) {
                    let td = document.createElement('td')
                    if (tds[j] == 'print') {
                        let printBut = document.createElement('a')
                        printBut.classList.add('print_but')
                        printBut.innerHTML = '<i class="fa fa-print" aria-hidden="true"></i>'
                        printBut.setAttribute('href', './elems/print_pko.php?doc_id=' + payDocs[i]['ID'] + '&doc_type=' + payDocs[i]['type'])
                        printBut.setAttribute('target', '_blank')
                        td.appendChild(printBut)
                    } else if (tds[j] == 'type') {
                        if (payDocs[i]['type'] == 'pko') {
                            td.innerHTML = 'ПКО'
                        } else {
                            td.innerHTML = 'Счёт'
                        }
                    } else if (tds[j] == 'done') {
                        if (payDocs[i]['done'] == 0) {
                            td.innerHTML = 'Не проведен'
                        } else {
                            td.innerHTML = 'Проведен'
                        }
                    } else {
                        td.innerHTML = payDocs[i][tds[j]]
                    }
                    tr.appendChild(td)
                }
                table.appendChild(tr)
            }
            tables.appendChild(table)
            console.log(payDocs)
        }
        
        async function getPayDocs() {
            let result
            let searchParams = new URLSearchParams()
            searchParams.set('get_pay_docs', 1)
            try {
                result = await getAjaxPost(searchParams)
            } catch(e) {
                console.error(e)
            }
            return result
        }
        
        async function getUserUnpayedTransfers() {
            let result
            let searchParams = new URLSearchParams()
            searchParams.set('get_user_unpaed_transfers', 1)
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
    getAsync()
</script>
