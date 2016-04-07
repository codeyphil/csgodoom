<?php
$secured=true;
require_once 'include/config.php';
require_once 'include/functions.php';
require_once 'steamauth/steamauth.php';

$mysql=@new mysqli($db['host'],$db['user'],$db['pass'],$db['name']);

if($mysql->connect_errno)
{
	die('Database connection could not be established. Error number: '.$mysql->connect_errno);
}

$currentgame=$mysql->query('SELECT `value` FROM `info` WHERE `name`="current_game"')->fetch_assoc();
$currentgame=(int)$currentgame['value'];

$lastgameinfo=$mysql->query('SELECT * FROM `games` WHERE `id`<"'.$currentgame.'" AND `totalvalue`>0 ORDER BY `id` DESC LIMIT 1')->fetch_assoc();
$lastgame=(int)$lastgameinfo['id'];

$lastitems=$mysql->query('SELECT * FROM `'.$prf.$lastgame.'` ORDER BY `value` DESC');

if($lastwinnerinfo=$mysql->query('SELECT * FROM `users` WHERE `steamid`="'.$lastgameinfo['winneruserid'].'"')->fetch_assoc()){
	$lastwinneravatar=$lastwinnerinfo['avatar'];
	$lastwinnername=htmlspecialchars($lastwinnerinfo['name']);
}else{
	$lastwinneravatar=$site['static'].'/img/defaultavatar.jpg';
	$lastwinnername=$lastgameinfo['winneruserid'];
}

$lastwinnerbet=$mysql->query('SELECT SUM(`value`) AS `total` FROM `'.$prf.$lastgame.'` WHERE `userid`="'.$lastgameinfo['winneruserid'].'"')->fetch_assoc();
//$lastchance=($lastwinnerbet['total']/$lastgamevalueoriginal)*100;

$aplayerq=$mysql->query('SELECT DISTINCT `userid` FROM `'.$prf.$lastgame.'`'); //fuck infinite loops bro

$images=array();
while($aplayer=$aplayerq->fetch_assoc())
{
	if(!$moreinfoonthisplayer=$mysql->query('SELECT avatar,username,SUM(value) as totalvalue FROM `'.$prf.$lastgame.'` WHERE userid="'.$aplayer['userid'].'"')->fetch_assoc()){
		echo $mysql->error;
	}
	if($aplayer['userid']!=$lastwinnerinfo['steamid'])
		$images[]='<img src="'.$moreinfoonthisplayer['avatar'].'" alt="'.$i.'"/>'."\n";
}

shuffle($images);
$countimages=count($images);


$images[]='<img src="'.$lastwinneravatar.'"/>'."\n";
$stringimages=implode($images);

	echo'<script type="text/javascript" src="static/js/roulette/roulette.min.js"></script>';

	echo'<div class="roulette" style="display:none;">';
	echo $stringimages;
	echo'</div>';

	echo'<span id="messagedisplay"></span></div>';

	echo"<script type=\"text/javascript\">

		var option = {
		  speed : 15,
		  duration : 3,
		  stopImageNumber : ".$countimages.",
		  startCallback : function() {
		    console.log('start');
		  },
		  slowDownCallback : function() {
		    console.log('slowDown');
		  },
			stopCallback : function($stopElm) {
				console.log('stop');
				$('#winnername').fadeIn(500);
				$('.hidemetheyrecoming').show(1000);
				setTimeout(function(){
					$('#roulette').fadeOut(500);
				},6000);
			}
		}

		$('div.roulette').roulette(option); 


		// START!
		$('.start').click(function(){
		  $('div.roulette').roulette('start');  
		});

		// STOP!
		$('.stop').click(function(){
		  $('div.roulette').roulette('stop'); 
		});

		</script>
		<script type=\"text/javascript\">
		function startroulette(){
			$('div.roulette').roulette('start');  
		}
		startroulette();
		</script>
	";
?>