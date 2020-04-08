<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Matheroids</title>
	<link rel="stylesheet" type="text/css"href="stylesheet.css">
	<style>
		body{
			background-color: #FFFFFF;
			font-family:Courier New;
			letter-spacing: 0.5px;
		}
		div {
			color:#000000;
		}
		h1{
			color:#000000;
		}
		#mainMenu{
			color:#22D2A0;
			display:block;
			padding: 15px;
			
		}
		#accountSettings{
			color:#FF5555;
			padding: 15px;
			display:block;
		}
		#teacherDashboard{
			color:#FFD913;
			padding: 15px;
			display:block;
		}
	</style>
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


	echo "<h1>Welcome to Matheroids, ".$query_array["user"]."</h1>";
	echo"</pre>";

?>

<form action="../Logout.php">
	<button type="submit">Logout</button></a>
</form>

<br />
<br />

<script>
	var level = 1;

	function playGame(difficulty){
		localStorage.setItem("difficulty",difficulty);

		if(difficulty = 1){
			console.log("Playing Addition");
		}else if(difficulty = 2){
			console.log("Playing Subtraction");
		}else{
			console.log("ERROR: Unspecified Difficulty");
		}
	}

	function updateLevel(){
		document.getElementById("levelText").innerHTML = "You are on level "+level;
		disableLevels();
	}

	function increaseLevel(){
		if(level < 7){
			level += 1;
		}
		updateLevel();
	}

	function decreaseLevel(){
		if(level >0){
			level -= 1;
		}
		updateLevel();
	}

	function resetLevel(){
		level = 1;
		updateLevel();
	}

	function disableLevels(){
		console.log("Attempted to enable levels.");
		var buttons = ["level1Button","level2Button","level3Button","level4Button","level5Button","level6Button","level7Button"];

		for(var i = 0; i < level; i++){
			document.getElementById(buttons[i]).disabled = false;
		}
		for(var j = level; j < 7; j++){
			document.getElementById(buttons[j]).disabled = true;
		}
	}

</script>

<div id="mainMenu" style="border:3px; border-style:solid; border-color: #22D2A0; padding: 5px; width: 500px">
	<h2>MAIN MENU</h2>
	<p style="color:#000000;" id="levelText">You are on level *</p>
	<form action="../game.php/?user=$user" >
		<button type="submit" onsubmit="playGame(1);" id="level1Button" disabled>Play Game (Addition)</button>
		<button type="submit" onsubmit="playGame(2);" id="level2Button" disabled>Play Game (Subtract)</button>
		<button type="submit" onsubmit="playGame(3);" id="level3Button" disabled>Play Game (Level 3)</button>
		<button type="submit" onsubmit="playGame(4);" id="level4Button" disabled>Play Game (Level 4)</button>
		<button type="submit" onsubmit="playGame(5);" id="level5Button" disabled>Play Game (Level 5)</button>
		<button type="submit" onsubmit="playGame(6);" id="level6Button" disabled>Play Game (Level 6)</button>
		<button type="submit" onsubmit="playGame(7);" id="level7Button" disabled>Play Game (Level 7)</button>
	</form>
</div>
<br />

<script>
	updateLevel()
</script>

<?php if($query_array["type"] == "Teacher") : ?>
<div id="teacherDashboard" style="border:3px; border-style:solid; border-color: #FFD913; padding: 5px; width: 500px">
	<h2>TEACHER DASHBOARD</h2>
	<div style="border:3px; border-style:solid; border-color: #FFD913; padding: 5px; ">
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
<br />

<div id = "accountSettings" style="border:3px; border-style:solid; border-color: #FF5555; padding: 5px; width: 500px">
	<h2>ACCOUNT SETTINGS</h2>

	<form onsubmit="resetLevel(); return false;">
		<button>Reset Level</button>
	</form>

	<form onsubmit="increaseLevel(); return false;">
		<button>Increase Level</button>
	</form>

	<form onsubmit="decreaseLevel(); return false;">
		<button>Decrease Level</button>
	</form>

	<br />
	<div style="border:3px; border-style:solid; border-color: #FF5555; padding: 5px; ">
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
