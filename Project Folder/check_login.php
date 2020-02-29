<?php
require_once 'files.php';
require_once 'config.php';
echo "<pre>";


extract($_POST);


//1: can login 2: user does not exist  3: invaild password
$re = checkLogin($username, $password);
//$re = 2; //Please comment this after completing your checkLogin function  

if($re===1){
	/*Redirect browser*/
	header("Location: mainMenu.php/?user=$username");

}else{
	echo "Student or teacher account not found";
	echo "\nYou will be redirected to the login page shortly...";

	/*Redirect to login.php after 5 seconds*/
    header("refresh:5; url=login.php");
}


/**
*Returns 1: can login
*		 2: user does not exist
		 3: invaild password
	*/
function checkLogin($name, $pw){
	$all_user = get_user_info(USERFILE);
	$usernames = array_keys($all_user);

	for($i = 0; $i <= sizeof($usernames)- 1; $i++){
		if($name == $usernames[$i]){
			$user = $all_user[$usernames[$i]];

			if($pw == $user['password']){
				return 1;
			} else{
				return 3;
			}
		}
	}
	return 2;
}
?>