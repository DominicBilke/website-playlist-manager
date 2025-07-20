<?php

	/*
		Verbindung zu Apple Music Service
		Mit OAuth und Playlisten und Player
	*/
require 'vendor/autoload.php';
use PouleR\AppleMusicAPI\Request\LibraryPlaylistCreationRequest;

class AppleMusic {

	private $api = null;
	private $storefront = "de";
	private $playlists = null;
	public $jwtToken = null;

	public function Connect() {

	/*
		Verbindung zu Apple Music Service
	*/
		$tokenGenerator = new PouleR\AppleMusicAPI\AppleMusicAPITokenGenerator();
		$this->jwtToken = $tokenGenerator->generateDeveloperToken(
    		'GCAHH74QFP',
    		'D8CPQWP5JR',
    		'https://playlist-manager.de/AuthKey_D8CPQWP5JR.p8'
		);	

		$curl = new \Symfony\Component\HttpClient\CurlHttpClient();
		$client = new PouleR\AppleMusicAPI\APIClient($curl);
		$client->setDeveloperToken($this->jwtToken);

		$this->api = new PouleR\AppleMusicAPI\AppleMusicAPI($client);
		
		//$this->storefront =  $this->api->getUsersStorefront();

	}
	public function de_DisplayCreatePlaylist() {
		/*
			Zeige Deutsches Formular zur Erstellung von Playlisten.
		*/

		//$this->playlists = $this->api->getAllLibraryPlaylists(50);

		echo '
<script src="https://js-cdn.music.apple.com/musickit/v3/musickit.js" data-web-components async></script>
<script>
function Apple_Logout() {
MusicKit.getInstance().unauthorize();
setTimeout(() => {window.location.href = "de_editaccount.php?'.http_build_query($_SESSION['editaccount']).'"; }, 3000);
}
</script>
		<label for="apple_playlist_id">Wähle eine Apple-Music-Wiedergabeliste ( <a onclick="Apple_Logout()">Neuen Login anfordern!</a> ):</label>
		<select name="apple_playlist_id" id="apple_playlist_id">
			<option value="'.$_SESSION['editaccount']['apple_playlist_id'].'"> -- AKTUELLE PLAYLIST -- </option>
			<option value=""> -- KEINE PLAYLIST -- </option>
		</select><br>
		<script>
document.addEventListener(\'musickitloaded\', async function () {
  try {
    await MusicKit.configure({
      developerToken: \''.$this->jwtToken.'\',
      app: {
        name: \'Playlist-Manager\',
        build: \'2023.06.07\',
      },
    });
  } catch (err) {
console.log(\'Error in configuring Musickit!\');
console.log(err);
  }
const music = MusicKit.getInstance();

await music.authorize();
const { data: result } = await music.api.music(\'v1/me/library/playlists\');
console.log(result.data);
for(let i=0;i<result.data.length;i++) {
let option = new Option(result.data[i].attributes.name,result.data[i].id);
document.getElementById(\'apple_playlist_id\').add(option)
if(result.data[i].id === \''.$_GET['apple_playlist_id'].'\') option.selected = \'selected\';
}

});
		</script>';
	}


	public function DisplayCreatePlaylist() {
		/*
			Zeige Englisches Formular zur Erstellung von Playlisten.
		*/

		//$this->playlists = $this->api->getAllLibraryPlaylists(50);

		echo '
		<script src="https://js-cdn.music.apple.com/musickit/v3/musickit.js" data-web-components async></script>
<script>
function Apple_Logout() {
MusicKit.getInstance().unauthorize();
setTimeout(() => {window.location.href = "de_editaccount.php?'.http_build_query($_SESSION['editaccount']).'"; }, 3000);
}
</script>
		<label for="apple_playlist_id">Choose a Apple-Music playlist ( <a onclick="Apple_Logout()">New login request!</a> ):</label>
		<select name="apple_playlist_id" id="apple_playlist_id">
			<option value="'.$_SESSION['editaccount']['apple_playlist_id'].'"> -- CURRENT PLAYLIST -- </option>
			<option value=""> -- NO PLAYLIST -- </option>
		</select><br>
		<script>
document.addEventListener(\'musickitloaded\', async function () {
  try {
    await MusicKit.configure({
      developerToken: \''.$this->jwtToken.'\',
      app: {
        name: \'Playlist-Manager\',
        build: \'2023.06.07\',
      },
    });
  } catch (err) {
console.log(\'Error in configuring Musickit!\');
console.log(err);
  }
const music = MusicKit.getInstance();

await music.authorize();
const { data: result } = await music.api.music(\'v1/me/library/playlists\');
console.log(result.data);
for(let i=0;i<result.data.length;i++) {
let option = new Option(result.data[i].attributes.name,result.data[i].id);
document.getElementById(\'apple_playlist_id\').add(option)
if(result.data[i].id === \''.$_GET['apple_playlist_id'].'\') option.selected = \'selected\';
}

});
		</script>';
	}

	public function CreatePlaylist($playlist, $name) {
		/*
			Erstellung von zufälligen Playlisten.
			$playlist - aus dieser Playlist wird erstellt.
			$name - Name der neuen Playlist
		*/


		$playlistTracks = $this->api->getCatalogPlaylist($this->storefront, $playlist);
		$tracks=[];
		foreach ($playlistTracks->items as $track) {
		    $tracks[] = $track->track;
		}
		$newplaylist = LibraryPlaylistCreationRequest($name);
		$newplaylist->setDescription("Playlist-Manager for Highlight-Concerts GmbH");
		foreach ($playlistTracks->getTracks() as $track) {
			try {
			$rand_track_nr = rand(0,count($tracks)-1);
			$newplaylist->addTrack($new_playlist->id, [
    				$tracks[$rand_track_nr]->id
			]);
			}
			catch (Exception $e) { }
		}
		return createLibraryPlaylist($newplaylist)->getId();
	}

	public function init() {	
		$tokenGenerator = new PouleR\AppleMusicAPI\AppleMusicAPITokenGenerator();
		$this->jwtToken = $tokenGenerator->generateDeveloperToken(
    		'4366JTQ9DX',
    		'6L6GA98643',
    		'https://playlist-manager.de/AuthKey_6L6GA98643.p8'
		);	
		echo "cd /var/www/vhosts/playlist-manager.de/script.playlist-manager.de/vendor/thinmusic/ && REACT_APP_MUSICKIT_TOKEN=".$this->jwtToken." REACT_APP_FIREBASE_TOKEN=AIzaSyCvfMD9WIyhHgtwZKx9JpmEs_4Hay0vHy4 npm start";
	}

	public function Playlist($playlist_url, $lang='en') {
		/*
			Abspielen der Playlisten und Darstellung als Plugin.
		*/

		//$this->playlists = $this->api->getAllLibraryPlaylists(50);
		echo '
<script>

document.addEventListener(\'musickitloaded\', async function () {
  try {
    const music = await MusicKit.configure({
      developerToken: \''.$this->jwtToken.'\',
      app: {
        name: \'Playlist-Manager\',
        build: \'2023.06.07\',
      },
    });
  } catch (err) {
//document.getElementById(\'apple_error\').innerHTML += \'<br>Error in configuring Musickit!\';
console.log(\'Error in configuring Musickit!\');
console.log(err);
  }
const music = MusicKit.getInstance();

await music.authorize();

music.setQueue({
  playlist: \''.$playlist_url.'\'
}); 
music.repeatMode = 2;
music.shuffleMode = 1;
try {

const { data: playlist } = await music.api.music(\'/v1/catalog/de/playlists/'.$playlist_url.'\');
//const playlist = await music.api.playlist(\''.$playlist_url.'\');
//document.getElementById(\'apple_playlist\').innerHTML = \'Current Playlist: \'+playlist.attributes.name; 
}
catch (err) {
//document.getElementById(\'apple_error\').innerHTML += \'<br>Error: Playlist not available!\';
console.log(\'Playlist not available!\');
console.log(err);
  }
/*
const playlists = await music.api.library.playlists(null);
for(let i=0;i<playlists.length;i++) {
let option = new Option(playlists[i].attributes.name,playlists[i].id);
document.getElementById(\'apple_playlist_id\').add(option);
if(playlists[i].id === \''.$playlist_url.'\') option.selected = \'selected\';
}*/


	 /* setup click handlers
    document.getElementById(\'add-to-q-btn\').addEventListener(\'click\', () => {
      const idInput   = document.getElementById(\'apple_playlist_id\');
      music.setQueue({
        playlist: idInput.value
      });

    });*/

    document.getElementById(\'play-btn\').addEventListener(\'click\', () => {
      /***
        Resume or start playback of media item
        https://developer.apple.com/documentation/musickitjs/musickit/musickitinstance/2992709-play
      ***/
      music.play();
    });

    document.getElementById(\'pause-btn\').addEventListener(\'click\', () => {
      /***
        Pause playback of media item
        https://developer.apple.com/documentation/musickitjs/musickit/musickitinstance/2992708-pause
      ***/
      music.pause();
    });

    document.getElementById(\'next-item\').addEventListener(\'click\', () => {
      music.skipToNextItem();
    });

    document.getElementById(\'previous-item\').addEventListener(\'click\', () => {
      music.skipToPreviousItem();
    });

String.prototype.toHHMMSS = function () {
    var sec_num = parseInt(this, 10); 
    var hours   = Math.floor(sec_num / 3600);
    var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
    var seconds = sec_num - (hours * 3600) - (minutes * 60);

    if (hours   < 10) {hours   = "0"+hours;}
    if (minutes < 10) {minutes = "0"+minutes;}
    if (seconds < 10) {seconds = "0"+seconds;}
    return hours+\':\'+minutes+\':\'+seconds;
}

    // expose our instance globally for testing
    window.music = music;


				let ecplay = false; // ist gerade Spielzeit?
				let datplay = new Date(); // es wird gespielt bis
				let datpause = new Date(); // es wird Pause gemacht bis
					
					// Aufrufen der benutzerdefinierten Funktion jede 1 Sekunden
					setInterval(function() {

					if(music.nowPlayingItem !== undefined) {
					var t = music.nowPlayingItem.artwork.url;
					t = t.replace("{w}", music.nowPlayingItem.artwork.width.toString());
					t = t.replace("{h}", music.nowPlayingItem.artwork.height.toString());
					document.getElementById(\'artwork\').src = t;
					document.getElementById(\'playlist_title\').innerHTML = music.nowPlayingItem.playlistName;
					document.getElementById(\'track_title\').innerHTML = music.nowPlayingItem.title;
					document.getElementById(\'from\').innerHTML = String(music.currentPlaybackTime).toHHMMSS();
					document.getElementById(\'progress\').value = music.currentPlaybackProgress*100;
					document.getElementById(\'to\').innerHTML = String(music.currentPlaybackDuration).toHHMMSS(); }
	
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
						 music.pause();
						console.log(\'pause!\');
						ecplay = false;
					}

					// Spielen mit Algorithmus
					if(!ecplay && \''.$_SESSION["days"].'\'.indexOf(d) >= 0 && datfrom.getTime() < dat.getTime() && dat.getTime() < datto.getTime() && dat.getTime() > datpause.getTime()) {
						let rand_play = Math.floor(Math.random() * 600) + 61;
						datplay = new Date();
						datplay.setSeconds(datplay.getSeconds() + rand_play);
						 music.play();
						ecplay = true;
						console.log(\'play for \'+rand_play+\' seconds\');
					}
					
					// Pause mit Algorithmus
					if(ecplay && \''.$_SESSION["days"].'\'.indexOf(d) >= 0 && datfrom.getTime() < dat.getTime() && dat.getTime() < datto.getTime() && dat.getTime() > datplay.getTime()) {
						let rand_pause = Math.floor(Math.random() * 600) + 61;
						datpause = new Date();
						datpause.setSeconds(datpause.getSeconds() + rand_pause);
						 music.pause();
						console.log(\'pause for \'+rand_pause+\' seconds\');
						ecplay = false;
					}

					// Pause nach der gewünschten Zeit
					if(ecplay && \''.$_SESSION["days"].'\'.indexOf(d) >= 0 && dat.getTime() > datto.getTime()) {
						 music.pause();
						console.log(\'pause!\');
						ecplay = false;
					}
					
					// in der regulären Spielzeit wird die Satistik gefüllt.
					if(\''.$_SESSION["days"].'\'.indexOf(d) >= 0 && datfrom.getTime() < dat.getTime() && dat.getTime() < datto.getTime()) 
					$.ajax({
  						method: "POST",
  						url: "script/log_spotify.php",
  						data: { }
					})
  					.done(function( response ) {  
  					});

					}, 1000);

});
			</script>
  <div id="apple_error"></div>
  <div style="border-radius: 5px; background-color: #f2f2f2;">
  <div style="float: left; padding: 20px;"><img src="" width="300" height="300" id="artwork"/></div>
  <div style="padding: 20px;">
  <div><strong>'.($lang=='en' ? 'Playlist: ' : 'Wiedergabeliste: ').'</strong><br><span id="playlist_title"></span></div><br>
  <div><strong>'.($lang=='en' ? 'Track: ' : 'Lied: ').'</strong><br><span id="track_title"></span></div><br>
  <div><strong>'.($lang=='en' ? 'Time elapsed: ' : 'Spielzeit: ').'</strong><br><span id="from"></span> &nbsp; <progress id="progress" value="0" max="100"> 0% </progress> &nbsp; <span id="to"></span></div><br>
  <button id="previous-item"><img alt="'.($lang=='en' ? 'Previous' : 'Letztes Lied').'" src="images/angle_arrow_left_icon.png" width="25" style="vertical-align: middle;"/></button>
  <button id="play-btn"><img alt="'.($lang=='en' ? 'Play' : 'Abspielen').'" src="images/circle_forward_icon.png" width="25" style="vertical-align: middle;"/></button>
  <button id="pause-btn"><img alt="Pause" src="images/pause_icon.png" width="25" style="vertical-align: middle;"/></button>
  <button id="next-item"><img alt="'.($lang=='en' ? 'Next' : 'Nächstes Lied').'" src="images/angle_arrow_right_icon.png" width="25" style="vertical-align: middle;"/></button>
  <button id="login-btn">'.($lang=='en' ? 'Login to Apple Music' : 'Bei Apple Music einloggen').'</button>
</div>
</div>
<script src="https://js-cdn.music.apple.com/musickit/v2/musickit.js"></script>
<script>
    document.getElementById(\'login-btn\').addEventListener(\'click\', () => {
      /***
        Returns a promise which resolves with a music-user-token when a user successfully authenticates and authorizes
        https://developer.apple.com/documentation/musickitjs/musickit/musickitinstance/2992701-authorize
      ***/
      if(MusicKit.getInstance().isAuthorized) MusicKit.getInstance().unauthorize();
      //window.location.href = "'.$_SESSION['url'].'";
      window.location.reload();
    });
</script>
';
	}



	public function DeletePlaylistManager() {
		/* Playlist ID löschen

		$this->playlists = $this->api->getAllLibraryPlaylists(50);
		foreach ($this->playlists->items as $playlist) {
			if($playlist->name == 'Playlist-Manager')
				$this->api->unfollowPlaylist($playlist->id); }*/
	}


	public function DeletePlaylist($playlist) {
		/* Playlist ID löschen

		$this->api->unfollowPlaylist($playlist); */
	}

	public function GetPlaylists() {
		/*
			Playlisten darstellen als Tabelle.
		*/

		$this->playlists = $this->api->getAllLibraryPlaylists(50);

		echo '
		<table>';
				

		foreach ($this->playlists->data as $playlist) {
   			 echo '
				<tr>
					<td width="90%"><a href="' . $playlist->href . '" target="_blank">' . $playlist->attributes->name . '</a> <br></td>
					<td width="10%" align="right"><a href="script/applemusic_delete.php?playlist_id='.$playlist->id.'" class="icon solid fa-trash-alt"><span class="label">Delete</span></a>
					</td>
				</tr>
			';
		}

		echo '
		</table>';
	}

}
?>