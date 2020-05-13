<?php
	session_start();
?>

<?php
require_once 'files.php';
require_once 'config.php';
echo "<pre>";

$pw = 'null';
extract($_POST);



$re = checkLogin($firstname, $lastname, $pw);

if($re===1){ // Student correct login
	/*Redirect browser to student menu TODO*/
	$_SESSION['user'] = strtolower($firstname);
	$_SESSION['lastname'] = strtolower($lastname);
	$_SESSION['type'] = "Student";
	header("Location: mainMenu.php");

}
elseif($re===2){ // Teacher correct login
	/*Redirect browser to teacher menu TODO*/
	$_SESSION['user'] = strtolower($firstname);
	$_SESSION['lastname'] = strtolower($lastname);
	$_SESSION['type'] = "Teacher";
	$_SESSION['class'] = $class;
	$_SESSION['accountError'] = null;
	$_SESSION['accountSuccess'] = null;
	header("Location: mainMenu.php");
}
elseif($re===3){ // Teacher incorrect password
	/*Redirect to teacher login*/
	echo "Incorrect password";
	echo "\nYou will be redirected to the login page shortly...\n";

	header("refresh:5; url=teacher_login.php");
}
else{ // Account not found
	echo "Account not found";
	echo "\nYou will be redirected to the login page shortly...";

	/*Redirect to welcome.php after 5 seconds*/
    header("refresh:5; url=welcome.php");
}


/**
 * Returns 1: student successful login
 *         2: teacher successful login
 *		   3: teacher password incorrect
 *		   4: account not found
 */
function checkLogin ($firstname, $lastname, $pw){
	$all_user = get_user_info(USERFILE);

	foreach ($all_user as $user) {
		$userFirstName = $user["first"];
		$userLastName = $user["last"];

		if(strtolower($userFirstName)==strtolower($firstname) && strtolower($userLastName)==strtolower($lastname)){
			if($user["type"]=="student") {
				return 1;
			}
			elseif($user["type"]=="teacher"){
				if(password_verify($pw, $user["password"])) {
					return 2;
				}
				else {
					return 3;
				}
			}
			return 4;
		}
	}
	return 4;
}

?>
