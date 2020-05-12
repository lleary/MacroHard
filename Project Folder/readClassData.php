<?php
	require_once 'files.php';
	require_once 'config.php';

	extract($_POST);

	$bossDigitMin = 0;
	$bossDigitMax = 0;
	$normalDigitMin = 0;
	$normalDigitMax = 0;
	
	$all_classes = get_class_info(CLASSFILE); //Reads data from classes.txt

	$userFirstName = $_SESSION['user'];
	$userLastName = $_SESSION['lastname'];

	$userClass = findClass($userFirstName,$userLastName);

	//This function finds the class that the signed in user belongs too.
	function findClass ($firstname, $lastname){

		$all_user = get_user_info(USERFILE);	//Reads data from users.txt

		foreach ($all_user as $user) {
			if($user["first"]==$firstname && $user["last"]==$lastname){
				return $user["enrolledClass"];
			}
		}
	}

	foreach($all_classes as $class){			//iterates through each class.
		if($class["className"] == $userClass){	//finds the class that the user belongs to

				$bossAnswerMin = $class['bossAnswerMin'];	//Sets each variable to that belonging to the class.
				$bossAnswerMax = $class['bossAnswerMax'];
				$normalAnswerMin = $class['normalAnswerMin'];
				$normalAnswerMax = $class['normalAnswerMax'];
		}
	}
?>
