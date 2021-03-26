<?php
require_once '/home/a0365707/domains/teleport-v-airport.ru/public_html/agent/clases/mysql.php';
$db_passengers_data = new Mysql('passengers_data');
$db_new_transfers = new Mysql('new_transfers');


function passengerTest($u_id, $fio, $birth_date, $phone) {
    global $db_passengers_data;
    $sql_u_pass = $db_passengers_data->getSome($u_id, 'agent_id');    
    while ($res_u_pass = mysqli_fetch_array($sql_u_pass)) {
        if ($res_u_pass['fio'] == $fio && $res_u_pass['phone'] == $phone) {
            return $res_u_pass['ID'];
        }
        if ($res_u_pass['birth_date'] == $birth_date && $res_u_pass['phone'] == $phone) {
            return $res_u_pass['ID'];
        }
    }
    return false;
}
function getPayDocTransfers($doc_type, $doc_id, $pay_control, $passengers, $u_id) {
    $sql_trans = $doc_type->getSome($doc_id, 'ID');    
    $res_trans = mysqli_fetch_assoc($sql_trans);
    if ($u_id == $res_trans['user_id_debt']) {
        $trans = json_decode($res_trans['transfers']);
        $result = [];
        foreach ($trans as $id => $cost) {
            $sql_trans_val = $pay_control->getSome($id, 'trans_id');
            $res_trans_val = mysqli_fetch_assoc($sql_trans_val);
            $sql_pass_fio = $passengers->getOne('fio', $res_trans_val['pass_id'], 'ID');
            $res_pass_fio = mysqli_fetch_assoc($sql_pass_fio);
            $pass_fio = $res_pass_fio['fio'];
            $res = [$id, date('d.m.Y / H:i:s', strtotime($res_trans_val['pay_deadline'])), $pass_fio, $res_trans_val['cost'], $res_trans_val['comision'], $cost, $res_trans_val['payed']];
            $result[] = $res;
        }
        return $result;
    } 
    return false;
    
}
function num2str($num) {
	$nul='ноль';
	$ten=array(
		array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),
		array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять'),
	);
	$a20=array('десять','одиннадцать','двенадцать','тринадцать','четырнадцать' ,'пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать');
	$tens=array(2=>'двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят' ,'восемьдесят','девяносто');
	$hundred=array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот','восемьсот','девятьсот');
	$unit=array( // Units
		array('копейка' ,'копейки' ,'копеек',	 1),
		array('рубль'   ,'рубля'   ,'рублей'    ,0),
		array('тысяча'  ,'тысячи'  ,'тысяч'     ,1),
		array('миллион' ,'миллиона','миллионов' ,0),
		array('миллиард','милиарда','миллиардов',0),
	);
	//
	list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));
	$out = array();
	if (intval($rub)>0) {
		foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
			if (!intval($v)) continue;
			$uk = sizeof($unit)-$uk-1; // unit key
			$gender = $unit[$uk][3];
			list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
			// mega-logic
			$out[] = $hundred[$i1]; # 1xx-9xx
			if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
			else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
			// units without rub & kop
			if ($uk>1) $out[]= morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
		} //foreach
	}
	else $out[] = $nul;
	$out[] = morph(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); // rub
	$out[] = $kop.' '.morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
	return trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
}

/**
 * Склоняем словоформу
 * @ author runcore
 */
function morph($n, $f1, $f2, $f5) {
	$n = abs(intval($n)) % 100;
	if ($n>10 && $n<20) return $f5;
	$n = $n % 10;
	if ($n>1 && $n<5) return $f2;
	if ($n==1) return $f1;
	return $f5;
}