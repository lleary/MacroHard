<?php

extract($_POST);

if ($login_type === "student") {
	header("Location: student_login.php");
}
else if ($login_type === "teacher") {
	header("Location: teacher_login.php");
}


?>