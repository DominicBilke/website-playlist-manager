<?php

	/*
		GRAPH FÜR editaccount.php englisch
	*/
session_start();

// Vergleich der Zeiten, d.h. extrahieren der zeiten aus Datenbank, zusammenrechnen
function matching_time($stamp_from, $stamp_to, $time_from, $time_to, $days) {
 $stamp_from_w = date('W', $stamp_from);
 $stamp_from_d = date('w', $stamp_from);
 $stamp_from_h = date('H', $stamp_from);
 $stamp_from_m = date('i', $stamp_from);
 $stamp_to_d = date('w', $stamp_to);
 $stamp_to_w = date('W', $stamp_to);
 $stamp_to_h = date('H', $stamp_to);
 $stamp_to_m = date('i', $stamp_to);
 $arr_from = explode(':', $time_from);
 $from_h = (isset($arr_from[0]) && $arr_from[0] ? $arr_from[0] : 0);
 $from_m = (isset($arr_from[1]) && $arr_from[1] ? $arr_from[1] : 0);
 $arr_to = explode(':', $time_to);
 $to_h = (isset($arr_to[0]) && $arr_to[0] ? $arr_to[0] : 0);
 $to_m = (isset($arr_to[1]) && $arr_to[1] ? $arr_to[1] : 0);


$i = 0;
$result = [];
for($stamp_week=$stamp_from_w; $stamp_week<=$stamp_to_w; $stamp_week++) 
for($stamp_day=($stamp_week == $stamp_from_w ? $stamp_from_d : 0); $stamp_day<=($stamp_week == $stamp_to_w ? $stamp_to_d : 6); $stamp_day++) 
 {

$stamp_from_current = strtotime("+".$i." days", $stamp_from);

$stamp_day = strval($stamp_day);
// innerhalb der Spielzeit
$first=FALSE;
 if(strpos($days, $stamp_day) !== FALSE && $stamp_from_d == $stamp_to_d && ($stamp_from_h > $from_h || ($stamp_from_h >= $from_h && $stamp_from_m >= $from_m)) && ($stamp_to_h < $to_h || ($stamp_to_h <= $to_h && $stamp_to_m <= $to_m))) {
	array_push($result,[date('d.m.Y', $stamp_to), ($stamp_to_h-$stamp_from_h+(($stamp_to_m-$stamp_from_m) / 60))]);
	$first=TRUE; 
}

//Anfang in Spielzeit
 else if(!$first && strpos($days, $stamp_day) !== FALSE && $stamp_from_d == $stamp_day && ($stamp_from_h > $from_h || ($stamp_from_h >= $from_h && $stamp_from_m >= $from_m)) && ( $stamp_from_h < $to_h || ($stamp_from_h <= $to_h && $stamp_from_m <= $to_m))) {
	array_push($result,[date('d.m.Y', $stamp_from), ($to_h-$stamp_from_h+(($to_m-$stamp_from_m) / 60))]);
}

// Ende in Spielzeit
  else if(!$first && strpos($days, $stamp_day) !== FALSE && $stamp_day == $stamp_to_d && ($stamp_to_h > $from_h || ($stamp_to_h >= $from_h && $stamp_to_m >= $from_m)) && ($stamp_to_h < $to_h || ($stamp_to_h <= $to_h && $stamp_to_m <= $to_m))) {
	array_push($result,[date('d.m.Y', $stamp_to), ($stamp_to_h-$from_h+(($stamp_to_m-$from_m) / 60))]);
}

// Anfang vor der Spielzeit, Ende nach der Spielzeit
  else if(strpos($days, $stamp_day) !== FALSE && $stamp_from_d == $stamp_to_d && ($stamp_from_h < $from_h || ($stamp_from_h < $from_h && $stamp_from_m < $from_m)) && ($stamp_to_h > $to_h || ($stamp_to_h > $to_h && $stamp_to_m > $to_m))) {
	array_push($result,[date('d.m.Y', $stamp_from_current), ($to_h-$from_h+(($to_m-$from_m) / 60))]); 
}

// Vor dem Tag, bis nach dem Tag
  else if(strpos($days, $stamp_day) !== FALSE && $stamp_from < $stamp_from_current && $stamp_from_current < $stamp_to) {
	array_push($result,[date('d.m.Y', $stamp_from_current), ($to_h-$from_h+(($to_m-$from_m) / 60))]); 
}

$i++;
}
 return $result;
}


