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
		$threshold = 15;
		$threshold = findThreshold($firstname, $lastname);

		foreach ($all_user as $user) {
			if($user["first"]==$firstname && $user["last"]==$lastname){
				if($user["level1Score"] >= $threshold){
					$level+=1;	
				}
				if($user["level2Score"] >= $threshold){
					$level+=1;	
				}
				if($user["level3Score"] >= $threshold){
					$level+=1;	
				}

				if($user["class"] == 'teacher'){
					$level = 4;
				}
			}
			
		}
		return $level;
	}

	function findThreshold ($firstname, $lastname){

		$all_classes = get_class_info(CLASSFILE); //Reads data from classes.txt
		$userClass = findClass($firstname, $lastname);

		foreach ($all_classes as $class) {

			$className = $class["className"];

			if($className == $userClass){
				return $class["threshold"];
	 		}
	 	}
	 }

?>
