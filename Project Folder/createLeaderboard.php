<?php
	require_once 'files.php';
	require_once 'config.php';

	extract($_POST);

	$all_user = get_user_info(USERFILE);

	for ($i = 1; $i <= 7; $i+=1) {
		echo "<li>";
	   		echo "<h2>Level $i</h2>";
	   		echo "<ul id='leaderboardLevelList'>";
	   		foreach ($all_user as $user) {
		   		if($user["level"] == $i){
		   			$firstName = $user["first"];
		   			if($firstName == $_SESSION["user"]){
		   				echo "<li id='userLeaderboardSpot'>$firstName</li>";
		   			} else{
		   				echo "<li id='leaderboardSpot'>$firstName</li>";
		   			}
		   		}
		   	}
	   		echo "</ul>";
   		echo "</li>";
	} 

?>


<!-- An example of a properly formed list !-->

<!--<ul id="leaderboardList">
		<li>
			<h2>Level 1</h2>
			<ul>
				<li>Person 1</li>
				<li>Person 2</li>
			</ul>
		</li>
		<li>
			<h2>Level 2</h2>
			<ul>
				<li>Person 1</li>
				<li>Person 2</li>
			</ul>
		</li>
		<li>
			<h2>Level 3</h2>
			<ul>
				<li>Person 1</li>
				<li>Person 2</li>
			</ul>
		</li>
		<li>
			<h2>Level 4</h2>
			<ul>
				<li>Person 1</li>
				<li>Person 2</li>
			</ul>
		</li>
		<li>
			<h2>Level 5</h2>
			<ul>
				<li>Person 1</li>
				<li>Person 2</li>
			</ul>
		</li>
		<li>
			<h2>Level 6</h2>
			<ul>
				<li>Person 1</li>
				<li>Person 2</li>
			</ul>
		</li>
		<li>
			<h2>Level 7</h2>
			<ul>
				<li>Person 1</li>
				<li>Person 2</li>
			</ul>
		</li>
	</ul>!-->