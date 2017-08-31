<?php
session_start();
if(!$_SESSION["logged_in"])	{
	header("location:../../login.php");
}
include "../../connectDb.php";
include "../functions/get_time.php";
include "../functions/get_time_offset.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"> 
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Science Market - Relevant questions for you.</title>

<link rel="stylesheet" type="text/css" href="../../styles/header.css">
<link rel="stylesheet" type="text/css" href="../../styles/dashboard.css">
<link rel="stylesheet" type="text/css" href="../../styles/qa_forum.css">
<link rel="stylesheet" type="text/css" href="../../styles/footer.css">
<link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type = "text/javascript" src = "https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
<link rel="stylesheet" href="../../styles/bootstrap.min.css">
<!-- Latest compiled JavaScript -->
<script src="../../js/bootstrap.min.js"></script>
<script type="text/javascript" src="../../js/qa_forum.js"></script>
<!--<script type="text/javascript" src="js/posts_vote.js"></script>-->
<script type="text/javascript" src="../../js/header.js"></script></head>
<body>
<div id="block"></div>
<?php include "../../header.php"; ?>
	</br>
	<div class="container">
			<?php include "../common_code.php"; ?>
			<div class="col-sm-10">

				<div id="qstn-res">
				
				<?php
					try	{
					$query_string="";
					$sql_fetch_user_interests="select b.tag_name 
									   from user_tags a
									   inner join tags b
									   on a.tag_id=b.tag_id
									   where a.user_id='".$_SESSION['user']."'";
					foreach($conn->query($sql_fetch_user_interests) as $result_user_interest)	{
						$query_string=$query_string.$result_user_interest['tag_name']."|";
					}
					$query_string=substr($query_string,0,strlen($query_string)-1);
					
					$sql="select distinct
								 a.qstn_id,
								 a.qstn_titl,
								 a.qstn_desc,
								 a.posted_by,
								 a.up_votes,
								 a.down_votes,
								 a.topic_id,
								 a.created_ts 
						 from questions a 
						   inner join qstn_tags b
						   on a.qstn_id=b.qstn_id
						   inner join tags c 
						   on b.tag_id=c.tag_id 
						   where c.tag_name REGEXP ('".$query_string."')
						   or a.qstn_titl REGEXP ('".$query_string."')
						   or a.qstn_desc REGEXP ('".$query_string."')
						   order by a.created_ts desc";
					
						include "../fetch_answers1.php";
						if($stmt->rowCount() <=0)	{
								echo '<div class="alert alert-info">
									  <strong>Oops!!</strong> We can\'t find any relevant questions for you. Please add some interests so that we can display questions which might be relevant to you. <strong><a href="profile.php">Click here</a></strong>
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