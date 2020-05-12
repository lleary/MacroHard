<?php
	require_once 'files.php';
	require_once 'config.php';

	extract($_POST);

	$all_user = get_user_info(USERFILE);

	$userFirstName = $_SESSION['user'];
	$userLastName = $_SESSION['lastname'];

	$userClass = findClass($userFirstName,$userLastName);

	foreach ($all_user as $user) { 	
		if($user["class"] == 'student'){			#Doesn't include teachers, eventually will only include students in a class.
			if($user["enrolledClass"] == $userClass){			
				echo "<tr>";
				for ($i = 1; $i <= 6; $i+=1){
						echo "<td>";
						if($i == 1){						#I am not entirely sure if I can just do echo "$user[$i]" so there are a bunch of if statements.
							$firstName = $user["first"];
							echo "$firstName";
						}else if($i == 2){
							$lastName = $user["last"];
							echo "$lastName";
						}else if($i == 3){
							$level1score = $user["level1Score"];
							echo "$level1score";
						}else if($i == 4){
							$level2score = $user["level2Score"];
							echo "$level2score";
						}else if($i == 5){
							$level3score = $user["level3Score"];
							echo "$level3score";
						}else if($i == 6){
							$level4score = $user["level4Score"];
							echo "$level4score";
						}
					echo "</td>";			
				}
				echo "</tr>";
			}
		}
	} 

	//This function finds the class that the signed in user belongs too.
	function findClass ($firstname, $lastname){

		$all_user = get_user_info(USERFILE);	//Reads data from users.txt

		foreach ($all_user as $user) {
			if($user["first"]==$firstname && $user["last"]==$lastname){
				return $user["enrolledClass"];
			}
		}
	}

?>
