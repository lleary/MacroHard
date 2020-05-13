<?php
	session_start();
	$_SESSION['accountError'] = null;
	$_SESSION['accountSuccess'] = null;
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Matheroids</title>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<style><?php include 'stylesheet.css'; ?></style>
</head>

<body class="welcomeBody">
	<center>
		<br/><h2>Welcome to Matheroids!</h2><br/>
		<form action="student_login.php" method="post" id="form_id">
			<input type="submit" class="studentlogin" value="STUDENT LOGIN">
		</form>
		<br><br><br><br>
		<form action="teacher_login.php" method="post" id="form_id">
			<input type="submit" class="teacherlogin" value="TEACHER LOGIN">
		</form>
		<br>
		<form action="new_teacher_form.php" method="post" id="form_id">
			<input type="submit" class="createteacher" value="CREATE TEACHER ACCOUNT">
		</form>
	</center>
</body>

</html>