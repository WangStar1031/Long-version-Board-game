function setContentsToCards(strTopic, arrQuestions){
	if( strTopic == "")return;
	$("#topicList > option").filter(function(){
		return $(this).html() === strTopic;
	}).prop('selected', true);
	jQuery.ajax({
		type: 'POST',
		url: 'topicManager.php',
		data: { getContents: strTopic},
		success: function(obj, textstatus){
			var arrRet = obj.split("\n");
			var strHtml = "";
			for( i = 0; i < arrRet.length; i++){
				if(arrRet[i] == "")
					continue;
				arrContents.push(arrRet[i]);
			}
			if(arrQuestions.length != 0){
				arrQuestionNumbers = arrQuestions;
			} else{
				if ( arrContents.length < nAllCardCount) {
					for( ii = 1; ii < nAllCardCount; ii++){
						var ran = Math.floor(Math.random()*arrContents.length);
						arrQuestionNumbers.push(ran);
					}
				} else{
					while( arrQuestionNumbers.length < nAllCardCount){
						var ran = Math.floor(Math.random()*arrContents.length);
						if( arrQuestionNumbers.indexOf(ran) == -1){
							arrQuestionNumbers.push(ran);
						}
					}
				}
			}
			setTimeout(function(){
				__user_obj = {'type':'g_card_contents', 'gameCode': gameCode, 'masterName':userName, 'cardContents':arrQuestionNumbers.join(",")};
				socket.emit('sentence message', JSON.stringify(__user_obj));
			}, 1000);
	
			for( i = 1; i < nAllCardCount; i++){
				$("#cardContainer"+i).find(".questionTxt").html(arrContents[arrQuestionNumbers[i-1]]);
			}
		}
	});
}
function setBackgroundImg(nImgNo){
	$(".game_container").css('background-image', 'url("assets/img/bg-img/background-0' + nImgNo + '.png")');
}
setContentsToCards(g_topicName, arrQuestionNumbers);
$(".dice_Btn").on("click", function(){	//NEXT button
	if( $("#myAnswer").val() == "" && document.getElementById("myAnswer").disabled == false){
		alert("Please enter the answer.");
		return;
	}
	var _answer = $("#myAnswer").val();
	$("#myAnswer").val("");
	$(".flip-container").removeClass("hover");
	sendNextTurnMsg(_answer);
});
function sendNextTurnMsg(_answer){
	if( gameState == false)	{
		var strHtml = '<p>A:' + _answer +'</p>';
		$(".answerList").append(strHtml);
		isCanPlay = true;
		return;
	}
	nCurrentUserNumber ++;
	nCurrentUserNumber %= nAllUserCount;
	setUserTurn( arrPlayerNames[nCurrentUserNumber], _answer);
}
function getUserPanelStep(userStep){
	if( userStep < 16) return 1;
	if( userStep < 19 + 16) return 2;
	if( userStep < 19 + 16 + 13) return 3;
	return 4;
}
function setProgress(){
	for( var i = 0; i < nAllUserCount; i++){
		var car_pro = $("#player_prog"+(i+1));
		if( arrCurStep[i] == -1) continue;
		var tblElem = $("#barCell" + [arrCurStep[i]]);
		var curStep = arrCurStep[i];
		var eqCount = 1;
		for( var j = 0; j < i; j++){
			if( arrCurStep[j] == curStep){
				eqCount ++;
			}
		}
		var nleft = tblElem.position().left - 25;
		var ntop = tblElem.position().top - eqCount * 50;
		car_pro.css({top:ntop, left:nleft, position:'absolute'});
	}
}
function gotoStep(carName, number){
	isCanPlay = false;
	if(nAllUserCount == 0) return;
	nCurrentUserNumber = arrPlayerNames.indexOf(carName);
	if(arrCurStep[nCurrentUserNumber] >= nAllCardCount+1)
		return;
	var prevStep = arrCurStep[nCurrentUserNumber];
	arrCurStep[nCurrentUserNumber] += number;
	if( carName == userName){
		if( arrCurStep[nCurrentUserNumber] < 16){
			// setBackgroundImg(1);
		} else if( arrCurStep[nCurrentUserNumber] < 19+16){
			$("#boardPanel0").removeClass("DisplayBlock");
			$("#boardPanel0").addClass("DisplayNone");
			$("#boardPanel1").removeClass("DisplayNone");
			$("#boardPanel1").addClass("DisplayBlock");
			// setBackgroundImg(2);
		} else if( arrCurStep[nCurrentUserNumber] < 19+16+13){
			$("#boardPanel1").removeClass("DisplayBlock");
			$("#boardPanel1").addClass("DisplayNone");
			$("#boardPanel2").removeClass("DisplayNone");
			$("#boardPanel2").addClass("DisplayBlock");
			// setBackgroundImg(3);
		} else {
			$("#boardPanel2").removeClass("DisplayBlock");
			$("#boardPanel2").addClass("DisplayNone");
			$("#boardPanel3").removeClass("DisplayNone");
			$("#boardPanel3").addClass("DisplayBlock");
			// setBackgroundImg(4);
		}
		setBackgroundImg( getUserPanelStep(arrCurStep[nCurrentUserNumber]));
	}
	if(arrCurStep[nCurrentUserNumber] >= nAllCardCount){
		arrCurStep[nCurrentUserNumber] = nAllCardCount;
		$(".ani-gif").removeClass("HideItem");
		$(".ani-gif").addClass("ShowItem");
		setTimeout(function(){
			$(".ani-gif").removeClass("ShowItem");
			$(".ani-gif").addClass("HideItem");
		}, 5000);
		var audio = $("audio")[1];
		try{
			audio.pause();
			audio.currentTime = 0;
			audio.play();
		} catch(e){
			console.log(e);
		}
	}
	for( i = 0; i < nAllUserCount; i++){
		if( getUserPanelStep(arrCurStep[myNumber]) != getUserPanelStep(arrCurStep[i])){
			arrCars[i].removeClass("ShowItem");
			arrCars[i].addClass("HideItem");
		} else {
			arrCars[i].removeClass("HideItem");
			arrCars[i].addClass("ShowItem");
		}
	}
	var car = arrCars[nCurrentUserNumber];
	if( arrCurStep[nCurrentUserNumber] != 0 && arrCurStep[nCurrentUserNumber]!= nAllCardCount){
		var curCardElem = $("#cardContainer"+arrCurStep[nCurrentUserNumber]);
		curCardElem.toggleClass('hover');	
	}
	var carPos = calcCarPosition(nCurrentUserNumber, arrCurStep[nCurrentUserNumber], true);
	var nleft = carPos.left;
	var ntop = carPos.top;
	car.css({top:ntop, left:nleft, position:'absolute'});
	// if(number == 0)return;
	for( i = 0; i < nAllUserCount; i++){
		if( (arrCurStep[i] == prevStep) && (arrCurStep[i] < nAllCardCount+1)){
		// if( (arrCurStep[i] == prevStep) && (arrCurStep[i] < nAllCardCount+1)){
			car = arrCars[i];
			arrCarPosInCard[i]--;
			carPos = calcCarPosition(i, arrCurStep[i], false);
			car.css({top:carPos.top, left:carPos.left, position:'absolute'});
		} else{
			car = arrCars[i];
			// arrCarPosInCard[i]--;
			carPos = calcCarPosition(i, arrCurStep[i], false);
			car.css({top:carPos.top, left:carPos.left, position:'absolute'});
		}
	}
	setProgress();
	return arrCurStep[nCurrentUserNumber];
}
for( var _i = 0; _i < nAllUserCount; _i ++){
	gotoStep(arrPlayerNames[_i], 1);
}
isCanPlay = (arrPlayerNames[0] == userName) ? true : false;
function rolledDice(number){
	var stepNumber = gotoStep(userName, number);
	console.log("Rolled Number : "+stepNumber);
	var question = $("#cardContainer"+stepNumber).find(".questionTxt").html();
	var strHtml = '<p>Q:' + question +'</p>';
	$(".answerList").append(strHtml);
	sendMyDiceNumber(number);
}
function rolledDiceNameNumber(_name,_number){
	nCurrentUserNumber++;
	nCurrentUserNumber %= nCurPlayingCount;
	var stepNumber = gotoStep(_name, _number);
	var question = $("#cardContainer"+stepNumber).find(".questionTxt").html();
	var strHtml = '<p >' + question +'</p>';
	$(".answerList").append(strHtml);
}
function calcCarPosition( nCurrentUser, nCardNumber, isNew){
	if( nCardNumber < 0) return {left:0,top:0};
	var nleft;
	var ntop;
	var samePosCount = -1;
	for ( i = 0; i < nAllUserCount; i++){
		if(arrCurStep[i] == nCardNumber){
			samePosCount++;
		}
	}
	if( isNew)
		arrCarPosInCard[nCurrentUser] = samePosCount;
	var cardElem = $("#cardContainer"+nCardNumber);
	var position = cardElem.position();
	console.log(nCardNumber);
	nleft = position.left;// - 500;
	ntop = position.top;

	col = cardElem.parent().parent().children().index(cardElem.parent());
	if( col == 0){
		nleft -= 60;
		ntop -= 50 * parseInt( arrCarPosInCard[nCurrentUser] / 2);
		nleft -= 50 * parseInt(arrCarPosInCard[nCurrentUser] % 2);
	} else if( col == 6){
		nleft += 150;
		ntop -= 50 * parseInt( arrCarPosInCard[nCurrentUser] / 2);
		nleft += 50 * parseInt(arrCarPosInCard[nCurrentUser] % 2);
	} else {
		ntop -= 50;
		ntop += 50 * parseInt( arrCarPosInCard[nCurrentUser] / 2);
		nleft += 50 * parseInt(arrCarPosInCard[nCurrentUser] % 2);
	} 
	return {left:nleft, top:ntop};
}
function fillImageCards(){
	for ( i =  0; i < nAllCardCount+1; i++){
		$("#cardContainer"+i).find(".front").css('background-image', 'url("assets/img/card-img/card-img-0'+(i % 6+ 1)+'.png")');
	}
	$("#cardContainer0").find(".front").html('Start');
	$("#cardContainer"+nAllCardCount).find(".front").html('Finish');
}
fillImageCards();

