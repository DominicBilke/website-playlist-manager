<?php

/*
Registrierung bei Youtube

Highlight-Concerts GmbH
*/

require_once __DIR__ . '/vendor/autoload.php';

class YoutubeApi {
	/*
		Youtube Music Playlist Manager API
		Integration der Developer Api von Youtube Music in eine PHP-Klasse.
		Spezielle Anpassungen an die Webseite, also nicht separat verwendbar.
	*/
	private $playlists = null;
	private $uuid = null;
	private $service = null;
	private $url;

	public function Connect($url) {
	/*
		Verbinde zu Service
	*/
		if(isset($_SESSION['editaccount']['youtube_search'])) $_GET['youtube_search'] = $_SESSION['editaccount']['youtube_search'];
		if(!isset($_GET['youtube_search']) && !isset($_SESSION['editaccount']['youtube_search'])) {
echo '
<script>
var s = prompt("Enter a Youtube Music Playlist to search for: ");
window.location.href="https://playlist-manager.de/script/refer_editaccount.php?search="+s+"&link='.$url.'";
</script>'; } }

	public function de_Connect($url) {
	/*
		Verbinde zu Service auf Deutsch.
	*/
		if(isset($_SESSION['editaccount']['youtube_search'])) $_GET['youtube_search'] = $_SESSION['editaccount']['youtube_search'];
		if(!isset($_GET['youtube_search']) && !isset($_SESSION['editaccount']['youtube_search'])) {
echo '
<script>
var s = prompt("Geben Sie eine Youtube Music Playliste als Suche ein: ");
window.location.href="https://playlist-manager.de/script/refer_editaccount.php?search="+s+"&link='.$url.'";
</script>'; } }


	public function Connect1($url) {
	/*
		Verbindung zu Youtube und nicht zu Youtube Music
	*/
	$this->url = $url;
$client = new Google_Client();
$client->setApplicationName('Playlist-Manager');
$client->setDeveloperKey("AIzaSyAwcEuUC3L33Ejj0EBePZuHVIgNLLVCZiY");
$client->setScopes([
    'https://www.googleapis.com/auth/youtube',
]);
$client->setAuthConfig('/var/www/vhosts/playlist-manager.de/client_secret_238320363514-lcieui6842pfve19av7clqavbnj0sfm2.apps.googleusercontent.com.json');
$client->setAccessType('offline');

if(isset($_GET['youtube_revoke']) && $_GET['youtube_revoke']==1) {
	unset($_SESSION['youtube_code']);
	unset($_SESSION['youtube_access_token']);
	unset($_GET['youtube_revoke']);
	unset($_SESSION['editaccount']['youtube_revoke']);
}

if(!isset($_SESSION['youtube_code']['code'])) {
// Request authorization from the user.
$client->setRedirectUri($url);
$authUrl = $client->createAuthUrl();
echo '<a href="'.$authUrl.'" target="_self">Open this link to login Youtube!</a><br><br>
<input type="hidden" name="youtube_playlist_id" value="'.(isset($_GET['youtube_playlist_id']) ? $_GET['youtube_playlist_id'] : $_SESSION['youtube_playlist_id']).'">';
/*echo '
<script>
var YoutubeFenster = window.open("'$authUrl.'", "YoutubeFenster", "width=300,height=400,left=100,top=200");
var verCode = prompt("Enter verification code: ");
window.location.href="https://playlist-manager.de/script/refer_editaccount1.php?authCode="+verCode+"&link='.$url.'";
</script>';*/
}
else  {
		try {
if (isset($_SESSION['youtube_access_token']) && $_SESSION['youtube_access_token']) {
  $client->setAccessToken($_SESSION['youtube_access_token']); 
// Define service object for making API requests.
$this->service = new Google_Service_YouTube($client);}
else {
if(isset($_GET['code']) && isset($_GET['scope']))
	$authCode = trim($_GET['code']);
else
	$authCode = trim($_SESSION['youtube_code']['code']);

$client->authenticate($authCode);
$_SESSION['youtube_access_token'] = $client->getAccessToken();
//$client->setAccessToken($_SESSION['youtube_access_token']);
// Exchange authorization code for an access token.
//$_SESSION['youtube_access_token'] = $client->fetchAccessTokenWithAuthCode($authCode);
//$client->setAccessToken($_SESSION['youtube_access_token']);
// Define service object for making API requests.
$this->service = new Google_Service_YouTube($client);
}
		}
		catch(Exception $e) {
			echo 'Error in Youtube-Connect!<br><br><script>console.log("'.$e->__toString().'");</script>';
			unset($_SESSION['youtube_code']);
			unset($_SESSION['youtube_access_token']);
			$this->service = null;
			$this->Connect1($url);
		}

}
	}

	public function de_DisplayCreatePlaylist() {
		/*
			Zeige Deutsches Formular zur Erstellung von Playlisten.
		*/
		
		if(isset($_GET['youtube_search'])) {
		/*if($this->service != null) {
			$queryParams = [
			    'maxResults' => 25,
			    'mine' => true
			];

		$this->playlists = $this->service->playlists->listPlaylists('id,snippet,player', $queryParams);*/
		$result = shell_exec("python3 /var/www/vhosts/playlist-manager.de/httpdocs/script/getPlaylists.py ".$_GET['youtube_search']);
		//echo $result;
		$this->playlists = array();
		$result = explode(',', $result);
		foreach($result as $i=>$res) {
			$r = explode(':', $res);
			if($r[0] && $r[1]) {
			  $this->playlists[$i]['id'] = $r[0];
			  $this->playlists[$i]['title'] = $r[1];
			}
		}
		//print_r($this->playlists);
		$new_playlist_id = $_GET['youtube_playlist_id'];
		echo '
		<label for="youtube_playlist_id">Wählen Sie eine Youtube-Music-Wiedergabeliste aus:</label>
		<select name="youtube_playlist_id">
			<option value="'.$new_playlist_id.'"> -- AKTUELLE PLAYLIST -- </option>
			<option value=""> -- KEINE PLAYLIST -- </option>';
		foreach ($this->playlists as $playlist) {
   			 echo '
				<option value="'.$playlist["id"].'" '.($new_playlist_id==$playlist["id"] ? 'selected' : '').' >'.$playlist["title"].'</option>';
		}
		echo '
		</select><br>'; }
	}

	public function DisplayCreatePlaylist() {
		/*
			Zeige Englisches Formular zur Erstellung von Playlisten.
		*/
		if(isset($_GET['youtube_search'])) {
		/*if($this->service != null) {
			$queryParams = [
			    'maxResults' => 25,
			    'mine' => true
			];

		$this->playlists = $this->service->playlists->listPlaylists('id,snippet,player', $queryParams);*/
		$result = shell_exec("python3 /var/www/vhosts/playlist-manager.de/httpdocs/script/getPlaylists.py ".$_GET['youtube_search']);
		//echo $result;
		$this->playlists = array();
		$result = explode(',', $result);
		foreach($result as $i=>$res) {
			$r = explode(':', $res);
			if($r[0] && $r[1]) {
			  $this->playlists[$i]['id'] = $r[0];
			  $this->playlists[$i]['title'] = $r[1];
			}
		}
		//print_r($this->playlists);
		$new_playlist_id = $_GET['youtube_playlist_id'];
		echo '
		<label for="youtube_playlist_id">Choose a Youtube-Music-Playlist:</label>
		<select name="youtube_playlist_id">
			<option value="'.$new_playlist_id.'"> -- CURRENT PLAYLIST -- </option>
			<option value=""> -- NO PLAYLIST -- </option>';
		foreach ($this->playlists as $playlist) {
   			 echo '
				<option value="'.$playlist["id"].'" '.($new_playlist_id==$playlist["id"] ? 'selected' : '').' >'.$playlist["title"].'</option>';
		}
		echo '
		</select><br>'; }
	}

	public function de_DisplayCreatePlaylist1() {
		/*
			Zeige Deutsches Formular zur Erstellung von Playlisten.
		*/
		
		if($this->service != null) {
			$queryParams = [
			    'maxResults' => 25,
			    'mine' => true
			];
		try {
		$this->playlists = $this->service->playlists->listPlaylists('id,snippet,player', $queryParams);
		//print_r($this->playlists);
		$new_playlist_id = $_GET['youtube_playlist_id'];
		echo '
		<label for="youtube_playlist_id">Wählen Sie eine Youtube-Wiedergabeliste aus (<a href="de_editaccount.php?'.http_build_query($_SESSION['editaccount']).'&youtube_revoke=1">Neuen Login anfordern!</a>):</label>
		<select name="youtube_playlist_id">
			<option value="'.$new_playlist_id.'"> -- AKTUELLE PLAYLIST -- </option>
			<option value=""> -- KEINE PLAYLIST -- </option>';
		foreach ($this->playlists["items"] as $playlist) {
   			 echo '
				<option value="'.$playlist["id"].'" '.($new_playlist_id==$playlist["id"] ? 'selected' : '').' >'.$playlist["snippet"]["title"].'</option>';
		}
		echo '
		</select><br>';
		}
		catch(Exception $e) {
			$this->playlists["items"] = [];
			echo '<script>console.log("'.$e->__toString().'");</script>';
			unset($_SESSION['youtube_code']);
			unset($_SESSION['youtube_access_token']);
			$this->service = null;
			$this->Connect1($this->url);
		} }
	}

	public function DisplayCreatePlaylist1() {
		/*
			Zeige Englisches Formular zur Erstellung von Playlisten.
		*/
		if($this->service != null) {
			$queryParams = [
			    'maxResults' => 25,
			    'mine' => true
			];

		try {
		$this->playlists = $this->service->playlists->listPlaylists('id,snippet,player', $queryParams);
		//print_r($this->playlists);
		$new_playlist_id = $_GET['youtube_playlist_id'];
		echo '
		<label for="youtube_playlist_id">Choose a Youtube-Playlist (<a href="editaccount.php?'.http_build_query($_SESSION['editaccount']).'&youtube_revoke=1">New login request!</a>):</label>
		<select name="youtube_playlist_id">
			<option value="'.$new_playlist_id.'"> -- CURRENT PLAYLIST -- </option>
			<option value=""> -- NO PLAYLIST -- </option>';
		foreach ($this->playlists["items"] as $playlist) {
   			 echo '
				<option value="'.$playlist["id"].'" '.($new_playlist_id==$playlist["id"] ? 'selected' : '').' >'.$playlist["snippet"]["title"].'</option>';
		}
		echo '
		</select><br>';
		}
		catch(Exception $e) {
			$this->playlists["items"] = [];
			echo '<script>console.log("'.$e->__toString().'");</script>';
			unset($_SESSION['youtube_code']);
			unset($_SESSION['youtube_access_token']);
			$this->service = null;
			$this->Connect1($this->url);
		} }
	}

	public function Playlist($playlist_id, $url) {
		/*
			Abspielen der Playlisten und Darstellung als Plugin.
		*/

		//$tracks = array();
		//$this->playlists = $this->service->playlists->listPlaylists($playlist_id);
		/*foreach ($playlist->results as $track) {
			$tracks[] = $track['id'];
		}
		shuffle($tracks);*/
		$this->Connect1($url);
		$uid = uniqid();
		$uid2 = uniqid();
		echo '
			<!-- Der Iframe Container -->
			  <div id="player"></div>
			<script>
				let ec = null; //EmbedController
				let ecplay = false; // ist gerade Spielzeit?
				let datplay = new Date(); // es wird gespielt bis
				let datpause = new Date(); // es wird Pause gemacht bis

 var tag = document.createElement("script");

      tag.src = "https://www.youtube.com/iframe_api";
      var firstScriptTag = document.getElementsByTagName("script")[0];
      firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

      // 3. This function creates an <iframe> (and YouTube player)
      //    after the API code downloads.
      var player;
      function onYouTubeIframeAPIReady() {
        player = new YT.Player("player", {
          height: "800",
          width: "100%",
          playerVars: {
             autoplay: 0,
             loop: 1,
             shuffle: 1,
             mute: 1,
	     list: "'.$playlist_id.'", 
             listType: "playlist", 
          },
          events: {
            onReady: onPlayerReady

          }
        });
      }
      // 4. The API will call this function when the video player is ready.
      function onPlayerReady(event) { setTimeout( function() {
		    document.getElementById("player").click();
		    /*player.loadPlaylist({list: "'.$playlist_id.'", 
					 listType: "playlist", 
					 index: 0, 
					 startSeconds: 0, 
					 suggestedQuality: "large"
					});*/
					player.setShuffle(true);
					player.setLoop(true);
					player.stopVideo();
					player.mute();
					ecplay = false;
					// Aufrufen der benutzerdefinierten Funktion jede 1 Sekunden
					setInterval(function() {
					// Datumseinstellungen
					const dat = new Date();
					let h = dat.getHours();
					let m = dat.getMinutes();
					let d = dat.getDay();
					const datfrom = new Date();
					datfrom.setHours('.date_parse($_SESSION["daytime_from"])["hour"].');
					datfrom.setMinutes('.date_parse($_SESSION["daytime_from"])["minute"].');
					const datto = new Date();
					datto.setHours('.date_parse($_SESSION["daytime_to"])["hour"].');
					datto.setMinutes('.date_parse($_SESSION["daytime_to"])["minute"].');
					// Pause vor der gewüsnchten Zeit.
					if(ecplay && \''.$_SESSION["days"].'\'.indexOf(d) >= 0 && datfrom.getTime() > dat.getTime()) {
						player.pauseVideo();
						player.mute();
						console.log(\'pause!\');
						ecplay = false;
					}

					// Spielen mit Algorithmus
					if(!ecplay && \''.$_SESSION["days"].'\'.indexOf(d) >= 0 && datfrom.getTime() < dat.getTime() && dat.getTime() < datto.getTime() && dat.getTime() > datpause.getTime()) {
						let rand_play = Math.floor(Math.random() * 600) + 61;
						datplay = new Date();
						datplay.setSeconds(datplay.getSeconds() + rand_play);
						player.setShuffle(true);
						player.setLoop(true);
						player.playVideo();
						player.unMute();
						ecplay = true;
						console.log(\'play for \'+rand_play+\' seconds\');
					}
					
					// Pause mit Algorithmus
					if(ecplay && \''.$_SESSION["days"].'\'.indexOf(d) >= 0 && datfrom.getTime() < dat.getTime() && dat.getTime() < datto.getTime() && dat.getTime() > datplay.getTime()) {
						let rand_pause = Math.floor(Math.random() * 600) + 61;
						datpause = new Date();
						datpause.setSeconds(datpause.getSeconds() + rand_pause);
						player.pauseVideo();
						player.mute();
						console.log(\'pause for \'+rand_pause+\' seconds\');
						ecplay = false;
					}

					// Pause nach der gewünschten Zeit
					if(ecplay && \''.$_SESSION["days"].'\'.indexOf(d) >= 0 && dat.getTime() > datto.getTime()) {
						player.pauseVideo();
						player.mute();
						console.log(\'pause!\');
						ecplay = false;
					}

					
						// in der regulären Spielzeit wird die Statistik gefüllt.
					if(\''.$_SESSION["days"].'\'.indexOf(d) >= 0 && datfrom.getTime() < dat.getTime() && dat.getTime() < datto.getTime()) 
					$.ajax({
  						method: "POST",
  						url: "script/log_spotify.php",
  						data: { }
					})
  					.done(function( response ) {  
  					});

					}, 2000);
				 }, 10000); }
			</script>';

	}



}
?>