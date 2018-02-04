<?php
session_start();
if(isset($_SESSION['logged_in']) and $_SESSION['logged_in'])
	$logged_in=1;
else
	$logged_in=0;
if(!$_SESSION["logged_in"])	{
	header("location:index.php");
}
include "connectDb.php";
include "functions/get_time.php";
function get_first_name($x)	{
	if(strpos($x," "))	
		return substr($x,0,strpos($x," "));
	else
		return $x;
}
?>
<html>
<head>
<title>Science Market - Bookmarks</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="../styles/header.css">
<link rel="stylesheet" type="text/css" href="../styles/profile.css">
<link rel="stylesheet" type="text/css" href="../styles/qa_forum.css">
<link rel="stylesheet" type="text/css" href="../styles/dashboard.css">
<link rel="stylesheet" type="text/css" href="../styles/bookmarks.css">
<link rel="stylesheet" type="text/css" href="../styles/footer.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type = "text/javascript" src = "https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
<link rel="stylesheet" href="../styles/bootstrap.min.css">
<script src="../js/bootstrap.min.js"></script>
<script src="../js/header.js"></script>
<script src="../js/qa_forum.js"></script>
<script src="../js/profile.js"></script>
<script src="../js/bookmarks.js"></script>
</head>
<body>
<div id="block"></div>
<?php include "../header.php"; ?>
<!--<div id="block"></div>-->
	</br>
	<div class="container">
		<?php 
			if($logged_in == 1)
				include "common_code.php"; 
			else
				include "common_code_guest.php"; 
		?>
		<div class="col-sm-9" id="detl-section">
			<h3>Bookmarked posts</h3>
			<div id="bk-main-section">	</div>
			<ul class="pagination" id="nav-section" data-active="1"></ul>
		</div>
	</div>		
</div>
</br></br>
	

<?php
	include "../footer.php";
?>
</body>
</html>
