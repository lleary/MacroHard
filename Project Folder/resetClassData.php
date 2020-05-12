<?php
	session_start();
	require_once 'files.php';
	require_once 'config.php';

	extract($_POST);
	$all_user = get_user_info(USERFILE);

	resetClassData();

	//This function finds the class that the signed in user belongs too.
	function findClass ($firstname, $lastname){

		$all_user = get_user_info(USERFILE);	//Reads data from users.txt

		foreach ($all_user as $user) {
			if($user["first"]==$firstname && $user["last"]==$lastname){
				return $user["enrolledClass"];
			}
		}
	}

	function resetClassData(){
		$myfile = fopen(CLASSFILE, "r+") or die("Failed to create files");
		$str = "";
		$all_classes = get_class_info(CLASSFILE); //Reads data from classes.txt

		$userFirstName = $_SESSION['user'];
		$userLastName = $_SESSION['lastname'];
		$userClass = findClass($userFirstName,$userLastName);

		foreach ($all_classes as $class) {

			$className = $class["className"];

			for($i = 1; $i <= 8; $i+=1){
				if($i==1){
					$value = $class["className"];
				}else if($i==2){
					$value = $class["enabledLeaderboard"];

					if($className == $userClass){
						$value = 1;
					}
				}else if($i==3){
					$value = $class["bossAnswerMin"];

					if($className == $userClass){
						$value = 20;
					}

				}else if($i==4){
					$value = $class["bossAnswerMax"];

					if($className == $userClass){
						$value = 39;
					}

				}else if($i==5){
					$value = $class["normalAnswerMin"];

					if($className == $userClass){
						$value = 0;
					}

				}else if($i==6){
					$value = $class["normalAnswerMax"];

					if($className == $userClass){
						$value = 19;
					}

				}else if($i==7){
					$value = $class["threshold"];

				}else if($i==7){
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
	}

	header("Location: mainMenu.php");
?>