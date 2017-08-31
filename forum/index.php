<?php
session_start();
if(!$_SESSION["logged_in"])	{
	header("location:../login.php");
}
include "../connectDb.php";
include "functions/get_time.php";
include "functions/get_time_offset.php";


?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"> 
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Science Market - Question and Answer forum. Homepage.</title>

<link rel="stylesheet" type="text/css" href="../styles/header.css">
<link rel="stylesheet" type="text/css" href="../styles/dashboard.css">
<link rel="stylesheet" type="text/css" href="../styles/qa_forum.css">
<link rel="stylesheet" type="text/css" href="../styles/footer.css">
<link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type = "text/javascript" src = "https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
<link rel="stylesheet" href="../styles/bootstrap.min.css">
<!-- Latest compiled JavaScript -->
<script src="../js/bootstrap.min.js"></script>
<script type="text/javascript" src="../js/qa_forum.js"></script>
<!--<script type="text/javascript" src="js/posts_vote.js"></script>-->
<script type="text/javascript" src="../js/header.js"></script></head>
<body>
<div id="block"></div>
<?php include "../header.php"; ?>
	</br>
	<div class="container">
			<?php include "common_code.php" ?>
			<div class="col-sm-10">

				<div id="qstn-res">
				
				<?php
					try	{
					$query_string="";
					$sql="select a.qstn_id,a.qstn_titl,a.qstn_desc,a.topic_id,a.posted_by,a.up_votes,a.down_votes,a.created_ts from questions a 
					where posted_by='".$_SESSION['user']."' order by created_ts desc";
					
					include "fetch_answers1.php";
					if($stmt->rowCount() <=0)	{
						echo '<div class="alert alert-info">
								You haven\'t posted any questions yet. Please <strong><a href="../qstn.php">Click here</a></strong> to post questions
						  </div>';
					}
				}
				catch(PDOException	$e)	{
					echo 'Error fetching Question';
				}
				?>
				</div>
			</div>
		</div>
	</div>
</body>
</html>