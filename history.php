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

?>
	<style>
		html,body {
			overflow:auto;
		}
	</style>
	<div class="fullWitchContent">
	 <div class="title">ROUND HISTORY</div>
        <?php

        $currentgame=$mysql->query('SELECT `value` FROM `info` WHERE `name`="current_game"')->fetch_assoc();
        $currentgame=(int)$currentgame['value'];

        $total=$mysql->query('SELECT * FROM `games` WHERE `id`<"'.$currentgame.'" AND `id`>1000')->num_rows;

        $page=(isset($_GET['page']) && !empty($_GET['page']) && $_GET['page']>0 && $_GET['page']<$total) ? (int)$_GET['page'] : 1;
        $perpage=15;
        $roundq=$mysql->query('SELECT * FROM `games` WHERE `id`<"'.$currentgame.'" AND `id`>1000 ORDER BY `id` DESC LIMIT '.(($page-1)*$perpage).','.$perpage);


        //http://mis-algoritmos.com/digg-style-pagination-class
        $p = new pagination;
        $p->Items($total);
        $p->limit($perpage);
        $p->currentPage($page);
        $p->nextLabel('');//removing next text
        $p->prevLabel('');//removing previous text
        $p->nextIcon('&#9658;');//Changing the next icon
        $p->prevIcon('&#9668;');//Changing the previous icon

        $p->show();

        echo'<div id="historyTable">';
        while($round=$roundq->fetch_assoc()){
            if($round['totalvalue']>0){
				
				
				echo'<div class="historyItem">';
					
					if(!$winnerinfo=$mysql->query('SELECT * FROM `users` WHERE `steamid`="'.$round['winneruserid'].'"')->fetch_assoc()){
						$winnerinfo['name']=empty($winnerinfo['name']) ? $round['winneruserid'] : $winnerinfo['name'];
						$winnerinfo['avatar']=$site['static'].'/img/defaultavatar.jpg';
					}
					echo'<img class="historyPic" src="'.$winnerinfo['avatar'].'" alt="." style="height:120px;width:120px" />';
					$deposit=$mysql->query('SELECT SUM(`value`) AS `total` FROM `'.$prf.$round['id'].'` WHERE `userid`="'.$round['winneruserid'].'"')->fetch_assoc();
					$deposit=$deposit['total'];
					$chance=$deposit/$round['totalvalue']*100;

    
                    echo'<div class="historyInfo"><b class="historyID">Round ID #'.$round['id'].'</b><br />';
                    echo'<b><a class="HistoryUserLink" href="http://steamcommunity.com/profiles/'.$round['winneruserid'].'" target="_blank">'.htmlspecialchars(antispam($winnerinfo['name'])).cc($winnerinfo['steamid']).'</a></b><br />';
                    echo'<span class="HistoryWon">Won <b>$'.myround($round['totalvalue']).'</b> with a $'.myround($deposit).' deposit ('.myround($chance).'% chance)</span>';
                    echo'<br /><b>Hash:</b> '.$round['hash'].'<br/><b>Secret:</b> '.$round['secret'].'<br/><b>Winning ticket at:</b> '.$round['winnerpercent'].'%<br/><b>Winning ticket:</b> '.floor((floor((float)$round['winnerticket'] * 100) / 100)*100).' (<a href="provablyfair.php?hash='.$round['hash'].'&amp;secret='.$round['secret'].'&amp;roundwinpercentage='.$round['winnerpercent'].'&amp;totaltickets='.$round['totalvalue'].'" target="_blank">√</a>)';
                    
					echo'</div><div class="historyDeposit">';

                    $items=$mysql->query('SELECT * FROM `'.$prf.$round['id'].'` ORDER BY `id` ASC');
                    $itemsnum=$items->num_rows;

                    echo '<b class="HistoryDepositedText">'.$itemsnum.' items deposited:</b><br/>';

                    while($item=$items->fetch_assoc()){
                        $countitems=$countitems+1;
                        echo'<img src="http://steamcommunity-a.akamaihd.net/economy/image/'.$item['image'].'/105fx98f" title="$'.$item['value'].'<br/>'.str_replace('?','&#9733;',$item['item']).'" style="max-width:70px;cursor:hand;'.(($item['userid']==$round['winneruserid']) ? '' : '').'"/>';
                        if(in_array($countitems, array(8,16,24,32,40,48))){
                            
                        }
                    }
                    $countitems=0;

                    echo'</div>';
                echo'</div>';
            }else{
            }

        }
        echo'</div>';

        $p->show();

        ?>
</div>
<?php require 'assets/include/footer.php'; ?>