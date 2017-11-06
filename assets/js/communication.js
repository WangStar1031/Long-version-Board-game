var arrUsers_total;
var arrUsers;
$(function(){
	arrUsers_total = [];
	function __insert_user(__user, state){
		var __d = new Date();
		var __n = __d.getTime();
		__user_index = -1;
		for(i=0; i<arrUsers_total.length; i++){
			if(arrUsers_total[i].name == __user){
			__user_index = i;
			arrUsers_total[i].live_time = __n;
			arrUsers_total[i].state = state;
			break;
			}
		}
		if(__user_index == -1){
			__new_user = {};
			__new_user.name = __user;
			__new_user.live_time = __n;
			__new_user.state = state;
			arrUsers_total[arrUsers_total.length] = __new_user;
		}
	}
	socket.on('sentence message', function(msg){
		msgObj = JSON.parse(msg);
		if( msgObj.gameCode != gameCode) return;
		if(msgObj.type != 'g_init')console.log(msgObj);
		if(msgObj.type == 'g_init'){
		} else if( msgObj.type == 'g_exit_game'){

		} else if( msgObj.type == 'g_dice_Number'){
			if( msgObj.masterName == masterUserName && msgObj.curUserName != userName){
				rollDiceNameNumber(msgObj.curUserName, msgObj.diceNumber);
			}
		} else if( msgObj.type == 'g_user_Turn'){
			if( msgObj.masterName == masterUserName){
				if(msgObj.nextUserName == userName){
					isCanPlay = true;
				}
				setCurrentUserName( msgObj.nextUserName, msgObj.answer);
			}
		} else if( msgObj.type == 'g_user_Turn_Slave'){
			if( msgObj.masterName == userName && msgObj.curUserName != masterUserName){
				sendNextTurnMsg(msgObj.answer);
			}
		} else if( msgObj.type == 'g_card_contents'){
			if( msgObj.masterName != userName){
				arrQuestionNumbers = msgObj.cardContents.split(",");
				for( var i = 1; i < nAllCardCount; i++){
					$("#cardContainer"+i).find(".questionTxt").html(arrContents[arrQuestionNumbers[i-1]]);
				}

			}
		}
	});
	function setCurrentUserName( nextUserName, _answer){
		$(".flip-container").removeClass("hover");
		$(".onesTurn").removeClass("HideItem");
		$(".onesTurn").removeClass("ShowItem");
		$("#turnUserName").html(nextUserName + "`s ");
		if( _answer != ''){
			var strHtml = '<p>A:' + _answer +'</p>';
			$(".answerList").append(strHtml);
		}
		var userIndex = arrPlayerNames.indexOf(nextUserName);
		$("#playingUsers p").css("color","black");
		$("#playing_"+nextUserName).css("color","red");
		var strHtml = '<p style="font-weight:bold;">' + nextUserName +'</p>';
		$(".answerList").append(strHtml);
	}
	setInterval( function(){
		if(userName == ""){
			return;
		}
		__user_obj = {"type":"g_init", 'gameCode': gameCode, "name": userName, "state": gameState};
		socket.emit('sentence message', JSON.stringify(__user_obj));
	}, 2000);
})
