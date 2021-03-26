let orderDate = document.getElementById('transfer_date')    
    let townPointList = document.getElementById('town_point_list')
    let oblTownsListHide = document.getElementById('obl_towns_list_hide')
    let townNameRuInput = document.getElementById('town_name_ru')
    let townNameRu = townNameRuInput.value
    let parentField = document.querySelector('.step_by_step')
    let transferLogsBut = document.getElementById('transfer_logs')
    let transferRedact = document.getElementById('transfer_redact')
    let transferRedactID = document.getElementById('transfer_redact_id')
    let uComment = document.getElementById('u_comment')
    let chldSeatNumInput = document.getElementById('chld_seat_num_val')
    let airReisInput = document.getElementById('air_reis_input')
    let sentOrder = document.getElementById('sent_order')
    let allCostView = document.getElementById('all_cost_view')
    let orderDetail = document.getElementById('order_detail')
    let adressInputParentGroup = document.getElementById('adress_input_parent_group')
    let addAdressButGroup = document.getElementById('add_adress_group')
    let passengersPlace = document.getElementById('passengers_place')
    let adltNumValInput = document.getElementById('adlt_num_val')
    let chldNumValInput = document.getElementById('chld_num_val')
    let allPassValP = document.getElementById('all_pass_val')
    let typeOfTransfer = document.getElementById('type_of_transfer')
