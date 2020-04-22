<?php
require_once 'files.php';
require_once 'config.php';

extract($_POST);

updateLevel($_SESSION["user"], $_SESSION['lastname'], $_SESSION['level']);

function updateLevel ($firstname, $lastname, $level){
	$all_user = get_user_info(USERFILE);

	foreach ($all_user as $user) {
		if($user["first"]==$firstname && $user["last"]==$lastname){
			$student_info = array($username, $password, "student", "0", $_SESSION['level']);
			update_file(USERFILE, $student_info);
		}
	}
}

?>
