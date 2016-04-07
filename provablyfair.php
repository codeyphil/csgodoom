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
	 <div class="title">PROVABLY FAIR</div>

		<p>To show that our system is truly random we use a "provably fair" system. That means you can check if a round was fair (valid) by using these three elements:</p>

		<p><b>The winner percentage</b><br />
		To uncover the number the system uses the formula <b>&nbsp;(WinnerPercentage / 100) * TotalTickets&nbsp;</b><br/>
		After doing the math you'll end up with a number that contains the number of the winning ticket, from there you will only need to count the number of tickets, starting at the beginning of the round (being that we give 1 ticket per each 0.01 Steam Value deposited) and you'll find the fair winner - please note the fair winner is zero based, so the first ticket starts on zero, and goes from there.
		</p>

		<p><b>The round secret</b><br />
		The round secret is a random string generated at the beginning of the round but only shown at the end. It's meant to encrpyt the winner percentage and allow you to verify the winning ticket via the round identifier.
		</p>

		<p><b>The round hash</b><br />
		The round hash is an md5 string that is given at the beginning of the round, it is generated by joining the round secret with the winner percentage, separated by a "-", so a round hash is an md5 encrypted string of "[roundSecret]-[winningPercentage]".
		</p>


		<p><b>Verify a round:</b><br />
		To help you verify a round, we created this small helper tool that runs completly on the client side.<br />
		If you're a programmer feel free to check the source code, we added everything inline to allow you to see exactly what the code is doing over a quick inspection.
		</p>
		<br/>
    <div class="helper-tool">
        <input type="text" id="round-hash" placeholder="Round Hash" <?php if(isset($_GET['hash']) && !empty($_GET['hash']) && preg_match('/^[a-f0-9]{32}$/',$_GET['hash'])){ echo'value="'.htmlspecialchars($_GET['hash']).'"'; } ?>/>
        <input type="text" id="round-secret" placeholder="Round Secret" <?php if(isset($_GET['secret']) && !empty($_GET['secret']) && preg_match('/^[a-f0-9]{16}$/',$_GET['secret'])){ echo'value="'.htmlspecialchars($_GET['secret']).'"'; } ?>/>
        <input type="text" id="round-winner-percentage" placeholder="Round Winner Percentage" <?php if(isset($_GET['roundwinpercentage']) && !empty($_GET['roundwinpercentage'])){ echo'value="'.htmlspecialchars($_GET['roundwinpercentage']).'"'; } ?>/>
        <input type="text" id="round-tickets" placeholder="Round Total Tickets" <?php if(isset($_GET['totaltickets']) && !empty($_GET['totaltickets'])){ echo'value="'.htmlspecialchars($_GET['totaltickets']).'"'; } ?>/>
        <input type="button" value="Validate" onclick="doRoundValidation($('#round-hash').val(),$('#round-secret').val(),$('#round-winner-percentage').val(),$('#round-tickets').val())" />
    </div>
    <div id="status-validation" class="notice">

    </div>

    <script src="https://crypto-js.googlecode.com/svn/tags/3.0.2/build/rollups/md5.js"></script>
    <script type="text/javascript">
        var doRoundValidation = function(hash, secret, percentage, TotalTickets){
                //percentage = parseFloat(percentage);
                TotalTickets = parseFloat(TotalTickets);

                $("#status-validation").removeClass("success")
                                       .removeClass("error")
                                       .removeClass("warning");

                if(!isNaN(percentage) && percentage >= 0 && !isNaN(TotalTickets) && TotalTickets > 0) {
                    var winningTicket = validateRound(hash, secret, percentage, TotalTickets);
                    if(winningTicket !== false){
                        $("#status-validation").addClass("success")
                                               .text("The round is valid. Winning ticket: " + (Math.floor(winningTicket*100)));
                    }else{
                        $("#status-validation").addClass("error")
                                               .text("The round is not valid.");
                    }

                }
                else {
                    $("#status-validation").addClass("warning")
                                           .text("Please check your details, they seem to be incorrect.");
                }

            },
            validateRound = function(hash, secret, percentage, TotalTickets){
                var checksum = CryptoJS.MD5(secret + "-" + percentage).toString();
                console.log(secret + "-" + percentage);
                console.log(hash);
                console.log(checksum);

                if(checksum == hash){
                    // Note that this number is zero-based, so the first ticket is actually zero,
                    // We increase it before displaying it.
                    return ( percentage / 100 ) * TotalTickets;
                }
                return false;
            };
    </script>
<br/><br/><br/><br/><br/><br/><br/>
<!-- firefox height fix. no ragrets -->
</div>

<?php require 'assets/include/footer.php'; ?>