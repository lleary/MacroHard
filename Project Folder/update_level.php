<?php
require_once 'files.php';
require_once 'config.php';

extract($_POST);

function saveNewLevel($firstname, $lastname, $level){
	$all_user = get_user_info(USERFILE);

	foreach ($all_user as $user) {
		if($user["first"] == $firstname && $user["last"] == $lastname){
			$student_info = array($firstname, $lastname, "student", "0", $level);
			save_data(USERFILE, $student_info);
		}
	}
}

function increaseUserLevel(){
	$_SESSION["level"]++;
	saveNewLevel($_SESSION["user"], $_SESSION["lastname"], $_SESSION["level"]);
}

function decreaseUserLevel(){
	$_SESSION["level"]--;
	saveNewLevel($_SESSION["user"], $_SESSION["lastname"], $_SESSION["level"]);
}

function resetUserLevel(){
	$_SESSION["level"] = 1;
	saveNewLevel($_SESSION["user"], $_SESSION["lastname"], $_SESSION["level"]);
}

?>
