<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Matheroids</title>
	<link rel="stylesheet" type="text/css"href="stylesheet.css">
	</head>
<body>
<?php

   function prepare_query_string(){
		$re = [];
		$query_array = explode("&", $_SERVER["QUERY_STRING"]);
		foreach ($query_array as $key => $value) {
		$temp = explode("=", $value);
		$re[$temp[0]] = $temp[1]; 
		}
		return $re;
	}
?>


<?php 
	echo "<pre>";

	$query_array = prepare_query_string();


	echo "<p>Welcome to Matheroids, ".$query_array["user"]."</p>";
	echo"</pre>";
?>

<button action="logout.php">Log out</button>
<br />
<br />

<div style="border:3px; border-style:solid; border-color: black; padding: 5px; width: 500px">
	<h2>Main Menu</h2>
	<p>You are on level *</p>
	<button>Play Game</button>
</div>
<br />

 <!-- This needs to be setup so that it only appears if the logged-in account is a teacher. !-->
<div style="border:3px; border-style:solid; border-color: black; padding: 5px; width: 500px">
	<h2>Teacher Dashboard</h2>
	<div style="border:1.5px; border-style:solid; border-color: black; padding: 5px; ">
		<h2>Create Student Accounts</h2>
		<form action="check_login.php" method="post" id="form_id">
				Students First Name:
				<input type="text" name="username" id="username" placeholder="First Name" />
				<br/><br/>
				Students Last Name:
				<input type="text" name="password" id="password" placeholder="Last Name" /><br/><br/>
				<input type="submit" name="submit_id" id="create" value="Create Account" />
			</form>

</div>
</div>

</body>
</html>