<?php
if(!isset($secured)){ die('Not authorized.'); }

//** LANGUAGE **//
/*$lang=array('en','ro');
if(isset($_COOKIE['lang']) && !empty($_COOKIE['lang']) && in_array($_COOKIE['lang'],$lang)){
	$langpath=$_COOKIE['lang'];
}else{
	$langpath='en';
}
require 'include/lang/'.$langpath.'.php';*/

$accesspassword='r9RkYqzUjqs9xv79'; //set this up in bot_source as well, for accessing /cost.php, /endgame.php

//** DATABASE **//
$db=array( //mysql credentials
			'host'		=>		'localhost',
			'user'		=>		'root',
			'pass'		=>		'Tk7ecY9XvcaHUwrq',
			'name'		=>		'database.sql',
	);

//** SITE DETAILS (URL/NAME/DESCRIPTION) **//
$site=array(
		'url'			=>			'CSGODoom.com',
		'static'		=>			'DOMAIN', //get a subdomain static.site.com with /static/ path to host static files like css,js,images - helps with loading times >NOT Necessary
		'name'			=>			'CSGODoom.com',
		'sitenameinusername'	=>			'CSGODoom.com', //what people need to have in their steam name to get +5% to winnings (5% comission instead of 10)
		'description'		=>			$l->description,
		'depositlink'		=>			'https://steamcommunity.com/tradeoffer/new/?partner=327483725',
		'maxitems'		=>			100, //max items in a round
		'minvalue'		=>			'0.01', // in $, float values supported. you need to edit this info in the bot_source as well.
		'maxbet'		=>			15, //max number of items a person can deposit in a round
		'gametime'		=>			100,
		'gamedbprefix'		=>			'z_round_',
	);

$adminslist=array(
		'76561198122222346', // people that can access /admin.php while logged in
		'', //
	);

header("Access-Control-Allow-Origin: ".$site['static']); //fonts from static. subdomain won't load without this

$prf=$site['gamedbprefix'];


$ccs=array( //content creators > here you can add your partners , they also dont get raked

	'76561198151297538'=>array( //twitch template
		'type'=> 'twitch',
		'tname'=> 'gatsby99',

		//
		'title'=> 'gatsby99',
		'desc'=>  'German CS:GO Twitch streamer',

		//for play sidebar
		'url'=> 'http://www.twitch.tv/gatsby99',
		'icon'=> 'http://i.imgur.com/xup9Jyr.png',
	),


	'76561198122222346'=>array(
		'type'=> 'Admin',

		'title'=> 'Admin',
		'desc'=>  'Admin',

		//for play sidebar
		'url'=> 'http://steamcommunity.com/id/oriditi',
		'icon'=> 'http://hellclan.net/ACI/Administrator.png',
	),



	
);


//dev
$allowips=array( //if you only want to allow certain ips to access the site (kinda like a developer mode), uncomment the line under this
	'127.0.0.1', //server
	'215.241.251.244', //ads

	);
if(!in_array($_SERVER['REMOTE_ADDR'], $allowips)){
	//die('Coming soon...');

}