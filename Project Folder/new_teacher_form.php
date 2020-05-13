<?php
	session_start();
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Create Teacher Account</title>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<style><?php include 'stylesheet.css'; ?></style>
</head>
<body class="loginBody">
	<center class="flex-container">
		<div style="border:3px; border-style:solid; border-color: #7E57C2; border-radius: 5px; padding: 5px; width: 500px; height: 350px; color:white;">
			<form action="create_teacher.php" method="post" id="form_id">
				<h2>New Teacher Account</h2>
				<input type="text" name="firstname" id="firstname" placeholder="First Name" /><br/><br/>
				<input type="text" name="lastname" id="lastname" placeholder="Last Name" /><br/><br/>
				<input type="password" name="password" id="password" placeholder="Password" /><br/><br/>
				<input type="password" name="cPassword" id="cPassword" placeholder="Confirm Password" /><br/><br/>
				<input type="text" name="class" id="class" placeholder="Class name"/><br/><br/>
				<input type="submit" name="submit_id" id="login" value="Create"/>
			</form>
			<form action="welcome.php" method="post">
				<br/>
				<input type="submit" name="cancel" id="cancel" value="Cancel"/>
			</form>
			<br/>
			<br/>
			<?php if(isset($_SESSION["accountError"])) : ?>
				<p style="color:orange"><?php echo $_SESSION["accountError"] ?></p>
			<?php endif; ?>
			<!--success message is not necessary since success will result in redirecting to the teacher login page-->
		</div>
	</center>
</body>
</html>