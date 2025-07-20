<?php

/*
Registrierung bei Amazon Music:

Highlight-Concerts GmbH

*/


class Amazon {
	/*
		Amazon Music Playlist Manager API
		Integration der Developer Api von Amazon Music in eine PHP-Klasse.
		Spezielle Anpassungen an die Webseite, also nicht separat verwendbar.
		
		ACHTUNG: BIS JETZT GIBT ES KEINE FREIGABE FÜR AMZON MUSIC WEB API. DIESE IST NICHT VERWENDBAR ABER DOKUMENTIERT!
	*/
	private $playlists = null;
	private $cid = null;
	private $authCode = null;
	private $apiKey = 'amzn1.application-oa2-client.f333052d530644deb8bce1a75eac81b3'; // l.kont@highlgiht-concerts.com
	private $apiKey2 = 'amzn1.application-oa2-client.22ac5034ea45455999dd49680ebb3f02'; // bilkedominic@gmail.com
	private $SET_KEY = 2;

	public function Connect($playlist_id = '', $pure=false) {

	/*
		Verbindung zu Amazon Service
	*/
		echo "<div id=\"amazon-root\"></div>
<a href id=\"LoginWithAmazon\">
    <img border=\"0\" alt=\"Login with Amazon\"
        src=\"https://images-na.ssl-images-amazon.com/images/G/01/lwa/btnLWA_gold_156x32.png\"
        width=\"156\" height=\"32\" />
 </a><br><br>
 <script type=\"text/javascript\">

    var d = null;
    window.onAmazonLoginReady = function() {
      amazon.Login.setClientId('".($this->SET_KEY == 1 ? $this->apiKey : $this->apiKey2)."');
    };
    (function(d) {
      var a = d.createElement('script'); a.type = 'text/javascript';
      a.async = true; a.id = 'amazon-login-sdk';
      a.src = 'https://assets.loginwithamazon.com/sdk/na/login1.js';
      d.getElementById('amazon-root').appendChild(a);
    })(document);

    document.getElementById('LoginWithAmazon').onclick = function() {
       setTimeout(window.doLogin, 1);
       return false;
    };
    var authCode = '';
    var userId = '';
    function doLogin() {
        options = {};
        options.scope = 'profile';
        options.pkce = true;
        window.amazon.Login.authorize(options, function(response) {
            if ( response.error ) {
                alert('oauth error ' + response.error);
            return;
            }
            window.amazon.Login.retrieveToken(response.code, function(response) {
                if ( response.error ) {
                    alert('oauth error ' + response.error);
                return;
                }
		authCode = response.access_token;
                window.amazon.Login.retrieveProfile(response.access_token, function(response) {
                    //alert('Hello, ' + response.profile.Name);
                    //alert('Your e-mail address is ' + response.profile.PrimaryEmail);
                    //alert('Your unique ID is ' + response.profile.CustomerId);
		    userId = response.profile.CustomerId;
                    if ( window.console && window.console.log )
                       window.console.log(response);
		    ".($pure ? 'if(userId) var w = window.open("'.$playlist_id.'", "_blank");' : '')."

	/*
		ajax request für Benutzer und Playlisten
	*/";

if(!$pure)
echo "
$.ajax({
        type: 'POST',
        url: '".($this->SET_KEY == 1 ? 'https://playlist-manager.de/script/amazon_web.php' : 'https://playlist-manager.de/script/amazon_web2.php')."',
	//url: 'https://music-api.amazon.com/',
	data: {'authCode': authCode},
        dataType: 'json',
        success: function(data) {
		console.log(data);
		d = data;
		let comboBox = document.getElementById('amazon_playlist_id');
    		while (comboBox.options.length > 2) {                
        		comboBox.remove(2);
    		}        
		//for (let p in data.data.user.playlists.edges) {
		for (let i = 0; i < data.data.user.playlists.edgeCount; i++) {
			let p = data.data.user.playlists.edges[i];
			let newOption = new Option(p.node.title,p.node.id);
			let select = document.getElementById('amazon_playlist_id'); 
			select.add(newOption);
			if(newOption.value==='".$playlist_id."') newOption.selected=true;
		}
		}
    });";
echo "
                });
            });
        });


   }; 

";

if(!$pure)
if(isset($_SESSION['amazon_authCode']) && $_SESSION['amazon_authCode']) 
echo "
$.ajax({
        type: 'POST',
        url: '".($this->SET_KEY == 1 ? 'https://playlist-manager.de/script/amazon_web.php' : 'https://playlist-manager.de/script/amazon_web2.php')."',
	data: {'authCode': '".$_SESSION['amazon_authCode']."'
},
        dataType: 'json',
        success: function(data) {
		console.log(data);
		d = data;
		//for (let p in data.data.user.playlists.edges) {
		for (let i = 0; i < data.data.user.playlists.edgeCount; i++) {
			let p = data.data.user.playlists.edges[i];
			let newOption = new Option(p.node.title,p.node.url);
			let select = document.getElementById('amazon_playlist_id'); 
			select.add(newOption);
			if(newOption.value==='".$playlist_id."') newOption.selected=true;
		}
		}
    });";
echo "
   //setTimeout(window.doLogin, 3000);
</script>";
		
	}


