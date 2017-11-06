
<script src="assets/js/jquery.min.js"></script>
<script src='assets/js/jquery.inputmask.bundle.js'></script>

<style type="text/css">
	.Title{
		width: 100px;
		margin: auto;
		background-color: #4960A4;
		color: white;
		padding: 10 20 10 20;
		border-radius: 1em;
		font-size: 1.5em;
		text-align: center;
		margin-top: 2em;
	}
	.StudentLanding{
		width: 480px;
		margin: auto;
	}
	.Step1Info, .Step2Info{
		float: left;
		background-color: #647DB8;
		padding: 20px;
		border-radius: 20px;
		width: 190px;
		height: 450px;
		margin: 5px;
		margin-top: 30px;

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

	}
	#code{
		width: 7em;
	}
	.button{
		float: left;
		margin: auto !important;
		padding: 5px !important;
		margin-left: 0.3em !important;
		background-color: #2D3084 !important;
		color: white;
		font-size: 1.3em;
		border-radius: 10px;
	}
	.TitleForCode{
		background-color: #2D3084;
		padding: 0 10 0 10;
		width: 140px;
		margin: auto;
		border-radius: 15px;
		margin-bottom: 10px;
	}
	.TopicContainer{
		height: 350px;
		background-color: white;
	}
	.UsersContainer{
		height: 250px;
		background-color: white;
	}
	.Topics, .Users{
		margin: auto;
		top: 10px;
		left: 10px;
		position: relative;
		border:1px solid black;
		width: calc(100% - 20px);
		height: calc(100% - 20px);
		width: -webkit-calc(100% - 20px);
		height: -webkit-calc(100% - 20px);
		overflow: auto;
		font-size: 2em;
	}
	option{
		color: red;
	}
	.RequireContainer{
		color: white;
		text-align: center;
		margin-top: 5px;
	}
	.RequireText {
		cursor: pointer;
	}
	#check{
		width: 15px;
		height: 15px;
		margin-left: 15px;
		color: red;
		background-color: white;
		font-weight: bold;
	}
	input{
		color: red;
		text-align: center;
	}
	.BtnPlay{
		color: white;
		font-size: 2em;
		background-color: #2D3084;
		width: 2.5em;
		text-align: center;
		margin: auto;
		margin-top: 20px;
		border-radius: 10px;
	}
	.Btn{
		cursor: pointer;
	}
</style>
<div class="StudentLanding">
	<div class="Title">Leader</div>
	<div class="Step1Info">
		<label for="name">Name</label>
		<input id="name" type="text" name="name" onkeypress="nameEntered(event)">
		<p class="button Btn" onclick="nameClicked()">OK</p>
		<div style="clear: both;"></div>
		<label>Topic</label>
		<div class="TopicContainer">
			<select class="Topics" multiple="">
				
			</select>
		</div>
		<div class="RequireContainer">
			<div class="RequireText" onclick="RequireClick()">Require text<span id="check">X</span></div>
		</div>
	</div>
	<div class="Step2Info">
		<label for="code" class="TitleForCode Btn" onclick="genCode()">Generate code</label>
		<input id="code" type="text" name="code" onkeypress="codeEntered(event)">
		<label>Active Users<span id="myName"></span></label>
		<div class="UsersContainer">
			<select class="Users" multiple>
				
			</select>
		</div>
		<div style="clear: both;"></div>
		<div class="BtnPlay Btn" onclick="onPlay()">
			Play
		</div>
	</div>
</div>

<script src="assets/js/socket.io-1.2.0.js"></script>
<script type="text/javascript">
		var socket = io.connect('http://stctravel.herokuapp.com:80');
