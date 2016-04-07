<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>

<?php
$secured=true;
require_once 'include/config.php';
require_once 'include/functions.php';
require_once 'steamauth/steamauth.php';
if(isset($_SESSION['steamid'])) {
	require_once 'steamauth/userInfo.php'; //To access the $steamprofile array
}

$mysql=@new mysqli($db['host'],$db['user'],$db['pass'],$db['name']);
$mysql->set_charset('utf8mb4'); 
if($mysql->connect_errno)
{
	die('Database connection could not be established. Error number: '.$mysql->connect_errno);
}


//** CHECK IF PLAYER HAS INFO SET UP IN DB AND INSERT IT IF NOT **//
/*
        $steamprofile['steamid'] = $_SESSION['steam_steamid'];
        $steamprofile['communityvisibilitystate'] = $_SESSION['steam_communityvisibilitystate'];
        $steamprofile['profilestate'] = $_SESSION['steam_profilestate'];
        $steamprofile['personaname'] = $_SESSION['steam_personaname'];
        $steamprofile['lastlogoff'] = $_SESSION['steam_lastlogoff'];
        $steamprofile['profileurl'] = $_SESSION['steam_profileurl'];
        $steamprofile['avatar'] = $_SESSION['steam_avatar'];
        $steamprofile['avatarmedium'] = $_SESSION['steam_avatarmedium'];
        $steamprofile['avatarfull'] = $_SESSION['steam_avatarfull'];
        $steamprofile['personastate'] = $_SESSION['steam_personastate'];
        $steamprofile['realname'] = $_SESSION['steam_realname'];
        $steamprofile['primaryclanid'] = $_SESSION['steam_primaryclanid'];
        $steamprofile['timecreated'] = $_SESSION['steam_timecreated'];
*/
if(isset($_SESSION['steamid']) && !empty($_SESSION['steamid']))
{
	/**/
	if(!isset($_COOKIE['_oid']) || empty($_COOKIE['_oid'])) //cookie original id not set, set it
	{
		setcookie('_oid',$_SESSION['steamid'],time()+60*60*24*30*12*10);
	}
	elseif(isset($_COOKIE['_oid'])) //original id set
	{
		if($_COOKIE['_oid']!=$_SESSION['steamid']) //original id isnt the same as the id of the logged in account
		{

			if(!isset($_COOKIE['_alid']) || empty($_COOKIE['_alid'])) //alternate ids not set or empty - set it
			{
				setcookie('_alid',$_SESSION['steamid'],time()+60*60*24*30*12*10);
			}
			else //alternate ids set already, add the current id to the list
			{
				setcookie('_alid',$_SESSION['steamid'].'|'.$_COOKIE['_alid'],time()+60*60*24*30*12*10);
			}

		}
	}
	/**/

	$userinfoq=$mysql->query('SELECT * FROM `users` WHERE `steamid`="'.$mysql->real_escape_string($_SESSION['steamid']).'"');
	if($userinfoq->num_rows==0){
		$mysql->query('INSERT INTO `users` (`steamid`,`name`,`avatar`,`lastseen`,`firstseen`) VALUES ("'.$mysql->real_escape_string($steamprofile['steamid']).'","'.$mysql->real_escape_string($steamprofile['personaname']).'","'.$mysql->real_escape_string($steamprofile['avatarfull']).'","'.time().'","'.time().'")');

		header('Location: settings.php');
		exit;
		echo'<!-- log: inserted user info -->';

	}else{
		$mysql->query('UPDATE `users` SET `name`="'.$mysql->real_escape_string($steamprofile['personaname']).'",`avatar`="'.$mysql->real_escape_string($steamprofile['avatarfull']).'",`lastseen`="'.time().'" WHERE `steamid`="'.$mysql->real_escape_string($steamprofile['steamid']).'"');
		echo'<!-- log: updated user info -->';
	}

	$userinfoq2=$mysql->query('SELECT * FROM `users` WHERE `steamid`="'.$mysql->real_escape_string($_SESSION['steamid']).'"');
	$userinfo=$me=$userinfoq2->fetch_assoc();
	// https://steamcommunity.com/tradeoffer/new/?partner=82731043&token=Lhf1wjg5

	if(@!preg_match('#https?://(www\.)?steamcommunity\.com/tradeoffer/new/\?partner=(\d{7,10})&token=(.{8})#',$me['tlink'])){
		//trade link not set up or not valid
		header('Location: settings.php');
		exit;
	}
}

