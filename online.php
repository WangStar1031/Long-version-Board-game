<?php
	$userName = $_GET['Name'];
	$gameCode = $_GET['gameCode'];
	$topic = $_GET['topic'];
	$users = $_GET['users'];
	$req = $_GET['req'];
	$arrUsers = array();
	$arrUsers = explode( ",", $users);
	// print_r($arrUsers);
	$nUserCount = count($arrUsers) > 4 ? 4: count($arrUsers);

	$arrContents1 = array(-1,21,14,7,0,1,2,3,4,5,6,13,20,27,34,41,48);
	$arrContents2 = array(-1,42,35,28,21,14,7,0,1,2,3,4,5,6,13,20,27,34,41,48);
	$arrContents3 = array(-1,42,43,44,45,46,47,48,41,34,27,20,13,6);
	$arrContents4 = array(-1,0,1,2,3,4,5,6,13,20,27,34,41);
	$arrContents = array( $arrContents1, $arrContents2, $arrContents3, $arrContents4);
	// print_r($arrContents);
	$nCardCount = 0;
?>

<!DOCTYPE html>
<html>
<head>
	<title>Long-Version Board Game</title>
	<link rel="stylesheet" href="assets/css/index.css?<?= time(); ?>">
	<link rel="stylesheet" href="assets/css/dice_style.css">
	<link rel="stylesheet" href="assets/css/flip_style.css">
</head>
<div class="game_container">
	<?php
//	for( $boards = 0; $boards < 1; $boards++){
	for( $boards = 0; $boards < count($arrContents); $boards++){
		$arrBuff = $arrContents[$boards];
	?>
	<div class="boardPanel customPanel DisplayNone" id="boardPanel<?=$boards ?>">
		<div class="game_board">
			<div>
				<table class="cardTable">
					<?php
					$arrCardNumbers = array();
					for ($row=0; $row < 7; $row++) { 
						echo "<tr>";
						for ($col=0; $col < 7; $col++) {
							$cardNum = $row * 7 + $col;
							$ret = array_search($cardNum, $arrBuff);
							if( $ret != true){echo "<td></td>";continue;}
							echo "<td>";
							$curVal = $nCardCount + $ret-1;
							// echo $curVal;
							?>
			<div id="cardContainer<?= $curVal ?>" cardid="<?= $curVal ?>" class="flip-container">
				<div class="flipper">
					<div class="front">
					</div>
					<div class="back">
						<p class="questionTxt" style="margin: 0px;padding: 0px;text-align: center;"></p>
					</div>
				</div>
			</div>
							<?php
							echo "</td>";
						}
						echo("</tr>");
					}
					?>
				</table>
			</div>
		</div>
	</div>
	<?php
	$nCardCount += count($arrBuff) - 1;
	}
	?>
	<div class="answerPanel">
		<h3>Question and Answers</h3>
		<div class="answerList">
		</div>
	</div>
	<div class="carContainer">
		<?php
		$carNumbers = count($arrUsers) > 4 ? 4 : count($arrUsers);
		for( $i = 1; $i <= $carNumbers; $i++){
			?>
			<div id="player<?= $i ?>" class="player">
				<p class="playerName"><?= $arrUsers[$i-1]?></p>
				<div class="playerImage"></div>
			</div>
			<div id="player_prog<?= $i ?>" class="player_prog">
				<p class="playerName"><?= $arrUsers[$i-1]?></p>
				<div class="playerImage"></div>
			</div>
			<?php
		}
		?>
	</div>
	<div class="dice_board">
		<div class="dice_container">
			<section class="dice-container">
				<div id="dice" class="show-front">
					<figure class="front"></figure>
					<figure class="back"></figure>
					<figure class="right"></figure>
					<figure class="left"></figure>
					<figure class="top"></figure>
					<figure class="bottom"></figure>
				</div>
			</section>
			<p>Press to roll</p>
		</div>
		<div class="dice_number_container" style="display: none;">
			<div class="dice_number dicNum">0</div>
			<p>Move <span class="dicNum">0</span> spaces</p>
		</div>
		<div style="clear: both;"></div>
	</div>
	<div class="onesAnswer">
		<div class="onesTurn HideItem"><span id="turnUserName">Wang's</span> Turn</div>
		<p>Answer</p>
		<input type="text" name="myAnswer" id="myAnswer" disabled>
	</div>
	<div class="dice_Next_Container">
		<div class="dice_Btn action_Btn">NEXT</div>
		<p style="display: none;">After you answer the question press NEXT</p>
	</div>
	<div style="clear: both;"></div>
	<div class="progressBar">
		<table>
			<tr>
				<?php
				for( $ii = 0; $ii <= 59; $ii++){
					?>
					<td class="pBarCell" id="barCell<?= $ii ?>"></td>
					<?php
				}
				?>
			</tr>
		</table>
	</div>

	<div class="ani-gif-cong ani-gif HideItem">
		<img src="assets/img/ani-gif/ani-cong.gif">
	</div>
	<div class="ani-gif-star ani-gif HideItem">
		<img src="assets/img/ani-gif/ani-star.gif">
	</div>
	
<audio preload="auto">
	<source src="assets/sound/dice-sound.mp3"></source>
</audio>
<audio preload="auto">
	<source src="assets/sound/Success_128.mp3"></source>
</audio>

<script src="https://cdn.socket.io/socket.io-1.2.0.js"></script>
<script src="assets/js/jquery.min.js"></script>
<script type="text/javascript">
	var socket = io.connect('http://stctravel.herokuapp.com:80');
	var userName = "<?= $userName?>";
	var arrCars = [];
	var arrCurStep = [-1,-1,-1,-1];
	var arrCarPosInCard = [0,0,0,0];
	var nCurrentUserNumber = 0;
	var nMaxNumber = 4;
	var nAllUserCount = "<?= $nUserCount ?>" * 1;
	var nCurPlayingCount = nAllUserCount;
	var gameState = true;
	var nBoardImgNo = 0;
	var topicName = "";
	var arrQuestionNumbers = [];
	var isPlaying = false;
	var playerNames = "<?= $users ?>";
	var arrPlayerNames = playerNames.split(",");
	var masterUserName = arrPlayerNames[0];
	var isMaster = userName == arrPlayerNames[0] ? true : false;
	var isCanPlay = (arrPlayerNames[0] == userName) ? true : false;
	var gameCode = "<?= $gameCode ?>";
	var myNumber = arrPlayerNames.indexOf(userName);
	var g_topicName = "<?= $topic ?>";
	var arrContents = [];
	for( var i = 0; i < nAllUserCount; i++){
		var car = $("#player" + (i+1));
		car.find(".playerName").html(arrPlayerNames[i]);
		car.removeClass("HideItem");
		car.addClass("ShowItem");
		nAllUserCount = nAllUserCount;
		arrCars.push(car);
	}
	var nAllCardCount = 59;
	$("#boardPanel0").removeClass("DisplayNone");
	$("#boardPanel0").addClass("DisplayBlock");
</script>
<script  src="assets/js/communication.js?<?= time() ?>"></script>
<script  src="assets/js/index.js?<?= time() ?>"></script>
<script  src="assets/js/dice.js?<?= time() ?>"></script>

</div>
</html>