<?php
$secured=true;
require 'include/config.php';
require 'include/functions.php';
require 'steamauth/steamauth.php';
if(isset($_SESSION['steamid'])) {
	require 'steamauth/userInfo.php'; //To access the $steamprofile array
}

$mysql=@new mysqli($db['host'],$db['user'],$db['pass'],$db['name']);
$mysql->set_charset('utf8mb4'); 

if($mysql->connect_errno)
{
	die('Database connection could not be established. Error number: '.$mysql->connect_errno);
}

require 'assets/include/header.php';

if(isset($_SESSION['steamid']) && !empty($_SESSION['steamid']))
{
    $userinfoq=$mysql->query('SELECT * FROM `users` WHERE `steamid`="'.$mysql->real_escape_string($_SESSION['steamid']).'"');
    $userinfo=$me=$userinfoq->fetch_assoc();
    $tlink=$me['tlink'];
}else{
    header('Location:index.php');
    exit;
}


?>
	<style>
		html,body {
			overflow:auto;
		}
	</style>
	<div class="fullWitchContent">
	 <div class="title">TRADE URL</div>
<p>We require you to set up a valid trade link in order to use the site. This is used to send you the items when you win a round.<br/>
            You can get your steam trade url by following this link: <a href="http://steamcommunity.com/id/me/tradeoffers/privacy#trade_offer_access_url" target="_blank">http://steamcommunity.com/id/me/tradeoffers/privacy</a>
            </p> 
	 
<?php
            if(isset($_POST['tlink']) && !empty($_POST['tlink'])){
                $tlink=$mysql->real_escape_string($_POST['tlink']);
                if(preg_match('#https?://(www\.)?steamcommunity\.com/tradeoffer/new/\?partner=(\d{7,10})&token=(?P<newtoken>.{8})#',$tlink,$match)){
                    if($mysql->query('UPDATE `users` SET `tlink`="'.$tlink.'" WHERE `steamid`="'.$mysql->real_escape_string($_SESSION['steamid']).'"')){
                        $me['tlink']=$tlink;
                        $newtoken=$match['newtoken'];

                        echo'<div class="notice success">The trade link was successfully updated!<br/>You will be redirected to the main page in 5 seconds.</div>';
                        echo'<meta http-equiv="refresh" content="7;URL=\''.$site['url'].'\'" />';

                        if(!$mysql->query('UPDATE `queue` SET `token`="'.$mysql->real_escape_string($newtoken).'" WHERE `userid`="'.$mysql->real_escape_string($_SESSION['steamid']).'" AND `status`="active"')){
                            echo'<div class="notice error">Unknown error while updating trade offers. Contact an administrator!</div>';
                        }
                    }else{
                        echo'<div class="notice error">Unknown error. Try again or contact an administrator!</div>';
                    }
                }else{
                    echo'<div class="notice error">The link you provided seems to be invalid. Try again or contact an administrator!</div>';
                }
            }            
?>


        <form method="post" action="settings.php" class="form">
            <input type="text" name="tlink" value="<?=htmlspecialchars($me['tlink'])?>"/>
            <input type="submit" name="submit" value="Save"/>
        </form>
        <small color="lightgray" style="color:lightgray">If previously you set an invalid trade link and you won a round but not received the winnings, updating this link to the correct one will update the state of your offer.</small>


    </div>
<?php require 'assets/include/footer.php'; ?>