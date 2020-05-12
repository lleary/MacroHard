<?php
	
	require_once 'files.php';
	require_once 'config.php';
	echo "<pre>";

	extract($_POST);

	createTeacherAccount($firstname, $lastname, $password, $cPassword, $class);
	echo "Teacher ".$firstname." ".$lastname." added";

	function createTeacherAccount($firstname, $lastname, $password, $cPassword, $class){
		if($cPassword === $password && $firstname != '' && $lastname != '' && $password != '' && $class != ''){
			$teacher_info = array($firstname, $lastname, "teacher", password_hash($password, PASSWORD_DEFAULT), "4", $class, "0", "0", "0", "0");

			save_data(USERFILE, $teacher_info);

			header("Location: teacher_login.php");
		}
		else{
			header("Location: new_teacher_form.php");
		}
	}
?>