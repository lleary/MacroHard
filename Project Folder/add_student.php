<?php
	require_once 'files.php';
	require_once 'config.php';
	echo "<pre>";

	session_start();

	extract($_POST);

	createStudentAccount($firstname, $lastname);
	echo "Student ".$firstname." ".$lastname." added";

	function createStudentAccount($firstname, $lastname){
		// get the teacher who is creating this student account, and their class
		$class = "";
		$user_info = get_user_info(USERFILE);
		foreach($user_info as $user){
			if($_SESSION["user"] === $user["first"]){
				$class = $user["enrolledClass"];
			}
		}

		// if the student already exists in the class, thats invalid
		foreach($user_info as $user){
			if($user["first"] === $firstname && $user["last"] === $lastname && $user["enrolledClass"] === $class){
				inputError("student already exists");
			}
		}

		// inputs must not contain spaces
		if(strpos($firstname, ' ') !== false){ inputError("inputs must not contain spaces"); }
		if(strpos($lastname, ' ') !== false){ inputError("inputs must not contain spaces"); }

		// inputs must not be empty
		if($firstname != '' && $lastname != ''){
			$student_info = array(strtolower($firstname), strtolower($lastname), "student", "0", "1", $class, "0", "0", "0", "0", "#");
			save_data(USERFILE, $student_info);
			$_SESSION["accountSuccess"] = "student successfully created";
			$_SESSION["accountError"] = null;
			header("Location: mainMenu.php");
		}
		else{
			inputError("inputs must not be empty");
		}
	}

	function inputError($errorMessage){
		$_SESSION["accountError"] = $errorMessage;
		$_SESSION["accountSuccess"] = null;
		// we should add something to this function to inform the user what went wrong
		header("Location: mainMenu.php#teacherDashboard");
		exit;
	}
?>