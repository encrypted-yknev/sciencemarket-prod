<?php
session_start();
if(!$_SESSION["logged_in"])	{
	header("location:../index.php",true,301);
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
<title>Science Market - Question and Answer forum</title>
<meta name="description" content="Question and answer forum. Post questions. Answer questions. Discussion forums. clear your doubts. Portal for people to connect and discuss" >
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
<body onscroll="getHeight()">
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
						   where a.posted_by <> '".$_SESSION['user']."' and
							   (c.tag_name REGEXP ('".$query_string."')
							   or a.qstn_titl REGEXP ('".$query_string."')
							   or a.qstn_desc REGEXP ('".$query_string."'))
						   order by a.created_ts desc limit 10";
					
					
						include "fetch_answers1.php";
						if($stmt->rowCount() <=0)	{
								echo '<div class="alert alert-info">
									  <strong>Oops!!</strong> We can\'t find any relevant questions for you. Please add some interests so that we can display questions which might be relevant to you. <strong><a href="profile.php">Click here</a></strong>
								  </div>';
							}
						$qstn_array=array();
						$sql_fetch_all_qstn = "select distinct a.qstn_id
											   from questions a 
											   inner join qstn_tags b
											   on a.qstn_id=b.qstn_id
											   inner join tags c 
											   on b.tag_id=c.tag_id 
											   where a.posted_by <> '".$_SESSION['user']."' and
												   (c.tag_name REGEXP ('".$query_string."')
												   or a.qstn_titl REGEXP ('".$query_string."')
												   or a.qstn_desc REGEXP ('".$query_string."'))
											   order by a.created_ts desc";
						foreach($conn->query($sql_fetch_all_qstn) as $row_qid)	{
							$row_qstn_id=$row_qid['qstn_id'];
							array_push($qstn_array,$row_qstn_id);
						}
						$qstn_arr_str=implode("|",$qstn_array);
				}
				catch(PDOException	$e)	{
					echo 'Error fetching Question '.$e->getMessage();
				}
				?>
				</div>
				<div id="scroll-msg">
					<span>Loading more questions... </span>
					<span><img src="../../img/ajax-loader.gif" /></span>
				</div>
			</div>
		</div>
	</div>
	<input id="qid-array-list" type="hidden" value="<?php echo $qstn_arr_str; ?>" />
	<input id="page-locate-data" type="hidden" value="<?php echo $slashes; ?>" />
	<input id="scroll-flag" type="hidden" value="1" />
	<?php include "../footer.php"; ?>
</body>
</html>
