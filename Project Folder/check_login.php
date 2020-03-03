<?php
require_once 'files.php';
require_once 'config.php';
echo "<pre>";


extract($_POST);


//1: can login 2: user does not exist  3: invaild password
$re = checkLogin($firstname, $lastname);
//$re = 2; //Please comment this after completing your checkLogin function  

if($re===1){
	/*Redirect browser*/
	header("Location: mainMenu.php/?user=$username");

if($re===3){
	echo "teacher accounts not supported yet";
	/*redirect to login.php after 5 seconds*/
	header("refresh:5; url=login.php");
}

}else{
	echo "Student account not found";
	echo "\nYou will be redirected to the login page shortly...";

	/*Redirect to login.php after 5 seconds*/
    header("refresh:5; url=login.php");
}


/**
*Returns 1: can login
*		 2: user does not exist
		 3: invaild password
	*/
function checkLogin($firstname, $lastname){
	$all_user = get_user_info(USERFILE);

	foreach ($all_user as $user) {
		if($user["first"]==$firstname && $user["last"]==$lastname){
			if($user["class"]=="student") {
				return 1;
			}
			if($user["class"]=="teacher"){
				return 3;
			}
		return 2;
	}
}
?>