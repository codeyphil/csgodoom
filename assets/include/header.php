<?php if(!isset($secured)){ die('Not authorized.'); } ?>

<html>
 <head>

	<!-- meta -->
	<meta charset="utf-8"/>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="Content-Script-Type" content="text/javascript" />
	<meta name="description" content="<?=$l->description?>"/>

	<title><?=$site['name']?></title>

	<!-- jquery -->
	<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script> 

	<!-- tipTip -->
	<script type="text/javascript" src="<?=$site['static']?>/js/tipTip/jquery.tipTip.js"></script>

	<!-- isotope -->
	<script type="text/javascript" src="<?=$site['static']?>/js/isotope/isotope.pkgd.min.js"></script>
	<script type="text/javascript" src="<?=$site['static']?>/js/isotope/horizontal.js"></script>

	<!-- knob -->
	<!--[if IE]><script type="text/javascript" src="<?=$site['static']?>/js/knob/excanvas.js"></script><![endif]-->
	<script type="text/javascript" src="<?=$site['static']?>/js/knob/jquery.knob.min.js"></script>

	<!-- scrollbar -->
	<script type="text/javascript" src="<?=$site['static']?>/js/scrollbar/jquery.scrollbar.min.js"></script>
	
	<!-- particles.js 
	<script type="text/javascript" src="<?=$site['static']?>/js/particles/particles.min.js"></script>
	<script type="text/javascript" src="<?=$site['static']?>/js/particles/loadparticles.js"></script>
	-->
	<!-- pace -->
	<script type="text/javascript" src="<?=$site['static']?>/js/pace/pace.min.js"></script>
	<script src='https://cdn.rawgit.com/admsev/jquery-play-sound/master/jquery.playSound.js'></script>
	<!-- roulette -->
	<!--<script type="text/javascript" src="<?=$site['static']?>/js/roulette/roulette.min.js"></script>-->
	
	<!-- socket.io -->
	<script src="https://cdn.socket.io/socket.io-1.3.5.js"></script>

	<!-- -->
	<script type="text/javascript">
	$(document).ready(function(){
		$('[title]').tipTip({maxWidth:"350px"});
	    	$('.scrollbar-inner').scrollbar();
	});
    	</script>
		
	<script src="assets/scripts/jquery.mCustomScrollbar.js"></script>
	<script src="assets/scripts/jquery.sidr.js"></script>
	<script>
	$(document).ready(function(){
		$("#jackpotItems").mCustomScrollbar({
			axis:"x",
			autoHideScrollbar: true,
			theme: "minimal-dark",
			advanced:{autoExpandHorizontalScroll:true}
		});
		$(".messages").mCustomScrollbar({
			autoHideScrollbar: true,
			theme: "minimal-dark",
		});
		$("#content").mCustomScrollbar({
			autoHideScrollbar: true,
			theme: "minimal-dark",
		});
		
	$("#mobNavToggle").sidr({
      name: 'sidr-right',
	  side: 'right',
      source: '.headerRightSide, #nav'
    });
	
	
	});
	</script>

	<!-- css -->
	<link rel="stylesheet" type="text/css" href="<?=$site['static']?>/css/tipTip.css"/>
	<link rel="stylesheet" type="text/css" href="<?=$site['static']?>/css/pace.css"/>
	<link rel="stylesheet" type="text/css" href="assets/styles/main.css"/>
	<link href="assets/styles/main.css" media="screen" rel="stylesheet" type="text/css">
	<link href="assets/styles/jquery.mCustomScrollbar.css" media="screen" rel="stylesheet" type="text/css">
	<link href="assets/styles/jquery.sidr.light.css" media="screen" rel="stylesheet" type="text/css">
	
	<!-- favicon(sss) -->
	<?php /* http://realfavicongenerator.net/ */ ?>
	<link rel="apple-touch-icon" sizes="57x57" href="<?=$site['static']?>/favicon/apple-touch-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="60x60" href="<?=$site['static']?>/favicon/apple-touch-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="72x72" href="<?=$site['static']?>/favicon/apple-touch-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="76x76" href="<?=$site['static']?>/favicon/apple-touch-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="<?=$site['static']?>/favicon/apple-touch-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="<?=$site['static']?>/favicon/apple-touch-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="<?=$site['static']?>/favicon/apple-touch-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="<?=$site['static']?>/favicon/apple-touch-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="<?=$site['static']?>/favicon/apple-touch-icon-180x180.png">
	<link rel="icon" type="image/png" href="<?=$site['static']?>/favicon/favicon-32x32.png" sizes="32x32">
	<link rel="icon" type="image/png" href="<?=$site['static']?>/favicon/android-chrome-192x192.png" sizes="192x192">
	<link rel="icon" type="image/png" href="<?=$site['static']?>/favicon/favicon-96x96.png" sizes="96x96">
	<link rel="icon" type="image/png" href="<?=$site['static']?>/favicon/favicon-16x16.png" sizes="16x16">
	<link rel="manifest" href="<?=$site['static']?>/favicon/manifest.json">
	<meta name="msapplication-TileColor" content="#ffc40d">
	<meta name="msapplication-TileImage" content="<?=$site['static']?>/favicon/mstile-144x144.png">
	<meta name="theme-color" content="#ffffff">
<!-- Histats.com  START (hidden counter)-->
<script type="text/javascript">document.write(unescape("%3Cscript src=%27http://s10.histats.com/js15.js%27 type=%27text/javascript%27%3E%3C/script%3E"));</script>
<a href="http://www.histats.com" target="_blank" title="" ><script  type="text/javascript" >
try {Histats.start(1,3207040,4,0,0,0,"");
Histats.track_hits();} catch(err){};
</script></a>
<noscript><a href="http://www.histats.com" target="_blank"><img  src="http://sstatic1.histats.com/0.gif?3207040&101" alt="" border="0"></a></noscript>
<!-- Histats.com  END  -->
 </head>
 <body id="sidebar">
 
 
 
 <!--<div id="particles-js" style="position:fixed;top:0;width:100%;height:100%;"></div>-->
 
 
   <div id="header">
        <div id="mainHeader">
            <a href="http://www.csgodoom.com" id="logo"><img src="assets/images/logo.png" alt="logo" /></a>
            <div id="nav">
                <a href="http://www.csgodoom.com">Play</a>
                <a href="history.php">history</a>
                <a href="ranking.php">leaderboard</a>
				<a href="giveaways.php">giveaways</a>
				<a href="https://steamcommunity.com/groups/csgodoomjckpt" target="_blank">steam group</a>
            </div>
        </div>
		<div id="mobNavToggle"></div>
        <div class="headerRightSide">
			<?php if(isset($_SESSION['steamid'])): ?>
				<div id="userPanel">
					<a href="settings.php" title="Edit Settings"><img src="<?=$steamprofile['avatarfull']?>" alt=""/></a>
					<div class="UserPanelInfo">
						<a href="settings.php" class="username"><?=$steamprofile['personaname']?></a>
						<a href="steamauth/logout.php" class="logout">logout</a>
					</div>
				</div>
			<?php else: ?>
				<div id="steamLogIn">
					<?=steamlogin()?>
				</div>
			<?php endif; ?>
		</div>
		
    </div>
	
