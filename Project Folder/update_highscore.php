<?php
	session_start();

	require_once 'files.php';
	require_once 'config.php';

	extract($_POST);
	$all_user = get_user_info(USERFILE);

	$level = 0;
	$score = 0;
	//updateHighScore('1', '10000');
	updateFileScore($level, $score);

	function checkForNewHighScore($level, $score){
		foreach ($all_user as $user) { 
			$firstName = $user["first"];
			if($firstName == $_SESSION["user"]){
				$level1score = $user["level1Score"];
				$level2score = $user["level2Score"];
				$level3score = $user["level3Score"];
				$level4score = $user["level4Score"];

				if($level == 1 && $score >= $level1score){
					updateHighScore($level, $score);
				}else if($level == 2  && $score >= $level2score){
					updateHighScore($level, $score);
				}else if($level == 3  && $score >= $level3score){
					updateHighScore($level, $score);
				}else if($level == 4  && $score >= $level4score){
					updateHighScore($level, $score);
				}
			}
		}
	}

	function updateHighScore($level, $score){

		if ($level == 1){ 
			$student_info = array($_SESSION['user'], $_SESSION['lastname'], "student", "0", "1", "0", $score, "0", "0", "0");
		}else if($level == 2){
			$student_info = array($_SESSION['user'], $_SESSION['lastname'], "student", "0", "1", "0", "0", $score, "0", "0");
		}else if($level == 3){
			$student_info = array($_SESSION['user'], $_SESSION['lastname'], "student", "0", "1", "0", "0", "0", $score, "0");
		}else if($level == 4){
			$student_info = array($_SESSION['user'], $_SESSION['lastname'], "student", "0", "1", "0", "0", "0", "0", $score);
		}

		//update_file(USERFILE, $student_info);
	}

	function updateFileScore($level, $score){
		$myfile = fopen(USERFILE, "w") or die("Failed to create files");
		$str = "";
		foreach ($data as $key => $value) {
				//echo " ";
				//echo "$data";
				//echo "$key";
				//echo "$value";
				$str .= join(" ", $value);

		}

		fwrite($myfile, $str) or die("Could not write to file");

		fclose($myfile);
	}

	//header("Location: mainMenu.php");
?>