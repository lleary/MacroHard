<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Login</title>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	</head>
<body>
		<center>
			<form action="check_login.php" method="post" id="form_id" style="border:2px; border-style:solid; border-color: black; padding: 5px; width: 500px; height: 250px">
				<h2>Welcome to Matheroids!</h2>
				First Name:
				<input type="text" name="firstname" id="firstname" placeholder="First Name" /><br/><br/>
				Last Name:
				<input type="text" name="lastname" id="lastname" placeholder="Last Name" /><br/><br/>
				Password:
				<input type="text" name="pw" id="pw" placeholder="Password" /><br/><br/>
				<input type="submit" name="submit_id" id="login" value="Login" />
			</form>
		</center>
</body>
</html>