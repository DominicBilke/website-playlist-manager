<?php

/*
  Delete Spotify Playlist
  Erzeugt ein Cookie für 60 sek
  $_GET['playlist_id'] - Playlist ID
*/

session_start();
$url = $_SESSION['url'];

require 'Spotify.php';
$spotify = new Spotify();
$cookie_name = "user";
$cookie_value = "John Doe";
setcookie('playlist_id', $_GET['playlist_id'], time() + 60, "/"); // 86400 = 1 day
$spotify->Connect('https://playlist-manager.de/script/spotify_delete.php');
$spotify->DeletePlaylist($_COOKIE['playlist_id']);
header('Location: '.$url)

?>