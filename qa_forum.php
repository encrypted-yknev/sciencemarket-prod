<?php
session_start();
if(!$_SESSION["logged_in"])	{
	header("location:login.php");
}
include "connectDb.php";

function get_time_diff($timestamp_ans)	{
	date_default_timezone_set("Asia/Kolkata");
	$timestamp_cur=date("Y-m-d H:i:sa");
	
	$year1=substr($timestamp_ans,0,4);
	$month1=substr($timestamp_ans,5,2);
	$day1=substr($timestamp_ans,8,2);
	$hr1=substr($timestamp_ans,11,2);
	$min1=substr($timestamp_ans,14,2);
	$sec1=substr($timestamp_ans,17,2);


	$year2=substr($timestamp_cur,0,4);
	$month2=substr($timestamp_cur,5,2);
	$day2=substr($timestamp_cur,8,2);
	$hr2=substr($timestamp_cur,11,2);
	$min2=substr($timestamp_cur,14,2);
	$sec2=substr($timestamp_cur,17,2);

	if($year1 == $year2)	{
		if($month1 == $month2)	{
			if($day1 == $day2)	{
				if($hr1 == $hr2)	{
					if($min1 == $min2)	{
						if($sec1 == $sec2)	{
							$value=0;	
							$string="seconds";
						}
						else{
							$diff_sec=(int)$sec2-(int)$sec1;
							$value=$diff_sec;	
							$string="seconds";
						}
					}
					else{
						$diff_min=(int)$min2-(int)$min1;
						$value=$diff_min;
						$string="minutes";
					}
				}
				else{
					$diff_hr=(int)$hr2-(int)$hr1;
					$value=$diff_hr;
					$string="hours";
				}
			}
			else	{
				$diff_day=(int)$day2-(int)$day1;
				$value=$diff_day;
				$string="days";
			}
		}
		else	{
			$diff_mon=(int)$month2-(int)$month1;
			$value=$diff_mon;
			$string="months";
		}
	}
	if($value==1)
		$string=substr($string,0,strlen($string)-1);
	return $value.' '.$string.' ago';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"> 
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>BioForum</title>

<link rel="stylesheet" type="text/css" href="styles/header.css">
<link rel="stylesheet" type="text/css" href="styles/dashboard.css">
<link rel="stylesheet" type="text/css" href="styles/qa_forum.css">
<link rel="stylesheet" type="text/css" href="styles/footer.css">
<link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type = "text/javascript" src = "https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
<link rel="stylesheet" href="styles/bootstrap.min.css">
<!-- Latest compiled JavaScript -->
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/qa_forum.js"></script></head>
<script type="text/javascript" src="js/header.js"></script></head>
<body>
<div id="block"></div>
<?php include "header.php"; ?>
	</br>
	<div class="container">
		<div id="side-nav">
			<table border="0">
				<tr>
					<td>
						<div id="nav-id">
							<div class="side-bar"></div>
							<div class="side-bar"></div>
							<div class="side-bar"></div>
						</div>
					</td>
					<td>
						<div id="media-image"><img src="img/logo.jpg" width="200" height="50"/></div>
					</td>
				</tr>
			</table></br>
			<div id="page-title"><span>Q/A Forum</span></div></br>
			<div class="row">
				<div class="col-sm-3">
					<div id="row-1">
						<a href="qstn.php" class="btn btn-info">Ask Questions</a>
					</div>
				</div>
				<div class="col-sm-6">
					<div id="row-2">
						<input type="text" class="form-control" id="srch-box-media" placeholder="Search questions" />
					</div>
				</div>
			</div>
		</div>
		<div id="options-menu">
			<li class="side-menu"><a href="" class="list-group-item" >My Posts</a></li>
			<li class="side-menu"><a href="" class="list-group-item" >Relevant</a></li>
			<li class="side-menu"><a href="" class="list-group-item" >Most upvoted</a></li>
			<li class="side-menu"><a href="" class="list-group-item" >Recent </a></li>
			<li class="side-menu" id="side-menu-media-opt4"><a href="javascript:void(0)" class="list-group-item" onclick="showTopics('side-menu-topics')" >+ Topic based</a>
				<?php
				try	{
					$sql_fetch_topics="select topic_id,topic_desc from topics where parent_topic = 0";
					foreach($conn->query($sql_fetch_topics) as $row_topics)	{
						echo 
						'<li class="side-menu-topics"><a href="javascript:void(0)" 
						onclick="showSubTopics(\'sub-topic-media-'.$row_topics['topic_id'].'\')">+ '.$row_topics['topic_desc'].'</a>';
						 try	{
							$sql_fetch_sub_topic="select topic_id,topic_desc from topics where parent_topic=".$row_topics['topic_id'];
							echo '<ul class="sub-topic-section" id="sub-topic-media-'.$row_topics['topic_id'].'">';
							foreach($conn->query($sql_fetch_sub_topic) as $row_sub_topics)	{
								echo '<li class="side-menu-sub-topics"><a href="javascript:void(0)" onclick="showQstn(\''.$row_sub_topics["topic_desc"].'\')">'.$row_sub_topics["topic_desc"].'</a></li>';
							}
							echo '</ul>';
						}
						catch(PDOException $e)	{
							echo 'Error fetching sub-topics';
						}
						echo '</li>'; 
					}	
				}
				catch(PDOException $e)	{
					echo 'Error fetching topics';
				} 
				?>
			</li></br>
			<ul class="nav nav-pills nav-stacked">
				<li><a href="profile.php" ><span class="glyphicon glyphicon-user" ></span>&nbsp;My Profile</a></li>
				<li><a href="dashboard.php"><span class="glyphicon glyphicon-home" ></span>&nbsp;Dashboard</a></li>
				<li><a href="forum" ><span class="glyphicon glyphicon-question-sign"></span>&nbsp;Q/A Forum</a></li>
				<li><a href="" ><span class="glyphicon glyphicon-transfer" ></span>&nbsp;Expert Connect</a></li>
				<li><a href="" ><span class="glyphicon glyphicon-refresh" ></span>&nbsp;Collaborate</a></li>
				<li><a href="" ><span class="glyphicon glyphicon-gift" ></span>&nbsp;Favours</a></li>
				<li><a href="logout.php"><span class="glyphicon glyphicon-off"></span>&nbsp;Logout</a></li>
			</ul>
		</div>
		<div class="row">
			<div class="col-sm-2"></div>
			<div class="col-sm-10">
				<div id="head-bottom"></div>
				
			</div>
		</div></br>
		<div class="row">
			<div class="col-sm-2" id="side-qstn-cat" style="background-color:#F1F1F1;">
				<li class="side-menu"><a href="javascript:void(0)" onclick="showQstn('p')" >My Posts</a></li>
				<li class="side-menu"><a href="javascript:void(0)" onclick="showQstn('m')" >Relevant</a></li>
				<li class="side-menu"><a href="javascript:void(0)" onclick="showQstn('e')" >Most upvoted</a></li>
				<li class="side-menu"><a href="javascript:void(0)" onclick="showQstn('t')" >Recent </a></li>
				<li class="side-menu" id="side-menu-opt4"><a href="javascript:void(0)" onclick="showTopics('side-menu-topics')" >+ Topic based</a>
					<?php
					try	{
						$sql_fetch_topics="select topic_id,topic_desc from topics where parent_topic = 0";
						foreach($conn->query($sql_fetch_topics) as $row_topics)	{
							echo 
							'<li class="side-menu-topics"><a href="javascript:void(0)" 
							onclick="showSubTopics(\'sub-topic-'.$row_topics['topic_id'].'\')">+ '.$row_topics['topic_desc'].'</a>';
							 try	{
								$sql_fetch_sub_topic="select topic_id,topic_desc from topics where parent_topic=".$row_topics['topic_id'];
								echo '<ul class="sub-topic-section" id="sub-topic-'.$row_topics['topic_id'].'">';
								foreach($conn->query($sql_fetch_sub_topic) as $row_sub_topics)	{
									echo '<li class="side-menu-sub-topics"><a href="javascript:void(0)" onclick="showQstn(\''.$row_sub_topics["topic_desc"].'\')">'.$row_sub_topics["topic_desc"].'</a></li>';
								}
								echo '</ul>';
							}
							catch(PDOException $e)	{
								echo 'Error fetching sub-topics';
							}
							echo '</li>'; 
						}
					}
					catch(PDOException $e)	{
						echo 'Error fetching topics';
					} 
					?>
				</li>
			</div>
			<div class="col-sm-10">
				
<!--				<div id="list-option-section">
					
				</div> -->
				<div id="qstn-res">
				
				<?php
					try	{
					$query_string="";
					$sql="select a.qstn_id,a.qstn_titl,a.qstn_desc,a.posted_by,a.up_votes,a.down_votes,a.created_ts from questions a 
					where posted_by='".$_SESSION['user']."' order by created_ts desc";
						foreach($conn->query($sql) as $row)	{
							$qid=$row['qstn_id'];
							$posted_by=$row['posted_by'];
							$created_ts=$row['created_ts'];
							$up_votes=$row['up_votes'];
							$down_votes=$row['down_votes'];
				?>
				<div class="qstn_row">
				<a id="titl-link" href="<?php echo 'qstn_ans.php?qid='.$qid ?>"><?php echo $row["qstn_titl"]; ?></a>&emsp;
				<span id="qstn-ans-count"></span>
				<p id="qstn-desc"><?php echo $row["qstn_desc"]; ?></p>
		
				<?php
				try	{
					$sql_fetch_votes="select pro_img_url,up_votes,down_votes from users where user_id='".$posted_by."'";
					foreach($conn->query($sql_fetch_votes) as $row_user)
						$img_url=$row_user["pro_img_url"];
						$up_user_votes=$row_user["up_votes"];
						$down_user_votes=$row_user["down_votes"];
				}
				catch(PDOException	$e)	{
					echo "Error fetching user votes!</br>";
				}
				?>
				<span id="author-section">
					<div class="sub-section" id="sub-section-1"> Asked - <?php echo get_time_diff($created_ts); ?></div>
					
						<table id="user-qstn" border="0">
							<tr>
								<td>
									<img src="<?php echo $img_url; ?>" width="30" height="35"/>
								</td>
								<td id="table-col-2">
									<span class="auth-text" id="up-vote-count1"><?php echo $posted_by; ?></span></br>
									<span class="auth-text" id="up-vote-count2"><?php echo $up_user_votes; ?></span>
									<span class="auth-text" id="down-vote-count3"><?php echo $down_user_votes; ?></span>
								</td>
							</tr>
						</table>
				</span>
				<div id="tag-section">
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
				<button type="button" class="btn btn-primary" onclick="window.location.href='qstn_ans.php?qid=<?php echo $qid; ?>'" style="padding: 1px 2px; font-size:13px;">Answer</button>
				<span class="vote-sec" id="up-link">
					<a href="javscript:void(0)" class="vote-link-area" id="up-link-area" 
						onclick="increaseCount('<?php echo 'up-vote-qstn-'.$qid."',".$qid.",'".$posted_by."'";?>,0)">
						<span class="glyphicon glyphicon-thumbs-up"></span>
					<span id="up-vote-qstn-<?php echo $qid; ?>" class="vote-count-area"><?php echo $up_votes; ?></span>
				</a>
				</span>
				<span class="vote-sec" id="down-link">
					<a href="javscript:void(0)" class="vote-link-area" id="down-link-area" 
						onclick="increaseCount('<?php echo 'down-vote-qstn-'.$qid."',".$qid.",'".$posted_by."'";?>,1)">
						<span class="glyphicon glyphicon-thumbs-down"></span>
					<span id="down-vote-qstn-<?php echo $qid; ?>" class="vote-count-area"><?php echo $down_votes; ?></span>
				</a>
				</span>
				<?php
					try{
						$sql_ans_count="select count(1) as ans_count from answers where qstn_id=".$qid;
						$stmt_ans_count = $conn->prepare($sql_ans_count);
						$stmt_ans_count->execute();
						$row_cnt = $stmt_ans_count->fetch();
						$answer_count=$row_cnt['ans_count'];
					}
					catch(PDOException $e)	{
						$answer_count="Error fetching answers";
					}
				?>&emsp;
				<span id="ans-toggle-<?php echo $qid; ?>"><a href="javascript:void(0)" onclick="toggleAns('toggle-ans-sec-<?php echo $qid; ?>')"><?php echo $answer_count.' '; ?>Answers</a></span>
				
				<div class="toggle-ans-sec" id="toggle-ans-sec-<?php echo $qid; ?>" >
					<?php 
						try	{
							$sql_show_some_ans = "select ans_id,ans_desc,up_votes,down_votes,posted_by,created_ts from answers where 
												   qstn_id = ".$qid." order by created_ts desc limit 3";
							
							foreach($conn->query($sql_show_some_ans) as $row_ans)	{
								
								$ansid=$row_ans['ans_id'];
								$ans = $row_ans['ans_desc'];
								$ans_user=$row_ans['posted_by'];
								$sql_get_user_pic = "select pro_img_url from users where user_id='".$ans_user."'";
								
								$stmt_get_user_pic = $conn->prepare($sql_get_user_pic);
								$stmt_get_user_pic->execute();
								$row_pic = $stmt_get_user_pic->fetch();
								$ans_user_pic = $row_pic['pro_img_url'];
								?>
								<div class="ans-hidden-sec" id="ans-hidden-sec-<?php echo $ansid; ?>">
									<div class="photo-ans-sec" id="" style="background-image:url('<?php echo $ans_user_pic ?>');"></div></br>
									<?php echo $ans."</br>"; ?>
								</div>
								<?php
							}
							echo '</br><a href="qstn_ans.php?qid='.$qid.'">View all answers...</a>';
								
								#echo "No answer to this question yet.Be the first one to answer this question.";
								
						}
						catch(PDOException $e) {
							echo "Some error occured. We are working on it and will get back to you. Sorry for the inconvenience caused";
						}
					?>
				
				</div>
				</div></br>
				<?php 
					}
				}
				catch(PDOException	$e)	{
					echo 'Error fetching Question';
				}
				?>
			</div>
			</div>

</body>
</html>