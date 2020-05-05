<?php 
session_start();
  if (!isset($_SESSION['user']))
   {
      header("location: welcome.php");
      die();
   }

	include 'check_level.php';
	include 'update_level.php';
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Matheroids</title>
	<link rel="stylesheet" type="text/css"href="stylesheet.css">
	<style>

	<?php
		include 'stylesheet.css';
	?>

	</style>


</head>
<body>


<?php 
	echo "<pre>";

	echo "<h1>Welcome to Matheroids, ".$_SESSION["user"]."</h1>";
	echo"</pre>";

?>

<form action="./Logout.php">
	<button type="submit">Logout</button></a>
</form>

<br />
<br />



<script>
	var level = 1;
	var level = <?php echo $_SESSION['level'] ?>; 
	console.log(level);

	function setGamemode(gamemode){
		localStorage.setItem("gamemode", gamemode);
		console.log("gamemode set to " + gamemode);
	}

	function updateLevel(){
		document.getElementById("levelText").innerHTML = "You are on level "+level;
		disableLevels();
		<?php
			//updateLevel ($_SESSION["user"], $_SESSION["lastname"], $_SESSION["level"]);
		?>
	}


	function increaseLevel(){
		if(level < 7){
			level += 1;
		}

		<?php
			$_SESSION["level"]++;
		?>
		console.log("new level is: "+<?php echo $_SESSION['level'] ?>);

		updateLevel();
	}

	function decreaseLevel(){
		if(level >1){
			level -= 1;
		}
		updateLevel();
	}

	function resetLevel(){
		level = 1;
		updateLevel();
	}

	// disables buttons for levels the user does not have access to yet
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

	<form onsubmit="setGamemode(1)" action="./game.php">
		<button type="submit" id="level1Button" disabled>Play Game (Addition)</button>
	</form>
	<form onsubmit="setGamemode(2)" action="./game.php">
		<button type="submit" id="level2Button" disabled>Play Game (Subtraction)</button>
	</form>
	<form onsubmit="setGamemode(3)" action="./game.php">
		<button type="submit" id="level3Button" disabled>Play Game (Addition & Subtraction)</button>
	</form>
	<form onsubmit="setGamemode(4)" action="./game.php">
		<button type="submit" id="level4Button" disabled>Play Game (Level 4)</button>
	</form>
	<form onsubmit="setGamemode(5)" action="./game.php">
		<button type="submit" id="level5Button" disabled>Play Game (Level 5)</button>
	</form>
	<form onsubmit="setGamemode(6)" action="./game.php">
		<button type="submit" id="level6Button" disabled>Play Game (Level 6)</button>
	</form>
	<form onsubmit="setGamemode(7)" action="./game.php">
		<button type="submit" id="level7Button" disabled>Play Game (Level 7)</button>
	</form>
	<form onsubmit="setGamemode(0)" action="./game.php">
		<button type="submit" id="level0Button">Play Game (Digit Identification)</button>
	</form>
		
</div>
<br />

<script>
	updateLevel()
</script>

<?php if($_SESSION["type"] == "Teacher") : ?>
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

<div id = "leaderboard" style="border:3px; border-style:solid; border-color: #c238d1; padding: 5px; width: 500px">
	<h2>LEADERBOARD</h2>

	<ul id="leaderboardList">
		<?php
			include 'createLeaderboard.php';
		?>
	</ul>
	
</div>
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
