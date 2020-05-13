<?php
	session_start();
	require_once 'files.php';
	require_once 'config.php';

	extract($_POST);
	$all_user = get_user_info(USERFILE);

	updateNameData($newFirstName, $newLastName);

	function updateNameData($newFirstName, $newLastName){
		$myfile = fopen(USERFILE, "r+") or die("Failed to create files");
		$str = "";
		$all_user = get_user_info(USERFILE);
		foreach ($all_user as $user) {
			$lastName = $_SESSION["lastname"];
			$signedIn = $_SESSION["user"];

			$currentFirstName = $user["first"];
			$currentLastName = $user["last"];

			for($i = 1; $i <= 11; $i+=1){
				if($i==1){
					$value = $user["first"];
					if($signedIn == $currentFirstName && $lastName == $currentLastName){
						$value = strtolower($newFirstName);
					}
				}else if($i==2){
					$value = $user["last"];
					if($signedIn == $currentFirstName && $lastName == $currentLastName){
						$value = strtolower($newLastName);
					}
				}else if($i==3){
					$value = $user["type"];
				}else if($i==4){
					$value = $user["password"];
				}else if($i==5){
					$value = $user["level"];
				}else if($i==6){
					$value = $user["enrolledClass"];
				}else if($i==7){
					$value = $user["level1Score"];
				}else if($i==8){
					$value = $user["level2Score"];
				}else if($i==9){
					$value = $user["level3Score"];
				}else if($i==10){
					$value = $user["level4Score"];
				}else if($i==11){
					$value = $user["endMarker"];
				}

				if($i != 11){
					$str .= $value." ";
				}else{
					$str .= $value;
				}
			}
		}

		fwrite($myfile, $str) or die("Could not write to file");

		fclose($myfile);
	}

	header("Location: logout.php");

?>