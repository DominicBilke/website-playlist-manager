<?php

/*
 Logout aus Benutzerkonto
 Session Destroy
*/
include 'inc_start.php';

$url = $_SESSION['url'];

session_destroy();


$meldung='meldung=Logout erfolgreich!';

header("Location: ../index.php?".$meldung);
?>