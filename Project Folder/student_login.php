<?php
	session_start();
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Student Login</title>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<style><?php include 'stylesheet.css'; ?></style>
</head>

<body class="loginBody">
	<center class="flex-container">
		<div style="border:3px; border-style:solid; border-color: #512DA8; border-radius: 5px; padding: 5px; width: 500px; height: 310px; color:white;">
			<form action="check_login.php" method="post" id="form_id">
				<h2 style="font-size:40px; line-height: 30px">Student Login</h2>
				<input type="text" name="firstname" id="firstname" style="height:30px; width: 200px; font-size:20px" placeholder="First Name" /><br/><br/>
				<input type="text" name="lastname" id="lastname" style="height:30px; width: 200px; font-size:20px" placeholder="Last Name" /><br/><br/>
				<input type="submit" name="submit_id" id="login" style="height:30px; width:100px; font-size:18px" value="Login" />
			</form>
			<form action="welcome.php" method="post">
				<br/>
				<input type="submit" name="cancel" id="cancel" style="height:30px; width:100px; font-size:18px" value="Cancel"/>
			</form>
		</div>
	</center>
</body>
</html>