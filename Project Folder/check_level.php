<?php
require_once 'files.php';
require_once 'config.php';

extract($_POST);

$re = checkLevel($_SESSION["user"], $_SESSION['lastname']);

$_SESSION['level'] = $re;

#this function reads the user level from the database. As of release 3. Userlevels are determined by each student's highscores.
/*function checkLevelOld ($firstname, $lastname){
	$all_user = get_user_info(USERFILE);

	foreach ($all_user as $user) {
		if($user["first"]==$firstname && $user["last"]==$lastname){
			return $user["level"];
				
		}
	}
	return 0;
}*/

function checkLevel ($firstname, $lastname){
	$all_user = get_user_info(USERFILE);

	$level = 1;

	foreach ($all_user as $user) {
		if($user["first"]==$firstname && $user["last"]==$lastname){
			if($user["level1Score"] >= 15){
				$level+=1;	
			}
			if($user["level2Score"] >= 15){
				$level+=1;	
			}
			if($user["level3Score"] >= 15){
				$level+=1;	
			}

			if($user["class"] == 'teacher'){
				$level = 4;
			}
		}
		
	}
	return $level;
}

?>
