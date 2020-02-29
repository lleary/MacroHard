<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Matheroids</title>
	</head>
<body>
<?php

   function prepare_query_string(){
		//echo $_SERVER['QUERY_STRING'];
		$re = [];
		$query_array = explode("&", $_SERVER["QUERY_STRING"]);
		foreach ($query_array as $key => $value) {
		$temp = explode("=", $value);
		$re[$temp[0]] = $temp[1]; 
		}
		return $re;
	}
?>


<?php 
	echo "<pre>";
	/*foreach ($_SERVER as $key => $value) {
		echo "$key:$value\n";
	}*/

	$query_array = prepare_query_string();
	//print_r($query_array);


	echo "<p>Welcome to Matheroids, ".$query_array["user"]."</p>";
	echo"</pre>";
?>
<button>Log out</button>
<br />
<br />

<div style="border:3px; border-style:solid; border-color: black; padding: 5px; width: 500px">
	<h2>Main Menu</h2>
	<p>You are on level *</p>
	<button>Play Game</button>
</div>

</body>
</html>