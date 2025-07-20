<?php

require '../script/AppleMusic.php';

$music = new AppleMusic();
$music->Connect('https://playlist-manager.de/de_spotify_play.php');

?>
<?php

$music->DeletePlaylistManager();
if(isset($_SESSION['playlist_id']) && $_SESSION['playlist_id']) {
$playlist = $music->CreatePlaylist($_SESSION['playlist_id'], 'Playlist-Manager');
$music->Playlist($playlist[0], $playlist[1]); }
else echo "<h2>Error: Keine Playlist ausgewÃ¤hlt!</h2>";

?>