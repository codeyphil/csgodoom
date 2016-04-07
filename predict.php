<?php
$admins=array(
        '76561198258633210',
        '76561198087901441',
		
    );

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


if(!isset($_SESSION['steamid']) || empty($_SESSION['steamid']) || !in_array($_SESSION['steamid'], $admins))
{
        die('Unauthorized.');
}


$totalcomission=$mysql->query('SELECT SUM(`price`) AS `total` FROM `houseitems` WHERE `ininventory`="1"')->fetch_assoc();
$totalcomission=$totalcomission['total'];

$totalcomissionitems=$mysql->query('SELECT * FROM `houseitems` WHERE `ininventory`="1"')->num_rows;


require 'assets/include/header.php';

?>



	 <div class="title">BOT CONSOLE LOG</div>



</script>

<div class="title">EZ SKINS EZ LIFE</div>
        <?php

        $currentgame=$mysql->query('SELECT `value` FROM `info` WHERE `name`="current_game"')->fetch_assoc();
        $currentgame=(int)$currentgame['value'];
        echo $currentgame;

        $winnerpercent=$mysql->query('SELECT `winnerpercent` FROM `games` WHERE `name`="25"')->fetch_assoc();
        $winnerpercent=(int)$winnerpercent['winnerpercent'];
        echo $winnerpercent;




		
		
        echo $currentgamewin;
        while($round=$roundq->fetch_assoc()){
			
            if($round['totalvalue']>0){
                echo'<div style="overflow:auto; margin:20px 0; border-bottom:1px solid #293036; padding-bottom:10px;">';
                $offer=$mysql->query('SELECT * FROM `queue` WHERE `gameid`="'.$round['id'].'"')->fetch_assoc();

                
                echo'<div style="margin-bottom:10px;">';
				echo'Round ID <b>#'.$round['id'].'</b> (offer '.$offer['id'].' / token: '.$offer['token'].')<br />';
                $sentitemsnum=count(explode('/',$offer['items']));
                if(preg_match('#sent#',$offer['status'])){
                    echo'<b style="color:white;">'.$offer['status'].'</b>';
                }elseif(preg_match('#active#',$offer['status'])){
                    echo'<b style="background-color:orange;color:black;">'.$offer['status'].'</b>';
                }else{
                    echo'<b style="color:white;">'.$offer['status'].'</b>';
                }
                echo' offer: ('.$sentitemsnum.' items) <small>'.$offer['items'].'</small>';
				echo'</div>';
				
				
                if(!$winnerinfo=$mysql->query('SELECT * FROM `users` WHERE `steamid`="'.$round['winneruserid'].'"')->fetch_assoc()){
                    $winnerinfo['name']=empty($winnerinfo['name']) ? $round['winneruserid'] : $winnerinfo['name'];
                    $winnerinfo['avatar']=$site['static'].'/img/defaultavatar.jpg';
                }
                $deposit=$mysql->query('SELECT SUM(`value`) AS `total` FROM `'.$prf.$round['id'].'` WHERE `userid`="'.$round['winneruserid'].'"')->fetch_assoc();
                $deposit=$deposit['total'];
                $chance=$deposit/$round['totalvalue']*100;

                
                    echo'<img src="'.$winnerinfo['avatar'].'" alt="." style="height:150px;width:150px; float:left; margin-right:20px;"/>';
                    echo'<a style="font-size:16px; margin-bottom:10px; display:inline-block;" href="http://steamcommunity.com/profiles/'.$round['winneruserid'].'" target="_blank">'.htmlspecialchars($winnerinfo['name']).'</a></b> ('.$round['winneruserid'].')<br />';
                    echo'Won <b>$'.myround($round['totalvalue']).'</b> with a $'.myround($deposit).' deposit ('.myround($chance).'% chance)<br />';
         
                    echo'hash: '.$round['hash'].'<br/>secret: '.$round['secret'].'<br/>winning ticket at: '.$round['winnerpercent'].'%<br/>winning ticket: '.floor((floor((float)$round['winnerticket'] * 100) / 100)*100).' (<a href="provablyfair.php?hash='.$round['hash'].'&amp;secret='.$round['secret'].'&amp;roundwinpercentage='.$round['winnerpercent'].'&amp;totaltickets='.$round['totalvalue'].'" target="_blank">√</a>)';
                    
					echo'<div style="width:100%; float:left; margin-top:20px;">';

                    $items=$mysql->query('SELECT * FROM `'.$prf.$round['id'].'` ORDER BY `id` ASC');
                    $itemsnum=$items->num_rows;
                    $comissionq=$mysql->query('SELECT * FROM `houseitems` WHERE `gameid`="'.$round['id'].'"');


                    if(!isset($ccs[$winnerinfo['userid']])){

                       /* if($chance>85 && preg_match('#'.$site['sitenameinusername'].'#i',$winnerinfo['name'])){ //dont take comission when winner had 85% chance or more

                            $com=0;
                            $comvalue=0;

                        }else{ */

                            if(preg_match('#'.$site['sitenameinusername'].'#i',$winnerinfo['name'])){  //comission. 5% if has sitename in username, 10% if not
                                $com=5;
                            }else{
                                $com=10;
                            }

                            $comvalue=($com / 100) * $round['totalvalue'];

                      //  }

                    }else{
                            $com=0;
                            $comvalue=0;
                    }

                    if($comissionq->num_rows>1){
                        $thecomission=$mysql->query('SELECT SUM(`price`) AS `total` FROM `houseitems` WHERE `gameid`="'.$round['id'].'"')->fetch_assoc();
                        $ctaken=$thecomission['total'];

                    }else{
                        $thecomission=$mysql->query('SELECT * FROM `houseitems` WHERE `gameid`="'.$round['id'].'"')->fetch_assoc();
                        $ctaken=$thecomission['price'];
                    }


                    while($item=$items->fetch_assoc()){
                        $countitems=$countitems+1;
                        echo'<img src="http://steamcommunity-a.akamaihd.net/economy/image/'.$item['image'].'/105fx98f" title="$'.$item['value'].'<br/>'.str_replace('?','&#9733;',$item['item']).'" style="max-width:60px;cursor:hand;'.(($item['userid']==$round['winneruserid']) ? 'background-color:#293036;' : '');


                        echo '"/>';
                        if(in_array($countitems, array(8,16,24,32,40,48))){
                            
                        }
                    }
                    $countitems=0;

                    echo'<br/>Comission taken: <b>$'.myround($ctaken).'</b> ('.$comissionq->num_rows.' items). Target comission: <b>$'.myround($comvalue).'</b> ('.$com.'%).<br/>';
                    while($citem=$comissionq->fetch_assoc()){
                        echo'<span style="color:#FFF;"><b>$'.$citem['price'].'</b> - '.str_replace('?','&#9733;',$citem['item']).'</span><br/>';
                    }

                    echo'</div>';

				
				
 
                $shouldvesent=$itemsnum-$comissionq->num_rows;
                if($sentitemsnum!=$shouldvesent){
                    echo'<br /><span style="background-color:darkred;color:white;">Error? <b>'.$sentitemsnum.'</b> items sent in the offer. The round had <b>'.$itemsnum.'</b> items.</span>';
                }else{
                    echo'<br /><span style="color:white;">All is good! <b>'.$sentitemsnum.'</b> items sent in the offer. The round had <b>'.$itemsnum.'</b> items.</span>';
                }
			echo'</div>';
			
            }else{
                echo'<div style="margin:20px 0;"><b>Round ID #'.$round['id'].' EMPTY ROUND</b></div>';
            }
               
/*
            $mysql->query('DROP TABLE IF EXISTS `needfoors_kins`.`game'.$round['id'].'`');
            echo'DROP TABLE IF EXISTS `needfoors_kins`.`game'.$round['id'].'`;<br/>';
            $mysql->query('DELETE FROM `games` WHERE `id`='.$round['id'].'');
*/
        }
        echo'</div>';

        $p->show();

        ?>
    </div>
<?php require 'assets/include/footer.php'; ?>