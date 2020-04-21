<?php
require_once 'files.php';
require_once 'config.php';

extract($_POST);

$re = checkLevel($_SESSION["user"], $_SESSION['lastname']);

$_SESSION['level'] = $re;

function checkLevel ($firstname, $lastname){
	$all_user = get_user_info(USERFILE);

	foreach ($all_user as $user) {
		if($user["first"]==$firstname && $user["last"]==$lastname){
			return $user["level"];
				
		}
	}
	return 0;
}

?>
