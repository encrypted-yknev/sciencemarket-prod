<?php 
session_start();
if(!$_SESSION["logged_in"])	{
	echo "Please login </br>";
	header("location:login.php");
}
include "connectDb.php";
function get_user_date($time)	{
	$date = substr($time,8,2);
	$month = substr($time,5,2);
	$year = substr($time,0,4);
	$mth_str="";
	switch($month)	{
		case "01": $mth_str="Jan";
			break;
		case "02": $mth_str="Feb";
			break;
		case "03": $mth_str="Mar";
			break;
		case "04": $mth_str="Apr";
			break;
		case "05": $mth_str="May";
			break;
		case "06": $mth_str="Jun";
			break;
		case "07": $mth_str="Jul";
			break;
		case "08": $mth_str="Aug";
			break;
		case "09": $mth_str="Sep";
			break;
		case "10": $mth_str="Oct";
			break;
		case "11": $mth_str="Nov";
			break;
		case "12": $mth_str="Dec";
			break;
		default : $mth_str = "";
		break;
	}
	/* if(substr($date,1,1) == '1' and $date != "11")
		$post_date_str = "ST";
	else if(substr($date,1,1) == '2' and $date != "12")
		$post_date_str = "ND";
	else if(substr($date,1,1) == '3' and $date != "13")
		$post_date_str = "RD";
	else 
		$post_date_str = "TH"; */
	
	if($date == date('d') and $month == date('m') and $year == date('Y') and substr($time,11,5) == date("H:i"))
		return 'few seconds ago';
	else if($date == date('d') and $month == date('m') and $year == date('Y'))
		return 'Today '.substr($time,11,5);
	
	return $mth_str.' '.$date.', '.$year;
	
}
?>
<html>
<head>
<title>Science Market - Answer question</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="styles/header.css">
<link rel="stylesheet" type="text/css" href="styles/qstn_ans.css">
<link rel="stylesheet" type="text/css" href="styles/qstn.css">
<link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
<link rel="stylesheet" href="styles/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type = "text/javascript" src = "https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/qna.js"></script>
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/header.js"></script></head>
</head>
<body>
<div id="block"></div>

<?php

$qid=$_GET["qid"];

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
include "header.php";
?>

<div id="block-container"></div>
<div id="load-section"></div>
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
		<div id="page-title"><span>Answer question</span></div></br>
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
		<div class="row">
			<div class="col-sm-12" id="pic-row">
				<img src="<?php echo $_SESSION["pro_img"]; ?>" id="side-menu-img" alt="profile image" width="100" height="120"> 
			</div>
		</div></br>

		<div>upvotes   : <span class="badge"><?php echo $_SESSION["up_vote"]; ?></span></div>
		<div>downvotes : <span class="badge"><?php echo $_SESSION["down_vote"]; ?></span></div>
				
		</br>
		<ul class="nav nav-pills nav-stacked">
			<li><a href="profile.php" ><span class="glyphicon glyphicon-user" ></span>&nbsp;My Profile</a></li>
			<li><a href="dashboard.php"><span class="glyphicon glyphicon-home" ></span>&nbsp;Dashboard</a></li>
			<li><a href="qa_forum.php" ><span class="glyphicon glyphicon-question-sign"></span>&nbsp;Q/A Forum</a></li>
			<li><a href="" ><span class="glyphicon glyphicon-transfer" ></span>&nbsp;Expert Connect</a></li>
			<li><a href="" ><span class="glyphicon glyphicon-refresh" ></span>&nbsp;Collaborate</a></li>
			<li><a href="" ><span class="glyphicon glyphicon-gift" ></span>&nbsp;Favours</a></li>
			<li><a href="logout.php"><span class="glyphicon glyphicon-off"></span>&nbsp;Logout</a></li>
		</ul>
	</div>
	</br>	
	<div class="col-sm-8">
	<?php
	

	try	{
		$sql_qstn="select qstn_id,qstn_titl,qstn_desc,qstn_status,topic_id,posted_by,created_ts,last_updt_ts from questions where qstn_id='".$qid."'";
		foreach($conn->query($sql_qstn) as $row_qstn)	{
			$posted_by=$row_qstn["posted_by"];
			
			try	{
				$sql_fetch_disp_name="select disp_name from users where user_id='".$posted_by."'";
				foreach($conn->query($sql_fetch_disp_name) as $row_user_detls)
					$disp_name=$row_user_detls["disp_name"];
			}
			catch(PDOException $e)	{
				echo 'Error fetching user details';
			}
			?>
			<span id="q-titl-area"><hgroup><?php echo $row_qstn["qstn_titl"]; ?></hgroup></span>
			<span id="q-sub-titl"><?php echo "Asked by - ".$disp_name; ?></span>
			</br>
			<div class="panel panel-default">
			  <div class="panel-body"><?php echo $row_qstn["qstn_desc"]; ?></div>
			</div>
			<!--<div id="q-desc-area"><p><?php #echo $row_qstn["qstn_desc"]; ?></p></div></br>-->
		<?php
		}
	}
	catch(PDOException $e)	{
		echo "Some error occurred";
	}
	?>
	
	<!--
	<form id="ans-form" action="<?php# echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" >
	
		<input type="hidden" name="qstnid" value="<?php #echo $qid; ?>">
		<input type="hidden" name="postedby" value="<?php #echo $posted_by; ?>">
		<textarea class="form-control" id="user-ans" name="user-text" value="<?php #echo $ans_desc; ?>"></textarea>
		<script>
			CKEDITOR.replace('user-text');
		</script>
		</br></br>
		<button type="submit" id="ans-qstn-submit" class="btn btn-default">Post Answer</button>
	</form>