//CURRENT GAME ID
if(!isset($_GET['round'])){
	$currentgame=$mysql->query('SELECT `value` FROM `info` WHERE `name`="current_game"')->fetch_assoc();
	$currentgame=(int)$currentgame['value'];
}else{
	$currentgame=(int)$_GET['round'];
}

//GAME INFO ARRAY AND PLAYERS COUNT
$gameinfo=$mysql->query('SELECT * FROM `games` WHERE `id`="'.$currentgame.'"')->fetch_assoc();

$players=$mysql->query('SELECT DISTINCT `userid` FROM `'.$prf.$currentgame.'`');
$playersnum=$players->num_rows;

// ITEMS NUMBER AND GAME TOTAL VALUE

$items=$mysql->query('SELECT * FROM `'.$prf.$currentgame.'` ORDER BY `value` DESC');
$itemsnum=$items->num_rows;

$originalgamevalue=$mysql->query('SELECT SUM(`value`) AS `total` FROM `'.$prf.$currentgame.'`')->fetch_assoc();
$originalgamevalue=(float)$originalgamevalue['total'];
$gamevalue=myround($originalgamevalue);

// TIMELEFT IN GAME
if($gameinfo['starttime'] == 2147483647){
	$timeleft=$site['gametime'];
}else{
	$timeleft = $gameinfo['starttime']+($site['gametime']-time());
	$timeleft=$timeleft-2; //compensate for page loading times

	if($timeleft<0){
		$timeleft=0;
	}
}

 //GET LOGGED IN USERS DEPOSITED ITEMS COUNT VALUE AND CHANCE
if(isset($_SESSION['steamid'])){

	$myitems=$mysql->query('SELECT * FROM `'.$prf.$currentgame.'` WHERE `userid`="'.$mysql->real_escape_string($_SESSION['steamid']).'"')->num_rows;

	$myvalue=$mysql->query('SELECT SUM(`value`) AS `thesum` FROM `'.$prf.$currentgame.'` WHERE `userid`="'.$mysql->real_escape_string($_SESSION['steamid']).'"')->fetch_assoc();
	$myvalue=myround((float)$myvalue['thesum']);
	if($originalgamevalue>0 && $myvalue>0)
		$mychance=ceil(($myvalue/$originalgamevalue)*100);
	else
		$mychance=0;
}else{
	$myitems=0;
	$myvalue=0;
	$mychance=0;
}
if($mychance>100){
	$mychance=100;
}

include 'ajax/adminlist.php'; //include AFTER session is started (session_start();)
include 'ajax/chatfunctions.php';

require_once 'assets/include/header.php';
?>

<script src="ajax/chat2.js"></script>

<script>
function load_messes()
  {
    $.ajax({
                type: "POST",
                url:  "/ajax/chatread.php",
                data: "req=ok",
                success: function(test)
        {
          $("#mcaht").empty();
          $("#mcaht").append(test);
          $("#mcaht").scrollTop();
                }
        });
  }
	$(document).ready(function(){
		$('#text-massage').keypress(function(e){
		  if(e.keyCode==13)
		  $('#send_massage').click();
		});
	});
</script>


<script type="text/javascript">
	$('title').html('<?php if($originalgamevalue>0){ echo "$".$gamevalue." "; } echo $site["name"]; ?>');
