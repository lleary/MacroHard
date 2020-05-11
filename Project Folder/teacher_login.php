<?php
	session_start();
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Login</title>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<style><?php include 'stylesheet.css'; ?></style>
</head>
<body>
		<center>
		<div style="border:3px; border-style:solid; border-color: #7E57C2; border-radius: 5px; padding: 5px; width: 500px; height: 235px">
			<form action="check_login.php" method="post" id="form_id">
				<h2>Teacher Login</h2>
				First Name:
				<input type="text" name="firstname" id="firstname" placeholder="First Name" /><br/><br/>
				Last Name:
				<input type="text" name="lastname" id="lastname" placeholder="Last Name" /><br/><br/>
				Password:
				<input type="Password" name="pw" id="pw" placeholder="Password" /><br/><br/>
				<input type="submit" name="submit_id" id="login" value="Login" /><br/>
			</form>
		</div>
</center>
</body>
</html>