function initAllCurrentUsers(){
	$("#playingUsers").html("");
	arrPlayerNames = [];
	nCurPlayingCount = 0;
	if( userName != "")
		addUser(userName);
	else
		addUser("ME");
}
function userAccepted(_userName) {
	addUser(_userName);
}
$(".exit_Btn").on('click', function(){
	if( gameState == false)return;
	gameState = false;
	isPlaying = false;
	__user_obj = {'type':'g_exit_game', 'gameCode': gameCode, 'masterName':masterUserName, 'userName':userName};
	socket.emit('sentence message', JSON.stringify(__user_obj));
	$("#playingUsers").html("");
	$(".answerList").html("");
	nAllUserCount = 0;
	nCurPlayingCount = 0;
	arrPlayerNames = [];
	arrCarPosInCard = [];
	arrCurStep = [];
})
function setUserTurn(_name, _answer){
	if(isMaster == false){
		__user_obj = {'type':'g_user_Turn_Slave', 'gameCode': gameCode, 'masterName':masterUserName, 'curUserName': userName,'answer':_answer};
		socket.emit('sentence message', JSON.stringify(__user_obj));
		return;
	}
	__user_obj = {'type':'g_user_Turn', 'gameCode': gameCode, 'masterName':userName, 'nextUserName':_name, 'answer':_answer};
	socket.emit('sentence message', JSON.stringify(__user_obj));
}
function sendMyDiceNumber(_number){
	if( gameState == false)return;
	__user_obj = {'type':'g_dice_Number', 'gameCode': gameCode, 'masterName':masterUserName, 'curUserName': userName, 'diceNumber': _number};
	socket.emit('sentence message', JSON.stringify(__user_obj));
}
function checkHandle(checkboxElem){
	if( checkboxElem.checked){
		document.getElementById("myAnswer").disabled=false;
	} else {
		document.getElementById("myAnswer").disabled=true;
	}
}