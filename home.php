<?php
error_reporting(0);
session_start();
if(isset($_SESSION))	{
	if($_SESSION["logged_in"])	
		header("location:welcome.php");
}
?>
<html>
<head>
<title>BioForum - Questions & Answers | Expert Connect | Collaborations</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="styles/header.css">
<link rel="stylesheet" type="text/css" href="/styles/home.css" >
<link rel="stylesheet" type="text/css" href="styles/footer.css">
</head>
<body>

<?php include "header.php" ?>

<div id="main-container">
	<div id="main-section">
		<h2>Welcome Folks!</h2></br>
		<a class="login" href="register.php" target="_blank">New User? Sign up</a></br></br></br></br>
		<a class="login" href="login.php" target="_blank">Existing User? Login</a></br></br>
	</div>
</div>

<?php include "footer.php" ?>

</body>
</html>