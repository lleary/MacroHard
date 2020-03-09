<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Matheroids</title>
	<link rel="stylesheet" type="text/css"href="stylesheet.css">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
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
	<script type="text/javascript"> 
		function createAdditionProblem() {
			document.write("Debug:");
			var min=1; 
		    var max=10;  
		    var num1 = Math.random() * (+max - +min) + +min; 
		    num1 = Math.floor(num1)
		    var num2 = Math.random() * (+max - +min) + +min; 
		    num2 = Math.floor(num2)
		    var ans = num1+num2; 
		    document.write("\nNumber 1: " + num1 );  
		    document.write("\nNumber 2: " + num2 );  
		    document.write("\nMath Problem: " + num1+"+"+num2+"= ?" );  
		    document.write("\nAnswer: " + ans );  
		}
		createAdditionProblem();
	</script> 
	<br />
	<script type="text/javascript"> 
		var score = 0;
		document.write("Score: "+score);
	</script> 

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