function array_implode_with_keys($array){
    $return = '';
    if (count($array) > 0){
    foreach ($array as $key=>$value){
    $return .= $key . '||||' . $value . '----';
    }
    $return = substr($return,0,strlen($return) - 4);
    }
    return $return;
}

function array_explode_with_keys($string){
    $return = array();
    $pieces = explode('----',$string);
    foreach($pieces as $piece){
        $keyval = explode('||||',$piece);
        if (count($keyval) > 1){
        $return[$keyval[0]] = $keyval[1];
        } else {
        $return[$keyval[0]] = '';
        }
    }
    return $return;
}

$servername = "localhost";
$username = "d03c87b1";
$password = "WaBtpcMKcgf49wqp";


// DATENBANK
$pdo = new PDO('mysql:host=localhost;dbname=d03c87b1', 'd03c87b1', 'WaBtpcMKcgf49wqp');

$sql = "SELECT playing_time FROM users WHERE id=".$_GET['id'];
foreach ($pdo->query($sql) as $row) {
	$playing_time = array_explode_with_keys($row['playing_time']);
}

$playing_array1 = [];
$result = array();
foreach($playing_time as $d=>$t) {
	if(strpos($d, 'day')===FALSE) {
		$stampFrom = $d;
		$stampTo = $t;
		if(!isset($playing_time['daytime_from_'.$stampFrom]) || !isset($playing_time['daytime_from_'.$stampFrom]) || !isset($playing_time['daytime_to_'.$stampFrom]) || !isset($playing_time['days_'.$stampFrom])) continue;
		$daytime_from = $playing_time['daytime_from_'.$stampFrom];
		$daytime_to = $playing_time['daytime_to_'.$stampFrom];
		$days = $playing_time['days_'.$stampFrom];
		$result = matching_time($stampFrom, $stampTo, $daytime_from, $daytime_to, $days);
		foreach($result as $res) {
			$day = $res[0];
			$tim = $res[1];
			$playing_array1[$day][] = $tim;
		}

}
}
// playing_array auflösen
$playing_array = [];
foreach($playing_array1 as $d=>$t) {
	$playing_array[$d] = array_sum(array_unique($t));
	if((int) $playing_array[$d] >= 24) $playing_array[$d] = 24;
}

// kleiner als 5 min und größer als 90 Tage, dann löschen
foreach($playing_array as $d=>$t) {
	if($playing_array[$d] < 0.084) unset($playing_array[$d]);

	if(strtotime($d) < strtotime('-90 days')) unset($playing_array[$d]); 
	if(strtotime($d) > strtotime('now')) unset($playing_array[$d]); 


	if(!(isset($_GET['Reset']) || !isset($_GET['Filter']) || (strtotime($_GET['date_from']) <= strtotime($d) && strtotime($d) <= strtotime($_GET['date_to'])) ))
		unset($playing_array[$d]); 
}

// GRAPH aus jpgraph modul

require_once ('../jpgraph/jpgraph.php');
require_once ('../jpgraph/jpgraph_bar.php');
 
$datay=array(12,8,19,3,10,5);
 
// Create the graph. These two calls are always required
$graph = new Graph(800,count($playing_array)*30+100);
$graph->SetScale('textlin');
 
$top = 60;
$bottom = 30;
$left = 80;
$right = 30;
$graph->Set90AndMargin($left,$right,$top,$bottom);

// Add a drop shadow
$graph->SetShadow();
 
// Adjust the margin a bit to make more room for titles
//$graph->SetMargin(40,30,20,40);
 
// Create a bar pot
$bplot = new BarPlot(array_values($playing_array));
 
// Adjust fill color
$bplot->SetFillColor('orange');
$graph->Add($bplot);
 
// Setup X-axis
$graph->xaxis->SetTickLabels(array_keys($playing_array));

// Setup the titles
$graph->title->Set('Statistic');
$graph->xaxis->title->Set('Date');
$graph->yaxis->title->Set('Hours');
 
$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);
 
// Display the graph
$graph->Stroke();

?>