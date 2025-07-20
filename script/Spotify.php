<?php

/*
Registrierung bei Spotify Developers:

Highlight-Concerts-Test
Test-App for Highlight-Concerts GmbH

App Status Development mode
*/

include 'vendor/autoload.php';
include 'script/vendor/autoload.php';

class Spotify {
	/*
		Spotify Playlist Manager API
		Integration der Developer Api von Spotify in eine PHP-Klasse.
		Spezielle Anpassungen an die Webseite, also nicht separat verwendbar.
	*/
	private $session = null;
	private $accessToken = null;
	private $refreshToken = null;
	private $api = null;
	private $playlists = null;
	private $me = null;

	public function Connect($redirect_uri, $db_token=null) {
		/*
			Verbindung zu Spotify herstellen.
			Funktionen: Register, Connect,  Reconnect
			$redirect_uri - Weiterleitung an die gewünschte Seite nach Anmeldung.
						Es sollte die gleiche Seite sein. Der Connect berücksichtigt es so.
		*/
		switch (($db_token === null ? trim($_SESSION['db_token']) : $db_token)) {
  case "1":
		$CLIENT_ID = '4078ed7dc1264188a9e83dfd459a94a0';
		$CLIENT_SECRET = 'b9e4f66dbe5d4b659bdc635df002ed34';
    break;
  case "2":
		$CLIENT_ID = '9f93d278b1e9488eb8d65c75e4a981e6';
		$CLIENT_SECRET = '621df2c803d648af8a089786a9363dd3';
    break;
  case "3":
		$CLIENT_ID = '3cafe66bbec142dc939852b29d17a679';
		$CLIENT_SECRET = 'e00d44f67ef9449b9ecea6349c2a05eb';
    break;
  case "4":
		$CLIENT_ID = 'b9a9a81fc9fb4677aad346e0897696ad';
		$CLIENT_SECRET = '8c3310e0a2a5448194e7f6b1ceed720b';
    break;
  case "5":
		$CLIENT_ID = '35dce4c184994d40982be7f8caf86219';
		$CLIENT_SECRET = '38ddaf78ad7f4edeaaa9cbd911fed207';
    break;
  case "6":
		$CLIENT_ID = '18abae9a0dc14f4998f159194c83a095';
		$CLIENT_SECRET = '4c121512401247b592dfabe8fde56283';
    break;
  case "7":
		$CLIENT_ID = '34b1a51a8454448381bb8952b8f46483';
		$CLIENT_SECRET = '68c9dcd27da14bd38d64da6415b828f4';
    break;
  case "8":
		$CLIENT_ID = '12fc6b0bdc6147fa9d8df7072ed5faf6';
		$CLIENT_SECRET = 'f3ebb1fb7da54c0dab5bad3b840ac531';
    break;
  case "9":
		$CLIENT_ID = '0a28970aee9a4817a6095dc9322b9fd7';
		$CLIENT_SECRET = 'cc4684b5167a4118ab76670366be0330';
    break;
  case "10":
		$CLIENT_ID = '841dded04f8747a8bc86dd1e0b7a7e6a';
		$CLIENT_SECRET = '67eaa06c54a04904b65818f80d01a27f';
    break;
  case "11":
		$CLIENT_ID = '88fbbcb072f541da999d4f1e19b60eb2';
		$CLIENT_SECRET = 'c7bd078b658d4f7ba7eabaebf967972e';
    break;

  case "12":
		$CLIENT_ID = '428e647c852740739a2774e159fa45cb';
		$CLIENT_SECRET = '24fb6130213747a9b6a6d25fa278d304';
    break;

  case "13":
		$CLIENT_ID = '7a727aa474a34953a297ba705e165aea';
		$CLIENT_SECRET = 'b1441c87b7a7409a89cc55e57273d748';
    break;

  case "14":
		$CLIENT_ID = 'ecb08e6644db417b9696a5033414dd11';
		$CLIENT_SECRET = 'aac6323915e84ef594132c9e62c65450';
    break;
  case "15":
		$CLIENT_ID = 'f5d56db0c357404fbe41ee417124fd63';
		$CLIENT_SECRET = '499cfb2e8df24383a1ee3312d9146c36';
    break;
  case "16":
		$CLIENT_ID = 'eafc823c6c764536abd36571fd98ddce';
		$CLIENT_SECRET = 'b843b3d1fe3447ada3b5eb9a6103c095';
    break;

  case "17":
		$CLIENT_ID = '2d3618caca7c4c22b13be6b096d6adc4';
		$CLIENT_SECRET = 'd6180d3724c645a0867f1e39c88fb753';
    break;

  case "18":
		$CLIENT_ID = 'ceedd5a1a4104119bec9cee7be5ad14a';
		$CLIENT_SECRET = '70c51bca40714f9fb63dd1fa576bcfee';
    break;

  case "19":
		$CLIENT_ID = '4d051f53f5b14dbcbd1b6f7d8628e6e0';
		$CLIENT_SECRET = 'cd8c98969b7149b890ae96392fd08d0d';
    break;
  case "20":
		$CLIENT_ID = '887038e0ee90461f9f8d3883b7f49434';
		$CLIENT_SECRET = 'b4cb30b30e2a4f418e613d5a05a835e5';
    break;


  default:
		$CLIENT_ID = '4078ed7dc1264188a9e83dfd459a94a0';
		$CLIENT_SECRET = 'b9e4f66dbe5d4b659bdc635df002ed34';
}

		try {
		$this->session = new SpotifyWebAPI\Session(
		    $CLIENT_ID,
		    $CLIENT_SECRET,
		    $redirect_uri
		);
		$options = [
    			'auto_refresh' => true,
    			'auto_retry' => true,
		];
		$this->api = new SpotifyWebAPI\SpotifyWebAPI($options);

		if (isset($_GET['code']) && !isset($_GET['state']) && !isset($_GET['scope'])) {
		    $this->session->requestAccessToken($_GET['code']);
		    $this->api->setAccessToken($this->session->getAccessToken());

		    $this->me = $this->api->me();
		} else {
		    $this->SetConfig();
		}

		// You can also call setSession on an existing SpotifyWebAPI instance
		$this->api->setSession($this->session);
		// Remember to grab the tokens afterwards, they might have been updated
		$this->accessToken = $this->session->getAccessToken();
		$this->refreshToken = $this->session->getRefreshToken();
		}
		catch(Exception $e) {
			$this->me  = null;
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}

	}

