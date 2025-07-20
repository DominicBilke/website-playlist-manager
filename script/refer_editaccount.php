<?php

/*
Weiterleitung des Links und der Suchmaske an den editaccount.php
*/
session_start();
//$_SESSION['editaccount']['youtube_code'] = $_GET['authCode'];
//header('Location: '.$_GET['link'].'?'.http_build_query($_SESSION['editaccount']));

$_SESSION['editaccount']['youtube_search'] = $_GET['search'];
header('Location: '.$_GET['link'].'?'.http_build_query($_SESSION['editaccount']));

?>