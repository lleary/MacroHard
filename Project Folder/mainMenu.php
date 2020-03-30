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

	<form action="../Logout.php">
		<button type="submit">Logout</button></a>
	</form>
<br />
<br />


<script>
	function playAddition(){
		localStorage.setItem("difficulty",1);
		console.log("Playing Addition");
	}

	function playSubtraction(){
		localStorage.setItem("difficulty",2);
		console.log("Playing Subtraction");
	}
</script>

<div style="border:3px; border-style:solid; border-color: black; padding: 5px; width: 500px">
	<h2>Main Menu</h2>
	<!--<p>You are on level *</p>!-->
	<?php
		echo "You are on a level (I have no idea)";
	?>
	<br />
	<br />
	<form action="../game.php/?user=$user" onsubmit="playAddition();">
		<button type="submit">Play Game (Addition)</button></a>
	</form>
	<form action="../game.php/?user=$user" onsubmit="playSubtraction();">
		<button type="submit">Play Game (Substraction)</button></a>
	</form>
</div>
<br />

<?php if($query_array["type"] == "Teacher") : ?>
<div style="border:3px; border-style:solid; border-color: black; padding: 5px; width: 500px">
	<h2>Teacher Dashboard</h2>
	<div style="border:1.5px; border-style:solid; border-color: black; padding: 5px; ">
		<h2>Create Student Accounts</h2>
		<form action="add_student.php" method="post" id="form_id">
			Students First Name:
			<input type="text" name="username" id="username" placeholder="First Name" />
			<br/><br/>
			Students Last Name:
			<input type="text" name="password" id="password" placeholder="Last Name" /><br/><br/>
			<input type="submit" name="submit_id" id="create" value="Create Account" />
		</form>
	</div>
</div>
<?php endif; ?>

<div style="border:3px; border-style:solid; border-color: black; padding: 5px; width: 500px">
	<h2>Account Settings</h2>
	<button>Reset Level</button>
	<br />
	<div style="border:1.5px; border-style:solid; border-color: black; padding: 5px; ">
		<h2>Change Name</h2>
		<form action="add_student.php" method="post" id="form_id">
			New First Name:
			<input type="text" name="username" id="username" placeholder="First Name" />
			<br/><br/>
			New Last Name:
			<input type="text" name="password" id="password" placeholder="Last Name" /><br/><br/>
			<input type="submit" name="submit_id" id="create" value="Change Name" />
		</form>
	</div>
</div>


</body>
</html>