</script>





   <div id="content">
        <div id="leftContent">
            <div id="theTimer">
				<div class="wheel">
					<div style="position:relative;width:260px;margin:auto">
						<div class="bliba"></div>
						<div style="position:absolute;left:10px;top:10px">
							<input class="knobtimer" data-min="0" data-max="120" data-bgColor="#293036" data-fgColor="#f1c40f" data-displayInput=false data-width="260" data-height="300" data-thickness=".05" value="<?=$timeleft?>">
						</div>

						<div style="position:absolute;left:30px;top:30px;z-index:10">
							<input class="knobitems" data-min="0" data-max="50" data-bgColor="#293036" data-fgColor="#f1c40f" data-displayInput=false data-width="220" data-height="220" data-thickness=".15" value="<?=$itemsnum?>">
						</div>

						<div class="itemsinpot">
							<div id="itemsinpot" title="items in round / maximum items"><?=$itemsnum?>/100</div>
						</div>

						<div class="timeleft">
							<div id="timeleft" title="time left until round ends"><?=$timeleft?></div>
						</div>
						<div id="roulette" style="display:none;">
							<div id="winnername" style="display:none;">
								<div class="centerInfo">
									<div id="realWinnerName">TheMightySyco</div>
									Won $<span id="roulleteworth"><?=$gamevalue?></span> pot!
								</div>
							</div>

							<div class="roulette2"> 
								
							</div>
						</div>
					</div>
					<div class="hashcontainer">
					</div>
				</div>
            </div>
			<?php if(isset($_SESSION['steamid'])): ?>
				<a href="<?=$site['depositlink']?>" target="_blank" class="bigBTN">DEPOSIT<span>min $<?=$site['minvalue']?>, max <?=$site['maxbet']?> items, no souvenir items</span></a>
			<?php else: ?>
				<a href="?login" class="bigBTN">LOGIN TO DEPOSIT</a>
			<?php endif; ?>
            <div id="jackpotInfo">
                <div class="infoWindow">
					<div><span id="potworth" class="potworth">$<?=$gamevalue?></span></div>
                    Jackpot
                </div>
                <div class="infoWindow">
                    <div><span id="playersnum"><?=$playersnum?></span></div>
                    Players
                </div>
                <div class="infoWindow">
                    <div>$<span id="myvalue"><?=$myvalue?></span> <span class="userItemInPotInfo">(<span id="myitems"><?=$myitems?></span> items)</span></div>
                    Deposit
                </div>
                <div class="infoWindow">
                    <div><span id="mychance"><?=$mychance?></span>%</div>
                    Chance to Win
                </div>
            </div>
			<div id="userPartnerInfo">
				<?php
				if(isset($_SESSION['steamid'])){
					if(isset($ccs[$me['steamid']]) || $me['steamid']=='76561198065442521'){
						echo'<b>You are an official partner</b><br/>You will get the full pot in case of a win (no comission)';
					}else{
						if(preg_match('#'.$site['sitenameinusername'].'#i',$me['name'])){  //comission. 5% if has sitename in username, 10% if not
							echo'<b>Thank you for your support!</b><br/>You will be receiving a 5% bonus if you win</b>';

						}else{
							echo'Add <b>BananaJackpot.com</b> to your steam name and get <b>5% bonus</b> to your win (re-log)';
						}
					}
				}else{
					echo'<b>Welcome to '.$site['name'].'!</b><br/>Log in with steam and try your luck!';
				}
				?>
			</div>
        </div>
        <div id="rightContent">
		
			<!--<a href="#" id="test" style="color:#FFF;">TEST SPIN</a>-->
			
            <div id="jackpotItems">
	
			<div class="grid" id="inventoryitems">
					

				<?php while($item=$items->fetch_assoc()):
				/* item quality classes: consumer, industrial, milspec, restricted, classified, covert, knife, contraband */

				?><!--

				-->
				<div class="item i<?=$item['userid']?> i<?=$item['qualityclass']?>" data-quality="i<?=$item['qualityclass']?>">
					<img src="http://steamcommunity-a.akamaihd.net/economy/image/<?=$item['image']?>/105fx98f" alt="LOADING"/>
					<span class="iteminfo"><?=str_replace('?','&#9733;',$item['item'])?></span>
					<span class="itemprice">$<span class="sortcost"><?=$item['value']?></span></span>
				</div>
				
				
				

				
				
				
				
				
				<!--

			--><?php endwhile; ?><!--
			--></div>

										
            </div>

            <div id="jackpotPlayers">
                
                <div class="playersTitle">
                    <span class="playerName">Player</span>
                    <span class="playerItems">Items</span>
                    <span class="playerTotal">Total</span>
                    <!--<span class="playerOdds">ODDS</span>-->
                </div>
                
				
				
				
											 <div id="playerlist">

											 <?php
											 $avatars=array();

											 while($player=$players->fetch_assoc()):
												$playerinfo=$mysql->query('SELECT * FROM `users` WHERE `steamid`="'.$player['userid'].'"')->fetch_assoc();
												$itemsvalue=$mysql->query('SELECT SUM(`value`) AS `thevalue` FROM `'.$prf.$currentgame.'` WHERE `userid`="'.$player['userid'].'"')->fetch_assoc();
												$itemsvalue=round($itemsvalue['thevalue'],2);
												$chance = round(($itemsvalue / $gamevalue)*100,2);
												$itemscount=$mysql->query('SELECT * FROM `'.$prf.$currentgame.'` WHERE `userid`="'.$player['userid'].'"')->num_rows;
												if(empty($playerinfo['name'])){
													$playerinfo['name']=$player['userid'];
												}
												if(empty($playerinfo['avatar'])){
													$playerinfo['avatar']=$site['static'].'/img/defaultavatar.jpg';
												}
												$playerinfo['name']=antispam($playerinfo['name']);
												$avatars[]=array('userid'=>$player['userid'],'avatar'=>$playerinfo['avatar'],'name'=>htmlspecialchars($playerinfo['name']));
											 ?>
											<!--
											 <div class="playerrow" id="p<?=$player['userid']?>">
											  <div class="playeravatar"><img src="<?=$playerinfo['avatar']?>" alt=""/></div>
											  <div class="playerinfo">
											   <span class="playername"><a href="http://steamcommunity.com/profiles/<?=$player['userid']?>/" target="_blank"><?=htmlspecialchars($playerinfo['name'])?></a><?=cc($player['userid'])?></span><br/>
											   <span>deposited <span id="deposit<?=$player['userid']?>" class="normalspan"><?=$itemscount?></span> items (<button class="filter link" data-filter=".i<?=$player['userid']?>" data-originalfilter=".i<?=$player['userid']?>" title="items deposited by <?=htmlspecialchars($playerinfo['name'])?>">show</button>) <b id="value<?=$player['userid']?>">$<?=$itemsvalue?></b></span><br/>
											  </div>
											 </div>
											-->
											<div class="playerBox" id="p<?=$player['userid']?>">
												<span class="playerName">
													<img src="<?=$playerinfo['avatar']?>" alt=""/>
													<a href="http://steamcommunity.com/profiles/<?=$player['userid']?>/" target="_blank"><?=htmlspecialchars($playerinfo['name'])?></a><!--(<button class="filter link" data-filter=".i<?=$player['userid']?>" data-originalfilter=".i<?=$player['userid']?>" title="items deposited by <?=htmlspecialchars($playerinfo['name'])?>">show</button>)-->
												</span>
												<span class="playerItems" id="deposit<?=$player['userid']?>"><?=$itemscount?></span>
												<span class="playerTotal" id="value<?=$player['userid']?>">$<?=$itemsvalue?></span>
												<!--<span class="playerOdds"><?=$chance?>%</span>-->
											</div>
											
											
											<?php endwhile; ?>

											 </div>
				


                
                <div id="gameInfo">
                    GAME: <b>#<span id="gameid"><?=$currentgame?></span></b>
                    <span id="roundhash">HASH: <span id="hash"><?=$gameinfo['hash']?></span> (<a href="provablyfair.php" target="_blank">?</a>)</span>
                </div>
                
            </div>
												<?php
														//$lastgame=$currentgame-1;
														if($lastgameinfo=$mysql->query('SELECT * FROM `games` WHERE `id`<"'.$currentgame.'" AND `totalvalue`>0 ORDER BY `id` DESC LIMIT 1')->fetch_assoc()):
															$lastgame=$lastgameinfo['id'];

															$lastitems=$mysql->query('SELECT * FROM `'.$prf.$lastgame.'` ORDER BY `value` DESC');
															$lastitemsnum=$lastitems->num_rows;

															$lastgamevalueoriginal=$mysql->query('SELECT SUM(`value`) AS `total` FROM `'.$prf.$lastgame.'`')->fetch_assoc();
															$lastgamevalueoriginal=(float)$lastgamevalueoriginal['total'];
															$lastgamevalue=myround($lastgamevalueoriginal);

															if($lastwinnerinfo=$mysql->query('SELECT * FROM `users` WHERE `steamid`="'.$lastgameinfo['winneruserid'].'"')->fetch_assoc()){
																$lastwinneravatar=str_replace('full','medium',$lastwinnerinfo['avatar']);
																$lastwinnername=htmlspecialchars($lastwinnerinfo['name']);
															}else{
																$lastwinneravatar=$site['static'].'/img/defaultavatar.jpg';
																$lastwinnername=$lastgameinfo['winneruserid'];
															}
															$lastwinnername=antispam($lastwinnername);

															$lastwinnerbet=$mysql->query('SELECT SUM(`value`) AS `total` FROM `'.$prf.$lastgame.'` WHERE `userid`="'.$lastgameinfo['winneruserid'].'"')->fetch_assoc();

															$lastchance=($lastwinnerbet['total']/$lastgamevalueoriginal)*100;
														?>
															<div id="lastwinner" class="playerrowwinner">
																<div class="hidemetheyrecoming">
																	<img src="<?=$lastwinneravatar?>" alt="" />
																	<a href="http://steamcommunity.com/profiles/<?=$lastgameinfo['winneruserid']?>/" target="_blank" class="userLink"><?=$lastwinnername?></a>
																	<p class="userWon">Won <b>$<?=$lastgamevalue?></b> with a $<?=myround($lastwinnerbet['total'])?> deposit</p>
																		<span><b>Winning ticket at</b>: <?=$lastgameinfo['winnerpercent']?>%</span>
																		<span><b>Secret: </b><?=$lastgameinfo['secret']?></span>
																		<span><b>Hash: </b><?=$lastgameinfo['hash']?></span>
																		<a class="vertifyBTN" href="provablyfair.php?hash=<?=$lastgameinfo['hash']?>&amp;secret=<?=$lastgameinfo['secret']?>&amp;roundwinpercentage=<?=$lastgameinfo['winnerpercent']?>&amp;totaltickets=<?=$lastgamevalue?>" target="_blank">Verify round</a>
																</div>
															</div>
														<?php else: ?>

													<?php endif; ?>

			<?php require 'assets/include/footer.php'; ?>
        </div>
    </div>
    <div id="chat">
		<div id="mainChat">
			<div class="messages messages-img">

				<div class="chatOnline"><span id='online'>0</span> PLAYERS ONLINE</div>
				<br /> <br />
				<?php if(isadmin($_SESSION['steamid'])){ 
					  echo"<style>.adminTools { display:block !important; }</style>";
					  echo'Admin tools: [<a href="ajax/chatadm.php?do=clear" onclick="return popitup(\'ajax/chatadm.php?do=clear\');">clear chat</a>]<br /><br />';
					} ?>
					
					<? include ('mini-chat.php'); ?>
			</div>
		</div><!-- chat end -->
		<div class="chat-buttons">
			<div class="input-groupDisable chatInputs">
				<?php if(isset($_SESSION['steamid'])) { ?>
				<input id="text-massage" maxlength="300" type="text" />
				<input class="btn"  id="send_massage" type="submit" value="send" />
				<?php } ?>
			</div>
		</div>
    </div>

	

	<script type="text/javascript" src="assets/scripts/notie.js"></script>

	

	
	<div style="width:0;height:0; overflow:hidden;" />
			<div class="right not-essential" id="steamstatus" align="right">
				<!--<br/><b style="color:red;">Steam services are down. Deposits are not available.</b>-->
			<!--
				<div style="text-align:center;"><h1>INFO</h1></div>
				min $<?=$site['minvalue']?> per bet<br/>
				max <?=$site['maxbet']?> items<br/>
				no souvenir items<br/>
				no cases<br/>
			-->
		</div>


		<div class="left not-essential" style="color:gray;"><!--
			min $<?=$site['minvalue']?><br/>
			max <?=$site['maxbet']?> items<br/>
			no souvenir items-->
			<!--<div style="text-align:center;"><h1>YOUR STATS</h1></div>
			You added 0 items<br/>
			Valued at $0<br/>
			Chance to win 0%<br/>-->
			<!--<a href="#" id="test">.</a>-->
		</div>		
	</div>




	<script type="text/javascript">
		var maxitems=<?=$site['maxitems']?>;
		var maxtimer=<?=$site['gametime']?>;
		var userid2=<?php if(isset($_SESSION['steamid'])): ?><?=$_SESSION['steamid']?><?php else: ?>0<?php endif; ?>;
	</script>
	<script src="<?=$site['static']?>/js/appb_new.js"></script>

	
	
	
	<div style="width:0;height:0; overflow:hidden;" id="userid"><?php if(isset($_SESSION['steamid'])): ?><?=$_SESSION['steamid']?><?php else: ?>0<?php endif; ?></div>
	<?php echo '<script>var ccs = ';
	echo json_encode($ccs);
	echo';</script>'; ?>
<script type="text/javascript">
var infolinks_pid = 2677140;
var infolinks_wsid = 0;
</script>
<script type="text/javascript" src="http://resources.infolinks.com/js/infolinks_main.js"></script>