-->
	<textarea class="form-control" id="user-ans" name="user-text" rows="7" ></textarea>
	
	</br></br>
	<a id="sub-link" href="javascript:void(0)" onclick="loadAnswerList(document.getElementById('user-ans').value,<?php echo $qid; ?>,'<?php echo $posted_by; ?>')">Answer</a>
	</br></br>
	<div id="ans_container">
	<?php
	try	{
	$sql_ans = "select ans_id,ans_desc,up_votes,down_votes,posted_by,created_ts,last_updt_ts from answers where qstn_id='".$qid."' order by created_ts desc";
	foreach($conn->query($sql_ans) as $row_ans)	{
		$ansid=$row_ans["ans_id"];
		$upvotes=$row_ans["up_votes"];
		$downvotes=$row_ans["down_votes"];
		$createdts=$row_ans["created_ts"];
		$postedby=$row_ans["posted_by"];
		
		$sql_fetch_img="select pro_img_url from users where user_id='".$postedby."'";
		$stmt=$conn->prepare($sql_fetch_img);
		$stmt->execute();
		$result=$stmt->fetch();
		$image=$result['pro_img_url'];
	?>	
	<div class="ans-section" id="user-answer-<?php echo $ansid; ?>">
		<div class="ans-user-img" style="background-image:url('<?php echo $image; ?>'); background-size:cover;"></div>
		<div class="auth-time-section">
			<?php echo $postedby." ".get_time_diff($createdts); ?>
		</div>
		</br>
		<div class="main-ans-block">
			<?php echo $row_ans["ans_desc"]; ?>
		</div>
		<?php 
		
			$sql_check_up_vote = "select count(1) as vote_count from user_posts_votes where user_id='".$_SESSION['user']."' 
								and post_type='A' and vote_type=0 and post_id=".$ansid;
			$sql_check_down_vote = "select count(1) as vote_count from user_posts_votes where user_id='".$_SESSION['user']."' 
								and post_type='A' and vote_type=1 and post_id=".$ansid;
			$stmt_check_up_vote = $conn->prepare($sql_check_up_vote);
			$stmt_check_up_vote->execute();
			$sql_row_0 = $stmt_check_up_vote->fetch();
			$count_row_0 = $sql_row_0['vote_count'];
			$stmt_check_down_vote = $conn->prepare($sql_check_down_vote);
			$stmt_check_down_vote->execute();
			$sql_row_1 = $stmt_check_down_vote->fetch();
			$count_row_1 = $sql_row_1['vote_count'];
				
			?>
			<input type="hidden" id="upvote-value-ans-<?php echo $ansid; ?>" value="<?php echo $count_row_0; ?>" />
			<input type="hidden" id="downvote-value-ans-<?php echo $ansid; ?>" value="<?php echo $count_row_1; ?>" />

		
		<span class="vote-sec">
		<?php 
					if($postedby != $_SESSION['user'])	{
				?>
			<a href="javscript:void(0)" class="vote-link-area" 
				onclick="increaseCount('<?php echo $ansid."','".$postedby."'";?>,0,document.getElementById('upvote-value-ans-<?php echo $ansid; ?>').value)">
				<span id="glyph-up-ans-<?php echo $ansid; ?>" class="glyphicon glyphicon-thumbs-up <?php echo ($count_row_0 > 0)?"glyph-ans-upvoted":"";  ?>"></span>
			<span id="up-vote-ans-<?php echo $ansid; ?>" class="vote-count-area"><?php echo $upvotes; ?></span></a>
			<?php } 
						else	{
					?>
			<span class="badge upvote-ans-badge">
				<span class="glyphicon glyphicon-thumbs-up"></span>
				<span id="up-vote-ans-<?php echo $ansid; ?>" class="vote-count-area"><?php echo $upvotes; ?></span>
			</span>
				<?php } ?>
		</span>
		<span class="vote-sec">
		<?php 
					if($postedby != $_SESSION['user'])	{
				?>
			<a href="javscript:void(0)" class="vote-link-area" 
				onclick="increaseCount('<?php echo $ansid."','".$postedby."'";?>,1,document.getElementById('downvote-value-ans-<?php echo $ansid; ?>').value)">
				<span id="glyph-down-ans-<?php echo $ansid; ?>"  class="glyphicon glyphicon-thumbs-down <?php echo ($count_row_1 > 0)?"glyph-ans-downvoted":"";  ?>"></span>
			<span id="down-vote-ans-<?php echo $ansid; ?>" class="vote-count-area"><?php echo $downvotes; ?></span></a>
		<?php } 
					else	{
				?>
		<span class="badge downvote-ans-badge">
			<span class="glyphicon glyphicon-thumbs-down"></span>
			<span id="down-vote-ans-<?php echo $ansid; ?>" class="vote-count-area"><?php echo $downvotes; ?></span>
		</span>
			<?php } ?>
		</span>
		<a class="comment-link" href="javascript:void(0)" onclick="showComment('comment-box-<?php echo $ansid; ?>')">Comment</a>
		</br>
		<div class="comment-box" id="comment-box-<?php echo $ansid; ?>"></br>	
			<textarea class="form-control" rows="2" id="comment-<?php echo $ansid; ?>" placeholder="Your comment goes here..."></textarea></br>
			<button type="button" class="btn btn-primary" style="padding: 1px 2px;" 
			onclick="addComment(<?php echo $ansid; ?>,document.getElementById('comment-<?php echo $ansid; ?>').value,'comment-area-<?php echo $ansid; ?>','<?php echo $postedby; ?>',<?php echo $qid; ?>,'<?php echo $posted_by; ?>')">Comment</button>
			<strong><span id="load-msg-<?php echo $ansid; ?>"></span></strong>
			</br>
			<div class="col-sm-11" id="comment-area-<?php echo $ansid; ?>">
			<?php
				try	{
					$sql_fetch_comment="select comment_id,comment_desc,posted_by,created_ts from comments where ans_id=".$ansid;
					foreach($conn->query($sql_fetch_comment) as $row_cmnt)	{
						$comment_id=$row_cmnt['comment_id'];
						$comment=$row_cmnt['comment_desc'];
						$posted_by=$row_cmnt['posted_by'];
						$created_ts = $row_cmnt['created_ts'];
						echo '<div class="user-comment-sec" id="comment-'.$comment_id.'">'.$comment.' - <strong>'.$posted_by.'</strong>&nbsp;&nbsp;<span class="time-sec">'.get_user_date($created_ts).'</span></div>';
					}
				}
				catch(PDOException $e)	{
					echo "Internal server error";
				}
			?>
			</div>
		</div>
	</div></br>
	<?php
	}
}
catch(PDOException $e)	{
	echo "Some error occured ".$e->getMessage();
}
?>
	</div>
</div>
<div class="col-sm-4"></div>
<div>

<?php #include "footer.php"; ?>

</body>
</html>
 