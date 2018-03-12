<?php
session_start();

if(isset($_SESSION['logged_in']) and $_SESSION['logged_in'])
	$logged_in=1;
else
	$logged_in=0;

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
<link href="https://fonts.googleapis.com/css?family=Quicksand" rel="stylesheet">
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
			<?php 
			if($logged_in == 1)
				include "common_code.php"; 
			else
				include "common_code_guest.php"; 
			?>
			<div class="col-sm-7">

				<div id="qstn-res">
				
				<?php
					try	{
					if($logged_in == 1)	{
						if(isset($_GET['tag']))	{
							$url_tag_id=$_GET['tag'];
							$sql="select  a.qstn_id,
										 a.qstn_titl,
										 a.qstn_desc,
										 a.posted_by,
										 a.up_votes,
										 a.down_votes,
										 a.topic_id,
										 a.created_ts 
								from questions a
								inner join qstn_tags b 
								on a.qstn_id = b.qstn_id
								where b.tag_id = ".$url_tag_id;
						}
						else	{
					/* $query_string="";
					$sql_fetch_user_interests="select b.tag_name 
									   from user_tags a
									   inner join tags b
									   on a.tag_id=b.tag_id
									   where a.user_id='".$_SESSION['user']."'";
					foreach($conn->query($sql_fetch_user_interests) as $result_user_interest)	{
						$query_string=$query_string.$result_user_interest['tag_name']." ";
					}
					$query_string=substr($query_string,0,strlen($query_string)-1); */
					$sql="select  a.qstn_id,
								  a.qstn_titl,
								  a.qstn_desc,
								  a.posted_by,
								  a.topic_id,
								  a.up_votes,
								  a.down_votes,
								  a.created_ts 
						from questions a
						order by a.created_ts desc limit 10"; 
						
					/* $sql="select     a.qstn_id,
								 a.qstn_titl,
								 a.qstn_desc,
								 a.posted_by,
								 a.up_votes,
								 a.down_votes,
								 a.topic_id,
								 a.created_ts
									 
									 from
									 (
									select 
									 a.qstn_id,
									 a.qstn_titl,
									 a.qstn_desc,
									 a.posted_by,
									 a.up_votes,
									 a.down_votes,
									 a.topic_id,
									 a.created_ts,
									 CASE WHEN a.qstn_id=@QID then @row:=@row + 1
										else @row:=1 
									END as rnum,
									@qid:=a.qstn_id
									
									FROM (select @row:=0,@qid:=0) t,
									questions a 
									inner join qstn_tags b
									on a.qstn_id=b.qstn_id
									inner join tags c 
									on b.tag_id=c.tag_id
									left outer join user_tags d
									on d.tag_id=c.tag_id
									and d.user_id='".$_SESSION['user']."' 
									where a.posted_by<>'".$_SESSION['user']."'
									and match(a.qstn_titl,a.qstn_desc) against ('".$query_string."' in NATURAL LANGUAGE MODE)
									) a
									where a.rnum=1
									order by a.created_ts
									limit 10
									"; */
						}
					} 
					else	{
						if(isset($_GET['tag']))	{
							$url_tag_id=$_GET['tag'];
							$sql="select  a.qstn_id,
														 a.qstn_titl,
														 a.qstn_desc,
														 a.posted_by,
														 a.up_votes,
														 a.down_votes,
														 a.topic_id,
														 a.created_ts 
												from questions a
												inner join qstn_tags b 
												on a.qstn_id = b.qstn_id
												where b.tag_id = ".$url_tag_id;
						}
						else	{
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
							}
						}
						include "fetch_answers1.php";
						if($stmt->rowCount() <=0)	{
							echo '<div class="alert alert-info">
								  <strong>Oops!!</strong> We can\'t find any relevant questions for you. Please add some interests so that we can display questions which might be relevant to you. <strong><a href="profile.php">Click here</a></strong>
							  </div>';
						}
						$qstn_array=array();
						if($logged_in == 1)	{
							if(isset($_GET['tag']))	{
								$sql_fetch_all_qstn="select distinct a.qstn_id 
													from questions a
													inner join qstn_tags b 
													on a.qstn_id = b.qstn_id
													where b.tag_id=".$_GET['tag'];
							}
							else	{
							$sql_fetch_all_qstn = "select a.qstn_id
										from questions a
										order by a.created_ts desc";
							/* $sql_fetch_all_qstn = "select a.qstn_id,
														  a.created_ts
												   from
													 (
													select 
													 a.qstn_id,													 
													 a.created_ts,
													 CASE WHEN a.qstn_id=@QID then @row:=@row + 1
														else @row:=1 
													END as rnum,
													@qid:=a.qstn_id
													
													FROM (select @row:=0,@qid:=0) t,
													questions a 
													inner join qstn_tags b
													on a.qstn_id=b.qstn_id
													inner join tags c 
													on b.tag_id=c.tag_id
													left outer join user_tags d
													on d.tag_id=c.tag_id
													and d.user_id='".$_SESSION['user']."' 
													where a.posted_by<>'".$_SESSION['user']."'
													and match(a.qstn_titl,a.qstn_desc) against ('".$query_string."' in NATURAL LANGUAGE MODE)
													) a
													where a.rnum=1
													order by a.created_ts"; */
							}
						}
						else	{
							if(isset($_GET['tag']))	{
								$sql_fetch_all_qstn="select distinct a.qstn_id 
													from questions a
													inner join qstn_tags b 
													on a.qstn_id = b.qstn_id
													where b.tag_id=".$_GET['tag'];
							}
							else	{
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
							}
						}
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
					<div id="btn-section">
						<button id="explore-btn" class="btn btn-primary" onclick="fetchMoreQuestions()">Explore more</button>
					</div>
				</div>
			</div>
			<div class="col-sm-3">
				<div id="tags-box">
					<span id="tag-box-title">Popular tags</span></br></br>
					<?php
						try	{
							$sql_fetch_tags_list="  select a.tag_id,a.tag_name,count(1) as cnt_follower
													from tags a
													inner join qstn_tags b 
													on a.tag_id = b.tag_id
													group by a.tag_id,a.tag_name
													order by 3 desc limit 50
												  ";
							$stmt_fetch_tags_list=$conn->prepare($sql_fetch_tags_list);
							$stmt_fetch_tags_list->execute();
							
							if($stmt_fetch_tags_list->rowCount() < 0)	{
								echo "Nothing to show now";
							}
							else	{
								while($row_tags_list=$stmt_fetch_tags_list->fetch())	{
									$tag_id=$row_tags_list['tag_id'];
									$tag_nm=$row_tags_list['tag_name'];
									$count_tags=$row_tags_list['cnt_follower'];
									
									echo "<span class='badge tag-name-list'><a href='".$slashes."forum/index.php?tag=".$tag_id."'>".$tag_nm."</a></span>";
								}
							}
						}
						catch(PDOException $e)	{
							echo "Some error occured in the server";
						}
					
					
					?>
					</br></br>
				</div></br>
			</div>
		</div>
	</div>
	<input id="qid-array-list" type="hidden" value="<?php echo $qstn_arr_str; ?>" />
	<input id="page-locate-data" type="hidden" value="<?php echo $slashes; ?>" />
	<input id="scroll-flag" type="hidden" value="1" />
	<?php include "../footer.php"; ?>
</body>
</html>
