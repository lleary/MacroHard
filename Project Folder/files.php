<?php 
	/******File Handling*******/
	$key_arr = array("first", "last", "class", "password", "level", "enrolledClass", "level1Score", "level2Score", "level3Score" ,"level4Score");

	$key_arr_class = array("className", "enabledLeaderboard", "bossAnswerMin", "bossAnswerMax", "normalAnswerMin", "normalAnswerMax");
	

	/*Write $data to $filename*/
	function save_data($filename, $data){
		$str = "\n".join(" ", $data);

		//$myfile = fopen("DB/$filename", "w") or die("Failed to create files");
		/*Append data to $filename*/
		/*More fopen modes on page 149*/
		$myfile = fopen($filename, "a") or die("Failed to create files");
		
		fwrite($myfile, $str) or die("Could not write to file");

		fclose($myfile);
	}


	function update_file($file, $data){
		//override
		$myfile = fopen($file, "w") or die("Failed to create files");
		$str = "";
		foreach ($data as $key => $value) {
			$str .= join(" ", $value);
		}

		fwrite($myfile, $str) or die("Could not write to file");

		fclose($myfile);
	}

	/***Reading from a file: fgets**/
	function get_user_info($filename){
		global $key_arr;

		$myfile = fopen($filename, "r") or die("File does not exist");

		/*could use fread()*/
		while($line=fgets($myfile)){
			//Convert to array by " " 
			$res = explode(" ", $line);
			$new_res = [];
			//Replace keys in $res
			for($i = 0; $i<count($key_arr); $i++){
				$new_res[$key_arr[$i]] = $res[$i];
			}

			$info_arr[$res[0]] = $new_res;
			//Destory local variable $new_res
			unset($new_res);
			unset($res);
		}

		fclose($myfile);
		return $info_arr;
	}

	function get_class_info($filename){
		global $key_arr_class;

		$myfile = fopen($filename, "r") or die("File does not exist");

		/*could use fread()*/
		while($line=fgets($myfile)){
			//Convert to array by " " 
			$res = explode(" ", $line);
			$new_res = [];
			//Replace keys in $res
			for($i = 0; $i<count($key_arr_class); $i++){
				$new_res[$key_arr_class[$i]] = $res[$i];
			}

			$info_arr[$res[0]] = $new_res;
			//Destory local variable $new_res
			unset($new_res);
			unset($res);
		}

		fclose($myfile);
		return $info_arr;
	}



	/*****Copy a file*****/
	/*$file2_name = "DB/useer_copy.txt";
	if(!copy($file_name, $file2_name)){
		echo "Could not copy file";
	}else{
		echo "Copy $file_name to $file2_name";
	}*/


	/*Question: How should we update use.txt file;*/
	/***Delete a file***/
  	//echo unlink($file_name)? "Delete file $file_name" : "Could not delete file $file_name";


?>