	public function de_DisplayCreatePlaylist() {
		/*
			Zeige Deutsches Formular zur Erstellung von Playlisten.
		*/

		$new_playlist_id = $_GET['amazon_playlist_id'];
		echo '

		<label for="amazon_playlist_id">Wählen Sie eine Amazon-Music-Wiedergabeliste aus:</label>
		<select name="amazon_playlist_id" id="amazon_playlist_id">
			<option value="'.$new_playlist_id.'"> -- AKTUELLE PLAYLIST -- </option>
			<option value=""> -- KEINE PLAYLIST -- </option>
		</select><br>';
		$this->Connect($new_playlist_id);
		echo '
			<script>
			let select2 = document.getElementById("amazon_playlist_id"); 
			for(let i=2; i<select2.options.length;i++)
				if(select2.options[i].value==="'.$new_playlist_id.'") select2.options[i].selected=true;
		     	</script>';
	}

	public function DisplayCreatePlaylist() {
		/*
			Zeige Englisches Formular zur Erstellung von Playlisten.
		*/

		$new_playlist_id = $_GET['amazon_playlist_id'];
		echo '
		<label for="amazon_playlist_id">Choose a Amazon-Music-Playlist:</label>
		<select name="amazon_playlist_id" id="amazon_playlist_id">
			<option value="'.$new_playlist_id.'"> -- CURRENT PLAYLIST -- </option>
			<option value=""> -- NO PLAYLIST -- </option>
		</select><br>';
		$this->Connect($new_playlist_id);
		echo '
			<script>
			let select2 = document.getElementById("amazon_playlist_id"); 
			for(let i=2; i<select2.options.length;i++)
				if(select2.options[i].value==="'.$new_playlist_id.'") select2.options[i].selected=true;
		     	</script>';
	}

public function  Playlist($playlist_id) {
	$this->Connect($playlist_id, TRUE);
}

	public function Playlist2($playlist_id) {
		/*
			Abspielen der Playlisten und Darstellung als Plugin.
		*/
		echo '
			<!-- Der Iframe Container -->
			<script>
				//var w = window.open("'.$playlist_id.'", "_blank");
				//w.mute = true;
				let ec = null; //EmbedController
				let ecplay = false; // ist gerade Spielzeit?
				let datplay = new Date(); // es wird gespielt bis
				let datpause = new Date(); // es wird Pause gemacht bis

					
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
						//w.mute = true;
						console.log(\'pause!\');
						ecplay = false;
					}

					// Spielen mit Algorithmus
					if(!ecplay && \''.$_SESSION["days"].'\'.indexOf(d) >= 0 && datfrom.getTime() < dat.getTime() && dat.getTime() < datto.getTime() && dat.getTime() > datpause.getTime()) {
						let rand_play = Math.floor(Math.random() * 600) + 61;
						datplay = new Date();
						datplay.setSeconds(datplay.getSeconds() + rand_play);
						//w.mute = false;
						ecplay = true;
						console.log(\'play for \'+rand_play+\' seconds\');
					}
					
					// Pause mit Algorithmus
					if(ecplay && \''.$_SESSION["days"].'\'.indexOf(d) >= 0 && datfrom.getTime() < dat.getTime() && dat.getTime() < datto.getTime() && dat.getTime() > datplay.getTime()) {
						let rand_pause = Math.floor(Math.random() * 600) + 61;
						datpause = new Date();
						datpause.setSeconds(datpause.getSeconds() + rand_pause);
						//document.getElementById("divapi").style.display="none";
						console.log(\'pause for \'+rand_pause+\' seconds\');
						ecplay = false;
					}

					// Pause nach der gewünschten Zeit
					if(ecplay && \''.$_SESSION["days"].'\'.indexOf(d) >= 0 && dat.getTime() > datto.getTime()) {
						//document.getElementById("divapi").style.display="none";
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
			</script>
			</div>';

	}



}
?>