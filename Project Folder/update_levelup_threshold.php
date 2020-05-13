<?php
	session_start();
	require_once 'files.php';
	require_once 'config.php';

	extract($_POST);
	$all_user = get_user_info(USERFILE);

	updateClassThreshold($levelUpThreshold);

	//This function finds the class that the signed in user belongs too.
	function findClass ($firstname, $lastname){

		$all_user = get_user_info(USERFILE);	//Reads data from users.txt

		foreach ($all_user as $user) {
			if($user["first"]==$firstname && $user["last"]==$lastname){
				return $user["enrolledClass"];
			}
		}
	}

	function updateClassThreshold($levelUpThreshold){
		if($levelUpThreshold != ''){
			$myfile = fopen(CLASSFILE, "r+") or die("Failed to create files");
			$str = "";
			$all_classes = get_class_info(CLASSFILE); //Reads data from classes.txt

			$userFirstName = $_SESSION['user'];
			$userLastName = $_SESSION['lastname'];
			$userClass = findClass($userFirstName,$userLastName);

			if(strpos($levelUpThreshold, ' ') !== false){ inputError("inputs must not contain spaces"); } else {$_SESSION["accountError"] = null;}

			foreach ($all_classes as $class) {

				$className = $class["className"];

				for($i = 1; $i <= 8; $i+=1){
					if($i==1){
						$value = $class["className"];
					}else if($i==2){
						$value = $class["enabledLeaderboard"];
					}else if($i==3){
						$value = $class["bossAnswerMin"];
					}else if($i==4){
						$value = $class["bossAnswerMax"];
					}else if($i==5){
						$value = $class["normalAnswerMin"];
					}else if($i==6){
						$value = $class["normalAnswerMax"];
					}else if($i==7){
						$value = $class["threshold"];

						if($className == $userClass){
							$value = $levelUpThreshold;
						}
					}else if($i==8){
						$value = $class["endMarker"];
					}

					if($i != 8){
						$str .= $value." ";
					}else{
						$str .= $value;
					}
				}
			}

			if($_SESSION["type"] != 'teacher'){
				fwrite($myfile, $str) or die("Could not write to file");
			}

			fclose($myfile);
		}else{
			inputError("Please enter a value.");
		}
	}

	function inputError($errormessage){
		$_SESSION["accountSuccess"] = null;
		$_SESSION["accountError"] = $errormessage;
		// we should add something to this function to inform the user what went wrong
		header("Location: mainMenu.php");
		exit;
	}

	header("Location: mainMenu.php");

?>