//    let groupWayAdress = document.getElementById('group_way_adress')
//    let groupAdressBut = document.getElementById('group_adr')
//    let groupAdressVal = document.getElementById('group_from_adress')
//    let groupDepartPoints = document.getElementById('group_depart_points')
//    let groupTownPoints = document.getElementById('town_points')
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
    var redOldVals
    let forAllCheckFlag = 0
    if (orderDate.value == 'on') {
        dateSet.value = orderDate.dataset.or_date
    }
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
        let forAllLabels = document.getElementsByClassName('for_all_labels')
        let forAllLen = forAllLabels.length
        
            for (let i = 0; i < forAllLen; i++) {
                if (allPass > 1) {
                    forAllLabels[i].classList.remove('hide')
                } else {
                    forAllLabels[i].classList.add('hide')
                }
            }
    }
    
    indTypeSelect.addEventListener('change', setMaxPass)
    
    function testTrueDate(){
        let now = new Date()
        let val = new Date(this.value)
        if (val >= now) {
            this.value = null
            focusEmpty2(this)
        }
        if (this.id == 'birth_date-0') {
            let chldTest = isItChld(this)
            if (transferRedact.value == 'off' || typeOfTransfer.value == 'ind') {
                if (chldTest === true) {
                    alert('Первый пассажир не может быть ребенком.')
                    focusEmpty2(this)
                    this.value = null
                }
            }
        }
    }
    function focusEmpty2(item) {
        item.focus()
        item.style.background = '#fd7d7d'
        item.style.transition = 'all 1s ease'
        setTimeout(function(){
            item.style.background = 'white'
        }, 1000)
    }
    /*
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
         */       
    function addPassData(num) {
        let div = document.createElement('div')
        div.style.display = 'flex'
        div.style.border = '1px solid #00000026'
        div.classList.add('passenger_item')
        div.setAttribute('id', num + '_divpass')
        let heads = ['ФИО пассажира', 'Дата рождения', 'Номер телефона', 'Серия и номер паспорта', 'Aдрес в г.' + townNameRu]
        let ids = ['fio', 'birth_date', 'phone', 'passport_num', 'adress']
        
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
                input.addEventListener('input', testTrueDate)
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
            if (ids[i] == 'adress') {
                if (typeOfTransfer.value == 'ind') {
                    let adressCenter = document.createElement('div')
                    adressCenter.setAttribute('id', 'adress_center')
                    let oblBut = document.createElement('button')
                    oblBut.setAttribute('id', 'obl_towns_button')
                    oblBut.innerHTML = 'Область'
                    adressCenter.appendChild(oblBut)
                    let oblTownsList = document.createElement('div')
                    oblTownsList.setAttribute('id', 'obl_towns_list')
                    oblTownsList.classList.add('hide')
                    let centerBut = document.createElement('button')
                    centerBut.setAttribute('id', 'center_button')
                    centerBut.innerHTML = townNameRu
                    let oblSelect = document.createElement('select')
                    oblSelect.setAttribute('id', 'obl_towns_list_select')
                    oblSelect.innerHTML = oblTownsListHide.innerHTML
                    oblTownsList.appendChild(oblSelect)
                    oblTownsList.appendChild(centerBut)
                    let adrPlace = document.createElement('div')
                    adrPlace.setAttribute('id', 'adr_place')
                    let adressInputParent = document.createElement('div')
                    adressInputParent.appendChild(input)
                    adressInputParent.setAttribute('id', 'adress_input_parent')
                    input.classList.add('adress_input')
                    adrPlace.appendChild(adressInputParent)
                    let addAdress = document.createElement('button')
                    addAdress.innerHTML = 'Добавить адрес'
                    addAdress.setAttribute('id', 'add_adress')
                    adrPlace.appendChild(addAdress)
                    divChld.appendChild(pHead)
                    divChld.appendChild(adressCenter)
                    divChld.appendChild(oblTownsList)
                    divChld.appendChild(adrPlace)
                    div.appendChild(divChld)
                } else {
                    let groupAdressBut = document.createElement('button')
                    groupAdressBut.innerHTML = 'Забрать с адреса'
                    groupAdressBut.classList.add('group_adress_but')
                    let p = document.createElement('p')
                    p.innerHTML = 'Выберите точку посадки'
                    p.classList.add('adress-control')
                    p.setAttribute('data-adress', 0)
                    let townPoints = document.createElement('select')
                    townPoints.innerHTML = townPointList.innerHTML
                    townPoints.classList.add('town_points')
                    let adressInputGroup = document.createElement('input')
                    adressInputGroup.setAttribute('type', 'text')
                    adressInputGroup.classList.add('adress_input_group')
                    adressInputGroup.classList.add('hide')
                    if (num == 0) {
                        let forAllLabel = document.createElement('label')
                        let forAllCheckbox = document.createElement('input')
                        let forAllSpan = document.createElement('span')
                        forAllCheckbox.setAttribute('type', 'checkbox')
                        forAllCheckbox.classList.add('for_all_check')
                        forAllCheckbox.addEventListener('click', function() {
    //                        let forAllLabels = document.getElementsByClassName('for_all_labels')
    //                        let forAllLen = forAllLabels.length
                            let ps = document.getElementsByClassName('adress-control')
                            let forAllLen = ps.length
                            let groupAdressButs = document.getElementsByClassName('group_adress_but')
                            let checks = document.getElementsByClassName('for_all_check')
                            let adressGroupInputs = document.getElementsByClassName('adress_input_group')
                            console.log(adressGroupInputs)
                            let townPointsSels = document.getElementsByClassName('town_points')
                            for (let i = 0; i < forAllLen; i++) {
                                if (this.checked == true) {
                                    if (checks[i] != this){
                                        if (p.dataset.adress == 0) {
                                            groupAdressButs[i].innerHTML = 'Забрать с адреса'
                                            ps[i].innerHTML = 'Выберите точку посадки'
                                            adressGroupInputs[i].classList.add('hide')
                                            townPointsSels[i].classList.remove('hide')
                                            townPointsSels[i].value = townPoints.value
                                            ps[i].setAttribute('data-adress', 0)
                                        } else {  
                                            groupAdressButs[i].innerHTML = 'Забрать с точки посадки'
                                            ps[i].innerHTML = 'Введите адрес'
                                            adressGroupInputs[i].classList.remove('hide')
                                            townPointsSels[i].classList.add('hide')
                                            ps[i].setAttribute('data-adress', 1)
                                            adressGroupInputs[i].value = adressInputGroup.value
                                            adressGroupInputs[i].setAttribute('readonly', true)
                                        }
                                    }
                                } else {
                                    if (checks[i] != this){
                                        groupAdressButs[i].innerHTML = 'Забрать с адреса'
                                        ps[i].innerHTML = 'Выберите точку посадки'
                                        adressGroupInputs[i].classList.add('hide')
                                        townPointsSels[i].classList.remove('hide')
                                        townPointsSels[i].selectedIndex = 0
                                        ps[i].setAttribute('data-adress', 0)
                                        adressGroupInputs[i].removeAttribute('readonly')
                                    }
                                }
                            }
                        })
                        adressInputGroup.addEventListener('input', function() {
                            let groupAdressInputs = document.getElementsByClassName('adress_input_group')
                            let numGroupInputs = groupAdressInputs.length
                            if (forAllCheckbox.checked === true) {
                                console.log(p.dataset.adress)
                                for (let i = 1; i < numGroupInputs; i++) {
                                    groupAdressInputs[i].value = adressInputGroup.value
                                }
                            }
                        })
                            
                        forAllSpan.innerHTML = 'Для всех'
                        forAllLabel.appendChild(forAllCheckbox)
                        forAllLabel.appendChild(forAllSpan)
                        forAllLabel.classList.add('for_all_labels')
                        divChld.appendChild(p)
                        divChld.appendChild(townPoints)
                        divChld.appendChild(adressInputGroup)
                        divChld.appendChild(forAllLabel)
                        divChld.appendChild(groupAdressBut)
                        div.appendChild(divChld)
                    } else {
                        divChld.appendChild(p)
                        divChld.appendChild(townPoints)
                        divChld.appendChild(adressInputGroup)
                        divChld.appendChild(groupAdressBut)
                        div.appendChild(divChld)                        
                    }
                    groupAdressBut.addEventListener('click', function(){
                        if (p.dataset.adress == 0) {
                            this.innerHTML = 'Забрать с точки посадки'
                            p.innerHTML = 'Введите адрес'
                            adressInputGroup.classList.remove('hide')
                            townPoints.classList.add('hide')
                            p.setAttribute('data-adress', 1)
                        } else {
                            this.innerHTML = 'Забрать с адреса'
                            p.innerHTML = 'Выберите точку посадки'
                            adressInputGroup.classList.add('hide')
                            townPoints.classList.remove('hide')
                            p.setAttribute('data-adress', 0)
                            
                        }
                    })
                }
                
            } else {
                divChld.appendChild(pHead)
                divChld.appendChild(input)
                div.appendChild(divChld)
            }
            
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
        let now = new Date()
        let val = new Date(this.value)
        let nowDate = addZero(now.getFullYear()) + '-' + addZero(now.getMonth() + 1) + '-' + addZero(now.getDate())
        let nowBirthDate = addZero(now.getFullYear()) + '-' + addZero(val.getMonth() + 1) + '-' + addZero(val.getDate())
        let age = now.getFullYear() - val.getFullYear()
        if (nowDate <= nowBirthDate) {
            age -= 1
        }
        
        if (age > 100) {
            this.value = null
            focusEmpty2(this)
        }
        if (typeOfTransfer.value == 'group') {
            let passItems = document.getElementsByClassName('passenger_item')
            let passItemsCount = passItems.length
            let chld = 0
            let adlt = 0
            let noVal = 0
            for (let i = 0; i < passItemsCount; i++) {
                let birthDateIn = document.getElementById('birth_date-' + passItems[i].id.split('_')[0])
                console.log(birthDateIn)
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
            console.log('NOOOOOVAAAL', noVal)
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
            let allPass = adltNumValInput.value + chldNumValInput.value
            console.log('ALLLLLLLLLLLLL', allPass)
            if (allPass > maxPass) {
//                alert('В выбранном тарифе максимальное количество пассажиров меньше нужного Вам.')
                adltNumValInput.value = maxPass
                chldNumValInput.value = 0
            } else if (allPass < minPass) {
//                alert('В выбранном тарифе минимальное количество пассажиров больше нужного Вам.')
                adltNumValInput.value = minPass
                chldNumValInput.value = 0
            }
            
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
    function inArray(item, arr) {
        let len = arr.length
        for (let i = 0; i < len; i++) {
            if (item == arr[i]) {
                return true
            }
        }
        return false
    }
    typeOfTransfer.addEventListener('change', function(){
//        console.log('TOWNPointss', groupTownPoints[groupTownPoints.selectedIndex].innerHTML)
        passengersPlace.innerHTML = ''
        if (this.value == 'ind') {
            addPassData(0)
            groupAdressFlag = 0
//            indWayAdress.classList.remove('hide')
//            groupWayAdress.classList.add('hide')
            indType.classList.remove('hide')
            if (inArray(transferGroupDestinations.value.split('_')[0], indCodeDestsArr)) {
                transferIndDestinations.value = transferGroupDestinations.value
            }            
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
            let passItems = document.getElementsByClassName('passenger_item');
            let passItemsLen = passItems.length
            for (let i = passItemsLen - 1; i > 0; i--) {
                passengersPlace.removeChild(passItems[i]);
            }
            let bagp = document.getElementById('bag_p')
            let bagInput = document.getElementById('bag_input')
            if (bagInput != null) {
                bagp.classList.add('hide')
                bagInput.classList.add('hide')                
            }
            setMaxPass()
        } else if (this.value == 'group') {
            let carretParent = reisCarret.parentNode
            let mskAdressP = document.getElementById('msk_adr_p')
            let mskAdressInput = document.getElementById('msk_adr_input')
            if (mskAdressInput != null) {
                carretParent.removeChild(mskAdressP)
                carretParent.removeChild(mskAdressInput)
            }
            groupAdressFlag = 1
//            indWayAdress.classList.add('hide')
//            groupWayAdress.classList.remove('hide')
            indType.classList.add('hide')
            if (inArray(transferIndDestinations.value.split('_')[0], groupCodeDestsArr)) {
                transferGroupDestinations.value = transferIndDestinations.value            
            }
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
            let bagp = document.getElementById('bag_p')
            let bagInput = document.getElementById('bag_input')
            if (bagInput == null) {
                let bagp = document.createElement('p')
                let bagInput = document.createElement('input')
                bagp.setAttribute('id', 'bag_p')
                bagInput.setAttribute('id', 'bag_input')
                bagp.innerHTML = 'Введите количество дополнительного багажа'
                bagInput.setAttribute('type', 'number')
                uComment.insertAdjacentElement('afterEnd', bagInput);
                uComment.insertAdjacentElement('afterEnd', bagp);
            } else {
                bagp.classList.remove('hide')
                bagInput.classList.remove('hide')
            }
            
            
            
            setMaxPass()
            countAllPass()
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
            setDestTypeP()
        }
            
    })
    transferIndDestinations.addEventListener('change', function(){
        if (typeOfTransfer.value == 'ind') {
            let itVal = this.value
            let itArr = itVal.split('_')
            transferIndDestinationSubPoints.innerHTML = ''
            createSubPointsOpt(transferIndDestinationSubPoints, 'ind', itArr[0])
            let itType = itArr[1]
            let carretParent = reisCarret.parentNode
            if (itType == 'air') {
                reisCarret.classList.remove('hide')
            } else {
                reisCarret.classList.add('hide')
            }
            if (itType == 'adr') {
                let mskAdressP = document.getElementById('msk_adr_p')
                let mskAdressInput = document.getElementById('msk_adr_input')
                if (mskAdressInput != null) {
                    mskAdressP.classList.remove('hide')
                    mskAdressInput.classList.remove('hide')
                } else {
                    let mskAdressImput = document.createElement('input')
                    let mskAdressP = document.createElement('p')
                    mskAdressP.innerHTML = 'Введите адрес в Москве'
                    mskAdressP.setAttribute('id', 'msk_adr_p')
                    mskAdressImput.setAttribute('id', 'msk_adr_input')
                    mskAdressImput.setAttribute('type', 'text')
                    mskAdressImput.style.width = '70%'
                    carretParent.appendChild(mskAdressP)
                    carretParent.appendChild(mskAdressImput)
                }
            } else {
                let mskAdressP = document.getElementById('msk_adr_p')
                let mskAdressInput = document.getElementById('msk_adr_input')
                if (mskAdressInput != null) {
                    mskAdressP.classList.add('hide')
                    mskAdressInput.classList.add('hide')
                }
                
            }
            setDestTypeP()
        }
    })
    transferDirection.addEventListener('change', setDestTypeP)
    function setDestTypeP() {
        let itVal
        if (typeOfTransfer.value == 'group') {
            itVal = transferGroupDestinations.value
        } else if (typeOfTransfer.value == 'ind') {
            itVal = transferIndDestinations.value
        }
        let itArr = itVal.split('_')
        let itType = itArr[1]
        let destTypeP = document.getElementById('dest_type_' + itType)
        let typeArr = destTypeP.innerHTML.split('___')
        if (transferDirection.value == 'to_msk') {
            dateTimeHead.innerHTML = typeArr[0]
        } else {
            dateTimeHead.innerHTML = typeArr[1]
        }
        
    }
    setDestTypeP()
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
    
    let adressInputParent = document.getElementById('adress_input_parent')
    let addAdressBut = document.getElementById('add_adress')
    let indAdress = document.getElementById('ind-adress')
    let indWayAdress = document.getElementById('ind_way_adress')
    let oblTownsButton = document.getElementById('obl_towns_button')
    let oblTownsListSel = document.getElementById('obl_towns_list_select')
    let adressCenter = document.getElementById('adress_center')
    let oblTownsList = document.getElementById('obl_towns_list')
    let adrPlace = document.getElementById('adr_place')
    let centerButton = document.getElementById('center_button')
    
    let groupTownPoints = document.querySelector('.town_points')
    
    oblTownsButton.addEventListener('click', function(){
        oblTownFlag = 1
        adressCenter.classList.add('hide')
        oblTownsList.classList.remove('hide')
        if (oblTownsListSel.value == 'off') {
            adrPlace.classList.add('hide')
        }
        
    })
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
    
    async function getAsync() {
        
        centerButton.addEventListener('click', await priceView)
        
        if (transferRedact.value == 'on') {
            let oldTransferIdInput2 = document.getElementById('transfer_redact_id')
            let oldTransferId2 = oldTransferIdInput2.value
            let logs = await getTransferLogs(oldTransferId2)
            let logsLen = logs.length
            let logsResult = []
            let fieldsNames = {
                ID: "",
                u_id: "Пользователь",
                create_time: "Изменено",
                type: "Тип трансфера",
                direction: "Направление",
                adlt_num: "Количество взрослых",
                chld_num: "Количество детей",
                u_comment: "Комментарий",
                tarif: "Тариф",
                msk_point: "Точка в Москве",
                air_reis: "Номер рейса",
                obl_town: "Областной город",
                adress: "Адрес",
                deadline: "Время вылета",
                depart_time: "",
                passenger: "Пассажир",
                msk_adress: "Адрес в Москве",
                add_bag: "Доп. багаж"
            }
            if (logsLen > 1) {
                
                for (let i = 1; i < logsLen; i++) {
                    let res = []
                    let logStart = '<b>' + logs[i].create_time + ' пользователь ' + logs[i].u_id + '</b>'
                    res.push(logStart)
                    for (let key in logs[i]) {
                        if (key != 'create_time' && key != 'depart_time') {
                            if (logs[i-1][key] != logs[i][key]) {
                                res.push('Значение поля <b>"' + fieldsNames[key] + '"</b> <b>"' + logs[i-1][key] + '"</b> заменено на <b>"' + logs[i][key] + '"</b>')
                            }
                        } 
                    }
                    logsResult.push(res)
                }
                console.log('LOOOOGGGGGG', logsResult)
                transferLogsBut.addEventListener('click', await async function(){
                    
                    let field = document.createElement('div')
                    field.style.position = 'fixed'
                    field.style.background = 'white'
                    field.style.right = '30px'
                    field.style.padding = '30px'
                    field.style.width = '500px'
                    field.style.height = '500px'
                    field.style.overflow = 'auto'
                    let closeBut = document.createElement('div')
                    closeBut.style.position = 'absolute'
                    closeBut.style.width = '20px'
                    closeBut.style.height = '20px'
                    closeBut.style.borderRadius = '10px'
                    closeBut.style.background = '#fd5e5e'
                    closeBut.style.top = '10px'
                    closeBut.style.right = '10px'
                    closeBut.addEventListener('click', await async function(){
                        parentField.removeChild(field)
                    })
                    field.appendChild(closeBut)
                    let logStepsNum = logsResult.length
                    for (let i = 0; i < logStepsNum; i++) {
                        let logLen = logsResult[i].length
                        for (let j = 0; j < logLen; j++) {
                            let p = document.createElement('p')
                            p.innerHTML = logsResult[i][j]
                            field.appendChild(p)
                        }
                    }
                    parentField.appendChild(field)
                })
                transferLogsBut.classList.remove('hide')
            }
            
            
            sentOrder.innerHTML = 'Сохранить'
            let oldValues = await getOldTransfer()
            redOldVals = oldValues
            typeOfTransfer.value = oldValues.type
            if (oldValues.type == 'ind') {
                groupAdressFlag = 0
//                indWayAdress.classList.remove('hide')
//                groupWayAdress.classList.add('hide')
                indType.classList.remove('hide')
//                transferIndDestinations.value = transferGroupDestinations.value
                transferGroupDestinationSubPoints.classList.add('hide')
                indTypeSelect.value = oldValues.tarif
                setMaxPass()
                let oldDestVal = oldValues.msk_point.substr(0, 3)
                let indChldDests = transferIndDestinations.children
                let indChldDestsNum = indChldDests.length
                for (let i = 0; i < indChldDestsNum; i++) {
                    if (indChldDests[i].value.split('_')[0] == oldDestVal) {
                        transferIndDestinations.value = indChldDests[i].value
                        if (oldDestVal == 'adr') {
                            let carretParent = reisCarret.parentNode
                            let mskAdressImput = document.createElement('input')
                            let mskAdressP = document.createElement('p')
                            mskAdressP.innerHTML = 'Введите адрес в Москве'
                            mskAdressP.setAttribute('id', 'msk_adr_p')
                            mskAdressImput.setAttribute('id', 'msk_adr_input')
                            mskAdressImput.setAttribute('type', 'text')
                            mskAdressImput.style.width = '70%'
                            mskAdressImput.value = oldValues.msk_adress
                            carretParent.appendChild(mskAdressP)
                            carretParent.appendChild(mskAdressImput)
                        }
                    }
                }
                let itVal = transferIndDestinations.value
                let itArr = itVal.split('_')
                transferIndDestinationSubPoints.innerHTML = ''
                createSubPointsOpt(transferIndDestinationSubPoints, 'ind', itArr[0])
                let isSubDest = oldValues.msk_point.split('-')
                if (isSubDest.length > 1) {
                    transferIndDestinationSubPoints.value = isSubDest[1]
                }
                
                let redAdressNum = oldValues.adress.split(';;;').length;
                for (let i = 0; i < redAdressNum; i++) {
                    if (i == 0) {
                        adressInputParent.children[0].value = oldValues.adress.split(';;;')[i]
                    } else {
                        let input = document.createElement('input')
                        input.classList.add('adress_input')
                        input.setAttribute('type', 'text')
                        input.value = oldValues.adress.split(';;;')[i]
                        adressInputParent.appendChild(input)
                    }
                }
                if (redAdressNum > 1) {
                    let adressDelButton = document.createElement('button')
                    adressDelButton.innerHTML = 'Убрать адрес'
                    adressDelButton.addEventListener('click', await delAdressInput)
                    adressDelButton.setAttribute('id', 'del_adress_but')
                    adrPlace.appendChild(adressDelButton)
                }

                let firstDestValInd2 = transferIndDestinations.value.split('_')
                transferIndDestinations.classList.remove('hide')
                transferGroupDestinations.classList.add('hide')
                let firstDestType2 = firstDestValInd2[1]
                if (firstDestType2 != 'air') {
                    reisCarret.classList.add('hide')
                } else {
                    reisCarret.classList.remove('hide')
                }
                let passItems = document.getElementsByClassName('passenger_item');
                let passItemsLen = passItems.length
                for (let i = passItemsLen - 1; i > 0; i--) {
                    passengersPlace.removeChild(passItems[i]);
                }
                if (oldValues.obl_town != 'noobl') {
                    oblTownFlag = 1
                    adressCenter.classList.add('hide')
                    oblTownsList.classList.remove('hide')
                    oblTownsListSel.value = oldValues.obl_town
                }
                setMaxPass()
            } else if (oldValues.type == 'group') {
                passengersPlace.innerHTML = ''
                addPassData(0)
                groupAdressFlag = 0
//                indWayAdress.classList.add('hide')
//                groupWayAdress.classList.remove('hide')
                indType.classList.add('hide')
                let bagp = document.getElementById('bag_p')
                let bagInput = document.getElementById('bag_input')
                if (bagInput == null) {
                    let bagp = document.createElement('p')
                    let bagInput = document.createElement('input')
                    bagp.setAttribute('id', 'bag_p')
                    bagInput.setAttribute('id', 'bag_input')
                    bagp.innerHTML = 'Введите количество дополнительного багажа'
                    bagInput.setAttribute('type', 'number')
                    uComment.insertAdjacentElement('afterEnd', bagInput);
                    uComment.insertAdjacentElement('afterEnd', bagp);
                    bagInput.value = oldValues.add_bag
                } else {
                    bagp.classList.remove('hide')
                    bagInput.classList.remove('hide')
                    bagInput.value = oldValues.add_bag
                }
                adltNumValInput.setAttribute('readonly', 'true')
                chldNumValInput.setAttribute('readonly', 'true')
                transferGroupDestinations.value = transferIndDestinations.value     
                transferIndDestinationSubPoints.classList.add('hide')
                let itVal = transferGroupDestinations.value
                let itArr = itVal.split('_')
                let oldDestVal = oldValues.msk_point.substr(0, 3)
                let grChldDests = transferGroupDestinations.children
                let grChldDestsNum = grChldDests.length
                for (let i = 0; i < grChldDestsNum; i++) {
                    if (grChldDests[i].value.split('_')[0] == oldDestVal) {
                        transferGroupDestinations.value = grChldDests[i].value
                    }
                }
                transferGroupDestinationSubPoints.innerHTML = ''
                createSubPointsOpt(transferGroupDestinationSubPoints, 'group', transferGroupDestinations.value.split('_')[0])
                let isSubDest = oldValues.msk_point.split('-')
                if (isSubDest.length > 1) {
                    transferGroupDestinationSubPoints.value = isSubDest[1]
                }
                let groupTownPoints = document.querySelector('.town_points')
                let ps = document.querySelector('.adress-control')
                let adressGroupInputs = document.querySelector('.adress_input_group')
                let groupAdressButs = document.querySelector('.group_adress_but')
                let groupTownPointsNum = groupTownPoints.length
                ps.dataset.adress = 1
                for (let i = 0; i < groupTownPointsNum; i++) {
                    if (groupTownPoints[i].innerHTML == oldValues.adress) {
                        groupTownPoints.selectedIndex = i
                        ps.dataset.adress = 0
                        break
                    }
                }
                if (ps.dataset.adress == 1) {
                    groupAdressButs.innerHTML = 'Забрать с точки посадки'
                    ps.innerHTML = 'Введите адрес'
                    adressGroupInputs.classList.remove('hide')
                    groupTownPoints.classList.add('hide')
                    adressGroupInputs.value = oldValues.adress
                }
//                if (groupAdressFlag == 0) {
//                    groupAdressVal.classList.remove('hide')
//                    groupDepartPoints.classList.add('hide')
//                    groupAdressBut.innerHTML = 'Забрать с точки посадки'
//                    let redAdressNum = oldValues.adress.split(';;;').length;
//                    for (let i = 0; i < redAdressNum; i++) {
//                        if (i == 0) {
//                            adressInputParentGroup.children[0].value = oldValues.adress.split(';;;')[i]
//                        } else {
//                            let input = document.createElement('input')
//                            input.classList.add('adress_input_group')
//                            input.setAttribute('type', 'text')
//                            input.value = oldValues.adress.split(';;;')[i]
//                            adressInputParentGroup.appendChild(input)
//                        }
//                            
//                    }
//                    
//                        
//                }
                
                
                priceView()
                
                
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
                setMaxPass()
                countAllPass()
            }
            uComment.value = oldValues.u_comment
            transferDirection.value = oldValues.direction
            let dateTimeRedArr = oldValues.deadline.split(' ')
            dateSet.value = dateTimeRedArr[0]
            timeSet.value = dateTimeRedArr[1].substr(0,5)
            await setDateTime()
            airReisInput.value = oldValues.air_reis
            adltNumValInput.value = parseInt(oldValues.adlt_num)
            chldNumValInput.value = parseInt(oldValues.chld_num)
            chldSeatNumInput.value = parseInt(oldValues.child_seat)
            let redPassData = await getUserPassData(oldValues.passenger)
            let redIds = ['fio', 'birth_date', 'phone', 'passport_num']
            let redIdsNum = redIds.length
            for (let i = 0; i < redIdsNum; i++) {
                let redInput = document.getElementById(redIds[i] + '-0')
                if (redIds[i] == 'passport_num') {
                    if (redPassData.passport != 'no_value') {
                        redInput.value = redPassData.passport
                    }                    
                } 
                if (redIds[i] == 'fio') {
                    redInput.value = redPassData.fio
                } 
                if (redIds[i] == 'birth_date') {
                    redInput.value = redPassData.birth_date
                } 
                if (redIds[i] == 'phone') {
                    redInput.value = redPassData.phone
                } 
            }
            
            countAllPass()
            priceView()
            console.log('OLDPASSSDATAAAAAAAAAAA', redPassData)
        }
        async function getUserPassData(passId) {
            let result
            let searchParams = new URLSearchParams()
            searchParams.set('get_user_pass_data', passId)
            if (transferRedact.value == 'on'){
                searchParams.set('check_redact', transferRedactID.value)
            }
            try {
                result = await getAjaxPost(searchParams)
            } catch(e) {
                console.error(e)
            }
            return result
        }
        async function getOldTransfer() {
            let result
            let oldTransferIdInput = document.getElementById('transfer_redact_id')
            let oldTransferId = oldTransferIdInput.value
            let searchParams = new URLSearchParams()
            searchParams.set('get_old_transfer', oldTransferId)
            if (transferRedact.value == 'on'){
                searchParams.set('check_redact', transferRedactID.value)
            }
            try {
                result = await getAjaxPost(searchParams)
            } catch(e) {
                console.error(e)
            }
            console.log('REDACT', result)
            return result
        }
//        addAdressButGroup.addEventListener('click', await async function(){
//            let adressInputsLen = document.getElementsByClassName('adress_input_group').length
//            let allPassNum = parseInt(adltNumValInput.value) + parseInt(chldNumValInput.value)
//            if (adressInputsLen < allPassNum){
//                if (adressInputsLen == 1) {
//                    let adressDelButton = document.createElement('button')
//                    adressDelButton.innerHTML = 'Убрать адрес'
//                    adressDelButton.addEventListener('click', await delAdressInputGroup)
//                    adressDelButton.setAttribute('id', 'del_adress_but_group')
//                    groupAdressVal.appendChild(adressDelButton)
//                }
//                let input = document.createElement('input')
//                input.classList.add('adress_input_group')
////                input.style.width = '100%'
//                input.setAttribute('type', 'text')
//    //            input.addEventListener('blur', await priceView)
//                adressInputParentGroup.appendChild(input)
//                
//            }
//            await priceView()
//        })
//        async function delAdressInputGroup() {
//            let adressInputsLen = document.getElementsByClassName('adress_input_group').length
//            let adressInputs = document.getElementsByClassName('adress_input_group')
//            if (adressInputsLen > 2) {
//                adressInputParentGroup.removeChild(adressInputs[adressInputsLen - 1])
//            } else {
//                adressInputParentGroup.removeChild(adressInputs[adressInputsLen - 1])
//                groupAdressVal.removeChild(this)
//            }
//            await priceView()
//        }
        addAdressBut.addEventListener('click', await async function(){
            let adressInputsLen = document.getElementsByClassName('adress_input').length
            if (adressInputsLen == 1) {
                let adressDelButton = document.createElement('button')
                adressDelButton.innerHTML = 'Убрать адрес'
                adressDelButton.addEventListener('click', await delAdressInput)
                adressDelButton.setAttribute('id', 'del_adress_but')
                adrPlace.appendChild(adressDelButton)
            }
            let input = document.createElement('input')
            input.classList.add('adress_input')
            input.setAttribute('type', 'text')
//            input.addEventListener('blur', await priceView)
            adressInputParent.appendChild(input)
            await priceView()
        })
        async function delAdressInput() {
            let adressInputsLen = document.getElementsByClassName('adress_input').length
            let adressInputs = document.getElementsByClassName('adress_input')
            if (adressInputsLen > 2) {
                adressInputParent.removeChild(adressInputs[adressInputsLen - 1])
            } else {
                adressInputParent.removeChild(adressInputs[adressInputsLen - 1])
                adrPlace.removeChild(this)
            }
            await priceView()
        }
        oblTownsListSel.addEventListener('change', await setDateTime)
        transferGroupDestinations.addEventListener('change', await setDateTime)
        transferIndDestinations.addEventListener('change', await setDateTime)
        typeOfTransfer.addEventListener('change', await async function(){
            await setDateTime
            if (typeOfTransfer.value == 'group'){
                let groupTownPoints = document.querySelector('.town_points')
                let bagInput = document.getElementById('bag_input')
                bagInput.addEventListener('change', await priceView)
                groupTownPoints.addEventListener('change', await setDateTime)
                let check = document.querySelector('.for_all_check')
                check.addEventListener('change', await priceView)
                let groupAdressButs = document.getElementsByClassName('group_adress_but')
                let groupLen = groupAdressButs.length
                for (let i = 0; i < groupLen; i++) {
                    groupAdressButs[i].addEventListener('click', await setDateTime)
                }
            }
        })
        
        
        
        dateSet.addEventListener('blur', await setDateTime)
        timeSet.addEventListener('blur', await setDateTime)
        
        transferDirection.addEventListener('change', await setDateTime)
        async function setDateTime(){
            if (dateSet.value && timeSet.value) {                
                let destType = document.getElementById('transfer-' + typeOfTransfer.value + '-destinations').value.split('_')[1]
                let destElem = document.getElementById('dest_type_' + destType)
                let timePlusDestTypeVar = destElem.dataset.time_plus
                let timeWaitDestTypeVar = destElem.dataset.time_wait
                let timePlusMskVar = document.getElementById('transfer-' + typeOfTransfer.value + '-destinations').value.split('_')[2]
                let result
                let groupTownPoints 
                let townPlus
                if (typeOfTransfer.value == 'group') {
                    let firstPointControl = document.querySelector('.adress-control')
                    if (firstPointControl.dataset.adress == 0) {
                        groupTownPoints = document.querySelector('.town_points')
                        townPlus = groupTownPoints.value.split('_')[1]
                    } else {
                        townPlus = 0
                    }
                    
                    
                } else {
                    townPlus = 0
                }
                if (dateSet.value < '2100-01-01'){
                     result = await getDepartTime(typeOfTransfer.value, transferDirection.value, timePlusMskVar, timePlusDestTypeVar, timeWaitDestTypeVar, townPlus, dateSet.value, timeSet.value, timeToMskVal, toMskSchedule, fromMskSchedule, oblTownsListSel.value)
                } else {
                    dateSet.value = null
                    focusEmpty(dateSet)
                    return
                }
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
        oblTownsListSel.addEventListener('change', await priceView)
        async function getTransferLogs(transId) {
            let result
            let searchParams = new URLSearchParams()
            searchParams.set('get_transfer_logs', transId)
            if (transferRedact.value == 'on'){
                searchParams.set('check_redact', transferRedactID.value)
            }
            try {
                result = await getAjaxPost(searchParams)
            } catch(e) {
                console.error(e)
            }
            return result
        }
        async function getDepartTime(type, direction, timePlusMsk, timePlusDestType, timeWaitDestType, timePlusTown, dateVal, timeVal, timeToMsk, toMskSchedule, fromMskSchedule, oblTownPlus) {
            let dateTime = Date.parse(dateVal+'T'+timeVal+':00')
            let timePlus = 0
            if (oblTownPlus != 'off') {
                let timePlusArr = oblTownPlus.split('_')[1].split(':')
                timePlus = timePlusArr[0] * 60 * 60 * 1000 + timePlusArr[1] * 60 * 1000
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
//        let testButton = document.createElement('button')
//        testButton.innerHTML = 'Аякс тест'
//        testButton.addEventListener('click', await async function(){           
//            let result
//            let searchParams = new URLSearchParams()
//            searchParams.set('ajax_test_post', 1)
//            try {
//                result = await getAjaxPost(searchParams)
//            } catch(e) {
//                console.error(e)
//            }
//            console.log('AJJJJJAAAAAAXXXX', result)
//            
//        })
//        parentField.appendChild(testButton)
        
        
        async function sentOrderAjax() {
            let result
            let searchParams = new URLSearchParams()
            searchParams.set('add_transfer_order', typeOfTransfer.value)
            searchParams.set('add_transfer_order_direction', transferDirection.value)
            let passengersSent = [adltNumValInput.value, chldNumValInput.value, chldSeatNumInput.value]
            let prices2 = await getPriceOfTransfer()
            let allPrice2
            if (typeOfTransfer.value == 'ind') {
                allPrice2 = prices2[1][0] + prices2[1][1] + prices2[1][2]
            } else {
                let adltCost = prices2[1][0] / adltNumValInput.value
                let chldCost = prices2[1][1] / chldNumValInput.value
                allPrice2 = [adltCost, chldCost, prices2[1][2]]
            }
            
                
            searchParams.set('add_transfer_order_pay', allPrice2)
            searchParams.set('add_transfer_order_passengers', passengersSent)
            searchParams.set('add_transfer_order_u_comment', uComment.value)
            if (transferRedact.value == 'on') {
                let oldTransferIdInput2 = document.getElementById('transfer_redact_id')
                let oldTransferId2 = oldTransferIdInput2.value
                searchParams.set('add_transfer_order_redact_mode', oldTransferId2)
                searchParams.set('add_transfer_order_redact_mode_create_time', redOldVals.create_time)
            } else {
                searchParams.set('add_transfer_order_redact_mode', 'off')
            }
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
                    if (transferIndDestinations.value.split('_')[1] == 'adr') {
                        let mskAdressInput = document.getElementById('msk_adr_input')
                        if (mskAdressInput.value.length > 3){
                            searchParams.set('add_transfer_order_msk_adress', mskAdressInput.value)
                        } else {
                            focusEmpty(mskAdressInput)
                            return
                        }
                    }
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
                    } else {
                        focusEmpty(adressInputs[i])
                        return
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
                    } else if(ids[i] == 'phone') {
                        if (passInput.value.length < 16) {
                            focusEmpty(passInput)
                            return
                        } else {
                            passDataSent.push(passInput.value)
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
                let bagInput = document.getElementById('bag_input')
                searchParams.set('add_transfer_order_add_bag', bagInput.value)
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
//                let ps = document.getElementsByClassName('adress-control')
//                let adressGroupInputs = document.getElementsByClassName('adress_input_group')
//                let psLen = ps.length
//                let adressArr = []
//                for (let i = 0; i < psLen; i++) {
//                    console.log('DATASET', ps[i].dataset.adress)
//                    if (ps[i].dataset.adress == 1) {
//                        if (!inArray(adressGroupInputs[i].value, adressArr)) {
//                            adressArr.push(adressGroupInputs[i].value)
//                        }
//                    }
//                }
//                let adressNum = adressArr.length
//            
//                if (groupAdressFlag == 0) {
//                    let adressInputs = document.getElementsByClassName('adress_input_group')
//                    let adressCount = adressInputs.length
//
//                    let adressStrRes = ''
//                    for (let i = 0; i < adressCount; i++) {
//                        if (adressInputs[i].value.length > 3) {
//                            if (i == 0) {
//                                adressStrRes += adressInputs[i].value
//                            } else {
//                                adressStrRes += ';;;' + adressInputs[i].value
//                            }
//                        }
//                    }
//                    if (adressStrRes.length > 3) {
//                        searchParams.set('add_transfer_order_group_type_adress', adressStrRes)
//                        searchParams.set('add_transfer_order_group_type_town_point', 'noval')
//                    } else {
//                        focusEmpty(adressInputs[0])
//                        return
//                    }
//                } else {
//                    searchParams.set('add_transfer_order_group_type_adress', 'noval')
//                    searchParams.set('add_transfer_order_group_type_town_point', groupTownPoints[groupTownPoints.selectedIndex].innerHTML)
//                    
//                }
                
                let ps = document.getElementsByClassName('adress-control')
                let adressGroupInputs = document.getElementsByClassName('adress_input_group')
                let groupTownPointsAll = document.getElementsByClassName('town_points')
                let passDataArrSent = []
                let adressArr = []
                let passDivs = document.getElementsByClassName('passenger_item')
                let passDivsNum = passDivs.length
                let ids = ['fio', 'birth_date', 'phone', 'passport_num', 'adress']
                let idsLen = ids.length
                let chld = false
                let firstPointControl = document.getElementsByClassName('adress-control')
                let groupTownPoints = document.getElementsByClassName('town_points')
                let destType = document.getElementById('transfer-' + typeOfTransfer.value + '-destinations').value.split('_')[1]
                let destElem = document.getElementById('dest_type_' + destType)
                let timePlusDestTypeVar = destElem.dataset.time_plus
                let timeWaitDestTypeVar = destElem.dataset.time_wait
                let timePlusMskVar = document.getElementById('transfer-' + typeOfTransfer.value + '-destinations').value.split('_')[2]
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
                        } else if(ids[i] == 'phone') {
                            if (passInput.value.length < 16) {
                                focusEmpty(passInput)
                                return
                            } else {
                                passDataSent[ids[i]] = passInput.value
                            }
                        } else if (ids[i] == 'adress') {
                            if (ps[j].dataset.adress == 1) {
                                passDataSent[ids[i]] = adressGroupInputs[j].value
                            } else {
                                passDataSent[ids[i]] = groupTownPointsAll[j][groupTownPointsAll[j].selectedIndex].innerHTML
                            }
                            
                            
                            if (firstPointControl[j].dataset.adress == 0) {
                                townPlus = groupTownPoints[j].value.split('_')[1]
                            } else {
                                townPlus = 0
                            }
                            let depArriveDates = await getDepartTime(typeOfTransfer.value, transferDirection.value, timePlusMskVar, timePlusDestTypeVar, timeWaitDestTypeVar, townPlus, dateSet.value, timeSet.value, timeToMskVal, toMskSchedule, fromMskSchedule, oblTownsListSel.value)
                            
                            if (depArriveDates != undefined) {
                                if (depArriveDates[0] != 'fail') {
                                    let departForSent = addZero(depArriveDates[0].getFullYear()) + '-' + addZero(depArriveDates[0].getMonth() + 1)  + '-' + addZero(depArriveDates[0].getDate()) + ' ' + addZero(depArriveDates[0].getHours()) + ':' + addZero(depArriveDates[0].getMinutes())
                                    let arriveForSent = addZero(depArriveDates[1].getFullYear()) + '-' + addZero(depArriveDates[1].getMonth() + 1)  + '-' + addZero(depArriveDates[1].getDate()) + ' ' + addZero(depArriveDates[1].getHours()) + ':' + addZero(depArriveDates[1].getMinutes())
                                    let datesStrRes = dateSet.value + ' ' + timeSet.value + '_' + departForSent + '_' + arriveForSent
//                                    searchParams.set('add_transfer_order_group_type_dep_arrive_dates', datesStrRes)
                                    passDataSent['dep_arrive'] = datesStrRes
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
                if (transferRedact.value == 'on'){
                    searchParams.set('check_redact', transferRedactID.value)
                }
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
                let adressNum = adressInputNum
//                for (let i = 0; i < adressInputNum; i++) {
//                    if (adressInputs[i].value.length > 5) {
//                        adressNum++
//                    }
//                }
                let allPass = parseInt(adltNumValInput.value) + parseInt(chldNumValInput.value) 
                let searchParams = new URLSearchParams()
                searchParams.set('get_ind_price', allPass)
                searchParams.set('get_ind_price_tarif', indTypeSelect.value)
                searchParams.set('get_ind_price_dest', transferIndDestinations.value.split('_')[0])
                if (oblTownFlag == 1) {
                    searchParams.set('get_ind_price_obl_town', oblTownsListSel.value)
                } else {
                    searchParams.set('get_ind_price_obl_town', 'off')
                }
                
                searchParams.set('get_ind_price_adress_num', adressNum)
                if (transferRedact.value == 'on'){
                    searchParams.set('check_redact', transferRedactID.value)
                }
                
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
            
            let ps = document.getElementsByClassName('adress-control')
            let adressGroupInputs = document.getElementsByClassName('adress_input_group')
            let psLen = ps.length
            let adressArr = []
            for (let i = 0; i < psLen; i++) {
                console.log('DATASET', ps[i].dataset.adress)
                if (ps[i].dataset.adress == 1) {
                    if (!inArray(adressGroupInputs[i].value, adressArr)) {
                        adressArr.push(adressGroupInputs[i].value)
                    }
                }
            }
            let adressNum = adressArr.length
            console.log('ADRARR', adressNum)
//            for (let i = 0; i < adressInputNum; i++) {
//                if (adressInputs[i].value.length > 5) {
//                    adressNum++
//                }
//            }
//            if (groupAdressFlag == 1) {
//                adressNum = 0
//            }
            let searchParams = new URLSearchParams()
            searchParams.set('get_group_price', transferGroupDestinations.value.split('_')[0])
            searchParams.set('get_group_price_adlt', parseInt(adltNumValInput.value))
            searchParams.set('get_group_price_chld', parseInt(chldNumValInput.value))
            searchParams.set('get_group_price_adress_num', adressNum)
            console.log('get_group_price_adress_num', adressNum)
            let bagInput = document.getElementById('bag_input')
            searchParams.set('get_group_price_bag', bagInput.value)
            if (transferRedact.value == 'on'){
                searchParams.set('check_redact', transferRedactID.value)
            }
            
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
                if (prices[1][3] > 0) {
                    let groupBag = document.createElement('p')
                    groupBag.innerHTML = prices[0][3] + ' - ' + prices[1][3] + ' руб.'
                    orderDetail.appendChild(groupBag)
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
        chldNumValInput.addEventListener('input', await async function(){
            await setPriceView()
            await groupInputsEvents()
        })
        adltNumValInput.addEventListener('input', await async function(){
            await setPriceView()
            await groupInputsEvents()
        })
        typeOfTransfer.addEventListener('change', await priceView)
        transferDirection.addEventListener('change', await priceView)
        indTypeSelect.addEventListener('change', await priceView)
            
        
        async function groupInputsEvents() {
            let groupAdressInputs = document.getElementsByClassName('adress_input_group')
            let groupLen = groupAdressInputs.length
            for (let i = 0; i < groupLen; i++) {
                groupAdressInputs[i].addEventListener('blur', await priceView)
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
        
//        groupAdressBut.addEventListener('click', await async function(){            
//            if (groupAdressVal.classList.contains('hide')) {
//                groupAdressFlag = 0
//                console.log(groupAdressFlag)
//                groupAdressVal.classList.remove('hide')
//                groupDepartPoints.classList.add('hide')
//                this.innerHTML = 'Забрать с точки посадки'
//            } else {
//                groupAdressFlag = 1
//                console.log(groupAdressFlag)
//                groupAdressVal.classList.add('hide')
//                groupDepartPoints.classList.remove('hide')
//                this.innerHTML = 'Забрать с адреса'            
//            }
//            await priceView()
//        })
    }
    getAsync()
    
    
    
    