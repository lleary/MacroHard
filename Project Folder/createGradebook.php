<?php
	require_once 'files.php';
	require_once 'config.php';

	extract($_POST);

	$all_user = get_user_info(USERFILE);

	$userFirstName = $_SESSION['user'];
	$userLastName = $_SESSION['lastname'];

	$userClass = findClass($userFirstName,$userLastName);

	foreach ($all_user as $user) { 	
		if($user["type"] == 'student'){			#Doesn't include teachers, eventually will only include students in a class.
			if($user["enrolledClass"] == $userClass){			
				echo "<tr>";
				for ($i = 1; $i <= 6; $i+=1){
						echo "<td>";
						if($i == 1){						#I am not entirely sure if I can just do echo "$user[$i]" so there are a bunch of if statements.
							$firstName = ucfirst($user["first"]);
							echo "$firstName";
						}else if($i == 2){
							$lastName = ucfirst($user["last"]);
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

?>
