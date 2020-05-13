<?php
	require_once 'files.php';
	require_once 'config.php';

	extract($_POST);

	$all_user = get_user_info(USERFILE);

/*	for ($i = 1; $i <= 4; $i+=1) { 					#For loop creates one sublist for each level i.
		echo "<li>";
	   		echo "<h2>Level $i</h2>";				#Prints the level name at the start of the list.
	   		echo "<ul id='leaderboardLevelList'>";  #Creates sublist.
	   		foreach ($all_user as $user) {
		   		if($user["level"] == $i && $user["type"] == 'student'){
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
	} */

	$userFirstName = $_SESSION['user'];
	$userLastName = $_SESSION['lastname'];

	$userClass = findClass($userFirstName,$userLastName);

	for ($i = 1; $i <= 4; $i+=1) { 					#For loop creates one sublist for each level i.
		echo "<li>";
	   		echo "<h2>Level $i</h2>";				#Prints the level name at the start of the list.
	   		echo "<ul id='leaderboardLevelList'>";  #Creates sublist.

	   		$firstPlaceName = '';
	   		$secondPlaceName = '';
	   		$thirdPlaceName = '';

	   		$firstPlaceScore = 0;
	   		$secondPlaceScore = 0;
	   		$thirdPlaceScore = 0;

	   		foreach ($all_user as $user) {
		   		if($user["type"] == 'student'){
		   			if($user["enrolledClass"] == $userClass){

			   			$firstName = ucfirst($user["first"]);

			   			if($i == 1){
			   				$score = $user["level1Score"];
			   			} else if($i == 2){
			   				$score = $user["level2Score"];
			   			} else if($i == 3){
			   				$score = $user["level3Score"];
			   			} else if($i == 4){
			   				$score = $user["level4Score"];
			   			}

			   			if($score >= $firstPlaceScore && $score != 0){
			   				$thirdPlaceName = $secondPlaceName;
			   				$thirdPlaceScore = $secondPlaceScore;
			   				$secondPlaceName = $firstPlaceName;
			   				$secondPlaceScore = $firstPlaceScore;

			   				$firstPlaceName = $firstName;
			   				$firstPlaceScore = $score;
			   			} else if ($score >= $secondPlaceScore && $score != 0){
			   				$thirdPlaceName = $secondPlaceName;
			   				$thirdPlaceScore = $secondPlaceScore;

			   				$secondPlaceName = $firstName;
			   				$secondPlaceScore = $score;
			   			} else if ($score >= $thirdPlaceScore && $score != 0){
			   				$thirdPlaceName = $firstName;
			   				$thirdPlaceScore = $score;
			   			}
			   		}
		   		}
		   	}

		   	if($firstPlaceName != ''){
		   		echo "<li id='LeaderboardSpot'>$firstPlaceName: $firstPlaceScore</li>";
		   	}
		   	if($secondPlaceName != ''){
		   		echo "<li id='LeaderboardSpot'>$secondPlaceName: $secondPlaceScore</li>";
		    }
		   	if($thirdPlaceName != ''){
		   		echo "<li id='LeaderboardSpot'>$thirdPlaceName: $thirdPlaceScore</li>";
			}


	   		echo "</ul>";
   		echo "</li>";
	} 

?>
