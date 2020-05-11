<?php
	
	require_once 'files.php';
	require_once 'config.php';
	echo "<pre>";

	extract($_POST);

	createTeacherAccount($firstname, $lastname, $password, $class);
	echo "Teacher ".$firstname." ".$lastname." added";

	function createTeacherAccount($firstname, $lastname, $password, $class){
		$teacher_info = array($firstname, $lastname, "teacher", $password, "4", $class, "0", "0", "0", "0");

		save_data(USERFILE, $teacher_info);
	}

	header("Location: welcome.php");
?>