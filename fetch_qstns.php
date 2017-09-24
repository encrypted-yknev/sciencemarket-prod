<?php 
session_start();

include "connectDb.php";
include "forum/functions/get_time.php";
include "forum/functions/get_time_offset.php";

$qstn_list="";
if(isset($_REQUEST['qstn_list']))	{
	$qstn_list=explode("|",$_REQUEST['qstn_list']);
}
if(isset($_REQUEST['root']))	{
	$slashes=$_REQUEST['root'];
}
if(isset($_REQUEST['questions']))	{
	$qstn_count=$_REQUEST['questions'];
}
$array_len=sizeof($qstn_list);

if($qstn_count >= $array_len)	{
	echo "0";
	return;
}
$start_qstn=$end_qstn=0;
if($qstn_count < $array_len)  {
	$start_qstn = $qstn_count;
}

if($qstn_count+10 > $array_len)	{
	$end_qstn = $array_len - 1;
}
else	{
	$end_qstn = $qstn_count+9;
}

while($start_qstn <= $end_qstn)	{
	$sql="select a.qstn_id,
				 a.qstn_titl,
				 a.qstn_desc,
				 a.posted_by,
				 a.topic_id,
				 a.up_votes,
				 a.down_votes,
				 a.created_ts 
		 from questions a
		 where a.qstn_id = ".$qstn_list[$start_qstn];
	$stmt=$conn->prepare($sql);
	$stmt->execute();
							
	if($stmt->rowCount() > 0)	{
		
		while($row = $stmt->fetch())	{
			$qid=$row['qstn_id'];
			$posted_by=$row['posted_by'];
			$created_ts=$row['created_ts'];
			$topic_id=$row['topic_id'];
			$up_votes=$row['up_votes'];
			$down_votes=$row['down_votes'];
				?>
				<div class="qstn_row">
				<div class="qstn-topic-section">
					<?php
						try	{
							$sql_fetch_topic = "select topic_desc,parent_topic from topics where topic_id = ".$topic_id;
							$stmt_fetch_topic=$conn->prepare($sql_fetch_topic);
							$stmt_fetch_topic->execute();
							$res_topic=$stmt_fetch_topic->fetch();
							
							$topic_desc=$res_topic['topic_desc'];
							$parent_topic=$res_topic['parent_topic'];
							
							$sql_fetch_parent_topic = "select topic_desc from topics where topic_id=".$parent_topic;
							$stmt_fetch_par_topic=$conn->prepare($sql_fetch_parent_topic);
							$stmt_fetch_par_topic->execute();
							$res_par_topic=$stmt_fetch_par_topic->fetch();
							
							$par_topic_desc = $res_par_topic['topic_desc'];
							echo $par_topic_desc." - ".$topic_desc;
						}
						catch(PDOException $e)	{
							
						}
						
						try	{
							$sql_fetch_votes="select pro_img_url,up_votes,down_votes from users where user_id='".$posted_by."'";
							foreach($conn->query($sql_fetch_votes) as $row_user)
								$img_url=$slashes.$row_user["pro_img_url"];
								$up_user_votes=$row_user["up_votes"];
								$down_user_votes=$row_user["down_votes"];
						}
						catch(PDOException	$e)	{
							echo "Error fetching user votes!</br>";
						}
					
					?>
				</div>
				<div class="user-img-section" style="background-image:url('<?php echo $img_url; ?>'); background-size:cover;">
					
				</div>
				<div class="auth-section">
					<?php
						echo $posted_by." . ".get_user_date($created_ts);
					?>
				</div></br>
				<a class="titl-link" href="<?php echo $slashes.'qstn_ans.php?qid='.$qid ?>"><?php echo $row["qstn_titl"]; ?></a>&emsp;
				<span id="qstn-ans-count"></span>
				<p id="qstn-desc"><?php echo $row["qstn_desc"]; ?></p>
		
				<div class="tag-section">
				<?php
					try	{
						$sql_fetch_qstn_tags="select a.tag_name 
											  from tags a,qstn_tags b 
											  where a.tag_id=b.tag_id 
											  and b.qstn_id=".$qid;
						foreach($conn->query($sql_fetch_qstn_tags) as $row_tags)
							echo '<span class="tag-name-section">'.$row_tags['tag_name'].'</span>';
					}
					catch(PDOException	$e)	{
						echo "Error fetching question tags!</br>";
					}
				?>
				</div>
				</br>
				<?php
					$upvotes_id='up-vote-qstn-'.$qid;
					$downvotes_id='down-vote-qstn-'.$qid;
				?>
				<!--
				<button type="button" class="btn btn-primary" onclick="window.location.href='<?php #echo $slashes; ?>qstn_ans.php?qid=<?php #echo $qid; ?>'" style="padding: 1px 2px; font-size:13px;">Answer</button>
				-->
				
				<?php
					$sql_check_up_vote = "select count(1) as vote_count from user_posts_votes where user_id='".$_SESSION['user']."' 
										and post_type='Q' and vote_type=0 and post_id=".$qid;
					$sql_check_down_vote = "select count(1) as vote_count from user_posts_votes where user_id='".$_SESSION['user']."' 
										and post_type='Q' and vote_type=1 and post_id=".$qid;
					$stmt_check_up_vote = $conn->prepare($sql_check_up_vote);
					$stmt_check_up_vote->execute();
					$sql_row_0 = $stmt_check_up_vote->fetch();
					$count_row_0 = $sql_row_0['vote_count'];
					$stmt_check_down_vote = $conn->prepare($sql_check_down_vote);
					$stmt_check_down_vote->execute();
					$sql_row_1 = $stmt_check_down_vote->fetch();
					$count_row_1 = $sql_row_1['vote_count'];
						
					?>
				<input type="hidden" id="upvote-value-<?php echo $qid; ?>" value="<?php echo $count_row_0; ?>" />
				<input type="hidden" id="downvote-value-<?php echo $qid; ?>" value="<?php echo $count_row_1; ?>" />
				<span class="vote-sec" id="up-link">
				<?php 
					if($posted_by != $_SESSION['user'])	{
				?>
					<span class="vote-link-area" id="up-link-area" style="cursor:pointer;"
						onclick="increaseCount(<?php echo $qid.",'".$posted_by."',0,'".$slashes."'";?>,document.getElementById('upvote-value-<?php echo $qid; ?>').value)">
						<span id="glyph-up-<?php echo $qid; ?>" 
						class="glyphicon glyphicon-thumbs-up <?php echo ($count_row_0 > 0)?"glyph-upvoted":""; ?>"></span>
					<span id="up-vote-qstn-<?php echo $qid; ?>" class="vote-count-area"><?php echo $up_votes; ?></span></span>
					<?php } 
						else	{
					?>
						<span class="glyphicon glyphicon-thumbs-up"></span>
						<span id="up-vote-qstn-<?php echo $qid; ?>" class="vote-count-area"><?php echo $up_votes; ?></span>
						<?php } ?>
				</span>
				
				<span class="vote-sec" id="down-link">
				<?php 
					if($posted_by != $_SESSION['user'])	{
				?>
					<span class="vote-link-area" id="down-link-area" style="cursor:pointer;"
						onclick="increaseCount(<?php echo $qid.",'".$posted_by."',1,'".$slashes."'";?>,document.getElementById('downvote-value-<?php echo $qid; ?>').value)">
						<span id="glyph-down-<?php echo $qid; ?>" class="glyphicon glyphicon-thumbs-down <?php echo ($count_row_1 > 0)?"glyph-downvoted":""; ?>"></span>
					<span id="down-vote-qstn-<?php echo $qid; ?>" class="vote-count-area"><?php echo $down_votes; ?></span></span>
				
				<?php } 
						else	{
					?>
						<span class="glyphicon glyphicon-thumbs-down"></span>
						<span id="down-vote-qstn-<?php echo $qid; ?>" class="vote-count-area"><?php echo $down_votes; ?></span>
						<?php } ?>
				</span>
				
				&nbsp;&nbsp;
				<span class="ans-toggle" id="ans-toggle-<?php echo $qid; ?>"><a href="javascript:void(0)" onclick="toggleAns(<?php echo $qid; ?>,0)">Recent answers</a></span>&nbsp;&nbsp;
				<span class="ans-toggle" id="top-ans-toggle-<?php echo $qid; ?>"><a href="javascript:void(0)" onclick="toggleAns(<?php echo $qid; ?>,1)">Top answers</a></span>
				
				</br></br>
				<div class="toggle-ans-sec" id="toggle-ans-sec-<?php echo $qid; ?>" >
					<?php include $slashes."recent_ans.php"; ?>
				</div>
				<div class="toggle-top-ans-sec" id="toggle-top-ans-sec-<?php echo $qid; ?>" >
					<?php include $slashes."top_ans.php"; ?>
				</div>
				</div></br>
				<?php 
					}
	}
	$start_qstn+=1;
}


	?>