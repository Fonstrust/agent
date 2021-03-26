<?
session_start();
include_once('db.php');
require_once '../clases/mysql.php';
require_once '../clases/users.php';
require_once '../functions/php_func.php';

$db_pko = new Mysql('pko');
$db_count = new Mysql('count');
$db_users = new Mysql('users');
$db_pay_control = new Mysql('pay_control');

if (isset($_GET['doc_id']) && isset($_GET['doc_type'])) {
    $doc_id = $_GET['doc_id'];
    $doc_type = $_GET['doc_type'];
    if ($doc_type == 'pko') {
        $doc_db = $db_pko;
    } elseif ($doc_type == 'count') {
        $doc_db = $db_count;
    }
    
    $sql_doc = $doc_db->getSome($doc_id, 'ID');
    $res_doc = mysqli_fetch_assoc($sql_doc);
    $u_id = $res_doc["user_id_debt"];
    $sql_user = $db_users->getSome($u_id, 'ID');
    $res_user = mysqli_fetch_assoc($sql_user);
    
    $transfers = json_decode($res_doc['transfers'], true);
    $user = new User($u_id);
    $months = array( 1 => 'января' , 'февраля' , 'марта' , 'апреля' , 'мая' , 'июня' , 'июля' , 'августа' , 'сентября' , 'октября' , 'ноября' , 'декабря');
    $day = date("n", strtotime($res_doc["create_time"]));
    
        
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Печать документа</title>
</head>
<body onload="window.print()">
	
	<?if($doc_type == "pko"):?>

	<style>
	.b{
		border: 1px solid black;
	}
	.bt{
		border-top: 1px solid black;
	}
	.bb{
		border-bottom: 1px solid black;
	}
	.bl{
		border-left: 1px solid black;
	}
	.br{
		border-right: 1px solid black;

	}
	.test{
		border-spacing: 0px 0px;
	}
	.brd{
		border-right: 1px dashed black;
	}
</style>

<table class="test" cellpadding="1" cellspasing="0" align="center">
	<tr>                
		<td class="bt bl"></td>
		<td colspan="8" width="440px" class="bt"></td>
		<td class="brd bt" width="10px"></td>
		<td width="15px" class="bt"></td>
		<td class="bt"></td>
		<td class="bt"></td>
		<td class="bt"></td>
		<td class="bt"></td>
		<td class="bt"></td>
		<td class="br bt"></td>
	</tr>        
	<tr>
		<td class="bl"></td>
		<td colspan="8" style="font-size: 10px"> 
			<table cellspacing="0" cellpadding="0" width="100%" style="font-size: 10px; border-collapse: collapse">
				<tr>
					<td>ФИО:</td>
					<td align="right">К оплате:</td>
                    
				</tr>
			</table>
		</td>
		<td class="brd" width="10px"></td>
		<td width="15px"></td>
		<td colspan="5" align="center" style="font-size: 10px"><b>ООО "ДЖИ-ЛАЙН</b></td>  
		<td class="br"></td>
	</tr>        
	<tr>
		<td class="bl"></td>
		<td rowspan="31" colspan="8" width="440px" valign="top">
            <?php
                foreach ($transfers as $key => $value) {
                    $sql_pay = $db_pay_control->getSome($key, 'trans_id');
                    $res_pay = mysqli_fetch_assoc($sql_pay);
                    $pass_id = $res_pay['pass_id'];
                    $pass = $user->getUserPassenger($pass_id);
                    echo '
                        <table cellspacing="0" cellpadding="0" width="100%" style="font-size: 10px; border-collapse: collapse">
                        <tr>
                        <td>'.$pass['fio'].'</td>
                        <td align="right">'.$value.' руб.</td>
                        </tr>	
                        </table>
                    ';
                }
            ?>
		</td>
        
		<td class="brd" width="10px"></td>
		<td width="15px"></td>
		<td colspan="5" align="center" style="font-size: 8px" valign="top" class="bt">организация</td>
		<td class="br"></td>
	</tr>        
	<tr>
		<td class="bl"></td>                
		<td class="brd" width="10px"></td>
		<td width="15px"></td>
		<td colspan="5" align="center" style="font-size: 10px"><br></td>
		<td class="br"></td>
	</tr>        
	<tr>
		<td class="bl"></td>                
		<td class="brd" width="10px"></td>
		<td width="15px"></td>
		<td colspan="5" align="center" style="font-size: 10px"><b>КВИТАНЦИЯ</b></td>
		<td class="br"></td>
	</tr> 
	<tr>
		<td class="bl"></td>                
		<td class="brd" width="10px"></td>
		<td width="15px"></td>
		<td colspan="4" align="center" style="font-size: 10px">к приходному кассовому ордеру №</td>
		<td align="center" style="font-size: 10px" class="b"><b><?=$doc_id?></b></td>
		<td class="br"></td>
	</tr>        
	<tr>
		<td class="bl"></td>                
		<td class="brd" width="10px"></td>
		<td width="15px"></td>
		<td align="center" style="font-size: 10px">от</td>
		<td colspan="3" align="center" style="font-size: 10px" class="bb"><b><?=date("d.m.Y",strtotime($res_doc["create_time"]))?></b></td>
		<td></td>
		<td class="br"></td>
	</tr>        
	<tr>
		<td class="bl"></td>                
		<td class="brd" width="10px"></td>
		<td width="15px"></td>
		<td><br></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td class="br"></td>
	</tr>  
	<tr>
		<td class="bl"></td>                
		<td class="brd" width="10px"></td>
		<td width="15px"></td>
		<td style="font-size: 10px" valign="bottom">Принято от</td>
		<td  colspan="4" style="font-size: 10px; max-width: 135px;" valign="bottom" class="bb"><b><?=$res_user["company"]?></b></td>
		<td class="br"></td>
	</tr>  
	<tr>
		<td class="bl"></td>                
		<td class="brd" width="10px"></td>
		<td width="15px"></td>
		<td style="font-size: 10px" valign="bottom">Основание:</td>
		<td colspan="4" style="font-size: 10px" valign="bottom" class="bb"><b>Оплата трансферов</b></td>
		<td class="br"></td>
	</tr>  
	<tr>
		<td class="bl"></td>                
		<td class="brd" width="10px"></td>
		<td width="15px"></td>
		<td><br></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td class="br"></td>
	</tr>   
	<tr>
		<td class="bl"></td>                
		<td class="brd" width="10px"></td>
		<td width="15px"></td>
		<td style="font-size: 10px" valign="bottom">Сумма:</td>
		<td colspan="2" style="font-size: 10px" valign="bottom" class="bb" align="center"><b><?=$res_doc['doc_sum']?></b></td>
		<td colspan="2" style="font-size: 10px" valign="bottom">руб. <b>00</b> коп.</td>
		<td class="br"></td>
	</tr>       
	<tr>
		<td class="bl"></td>                
		<td class="brd" width="10px"></td>
		<td width="15px"></td>
		<td><br></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td class="br"></td>
	</tr>       
	<tr>
		<td class="bl"></td>                
		<td class="brd" width="10px"></td>
		<td width="15px"></td>
		<td colspan="5" style="font-size: 10px" valign="bottom" class="bb sum"><b><?=num2str($res_doc['doc_sum'])?></b></td>
		<td class="br"></td>

	</tr>       
	<tr>
		<td class="bl"></td>                
		<td class="brd" width="10px"></td>
		<td width="15px"></td>
		<td colspan="5" style="font-size: 8px" valign="top" align="center">прописью</td>
		<td class="br"></td>
	</tr>
	<tr>
		<td class="bl"></td>                
		<td class="brd" width="10px"></td>
		<td width="15px"></td>
		<td colspan="5" style="font-size: 10px" valign="bottom">НДС не облагается</td>
		<td class="br"></td>
	</tr>
	<tr>
		<td class="bl"></td>                
		<td class="brd" width="10px"></td>
		<td width="15px"></td>
		<td><br></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td class="br"></td>
	</tr>
	<tr>  
		<td class="bl"></td>                
		<td class="brd" width="10px"></td>
		<td width="15px"></td>
		<td><br></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td class="br"></td>
	</tr>
	<tr>
		<td class="bl"></td>                
		<td class="brd" width="10px"></td>
		<td width="15px"></td>
		<td><br></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td class="br"></td>
	</tr>
	<tr>
		<td class="bl"></td>               
		<td class="brd" width="10px"></td>
		<td width="15px"></td>
		<td colspan="3" style="font-size: 10px" align="center">М.П. (штампа)</td>
		<td></td>
		<td></td>
		<td class="br"></td>
	</tr>
	<tr>
		<td class="bl"></td>                
		<td class="brd" width="10px"></td>
		<td width="15px"></td>
		<td><br></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td class="br"></td>
	</tr>
	<tr>
		<td class="bl"></td>                
		<td class="brd" width="10px"></td>
		<td width="15px"></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td class="br"></td>
	</tr>
	<tr>
		<td class="bl"></td>                
		<td class="brd" width="10px"></td>
		<td width="15px"></td>
		<td><br></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td class="br"></td>
	</tr>
	<tr>
		<td class="bl"></td>                
		<td class="brd" width="10px"></td>
		<td width="15px"></td>
		<td rowspan="2" style="font-size: 10px" valign="bottom">Главный<br>бухгалтер</td>
		<td class="bb"></td>
		<td colspan="3" style="font-size: 10px" valign="bottom" align="right" class="bb"><b>Богославцев К.Д.</b></td>
		<td class="br"></td>

	</tr>
	<tr>
		<td class="bl"></td>               
		<td class="brd" width="10px"></td>
		<td width="15px"></td>                
		<td style="font-size: 7px" valign="top" align="center">подпись</td>
		<td colspan="3" style="font-size: 7px" valign="top" align="right">расшифровка подписи</td>
		<td class="br"></td>

	</tr>
	<tr>
		<td class="bl"></td>                
		<td class="brd" width="10px"></td>
		<td width="15px"></td>
		<td><br></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td class="br"></td>
	</tr>
	<tr>
		<td class="bl"></td>                
		<td class="brd" width="10px"></td>
		<td width="15px"></td>
		<td style="font-size: 10px" valign="bottom">Кассир</td>
		<td class="bb"></td>                
		<td colspan="3" style="font-size: 10px" valign="bottom" align="right" class="bb"><b>Богославцев К.Д.</b></td>
		<td class="br"></td>
	</tr>
	<tr>
		<td class="bl"></td>                
		<td class="brd" width="10px"></td>
		<td width="15px"></td>
		<td></td>
		<td style="font-size: 7px" valign="top" align="center">подпись</td>
		<td colspan="3" style="font-size: 7px" valign="top" align="right">расшифровка подписи</td>
		<td class="br"></td>
	</tr>
	<tr>
		<td class="bl"></td>                
		<td class="brd" width="10px"></td>
		<td width="15px"></td>
		<td><br></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td class="br"></td>
	</tr>
	<tr>
		<td class="bl"></td>                
		<td class="brd" width="10px"></td>
		<td width="15px"></td>
		<td><br></td>
		<td></td>
		<td></td>                
		<td></td>
		<td></td>
		<td class="br"></td>
	</tr>
	<tr>
		<td class="bl"></td>                
		<td class="brd" width="10px"></td>
		<td width="15px"></td>
		<td></td>
		<td></td>
		<td></td>                
		<td></td>
		<td></td>
		<td class="br"></td>
	</tr>
	<tr>
		<td class="bl"></td>                
		<td class="brd" width="10px"></td>
		<td width="15px"></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td class="br"></td>
	</tr>
	<tr>
		<td class="bl"></td>                
		<td class="brd" width="10px"></td>
		<td width="15px"></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td class="br"></td>
	</tr>
	<tr>                
		<td class="bb bl"></td>                
		<td colspan="8" class="bb"></td>
		<td class="brd bb" width="10px"></td>
		<td width="15px" class="bb"></td>
		<td class="bb"></td>
		<td class="bb"></td>
		<td class="bb"></td>
		<td class="bb"></td>
		<td class="bb"></td>
		<td class="bb br"></td>
	</tr>        
</table>
<?endif;?>




<?if($doc_type == 'count'):?>


<table width="620">
	<tr align="left">
		<th>
			<p>Общество с ограниченной ответственностью "ДЖИ-ЛАЙН"<br>
				Адрес: 241007г. Брянск, ул. Евдокимова 8<br>
				Телефон/факс: +7(4832) 72-08-68
			</p>
		</th>
	</tr>
</table>
<table width="620" border="1" cellspacing="0">  
	<tr align="left">
		<td width="160">ИНН 3257035650</td >
		<td>КПП 325701001</td>
		<td rowspan="2">Счет №</td>
		<td rowspan="2">40702810200000011759</td>
	</tr>
	<tr align="left">
		<td width="160" colspan="2">Получатель<br>ООО "ДЖИ-ЛАЙН"</td>	
	</tr>
	<tr align="left">
		<td width="160" colspan="2" rowspan="2">Банк получателя<br>АО "Райффайзенбанк" г. Москва</td>
			<td>БИК</td >
			<td>044525700</td >	
		</tr>
		<tr align="left">
			<td>Корр.<br>сч. №</td>
			<td>30101810200000000700</td>
		</tr>
	</table>
	<table width="620" border="0" cellspacing="0">
		<tr>
			<th><p align="center" ><font size="5"><br><?='Счет № '.$doc_id.' от '.date("d", strtotime($res_doc["create_time"])).' '.$months[$day].' '.date("Y", strtotime($res_doc["create_time"])).' г.'?></font>
                </p></th>
        </tr>
	</table >
	<table width="620" border="0" cellspacing="0">
		<tr>
			<td><br><?='Плательщик: '.$res_user["company_full"].' ИНН/КПП: '.$res_user["inn"].' / '.$res_user["kpp"].', '.$res_user["real_adr"].', '.$res_user["phone"].'</td>'?>
		</tr>
		<tr>
			<td>Получатель: ООО "ДЖИ-ЛАЙН"</td>
		</tr>
	</table>
	<table width="620" border="1" cellspacing="0">
		<tr align="center">
			<td>№</td><td>Наименование товара</td><td>Коли-<br>чество</td><td>Сумма</td>
		</tr>
		<tr>
			<td align="center">1</td><td>Транспортные услуги</td><td align="center">1</td><td align="center"><?=$res_doc['doc_sum']?></td>
		</tr>
	</table>
	<table width="620" border="0" cellspacing="0">
		<tr>
			<th align="right">Итого</th><td align="center" width="115"><?=$res_doc['doc_sum']?></td>
		</tr>
		<tr>
			<th align="right" border="1">Без налога НДС</th><td align="center">-</td>
		</tr>
		<tr>
			<th align="right">Всего к оплате</th><td align="center"><?=$res_doc['doc_sum']?></td>
		</tr>
	</table>
	<table width="620" border="0" cellspacing="0" style="page-break-after: always;">
		<tr>
			<td><br><br>Всего наименований 1, на сумму <?=$res_doc['doc_sum']?></td>
		</tr>
		<tr>
			<td><b class="sum"><?=num2str($res_doc['doc_sum'])?></b></td>
		</tr>
		<tr>
			<td><br><br> Руководитель предприятия _____________ К.Д. Богославцев</td>
		</tr>
	</table>
	<!--- BREAK PAGE ---->
	<br>
	<table border="0" rules="rows" border-collapse="collapse">
		<tr>
			<td colspan="2">
				<h2>
					<?='Акт № '.$res_doc['ID'].' от '.date("d", strtotime($res_doc['create_time'])).' '.$months[$day].' '.date("Y", strtotime($res_doc["create_time"])).' года'?> 
				</h2>
			</td>   
		</tr>
		<tr>
			<td width="100px">
				Исполнитель:
			</td>
			<td width="700px">ООО “ДЖИ-ЛАЙН”, ИНН 3257035650, КПП 325701001 241007 г. Брянск, ул. Евдокимова 8 Телефон:+7(4832) 72-08- 68
			</td>    
		</tr>
	</table>
	<table border="0">
		<tr>
			<td width="100px">
				Заказчик:
			</td>
			<td width="700px">
				<?='Плательщик: '.$res_user["company_full"].' ИНН/КПП: '.$res_user["inn"].' / '.$res_user["kpp"].', '.$res_user["real_adr"].', '.$res_user["phone"].''?> 
			</td> 
		</tr>
	</table>
	<br>
	<table border="1" cellspacing="0">
		<tr align="center">
			<th width="50">№</th>
			<th width="370">Услуга</th>
			<th width="100">Цена</th>
			<th width="100">Сумма</th>
		</tr>
		<tr align="center">
			<td >1</td>
			<td align="left">&nbsp;&nbsp;&nbsp;&nbsp;Трансферные услуги</td>
			<td><?=$res_doc['doc_sum']?></td>
			<td><?=$res_doc['doc_sum']?></td>
		</tr>

	</table>
	<table border="0" cellspacing="1">
		<tr>
			<th width="520" align="right">Итого</th>
			<td width="120" align="center"><?=$res_doc['doc_sum']?></td>
		</tr>
		<tr>
			<th align="right" border="1">Без налога НДС</th><td align="center">-</td>
		</tr>
		<tr>
			<th align="right">Всего к оплате</th><td align="center"><?=$res_doc['doc_sum']?></td>
		</tr>
	</table>
	<table width="750" border="0" cellspacing="0">
		<tr>
			<td><br><br><b>Всего оказано услуг на сумму:</b> <b class="sum"><?=num2str($res_doc['doc_sum'])?></b></td>
		</tr>
		<tr>
			<td>Вышеперечисленные услуги выполнены полностью и в срок. Заказчик претензий по объему, качеству и срокам оказания услуг не имеет.</td>
		</tr>
	</table>
	<br><br>
	<table>
		<tr>
			<th width="300px" colspan="2">Исполнитель:</th>
			<th width="300px" colspan="2">Заказчик:</th>
		</tr>
	</table>
	<table rules="rows">
		<tr valign="bottom" height="50px" >
			<td width="120px"></td>
			<td width="180px">/Богославцев К.Д.</td>
			<td width="120px"></td>
			<td width="180px">/</td>
		</tr>
		<tr valign="top" style="font-size:.7em">
			<td>подпись</td>
			<td>расшифровка подписи</td>
			<td>подпись</td>
			<td>расшифровка подписи</td>
		</tr>

	</table>
	<br>
	<br>
	<br>
	<br>
	<table border="0" rules="rows" border-collapse="collapse">
		<tr>
			<td colspan="2">
				<h2>
					<?='Акт № '.$res_doc['ID'].' от '.date("d", strtotime($res_doc['create_time'])).' '.$months[$day].' '.date("Y", strtotime($res_doc["create_time"])).' года'?> 
				</h2>
			</td>    
		</tr>
		<tr>
			<td width="100px">
				Исполнитель:
			</td>
			<td width="700px">ООО “ДЖИ-ЛАЙН”, ИНН 3257035650, КПП 325701001 241007 г. Брянск, ул. Евдокимова 8 Телефон:+7(4832) 72-08- 68
			</td>    
		</tr>
	</table>
	<table border="0">
		<tr>
			<td width="100px">
				Заказчик:
			</td>
			<td width="700px">
				<?='Плательщик: '.$res_user["company_full"].' ИНН/КПП: '.$res_user["inn"].' / '.$res_user["kpp"].', '.$res_user["real_adr"].', '.$res_user["phone"].''?> 
			</td>    
		</tr>
	</table>
	<br>
	<table border="1" cellspacing="0">
		<tr align="center">
			<th width="50">№</th>
			<th width="370">Услуга</th>
			<th width="100">Цена</th>
			<th width="100">Сумма</th>
		</tr>
		<tr align="center">
			<td >1</td>
			<td align="left">&nbsp;&nbsp;&nbsp;&nbsp;Трансферные услуги</td>
			<td><?=$res_doc['doc_sum']?></td>
			<td><?=$res_doc['doc_sum']?></td>
		</tr>

	</table>
	<table border="0" cellspacing="1">
		<tr>
			<th width="520" align="right">Итого</th>
			<td width="120" align="center"><?=$res_doc['doc_sum']?></td>
		</tr>
		<tr>
			<th align="right" border="1">Без налога НДС</th><td align="center">-</td>
		</tr>
		<tr>
			<th align="right">Всего к оплате</th><td align="center"><?=$res_doc['doc_sum']?></td>
		</tr>
	</table>
	<table width="750" border="0" cellspacing="0">
		<tr>
			<td><br><br>
				<b>Всего оказано услуг на сумму:</b> 
				<b class="sum"><?=num2str($res_doc['doc_sum'])?></b>
			</td>
		</tr>
		<tr>
			<td>Вышеперечисленные услуги выполнены полностью и в срок. Заказчик претензий по объему, качеству и срокам оказания услуг не имеет.</td>
		</tr>
	</table>
	<br><br>
	<table>
		<tr>
			<th width="300px" colspan="2">Исполнитель:</th>
			<th width="300px" colspan="2">Заказчик:</th>
		</tr>
	</table>
	<table rules="rows">
		<tr valign="bottom" height="50px" >
			<td width="120px"></td>
			<td width="180px">/Богославцев К.Д.</td>
			<td width="120px"></td>
			<td width="180px">/</td>
		</tr>
		<tr valign="top" style="font-size:.7em">
			<td>подпись</td>
			<td>расшифровка подписи</td>
			<td>подпись</td>
			<td>расшифровка подписи</td>
		</tr>

	</table>



	<?endif?>

	<script  type="text/javascript">
		var text = document.querySelectorAll(".sum");
		for(var i = 0; i < text.length; i++){
			var textContent = text[i].textContent;
			var textUppercase = textContent.toUpperCase().slice(0,1);
			document.querySelector(".sum").textContent = textUppercase + textContent.slice(1, textContent.length);
		}
		
	</script>


</body>
</html>