</script>
<script type="text/javascript">
	var userName = "";
	var strCode = "";
	var strTopic = "";
	var arrUserInfos = [];
	var arrUsers = [];
	var strTopic = "";
	$("#code").inputmask({"mask":"9 9 9 - 9 9 9"});
	function nameEntered(event){
		if(event.keyCode == 13){
			nameClicked();
		}
	}
	function nameClicked(){
		// document.getElementById("code").focus();
		userName = document.getElementById("name").value;
		// if( userName == "") return;
		if( arrUsers.indexOf(userName) != -1){
			alert("Duplicated UserName");
			document.getElementById("name").value = "";
			userName = "";
			return;
		}
		document.getElementById("myName").innerHTML = " *" + userName;
	}
	function codeEntered(event){
		if( event.keyCode == 13){
			var code = $("#code").val();
			strCode = code.replace(/(\d)\s(\d)\s(\d)\s-\s(\d)\s(\d)\s(\d)/, "$1$2$3$4$5$6");
		}
		console.log(strCode);
	}
	var isRequire = false;
	function RequireClick(){
		isRequire = !isRequire;
		var reqVal = "";
		if( isRequire)
			reqVal = "O";
		else
			reqVal = "X";
		document.getElementById("check").innerHTML = reqVal;
	}
	function refreshTopicList(){
		jQuery.ajax({
			type: 'POST',
			url: 'topicManager.php',
			dataType: 'json',
			data: { getTopics: "getTopics"},
			success: function(obj, textstatus){
				var strHtml = "";
				for( i = 0; i < obj.length; i++){
					strHtml += "<option>" + obj[i] + "</option>";
				}
				$(".Topics").html(strHtml);
			}
		});
	}
	refreshTopicList();
	$(".Topics p").on("click", function(){
		console.log(this);
	});
	function genCode(){
		strCode = "";
		for( var i = 0; i < 6; i++){
			var rand = parseInt(Math.random() * 10);
			strCode += rand;
		}
		console.log(strCode);
		$("#code").val(strCode);
	}
	function refreshUserName(){
		$(".Users").html("");
		var strHtml = "";
		for( var i = 0; i < arrUsers.length; i++){
			strHtml += "<option>"+arrUsers[i]+"</option>";
		}
		$(".Users").html( strHtml);
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
	function addUser(strUserName){
		return(__insert_user(strUserName));
	}
	function __refresh_users(){
		var __d = new Date();
		var __n = __d.getTime();
		arrUsers = [];
		for ( i = 0; i < arrUserInfos.length; i++){
			if((__n - arrUserInfos[i].live_time < 4000) && (arrUserInfos[i].name != userName)){
				arrUsers[arrUsers.length] = arrUserInfos[i].name;
			}
		}
		var el = document.getElementsByClassName("Users")[0];
		arrSelUsers = [];
		var options = el && el.options;
		var opt;
		for( var i = 0, iLen = options.length; i<iLen; i++){
			opt = options[i];
			if( opt.selected){
				arrSelUsers.push( opt.text);
			}
		}
		var strHtml = "";
		for( i = 0; i < arrUsers.length; i++){
			if( arrSelUsers.indexOf(arrUsers[i]) != -1){
				strHtml += '<option value="' + arrUsers[i] + '" selected="selected">' + arrUsers[i] + '</option>';
			}else{
				strHtml += '<option value="' + arrUsers[i] + '">' + arrUsers[i] + '</option>';
			}
		}
		el.innerHTML = strHtml;
	}
	setInterval(function(){
		__refresh_users();
	}, 5000);
	socket.on('sentence message', function(msg){
		if(strCode == "")return;
		msgObj = JSON.parse(msg);
		if( msgObj.type == 'g_init'){
			if( msgObj.gameCode != strCode)return;
			if( addUser(msgObj.Name) == true){
				__refresh_users();
				__user_obj = {'type':'g_game_joined', 'gameCode': strCode, 'Name':msgObj.Name};
				socket.emit('sentence message', JSON.stringify(__user_obj));
			}
		} else if(msgObj.type == 'g_game_play'){
		} 
	});
	setInterval( function joinGame(){
		if( (userName == "") || (strCode == "")) return;
		__user_obj = {'type':'g_init', 'gameCode': strCode, 'Name':userName};
		socket.emit('sentence message', JSON.stringify(__user_obj));
	}, 2000);
	function onPlay(){
		var el = document.getElementsByClassName("Topics")[0];
		console.log(el);
		arrSelUsers = [];
		var options = el && el.options;
		var opt;
		for( var i = 0, iLen = options.length; i<iLen; i++){
			opt = options[i];
			if( opt.selected){
				strTopic = opt.text;
				break;
			}
		}
		console.log(strTopic);
		var req = "X";
		if(isRequire)req = "O";
		var usersField = "";
		if(arrUsers.length != 0)
			usersField = userName+","+arrUsers.join(',');
		else
			usersField = userName;
		__user_obj = {'type':'g_game_play', 'gameCode': strCode, 'Name':userName, 'topic':strTopic,'users': usersField,'req':req};
		socket.emit('sentence message', JSON.stringify(__user_obj));

		window.location.href = "online.php?Name=" + userName + "&gameCode="+strCode+"&topic="+strTopic+"&users="+usersField+"&req="+req;
	}
</script>