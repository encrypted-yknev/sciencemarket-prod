<?php
session_start();
if(!$_SESSION["logged_in"])	{
	header("location:../../index.php");
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
<title>Science Market - Recently posted questions</title>
<meta name="description" content="Recently posted questions alongwith answers. Forum with recent questions and answers.">
<link rel="stylesheet" type="text/css" href="../../styles/header.css">
<link rel="stylesheet" type="text/css" href="../../styles/dashboard.css">
<link rel="stylesheet" type="text/css" href="../../styles/qa_forum.css">
<link rel="stylesheet" type="text/css" href="../../styles/footer.css">
<link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type = "text/javascript" src = "https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
<link rel="stylesheet" href="../../styles/bootstrap.min.css">

<script src="../../js/bootstrap.min.js"></script>
<script type="text/javascript" src="../../js/qa_forum.js"></script>
<!--<script type="text/javascript" src="js/posts_vote.js"></script>-->
<script type="text/javascript" src="../../js/header.js"></script></head>
<body onscroll="getHeight()">
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
					$sql="select t.qstn_id,
						   t.qstn_titl,
						   t.qstn_desc,
						   t.posted_by,
						   t.topic_id,
						   t.up_votes,
						   t.down_votes,
						   t.created_ts,
						   t.answer_ts,
						   t.comment_ts,
						   (case when t.answer_ts >= t.comment_ts then t.answer_ts
								else t.comment_ts
						   end) score
					from
					(select a.qstn_id,
						   a.qstn_titl,
						   a.qstn_desc,
						   a.posted_by,
						   a.topic_id,
						   a.up_votes,
						   a.down_votes,
						   a.created_ts,
						   coalesce(max(b.created_ts),0) as answer_ts,
						   coalesce(max(c.created_ts),0) as comment_ts
					from questions a
					left join answers b 
					on a.qstn_id = b.qstn_id 
					left outer join comments c 
					on c.ans_id = b.ans_id
					group by a.qstn_id,a.qstn_titl,a.qstn_desc 
					ORDER BY answer_ts desc,comment_ts desc) t
					order by score desc
					limit 10";
					include "../fetch_answers1.php";
					if($stmt->rowCount() <=0)	{
						echo '<div class="alert alert-info">
								<strong>Oops!!</strong> No questions to show at this moment
						  </div>';
					}
					$qstn_array=array();
					$sql_fetch_all_qstn = "select t.qstn_id,
						   (case when t.answer_ts >= t.comment_ts then t.answer_ts
								else t.comment_ts
						   end) score
					from
					(select a.qstn_id,
						   coalesce(max(b.created_ts),0) as answer_ts,
						   coalesce(max(c.created_ts),0) as comment_ts
					from questions a
					left join answers b 
					on a.qstn_id = b.qstn_id 
					left outer join comments c 
					on c.ans_id = b.ans_id
					group by a.qstn_id,a.qstn_titl,a.qstn_desc 
					ORDER BY answer_ts desc,comment_ts desc) t
					order by score desc";
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
	<?php include "../../footer.php"; ?>
</body>
</html>