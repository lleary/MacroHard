<?php
	session_start();
?>

<?php 
	session_destroy();

	echo "Logging Out...";
	header("Location: welcome.php");
?>