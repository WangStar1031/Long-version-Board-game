
<script src="assets/js/jquery.min.js"></script>
<script src='assets/js/jquery.inputmask.bundle.js'></script>

<style type="text/css">
	.StudentLanding{
		width: 215px;
		margin: auto;
	}
	.StudentLanding p{
		background-color: #BA774A;
		color: white;
		padding: 10 20 10 20;
		border-radius: 1em;
		font-size: 1.5em;
		margin: 2em;
		text-align: center;
		margin-bottom: 1em; 
	}
	.StudentInfo{
		background-color: #647DB8;
		padding: 20px;
		border-radius: 20px;
	}
	label{
		display: block;
		color: white;
		font-size: 1.5em;

	}
	#name, #code{
		float: left;
		font-size: 1.5em;
		width: 5em;
		text-align: center;
		color: red;
	}
	.button{
		float: left;
		margin: auto !important;
		padding: 5px !important;
		margin-left: 0.3em !important;
	}
	#code{
		width: 7em;
		text-align: center;
	}

</style>
<div class="StudentLanding">
	<p>Student</p>
	<div class="StudentInfo">
		<label for="name">Name</label>
		<input id="name" type="text" name="name" onkeypress="nameEntered(event)">
		<p class="button" onclick="nameClicked()">OK</p>
		<div style="clear: both;"></div>

		<label for="code">Code</label>
		<input id="code" type="text" name="code" onkeypress="codeEntered(event)">
		<div style="clear: both;"></div>
	</div>
</div>

<script src="assets/js/socket.io-1.2.0.js"></script>
<script type="text/javascript">
		var socket = io.connect('http://stctravel.herokuapp.com:80');
</script>
<script type="text/javascript">
	$("#code").inputmask({"mask":"9 9 9 - 9 9 9"});
	var strName = "";
	var strCode = "";
	var isJoined = false;
	var arrUserInfos = [];
	var arrUsers = [];
	function nameEntered(event){
		if(event.keyCode == 13){
			nameClicked();
		}
	}
	function nameClicked(){
		strName = document.getElementById("name").value;
		if( arrUsers.indexOf(strName) != -1){
			alert("Duplicated Name.");
			strName = document.getElementById("name").value = "";

		}
		document.getElementById("code").focus();
	}
	function codeEntered(event){
		if( event.keyCode == 13){
			code = $("#code").val();
			strCode = code.replace(/(\d)\s(\d)\s(\d)\s-\s(\d)\s(\d)\s(\d)/, "$1$2$3$4$5$6");
		}
	}
	function __insert_user(__user){
		var __d = new Date();
		var __n = __d.getTime();
		__user_index = -1;
		for(i=0; i<arrUserInfos.length; i++){
			if(arrUserInfos[i].name == __user){
			__user_index = i;
			arrUserInfos[i].live_time = __n;
			break;
			}
		}
		if(__user_index == -1){
			__new_user = {};
			__new_user.name = __user;
			__new_user.live_time = __n;
			arrUserInfos[arrUserInfos.length] = __new_user;
			return true;
		}
		return false;
	}
	function __refresh_users(){
		var __d = new Date();
		var __n = __d.getTime();
		arrUsers = [];
		for ( i = 0; i < arrUserInfos.length; i++){
			if((__n - arrUserInfos[i].live_time < 4000) && (arrUserInfos[i].name != strName)){
				arrUsers[arrUsers.length] = arrUserInfos[i].name;
			}
		}
	}
	setInterval(function(){
		__refresh_users();
	}, 5000);
	function addUser(strUserName){
		if( strUserName == strName) return;
		return(__insert_user(strUserName));
	}
	setInterval( function joinGame(){
		if( (strName == "") || (strCode == "")) return;
		__user_obj = {'type':'g_init', 'gameCode': strCode, 'Name':strName};
		socket.emit('sentence message', JSON.stringify(__user_obj));
	}, 2000);
	socket.on('sentence message', function(msg){
		msgObj = JSON.parse(msg);
		// console.log(msgObj);
		if( msgObj.type == 'g_game_joined'){
			if( msgObj.Name == strName){
				console.log("Successful Joined!");
			}
		} else if(msgObj.type == 'g_game_play'){
			if( msgObj.gameCode != strCode)return;
			var arrUserNames = [];
			arrUserNames = msgObj.users.split(',');
			if( arrUserNames.indexOf(strName) == -1){
				alert("You are denied.");
				return;
			}
			window.location.href = "online.php?Name=" + strName + "&gameCode="+strCode+"&topic="+msgObj.topic+"&users="+msgObj.users+"&req="+msgObj.req;
		} else if( msgObj.type == 'g_init'){
			if( msgObj.gameCode != strCode)return;
			if( addUser(msgObj.Name) == true){
				__user_obj = {'type':'g_game_joined', 'gameCode': strCode, 'Name':msgObj.Name};
				socket.emit('sentence message', JSON.stringify(__user_obj));
			}
		}
	});
</script>