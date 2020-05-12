<?php
	session_start();
	require_once 'files.php';
	require_once 'config.php';
	header('Content-Type: application/json');


	if( !isset($_POST['functionname']) ) { $aResult['error'] = 'No function name!'; }

    if( !isset($_POST['arguments']) ) { $aResult['error'] = 'No function arguments!'; }

    if( !isset($aResult['error']) ) {

        switch($_POST['functionname']) {
            case 'updateFileScore':
               if( !is_array($_POST['arguments']) || (count($_POST['arguments']) < 2) ) {
                   $aResult['error'] = 'Error in arguments!';
               }
               else {
                   $aResult['result'] = updateFileScore(floatval($_POST['arguments'][0]), floatval($_POST['arguments'][1]));
               }
               break;

            default:
               $aResult['error'] = 'Not found function '.$_POST['functionname'].'!';
               break;
        }

    }

	extract($_POST);
	$all_user = get_user_info(USERFILE);


	function updateFileScore($level, $score){
		$myfile = fopen(USERFILE, "r+") or die("Failed to create files");
		$str = "";
		$all_user = get_user_info(USERFILE);
		foreach ($all_user as $user) {
			$firstName = $user["first"];
			$signedIn = $_SESSION["user"];
			for($i = 1; $i <= 11; $i+=1){
				if($i==1){
					$value = $user["first"];
				}else if($i==2){
					$value = $user["last"];
				}else if($i==3){
					$value = $user["class"];
				}else if($i==4){
					$value = $user["password"];
				}else if($i==5){
					$value = $user["level"];
				}else if($i==6){
					$value = $user["enrolledClass"];
				}else if($i==7){
					$value = $user["level1Score"];
					if($firstName == $signedIn){
						if($level == '1'){
							if($score >= $value){
								$value = $score;
							}
						}
					}
				}else if($i==8){
					$value = $user["level2Score"];
					if($firstName == $signedIn){
						if($level == '2'){
							if($score >= $value){
								$value = $score;
							}
						}
					}
				}else if($i==9){
					$value = $user["level3Score"];
					if($firstName == $signedIn){
						if($level == '3'){
							if($score >= $value){
								$value = $score;
							}
						}
					}
				}else if($i==10){
					$value = $user["level4Score"];
					if($firstName == $signedIn){
						if($level == '4'){
							if($score >= $value){
								$value = $score;
							}
						}
					}
				}else if($i==11){
					$value = $user["endMarker"];
				}

				if($i != 11){
					$str .= $value." ";
				}else{
					$str .= $value;
				}
			}
		}

		if($_SESSION["class"] != 'teacher'){
			fwrite($myfile, $str) or die("Could not write to file");
		}

		fclose($myfile);
	}

?>