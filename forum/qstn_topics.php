<?php
if($_SERVER['REQUEST_METHOD'] == 'POST')	{
	
	$sql="select a.qstn_id,a.qstn_titl,a.qstn_desc,a.topic_id,a.posted_by,a.up_votes,a.down_votes,a.created_ts from questions a inner join 	topics b on a.topic_id = b.topic_id where ";
	$sql_fetch_all_qstn="select a.qstn_id from questions a inner join topics b on a.topic_id = b.topic_id where ";
	$topic_nm = trim($_POST['filter']);
	$sort_order = trim($_POST['sort']);
	if(!empty($topic_nm) && !empty($sort_order))	{
		
		if($topic_nm != 'All topics')	{
			$sql.= "b.topic_desc = '".$topic_nm."' and ";
			$sql_fetch_all_qstn.="b.topic_desc = '".$topic_nm."' and ";
		}
		
		if($sort_order == 'Recent' or $sort_order == 'My posts' or $sort_order == 'Default')
			$sort = 'a.created_ts';
		else if($sort_order == 'Most upvoted')
			$sort = 'a.up_votes';
		
		if($logged_in == 1)	{
			if($sort_order == 'My posts')	{
				$sql.="b.parent_topic = ".$parent_topic_id." and a.posted_by='".$_SESSION['user']."' order by ".$sort." desc limit 10";
				$sql_fetch_all_qstn.="b.parent_topic = ".$parent_topic_id." and a.posted_by='".$_SESSION['user']."' order by ".$sort." desc";
			}
			else	{
				$sql.="b.parent_topic = ".$parent_topic_id." order by ".$sort." desc limit 10";
				$sql_fetch_all_qstn.="b.parent_topic = ".$parent_topic_id." order by ".$sort." desc";
			}
		}
		else	{
			$sql.="b.parent_topic = ".$parent_topic_id." order by ".$sort." desc limit 10";
			$sql_fetch_all_qstn.="b.parent_topic = ".$parent_topic_id." order by ".$sort." desc";
		}
	}
	else 	{
		$sql.="b.parent_topic = ".$parent_topic_id." order by created_ts desc limit 10";
		$sql_fetch_all_qstn.="b.parent_topic = ".$parent_topic_id." order by a.created_ts desc";
	}
}
else	{
	$sql="select a.qstn_id,a.qstn_titl,a.qstn_desc,a.topic_id,a.posted_by,a.up_votes,a.down_votes,a.created_ts from questions a inner join 	topics b on a.topic_id = b.topic_id where b.parent_topic = ".$parent_topic_id." order by a.created_ts desc limit 10";
	$sql_fetch_all_qstn="select a.qstn_id from questions a inner join topics b on a.topic_id = b.topic_id where b.parent_topic = ".$parent_topic_id." order by a.created_ts desc";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"> 
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Science Market - Questions on <?php echo $page_title; ?></title>
<meta name="description" content="<?php echo $page_desc; ?>" >
<link rel="stylesheet" type="text/css" href="../../../styles/header.css">
<link rel="stylesheet" type="text/css" href="../../../styles/dashboard.css">
<link rel="stylesheet" type="text/css" href="../../../styles/qa_forum.css">
<link rel="stylesheet" type="text/css" href="../../../styles/footer.css">
<link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Quicksand" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type = "text/javascript" src = "https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
<link rel="stylesheet" href="../../../styles/bootstrap.min.css">
<script src="../../../js/bootstrap.min.js"></script>
<script type="text/javascript" src="../../../js/qa_forum.js"></script>
<!--<script type="text/javascript" src="js/posts_vote.js"></script>-->
<script type="text/javascript" src="../../../js/header.js"></script></head>
<body>
<div id="block"></div>
<?php include "../../../header.php"; ?>
	</br>
	<div class="container">
		<?php 
			if($logged_in == 1)
				include "../../common_code.php"; 
			else
				include "../../common_code_guest.php"; 
			?>
			<div class="col-sm-10">
								<form class="form-inline" id="filter-form" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
					<div class="form-group" id="filter-section">
						<label for="filter-values">Choose topics</label>
						<select class="form-control dropdown-opt" id="filter-values" name="filter">
						<option>All topics</option>
						<?php
							try	{
								$sql_fetch_sub_topics = "select topic_id,topic_desc from topics where parent_topic = ".$parent_topic_id;
								foreach($conn->query($sql_fetch_sub_topics) as $row_sub_topics)	{
									$sub_topic_name = $row_sub_topics['topic_desc'];
									$sub_topic_id = $row_sub_topics['topic_id'];
									echo '<option>'.$sub_topic_name.'</option>';
								}
							}
							catch(PDOException $e)	{
								
							}
						?>
						</select>
						<script>
							document.getElementById("filter-values").value="<?php echo $topic_nm; ?>";
						</script>
					</div>
					<div class="form-group" id="sort-section">
						<label for="sort-values">Sort questions</label>
						<select class="form-control dropdown-opt" id="sort-values" name="sort">
							<option>Default</option>
							<option>Recent</option>
							<option>Most upvoted</option>
							<?php if($logged_in == 1)	{ ?>
							<option>My posts</option>
							<?php }	?>
						</select>
						<script>
							document.getElementById("sort-values").value="<?php echo $sort_order; ?>";
						</script>
					</div>
					
					<button type="submit" id="filter-submit" class="btn btn-default">Go</button>
				</form></br>
				<div id="qstn-res">
				
				<?php
					try	{
					$query_string="";
					include "../../fetch_answers1.php";
					if($stmt->rowCount() <=0)	{
						echo '<div class="alert alert-info">
								No questions posted on this topic yet. Please do check after some time
						  </div>';
					}
					$qstn_array=array();
					foreach($conn->query($sql_fetch_all_qstn) as $row_qid)	{
						$row_qstn_id=$row_qid['qstn_id'];
						array_push($qstn_array,$row_qstn_id);
					}
					$qstn_arr_str=implode("|",$qstn_array);
				}
				catch(PDOException	$e)	{
					echo 'Error fetching Questions';
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
	<?php include "../../../footer.php"; ?>
</body>
</html>
