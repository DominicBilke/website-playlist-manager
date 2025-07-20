<?php

/*
Weiterleitung des Links und der Suchmaske an den editaccount.php
*/
session_start();
$_SESSION['editaccount']['youtube_code'] = $_GET['authCode'];
$link = ($_GET['link'] == 'https://playlist-manager.de/editaccount.php' || $_GET['link'] == 'https://playlist-manager.de/de_editaccount.php' ? $_GET['link'].'?'.http_build_query($_SESSION['editaccount']) : $_GET['link']);
header('Location: '.$link);

//$_SESSION['editaccount']['youtube_search'] = $_GET['search'];
//header('Location: '.$_GET['link'].'?'.http_build_query($_SESSION['editaccount']));

?>