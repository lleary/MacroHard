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
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
	<style><?php include 'stylesheet.css'; ?></style>
</head>

<body>

	<!-- Navigation Bar !-->
	<ul id="navBar">
		<li id="navBarItem"><a class="active" href="#main" onClick="showMenu('mainMenu')">Main</a></li>
		<li id="navBarItem"><a href="#leaderboard" onClick="showMenu('leaderboard')">Leaderboard</a></li>
		<?php if($_SESSION["type"] == "Teacher") : ?>		<!-- Only shows teacher dashboard tab if a teacher is signed in !-->
			<li id="navBarItem"><a href="#teacherDashboard" onClick="showMenu('teacherDashboard')">Teacher Dashboard</a></li>
			<li id="navBarItem"><a href="#gradebook" onClick="showMenu('gradebook')">Gradebook</a></li>
		<?php endif; ?>
		<li id="navBarItem"><a href="#settings" onClick="showMenu('accountSettings')">Settings</a></li>
		<li id="navBarItem" style="float:right;"><a href="./Logout.php">Logout</a></li>
	</ul>

	<script>

		$(document).ready(function(){				//Only runs after the document is loaded.
			$('ul li a').click(function(){			//This fuction changed the class of the current menu tab to active.
	    		$('li a').removeClass("active");	//Removes menu class from old tab.
	   			$(this).addClass("active");			//Adds menu class to new tab.
			});
		});

		//This function hides sets the visibility of all menus to hidden except for the input menu.
		function showMenu(menu){
			document.getElementById('mainMenu').style.visibility='hidden';
			document.getElementById('leaderboard').style.visibility='hidden';
			document.getElementById('accountSettings').style.visibility='hidden';
			<?php if($_SESSION["type"] == "Teacher") : ?>									//Only disables the teacher dashboard if a teacher is signed in. 
				document.getElementById('teacherDashboard').style.visibility='hidden';
				document.getElementById('gradebook').style.visibility='hidden';
			<?php endif; ?>
			document.getElementById(menu).style.visibility='visible';
		}

	</script>

	<?php echo "<h1>Welcome to Matheroids, ".$_SESSION["user"]."</h1>"; ?>

	<br />

	<script>

		var level = 1;									//Set's the default level to 1 incase the session fails.
		var level = <?php echo $_SESSION['level'] ?>; 	//Updates the users level to the one stored in the session.

		//This function sets the localStorage "gamemode" to the given gamemode.
		function setGamemode(gamemode){
			localStorage.setItem("gamemode", gamemode);
		}



		//Increases the users level by 1, with a maximum of 4.
		function increaseLevel(){
			if(level < 4){
				level += 1;
			}
			updateLevel();
		}

		//Decreases the users level by 1, with a minimum of 1.
		function decreaseLevel(){
			if(level >1){
				level -= 1;
			}
			updateLevel();
		}

		//Sets the users level to 1.
		function resetLevel(){
			level = 1;
			updateLevel();
		}

		//This function updates the level in the database and on screen.
		function updateLevel(){
			document.getElementById("levelText").innerHTML = "You are on level "+level;
			disableLevels();
		}

		//Disables buttons for levels the user does not have access to yet
		function disableLevels(){
			console.log("Attempted to enable levels.");
			var buttons = ["level1Button","level2Button","level3Button","level4Button"];

			for(var i = 0; i < level; i++){
				document.getElementById(buttons[i]).disabled = false;
			}
			for(var j = level; j < 4; j++){
				document.getElementById(buttons[j]).disabled = true;
			}
		}

	</script>

	<div id="mainMenu" style="border:3px; border-style:solid; border-color: #22D2A0; padding: 5px; width: 500px">
		<h2>MAIN MENU</h2>

		<p style="color:#000000;" id="levelText">You are on level *</p>


		<form onsubmit="setGamemode(0)" action="./game.php">
			<button type="submit" id="level1Button">Play Game (Digit Identification)</button>
		</form>
		<form onsubmit="setGamemode(1)" action="./game.php">
			<button type="submit" id="level2Button" disabled>Play Game (Addition)</button>
		</form>
		<form onsubmit="setGamemode(2)" action="./game.php">
			<button type="submit" id="level3Button" disabled>Play Game (Subtraction)</button>
		</form>
		<form onsubmit="setGamemode(3)" action="./game.php">
			<button type="submit" id="level4Button" disabled>Play Game (Addition & Subtraction)</button>
		</form>

		<script> updateLevel() </script>	<!-- Unlocks buttons and updates text after they have been created. !-->
	</div>

	<br />


	<?php if($_SESSION["type"] == "Teacher") : ?>	<!-- Only draws teacher dashboard if a teacher is signed in. !-->
		<div id="teacherDashboard" style="border:3px; border-style:solid; border-color: #FFD913; padding: 5px; width: 500px">
			<h2>TEACHER DASHBOARD</h2>
			<div style="border:3px; border-style:solid; border-color: #FFD913; padding: 5px; ">
				<h2>Create Student Accounts</h2>
				<form action="add_student.php" method="post" id="form_id">
					Students First Name:
					<input type="text" name="firstname" id="firstname" placeholder="First Name" />
					<br/><br/>
					Students Last Name:
					<input type="text" name="lastname" id="lastname" placeholder="Last Name" /><br/><br/>
					<input type="submit" name="submit_id" id="create" value="Create Account" />
				</form>
			</div>

			<br />

			<!-- This is the form for teachers to edit the details of a level. !-->
			<!-- It is NOT functional. It will edit the details by class but classes aren't implemented yet. !-->
			<div style="border:3px; border-style:solid; border-color: #FFD913; padding: 5px; ">
				<h2>Edit Level</h2>
				<form action="" method="post" id="form_id">

					<label for="levels">Level:</label>
					<select id="levels">
					  <option value="1">1 (Addition)</option>
					  <option value="2">2 (Subtraction)</option>
					  <option value="3">3 (Addition & Subtraction)</option>
					  <option value="4">4 (Digit Identification)</option>
					</select>

					<br />

					<label for="levelUpThreshold">Level Up Threshold:</label>
					<input type="text" id="levelUpThreshold" placeholder="Threshold" />

					<br />

					<label for="levels">Range:</label>
					<input type="text" id="rangeMinimum" placeholder="Minimum" />
					to
					<input type="text" id="rangeMaximum" placeholder="Maximum" />

					<br />

					<input type="submit" name="submit_id" id="editLevel" value="Submit" />
					<input type="submit" name="submit_id" id="resetLevel" value="Reset to Default" />
				</form>
			</div>

		</div>

		<div id="gradebook" style="border:3px; border-style:solid; border-color: #3fb7d9; padding: 5px; width: 700px">
			<h2>GRADEBOOK</h2>
			<table id="gradebookTable">
				<tr>
			    	<th>Firstname</th>
			    	<th>Lastname</th> 
			    	<th>Level 1</th>
			    	<th>Level 2</th>
			    	<th>Level 3</th>
			    	<th>Level 4</th>
				</tr>

				<?php
					include 'createGradebook.php';	#Creates Gradebook by including the code from createGradebook.php
				?>
			</table>

		</div>


	<?php endif; ?>
	<br />


	<div id = "leaderboard" style="border:3px; border-style:solid; border-color: #c238d1; padding: 5px; width: 500px">
		<h2>LEADERBOARD</h2>

		<ul id="leaderboardList">
			<?php
				include 'createLeaderboard.php';	#Creates leaderboard by including the code from createLeaderboard.php
			?>
		</ul>
		
	</div>
	<br />

	<div id = "accountSettings" style="border:3px; border-style:solid; border-color: #FF5555; padding: 5px; width: 500px">
		<h2>ACCOUNT SETTINGS</h2>

		<?php if($_SESSION["type"] == "Teacher") : ?>
			<form onsubmit="resetLevel(); return false;">
				<button>Reset Level</button>
			</form>

			<form onsubmit="increaseLevel(); return false;">
				<button>Increase Level</button>
			</form>

			<form onsubmit="decreaseLevel(); return false;">
				<button>Decrease Level</button>
			</form>

			<br/>
		<?php endif; ?>

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