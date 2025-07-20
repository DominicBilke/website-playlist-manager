<?php

/*
 Benutzer einloggen, aus Datenbank abrufen.
 Liste darstellen.
*/

function sortBy($field, &$array, $direction = 'asc')
{
    usort($array, create_function('$a, $b', '
        $a = $a["' . $field . '"];
        $b = $b["' . $field . '"];

        if ($a == $b)
        {
            return 0;
        }

        return (strtoupper($a) ' . ($direction == 'desc' ? '>' : '<') .' strtoupper($b)) ? -1 : 1;
    '));

    return true;
}

$servername = "localhost";
$username = "d03c87b1";
$password = "WaBtpcMKcgf49wqp";

$pdo = new PDO('mysql:host=localhost;dbname=d03c87b1', 'd03c87b1', 'WaBtpcMKcgf49wqp');

$users = [];

$sql = "SELECT * FROM users ORDER BY login ASC";
foreach ($pdo->query($sql) as $row) {

 $users[$row['id']]['login'] = $row['login'];
 $users[$row['id']]['team'] = $row['team'];
 $users[$row['id']]['password'] = $row['password'];
 $users[$row['id']]['id'] = $row['id'];
 $users[$row['id']]['days'] = $row['days'];
 $users[$row['id']]['daytime_from'] = $row['daytime_from'];
 $users[$row['id']]['daytime_to'] = $row['daytime_to'];
 $users[$row['id']]['office'] = $row['office'];
 $users[$row['id']]['login_counter'] = $row['login_counter'];
 $users[$row['id']]['playing_time'] = $row['playing_time'];
 $users[$row['id']]['days_random'] = $row['days_random'];
 $users[$row['id']]['daytime_random'] = $row['daytime_random'];
 $users[$row['id']]['playlist_id'] = $row['playlist_id'];
 $users[$row['id']]['currently_playing'] = $row['currently_playing'];
 $users[$row['id']]['apple_playlist_id'] = $row['apple_playlist_id'];
 $users[$row['id']]['youtube_playlist_id'] = $row['youtube_playlist_id'];
 $users[$row['id']]['amazon_playlist_id'] = $row['amazon_playlist_id'];
 $users[$row['id']]['db_token'] = $row['db_token'];


$playing_time = array_explode_with_keys($row['playing_time']);


$users[$row['id']]['currently_playing'] = '0';
foreach($playing_time as $d=>$t)
	if(strpos($d, 'day')===FALSE && $t > strtotime('-5 minutes') && strpos($playing_time['days_'.$d],  date('w')) !== FALSE)
		$users[$row['id']]['currently_playing'] = '1';

}

echo '
<table>
<tr>
<th><a href="'.$_SERVER['PHP_SELF'].'?order=team'.(isset($_GET['dir']) && $_GET['dir']=='asc' ? '&dir=desc': '&dir=asc').'">Team-Nr.</a></th>
<th><a href="'.$_SERVER['PHP_SELF'].'?order=login'.(isset($_GET['dir']) && $_GET['dir']=='asc' ? '&dir=desc': '&dir=asc').'">Benutzer</a></th>
<th>Tag</th>
<th>Spielzeit</th>
<th>Login-Zähler</th>
<th><a href="'.$_SERVER['PHP_SELF'].'?order=currently_playing'.(isset($_GET['dir']) && $_GET['dir']=='asc' ? '&dir=desc': '&dir=asc').'">Aktuell abspielend?</a></th>
</tr>';

$days = ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag'];

if(isset($_GET['order']) && $_GET['order'] && isset($_GET['dir']) && $_GET['dir']) {
	$key_values = array_column($users, $_GET['order']); 
	array_multisort($key_values, ($_GET['dir'] == 'asc' ? SORT_ASC : SORT_DESC),SORT_FLAG_CASE | SORT_STRING, $users);
}
foreach($users as $id=>$user)
{
$db_days = explode(', ', $user['days']);
$user_days = array_intersect_key($days,array_flip($db_days));
$user_days = implode(', ', $user_days);
echo '
<tr>
<td>'.$user['team'].'</td>
<td><a href="de_editaccount.php?id='.$user['id'].'&login='.$user['login'].'&password='.$user['password'].'&days='.$user['days'].'&daytime_from='.$user['daytime_from'].'&daytime_to='.$user['daytime_to'].'&days_random='.$user['days_random'].'&daytime_random='.$user['daytime_random'].'&office='.$user['office'].'&playlist_id='.$user['playlist_id'].'&apple_playlist_id='.$user['apple_playlist_id'].'&youtube_playlist_id='.$user['youtube_playlist_id'].'&amazon_playlist_id='.$user['amazon_playlist_id'].'&db_token='.$user['db_token'].'&team='.$user['team'].'">'.$user['login'].'</a></td>
<td>'.$user_days.'</td>
<td>'.$user['daytime_from'].' - '.$user['daytime_to'].'</td>
<td>'.$user['login_counter'].'</td>
<td>'.($user['currently_playing']=='1' ? 'Ja': 'Nein').'</td>
';
}
echo '
</table>';

?>