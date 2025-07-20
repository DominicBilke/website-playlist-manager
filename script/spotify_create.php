<?php


/*
  Create Spotify Playlist
  Erzeugt zwei Cookies für 60 sek
  $_GET['playlist_id'] - Playlist ID als Vorlage
  $_GET['name'] - Name der neuen playlist
*/

session_start();
$url = $_SESSION['url'];

require 'Spotify.php';
$spotify = new Spotify();
setcookie('playlist_id', $_GET['playlist_id'], time() + 60, "/");
setcookie('playlist_name', $_GET['playlist_name'], time() + 60, "/");
$spotify->Connect('https://playlist-manager.de/script/spotify_create.php');
$spotify->CreatePlaylist($_COOKIE['playlist_id'], $_COOKIE['playlist_name']);
header('Location: '.$url.'meldung=Created the random playlist!')

?>