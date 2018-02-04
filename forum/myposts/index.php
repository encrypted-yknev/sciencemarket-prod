<?php
session_start();
if(isset($_SESSION['logged_in']) and $_SESSION['logged_in'])	{
	$logged_in=1;

include "../../connectDb.php";
include "../functions/get_time.php";
include "../functions/get_time_offset.php";
$qstn_array=$qstn_arr_str="";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"> 
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Science Market - Your questions</title>
<meta name="description" content="Questions posted by you appears here. list of questions with user answers to your questions">
<link rel="stylesheet" type="text/css" href="../../styles/header.css">
<link rel="stylesheet" type="text/css" href="../../styles/dashboard.css">
<link rel="stylesheet" type="text/css" href="../../styles/qa_forum.css">
<link rel="stylesheet" type="text/css" href="../../styles/footer.css">
<link href="https://fonts.googleapis.com/css?family=Quicksand" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type = "text/javascript" src = "https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
<link rel="stylesheet" href="../../styles/bootstrap.min.css">

<script src="../../js/bootstrap.min.js"></script>
<script type="text/javascript" src="../../js/qa_forum.js"></script>
<!--<script type="text/javascript" src="js/posts_vote.js"></script>-->
<script type="text/javascript" src="../../js/header.js"></script></head>
<body>
<div id="block"></div>
<?php include "../../header.php"; ?>
	</br>
	<div class="container">
			<?php 
			if($logged_in == 1)
				include "../common_code.php"; 
			else
				include "../common_code_guest.php"; 
			?>
			<div class="col-sm-10">

				<div id="qstn-res">
				
				<?php
					try	{
					$query_string="";
					$sql="select a.qstn_id,a.qstn_titl,a.qstn_desc,a.posted_by,a.topic_id,a.up_votes,a.down_votes,a.created_ts from questions a 
					where posted_by='".$_SESSION['user']."' order by created_ts desc limit 10";
					
					include "../fetch_answers1.php";
					if($stmt->rowCount() <=0)	{
						echo '<div class="alert alert-info">
								You haven\'t posted any questions yet. Please <strong><a href="../qstn.php">Click here</a></strong> to post questions
						  </div>';
					}
					$qstn_array=array();
					$sql_fetch_all_qstn = "select qstn_id
										from questions 
										where posted_by='".$_SESSION['user']."' 
										order by created_ts desc
										";
					foreach($conn->query($sql_fetch_all_qstn) as $row_qid)	{
						$row_qstn_id=$row_qid['qstn_id'];
						array_push($qstn_array,$row_qstn_id);
					}
					$qstn_arr_str=implode("|",$qstn_array);
				}
				catch(PDOException	$e)	{
					echo 'Error fetching Question';
				}
				?>
				
			</div>
			<div id="scroll-msg">
				<div id="btn-section">
						<button id="explore-btn" class="btn btn-primary" onclick="fetchMoreQuestions()">Explore more</button>
				</div>
			</div>
			</div>
		</div>
	</div>
	<input id="qid-array-list" type="hidden" value="<?php echo $qstn_arr_str; ?>" />
	<input id="page-locate-data" type="hidden" value="<?php echo $slashes; ?>" />
	<input id="scroll-flag" type="hidden" value="1" />
	
	<?php include "../../footer.php"; ?>
</body>
</html>
<?php	}
else	{
	$logged_in=0;
	header("location:../../index.php");
}
	?>
