<?php
$secured=true;
require 'include/config.php';
require 'include/functions.php';
require 'steamauth/steamauth.php';
if(isset($_SESSION['steamid'])) {
	require 'steamauth/userInfo.php'; //To access the $steamprofile array
}

$mysql=@new mysqli($db['host'],$db['user'],$db['pass'],$db['name']);

if($mysql->connect_errno)
{
	die('Database connection could not be established. Error number: '.$mysql->connect_errno);
}

require 'assets/include/header.php';

?>
	<style>
		html,body {
			overflow:auto;
		}
	</style>
	<div class="fullWitchContent">
	
		<div class="title">PARTNERS</div>
			<h1>Youtube/Twitch partners:</h1>
				<table>
					<tr>
						<td align="center" style="vertical-align: middle;">
							<img src="http://findicons.com/files/icons/282/flags/16/united_states_of_america.png" width="15px" class="flagimg" alt=""/> <b>Orishiti</b>
						</td>
					</tr>
					<tr>
						<td align="center">
							<br />
							<a href="http://www.twitch.tv/oriditi" target="_blank"><img src="http://i.imgur.com/9afBRMs.png" width="150px" class="partnerimg" alt=""/></a>
						</td>
					</tr>
				</table>
		<br />
		<br />
		<div class="title">How do I get partnered/sponsored?</div>
			<h1>Partnership</h1>
				<p>If you are a content creator (youtuber/twitch streamer) we can promote your channel on this page and in the site's sidebar (main page) in exchange for promoting our website (<?=$site['name']?>) on your channel. For more information about this contact us by <a href="https://csgodoom.freshdesk.com/support/tickets/new">opening a ticket</a>.</p>
			
	</div>
	
	

<?php require 'assets/include/footer.php'; ?>