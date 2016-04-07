<?php
$secured=true;
require_once 'include/config.php';
require_once 'include/functions.php';

/*
$url='https://crowbar.steamdb.info/Barney';
$get=disguise_curl($url);

$response=json_decode($get);

if($response->services->store->status=='major' || $response->services->community->status=='major' || $response->services->csgo_community->status=='major'){
	echo'<center><b>WARNING!</b><br/>Steam servers are experiencing issues at the moment! Your offers may be delayed or not go through at all!<br/><br/>';
	echo'Steam store: '.$response->services->store->title.'<br/>';
	echo'Steam community: '.$response->services->community->title.'<br/>';
	echo'CS:GO inventories: '.$response->services->csgo_community->title.'<br/>';
	echo'</center>';
}else{*/
	header('Location: '.$site['depositlink']);
//}