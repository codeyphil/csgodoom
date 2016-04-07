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
		br {display:none;}
		b {display:inline-block; width:100%;}
	</style>
	<div class="fullWitchContent">
	 <center><div class="title">AWP | BOOM MW:</div></center>
<?php 
$terms='
<center>
<p><b>This is Just and Example:</b>
<img  src="https://i.gyazo.com/315bf717486362b09c32b6e47a6754a1.png" alt="" border="0">
</center>
</p>';

echo nl2br($terms);
?>
</div>
<?php require 'assets/include/footer.php'; ?>