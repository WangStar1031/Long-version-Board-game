
<script src="assets/js/jquery.min.js"></script>
<style type="text/css">
	.MainLanding {
		width: 320px;
		margin: auto;
		text-align: center;
	}
	.MainLanding h1{
		margin-top: 3em;
	}
	.MainLanding p{
		float: left;
		color: white;
		background-color: #4960A4;
		border-radius: 1em;
		cursor: pointer;
		padding: 10 20 10 20;
		font-size: 1.5em;
		margin: 1em;
	}
	.MainLanding p:hover{
	}
	.student{
		background-color: #BA774A !important;
	}
</style>
<div class="MainLanding">
	<h1>ESL Ninja</h1>
	<p onclick="onStudent()" class="student">Student</p>
	<p onclick="onLeader()">Leader</p>
	<div style="clear: both;"></div>
</div>
<script type="text/javascript">
	function onStudent(){
		document.location.href = 'student.php';
	}
	function onLeader(){
		document.location.href = 'leader.php';
	}
</script>