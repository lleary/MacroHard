<?php
	
	require_once 'files.php';
	require_once 'config.php';
	echo "<pre>";

	extract($_POST);

	createStudentAccount($firstname, $lastname);
	echo "Student ".$firstname." ".$lastname." added";

	function createStudentAccount($firstname, $lastname){
		$student_info = array($firstname, $lastname, "student", "0", "1", "0", "0", "0", "0", "0");

		save_data(USERFILE, $student_info);
	}

	header("Location: mainMenu.php");
?>