	public function de_DisplayCreatePlaylist() {
		/*
			Zeige Deutsches Formular zur Erstellung von Playlisten.
		*/
		if($this->me != null) {
		try {
		$this->playlists = $this->api->getUserPlaylists($this->me->id, ['limit' => 50, ]);
		}
		catch(Exception $e) {
			echo 'Error im Spotify-Login! Es ist der richtige Token in dem aktuellen Account einzustellen!<br><br>
			      <input type="hidden" value="'.$_GET['playlist_id'].'" name="playlist_id">';
		}
		$new_playlist_id = $_GET['playlist_id'];
		echo '
		<!--form method="get" action="/script/spotify_create.php" id="episodes" style="border-radius: 5px; background-color: #f2f2f2;padding: 20px;"-->
<script>
function Spotify_Logout() {
const url = "https://www.spotify.com/logout/";                                                                                                                                                                                                                                                                               
const spotifyLogoutWindow = window.open(url, "Spotify Logout", "width=700,height=500,top=40,left=40");                                                                                               
setTimeout(() => spotifyLogoutWindow.close(), 2000);
setTimeout(() => {window.location.href = "de_editaccount.php?'.http_build_query($_SESSION['editaccount']).'"; }, 3000);
}
</script>
		<label for="playlist_id">Wählen Sie eine Spotify-Wiedergabeliste aus ( <a onClick="Spotify_Logout()">Neuen Login anfordern!</a> ):</label>
		<select name="playlist_id">
			<option value="'.$new_playlist_id.'"> -- AKTUELLE PLAYLIST -- </option>
			<option value=""> -- KEINE PLAYLIST -- </option>';
		foreach ($this->playlists->items as $playlist) {
   			 echo '
				<option value="'.$playlist->id.'" '.($new_playlist_id==$playlist->id ? 'selected' : '').' >'.$playlist->name.'</option>';
		}
		echo '
		</select><br>
		<!--label for="playlist_name">Name für die neue Playlist:</label>
		<input type="text" name="playlist_name" placeholder="Geben Sie einen Namen für die neue Wiedergabeliste ein." required /><br-->
		<!--input type="submit" value="Eine neue Liste erstellen" />
		</form-->'; }
		else 
			echo 'Error im Spotify-Login! Es ist der richtige Token in dem aktuellen Account einzustellen!<br><br>
			      <input type="hidden" value="'.$_GET['playlist_id'].'" name="playlist_id">';
	}

	public function DisplayCreatePlaylist() {
		/*
			Zeige Englisches Formular zur Erstellung von Playlisten.
		*/

		if($this->me != null) {
		try {
		$this->playlists = $this->api->getUserPlaylists($this->me->id, ['limit' => 50, ]);
		}
		catch(Exception $e) {
			echo 'Error in Spotify-Login! The right Token has to be set in this current Account!<br><br>
			      <input type="hidden" value="'.$_GET['playlist_id'].'" name="playlist_id">';
		}
		$new_playlist_id = $_GET['playlist_id'];

		echo '
		<!--form method="get" action="/script/spotify_create.php" id="episodes" style="border-radius: 5px; background-color: #f2f2f2;padding: 20px;"-->
<script>
function Spotify_Logout() {
const url = "https://www.spotify.com/logout/";                                                                                                                                                                                                                                                                               
const spotifyLogoutWindow = window.open(url, "Spotify Logout", "width=700,height=500,top=40,left=40");                                                                                               
setTimeout(() => spotifyLogoutWindow.close(), 2000);
setTimeout(() => {window.location.href = "editaccount.php?'.http_build_query($_SESSION['editaccount']).'"; }, 3000);
}
</script>
		<label for="playlist_id">Choose a Spotify playlist ( <a onClick="Spotify_Logout()">New login request!</a> ):</label>
		<select name="playlist_id">
			<option value="'.$new_playlist_id.'"> -- CURRENT PLAYLIST -- </option>
			<option value=""> -- NO PLAYLIST -- </option>';
		foreach ($this->playlists->items as $playlist) {
   			 echo '
				<option value="'.$playlist->id.'" '.($new_playlist_id==$playlist->id ? 'selected' : '').' >'.$playlist->name.'</option>';
		}
		echo '
		</select><br>
		<!--label for="playlist_name">Name for the new Playlist:</label>
		<input type="text" name="playlist_name" placeholder="Give a name for the new playlist.." required /><br>
		<input type="submit" value="Create a new list" />
		</form-->'; }
		else echo 'Error in Spotify-Login! The right Token has to be set in this current Account!<br><br>
			      <input type="hidden" value="'.$_GET['playlist_id'].'" name="playlist_id">';
	}

	public function CreatePlaylist($playlist, $name) {
		/*
			Erstellung von zufälligen Playlisten.
			$playlist - aus dieser Playlist wird erstellt.
			$name - Name der neuen Playlist
		*/

		$playlistTracks = $this->api->getPlaylistTracks($playlist);
		$tracks=[];
		foreach ($playlistTracks->items as $track) {
		    $tracks[] = $track->track;
		}

		$new_playlist = $this->api->createPlaylist([
    			'name' => $name
		]);
		foreach ($playlistTracks->items as $track) {
			try {
			$rand_track_nr = rand(0,count($tracks)-1);
			$this->api->addPlaylistTracks($new_playlist->id, [
    				$tracks[$rand_track_nr]->id
			]);
			}
			catch (Exception $e) { }
		}
		return [$new_playlist->id, $new_playlist->uri];
	}

	public function Playlist($playlist_id) {
		/*
			Abspielen der Playlisten und Darstellung als Plugin.
		*/

		if($this->me != null) {
		try {
		$this->playlists = $this->api->getUserPlaylists($this->me->id);
		$pllist = $this->api->getPlaylist($playlist_id);
		}
		catch(Exception $e) {
			echo 'Error in Spotify-Login! The right Token has to be set in Your Account! A new login is required!<br><br>';
			$pllist = array();
		}


		try {		
		if($pllist) 
		// reorder playlist tracks in random way to generate a snapshot!
		$range_start = rand(0, $pllist->tracks->total - 1);
		$insert_before = rand(0, $pllist->tracks->total - 1);
		$snapshot_id = $this->api->reorderPlaylistTracks($playlist_id, ['range_start' => $range_start, 'insert_before' => $insert_before]);

		for($i=0; $i < $pllist->tracks->total; $i++) {
			$insert_before = rand(0, $pllist->tracks->total-1);
			$snapshot_id = $this->api->reorderPlaylistTracks($playlist_id, ['range_start' => $i, 'insert_before' => $insert_before, 'snapshot_id' => $snapshot_id]);
		}
		}
		catch(Exception $e) {
			echo 'Shuffle not possible! Reordering of tracks only works in own playlists!<br><br>';
		}

		$uid = uniqid();
		$uid2 = uniqid();
		echo '<script>
function Spotify_Logout() {
const url = "https://www.spotify.com/logout/";                                                                                                                                                                                                                                                                               
const spotifyLogoutWindow = window.open(url, "Spotify Logout", "width=700,height=500,top=40,left=40");                                                                                               
setTimeout(() => spotifyLogoutWindow.close(), 2000);
setTimeout(() => {window.location.href = "'.$_SESSION["url"].'"; }, 3000);
}
</script>
		<div id="episodes" style="border-radius: 5px; background-color: #f2f2f2;padding: 20px;">';
		// Die Playlist-Buttons 
			echo '
			    <button data-spotify-id="spotify:playlist:'.$playlist_id.'" style="margin:10px;" id="'.$playlist_id.'">
 			     Reload Playlist!
			    </button>';
		/*foreach ($this->playlists->items as $playlist) {
   			 echo '
			    <button data-spotify-id="'.$playlist->uri.'" style="margin:10px;" id="'.$playlist->id.'">
 			     '.$playlist->name.'
			    </button>';

		}*/
		echo '
			<button onClick="Spotify_Logout()">Get a new Login-Prompt!</button>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
			<script src="https://open.spotify.com/embed/iframe-api/v1" async></script>
			<!-- Der Iframe Container -->
			<div id="embed-iframe"></div>
			<script>
				let ec = null; //EmbedController
				let ecplay = false; // ist gerade Spielzeit?
				let datplay = new Date(); // es wird gespielt bis
				let datpause = new Date(); // es wird Pause gemacht bis

				window.onSpotifyIframeApiReady = (IFrameAPI) => {
				  let element = document.getElementById(\'embed-iframe\');
				  // letzte Playlist wird zuerst gewählt
				  let options = {
 				     uri: \'spotify:playlist:'.$playlist_id.'\',
				     width: \'100%\',
				     height: \'800\'
			        };

				// Callback bei Änderungen am IFrame
				let callback = (EmbedController) => {
				  document.querySelectorAll(\'div#episodes > button\').forEach(
				    episode => {
					// Rücksetzen bei Klick auf Playlist.
				      episode.addEventListener(\'click\', () => {
			        	EmbedController.loadUri(episode.dataset.spotifyId);
				      datpause = new Date();
					datplay = new Date();
					ecplay = false;
				      });
				    });
					ec = EmbedController;
				};
				document.querySelector("#embed-iframe").setAttribute("allow", "autoplay; clipboard-write; encrypted-media *; fullscreen; picture-in-picture");
  				IFrameAPI.createController(element, options, callback);
				document.querySelector("iframe").setAttribute("allow", "autoplay; clipboard-write; encrypted-media *; fullscreen; picture-in-picture");
				document.querySelector("iframe").click();
				document.getElementById("'.$playlist_id.'").click();
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
						ec.pause();
						console.log(\'pause!\');
						ecplay = false;
					}

					// Spielen mit Algorithmus
					if(!ecplay && \''.$_SESSION["days"].'\'.indexOf(d) >= 0 && datfrom.getTime() < dat.getTime() && dat.getTime() < datto.getTime() && dat.getTime() > datpause.getTime()) {
						let rand_play = Math.floor(Math.random() * 600) + 61;
						datplay = new Date();
						datplay.setSeconds(datplay.getSeconds() + rand_play);
						ec.togglePlay();
						ecplay = true;
						console.log(\'play for \'+rand_play+\' seconds\');
					}
					
					// Pause mit Algorithmus
					if(ecplay && \''.$_SESSION["days"].'\'.indexOf(d) >= 0 && datfrom.getTime() < dat.getTime() && dat.getTime() < datto.getTime() && dat.getTime() > datplay.getTime()) {
						let rand_pause = Math.floor(Math.random() * 600) + 61;
						datpause = new Date();
						datpause.setSeconds(datpause.getSeconds() + rand_pause);
						ec.togglePlay();
						console.log(\'pause for \'+rand_pause+\' seconds\');
						ecplay = false;
					}

					// Pause nach der gewünschten Zeit
					if(ecplay && \''.$_SESSION["days"].'\'.indexOf(d) >= 0 && dat.getTime() > datto.getTime()) {
						ec.pause();
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

					}, 5000);
				};
			</script>
			</div>'; }
		else echo 'Fehler im Spotify Login!';

	}


	public function DeletePlaylistManager() {
		// Playlist ID löschen

		if($this->me != null) {
		try {
		$this->playlists = $this->api->getUserPlaylists($this->me->id, ['limit' => 50, ]);
		}
		catch(Exception $e) {
			echo 'Error in Spotify-Login! The right Token has to be set in Your Account! A new login is required!<br><br>';
			$this->playlists = array();
		}
		if($this->playlists)
		foreach ($this->playlists->items as $playlist) {
			if($playlist->name == 'Playlist-Manager')
				$this->api->unfollowPlaylist($playlist->id); }
		}
	}


	public function DeletePlaylist($playlist) {
		// Playlist ID löschen

		$this->api->unfollowPlaylist($playlist);
	}

	public function GetPlaylists() {
		/*
			Playlisten darstellen als Tabelle.
		*/

		$this->playlists = $this->api->getUserPlaylists($this->me->id, ['limit' => 50, ]);

		echo '
		<table>';
				

		foreach ($this->playlists->items as $playlist) {
   			 echo '
				<tr>
					<td width="90%"><a href="' . $playlist->external_urls->spotify . '" target="_blank">' . $playlist->name . '</a> <br></td>
					<td width="10%" align="right"><a href="script/spotify_delete.php?playlist_id='.$playlist->id.'" class="icon solid fa-trash-alt"><span class="label">Delete</span></a>
					</td>
				</tr>
			';
		}

		echo '
		</table>';
	}

	public function SetConfig() {
		/*
			Aufruf der Autorisation an Spotify.
		*/

		$options = [
    			'scope' => [
        		'user-library-modify',
        		'user-library-read',
        		'user-read-email',
        		'user-read-private',
			'playlist-read-private',
			'playlist-read-collaborative',
			'playlist-modify-private',
			'playlist-modify-public',
    			],
		];
   		header('Location: ' . $this->session->getAuthorizeUrl($options));
		die();
	} 

}
?>