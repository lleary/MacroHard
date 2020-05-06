<?php
	require_once 'files.php';
	require_once 'config.php';

	extract($_POST);

	$all_user = get_user_info(USERFILE);

	for ($i = 1; $i <= 4; $i+=1) { 					#For loop creates one sublist for each level i.
		echo "<li>";
	   		echo "<h2>Level $i</h2>";				#Prints the level name at the start of the list.
	   		echo "<ul id='leaderboardLevelList'>";  #Creates sublist.
	   		foreach ($all_user as $user) {
		   		if($user["level"] == $i && $user["class"] == 'student'){
		   			$firstName = $user["first"];
		   			if($firstName == $_SESSION["user"]){
		   				echo "<li id='userLeaderboardSpot'>$firstName</li>";	#Prints the users name in the leaderboard with a different id.
		   			} else{
		   				echo "<li id='leaderboardSpot'>$firstName</li>";		#Prints the rest of the users as normal.
		   			}
		   		}
		   	}
	   		echo "</ul>";
   		echo "</li>";
	} 

?>
