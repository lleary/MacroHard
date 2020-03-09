<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Matheroids</title>
	<link rel="stylesheet" type="text/css"href="stylesheet.css">
	</head>
<body>
<?php

   function prepare_query_string(){
		$re = [];
		$query_array = explode("&", $_SERVER["QUERY_STRING"]);
		foreach ($query_array as $key => $value) {
		$temp = explode("=", $value);
		$re[$temp[0]] = $temp[1]; 
		}
		return $re;
	}
?>
	<div class="gameBox">
		.
	</div>
	<br />
	<form>
		Answer
		<input type="text" name="answer" id="answer" placeholder="answer" /><br/><br/>
	</form>
	<a href="/Project%20Folder/mainMenu.php?user=$firstname"><button>Return to main menu</button></a>
</body>
</html>