<?php
	
	require_once 'files.php';
	require_once 'config.php';
	echo "<pre>";

	extract($_POST);

	createTeacherAccount($firstname, $lastname, $password, $cPassword, $class);
	echo "Teacher ".$firstname." ".$lastname." added";

	function createTeacherAccount($firstname, $lastname, $password, $cPassword, $class){
		// must not be a teacher account that already exists
		$all_users = get_user_info(USERFILE);
		foreach($all_users as $user){
			if($user["first"] === $firstname && $user["last"] === $lastname && $user["type"] == "teacher"){
				inputError();
			}
		}

		// inputs must not contain spaces
		if(strpos($firstname, ' ') !== false){ inputError(); }
		if(strpos($lastname, ' ') !== false){ inputError(); }
		if(strpos($password, ' ') !== false){ inputError(); } // no need to check for $cPassword since it must be equal to password
		if(strpos($class, ' ') !== false){ inputError(); }

		if($cPassword === $password && $firstname != '' && $lastname != '' && $password != '' && $class != ''){
			$teacher_info = array($firstname, $lastname, "teacher", password_hash($password, PASSWORD_DEFAULT), "4", $class, "0", "0", "0", "0", "#");

			save_data(USERFILE, $teacher_info);

			header("Location: teacher_login.php");
		}
		else{
			inputError();
		}
	}

	function inputError(){
		// we should add something to this function to inform the user what went wrong
		header("Location: new_teacher_form.php");
		exit;
	}
?>