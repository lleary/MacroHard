<?php
require_once 'files.php';
require_once 'config.php';

extract($_POST);

function updateLevel ($firstname, $lastname, $level){
	$all_user = get_user_info(USERFILE);

	foreach ($all_user as $user) {
		if($user["first"]==$firstname && $user["last"]==$lastname){
			$student_info = array($firstname, $lastname, "student", "0", $_SESSION['level']);
			save_data(USERFILE, $student_info);
		}
	}
}

?>
