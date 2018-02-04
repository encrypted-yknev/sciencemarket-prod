<?php 
session_start();
if(isset($_SESSION['logged_in']) and $_SESSION['logged_in'])
	$logged_in=1;
else
	$logged_in=0;

include "connectDb.php";
include "forum/functions/get_time.php";
include "forum/functions/get_time_offset.php";


if(isset($_GET['qid']))
	$qid=$_GET["qid"];
try	{
	$sql_updt_views = "update questions set views = views + 1 where qstn_id = ".$qid;
	$stmt_updt_views=$conn->prepare($sql_updt_views);
	$stmt_updt_views->execute();
}
catch(PDOException $e)	{
	
}
?>
<html>
<head>
<title>
	Science Market - <?php 
		try	{
			$sql_fetch_qstn_titl="select qstn_titl from questions where qstn_id = ".$qid;
			$stmt_fetch_qstn_titl = $conn->prepare($sql_fetch_qstn_titl);
			$stmt_fetch_qstn_titl->execute();
			$row_qstn_titl=$stmt_fetch_qstn_titl->fetch();
			echo $row_qstn_titl['qstn_titl'];
		}
		catch(PDOException $e)	{
			echo "Answer question";
		}
	?>
</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="styles/header.css">
<link rel="stylesheet" type="text/css" href="styles/footer.css">
<link rel="stylesheet" type="text/css" href="styles/qstn_ans.css">
<link rel="stylesheet" type="text/css" href="styles/qstn.css">
<link href="https://fonts.googleapis.com/css?family=Quicksand" rel="stylesheet">
<link rel="stylesheet" href="styles/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type = "text/javascript" src = "https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
<script src="ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="js/qna.js"></script>
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/header.js"></script></head>
</head>
<body>
<div id="block"></div>

<?php

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
					<div id="media-image">
						<img src="img/logo4.svg" width="55" height="55"/>
						<img src="img/logo.svg" width="150" height="50"/>
					</div>
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
		<?php if($logged_in == 1)	{	?>
		<div class="row">
			<div class="col-sm-12" id="pic-row">
				<img src="<?php echo $_SESSION["pro_img"]; ?>" id="side-menu-img" alt="profile image" width="100" height="120"> 
			</div>
		</div></br>

		<div>upvotes   : <span class="badge"><?php echo $_SESSION["up_vote"]; ?></span></div>
		<div>downvotes : <span class="badge"><?php echo $_SESSION["down_vote"]; ?></span></div>
		<?php	}	?>
		</br>
		<ul class="nav nav-pills nav-stacked">
			<li><a href="profile.php" ><span class="glyphicon glyphicon-user" ></span>&nbsp;My Profile</a></li>
			<li><a href="dashboard.php"><span class="glyphicon glyphicon-home" ></span>&nbsp;Dashboard</a></li>
			<li><a href="forum" ><span class="glyphicon glyphicon-question-sign"></span>&nbsp;Q/A Forum</a></li>
			<li><a href="expert_connect.php" ><span class="glyphicon glyphicon-transfer" ></span>&nbsp;Expert Connect</a></li>
			<li><a href="" ><span class="glyphicon glyphicon-refresh" ></span>&nbsp;Collaborate</a></li>
			<li><a href="" ><span class="glyphicon glyphicon-gift" ></span>&nbsp;Favours</a></li>
			<li>
			<?php if($logged_in == 1)	{	?>
			<a href="logout.php"><span class="glyphicon glyphicon-off"></span>&nbsp;Logout</a>
			<?php	}	else	{	?>
			<a href="index.php"><span class="glyphicon glyphicon-off"></span>&nbsp;Login / Register</a>
			<?php	}		?>
			</li>
		</ul>
	</div>
	</br>	
	<div class="col-sm-8" id="left-section">
	
	
	<?php
	try	{
		$sql_qstn="select qstn_id,qstn_titl,qstn_desc,qstn_status,topic_id,posted_by,created_ts,last_updt_ts from questions where qstn_id=".$qid;
		foreach($conn->query($sql_qstn) as $row_qstn)	{
			$posted_by=$row_qstn["posted_by"];
			
			try	{
				$sql_fetch_disp_name="select disp_name,pro_img_url from users where user_id='".$posted_by."'";
				foreach($conn->query($sql_fetch_disp_name) as $row_user_detls)	{
					$disp_name=$row_user_detls["disp_name"];
					$img_url=$row_user_detls["pro_img_url"];
				}
			}
			catch(PDOException $e)	{
				echo 'Error fetching user details';
			}
			?>
			<div class="user-qstn-image" style="background-image:url('<?php echo $img_url; ?>'); background-size:cover;" >
					</div>
			<span id="q-titl-area"><hgroup><?php echo $row_qstn["qstn_titl"]; ?></hgroup></span>
			<span id="q-sub-titl"><strong><?php echo $posted_by; ?></strong></span>
			</br></br>
			
			<div class="qstn-desc-section"><?php echo $row_qstn["qstn_desc"]; ?></div></br>
		<?php
		}
	}
	catch(PDOException $e)	{
		echo "Some error occurred";
	}
	?>
	<?php	if($logged_in == 1)	{	?>
	<textarea class="form-control" id="userans" name="userans" onfocus="showAlert(0,<?php echo $logged_in; ?>)" rows="5" cols="50"></textarea></br>
	<script>
		CKEDITOR.replace('userans');
	</script>
	<button class="btn btn-primary" onclick="loadAnswerList(<?php echo $qid; ?>,'<?php echo $posted_by; ?>')">Post Answer</button>
	
	<?php	}	?>

	</br></br><hr>
	<div id="ans_container">
	<?php
	try	{
	$sql_ans = "select ans_id,ans_desc,up_votes,down_votes,posted_by,created_ts,last_updt_ts from answers where qstn_id=".$qid." order by created_ts desc";
	foreach($conn->query($sql_ans) as $row_ans)	{
		$ansid=$row_ans["ans_id"];
		$upvotes=$row_ans["up_votes"];
		$downvotes=$row_ans["down_votes"];
		$createdts=$row_ans["created_ts"];
		$postedby=$row_ans["posted_by"];
		
		$user_id_fetch=$postedby;
		include $slashes."fetch_user_dtls.php";
	?>	
	<div class="ans-section" id="user-answer-<?php echo $ansid; ?>">
		<div class="ans-user-img" 
			onmouseleave='showUserCard(event,1,<?php echo $ansid; ?>,"a")' 
			onmouseenter='showUserCard(event,0,<?php echo $ansid; ?>,"a")' 
			style="background-image:url('<?php echo $img_url; ?>'); background-size:cover;"></div>
		<div class="auth-time-section">
			<?php
				echo "<span id='ans-posted-".$ansid."' 
				onmouseleave='showUserCard(event,1,".$ansid.",\"a\")' 
				onmouseenter='showUserCard(event,0,".$ansid.",\"a\")'>
				<a href='profile.php?user=".$postedby."'>".$postedby."</a></span> . ".
				get_user_date(convert_utc_to_local($createdts));
			?>
		</div></br>
		<?php 
			$msg_div_id = "msg-a-".$ansid;
			$post_type="a";
			$user_card=$postedby;
			$up_vote=$upvotes;
			$down_vote=$downvotes;
			$id=$ansid;
			include $slashes."user_card.php"; 
			include $slashes."message_box.php";
		?>
		<div class="main-ans-block">
			<?php echo $row_ans["ans_desc"]; ?>
		</div></br>
		<?php 
			if($logged_in == 1)	{
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
			}
			else	{
				$count_row_0 = 0;
				$count_row_1 = 0;
			}
			?>
			<input type="hidden" id="upvote-value-ans-<?php echo $ansid; ?>" value="<?php echo $count_row_0; ?>" />
			<input type="hidden" id="downvote-value-ans-<?php echo $ansid; ?>" value="<?php echo $count_row_1; ?>" />

		
		<span class="vote-sec">
		<?php 
			if($logged_in == 1)	{
					if($postedby != $_SESSION['user'])	{
				?>
			<span href="javscript:void(0)" class="vote-link-area" style="cursor:pointer;"
				onclick="increaseCount('<?php echo $ansid."','".$postedby."'";?>,0,document.getElementById('upvote-value-ans-<?php echo $ansid; ?>').value)">
				<span id="glyph-up-ans-<?php echo $ansid; ?>" class="glyphicon glyphicon-thumbs-up <?php echo ($count_row_0 > 0)?"glyph-ans-upvoted":"";  ?>"></span>
			<span id="up-vote-ans-<?php echo $ansid; ?>" class="vote-count-area"><?php echo $upvotes; ?></span></span>
			<?php }
				else	{
					?>
				<span class="glyphicon glyphicon-thumbs-up"></span>
				<span id="up-vote-ans-<?php echo $ansid; ?>" class="vote-count-area"><?php echo $upvotes; ?></span>
				<?php } 
			}
			else	{
				?>
			<span class="glyphicon glyphicon-thumbs-up"></span>
			<span id="up-vote-ans-<?php echo $ansid; ?>" class="vote-count-area"><?php echo $upvotes; ?></span>
			<?php } ?>
		</span>
		<span class="vote-sec">
		<?php 
			if($logged_in == 1)	{
					if($postedby != $_SESSION['user'])	{
				?>
			<span href="javscript:void(0)" class="vote-link-area" style="cursor:pointer;"
				onclick="increaseCount('<?php echo $ansid."','".$postedby."'";?>,1,document.getElementById('downvote-value-ans-<?php echo $ansid; ?>').value)">
				<span id="glyph-down-ans-<?php echo $ansid; ?>"  class="glyphicon glyphicon-thumbs-down <?php echo ($count_row_1 > 0)?"glyph-ans-downvoted":"";  ?>"></span>
			<span id="down-vote-ans-<?php echo $ansid; ?>" class="vote-count-area"><?php echo $downvotes; ?></span></span>
		<?php }
				else	{
					?>
				<span class="glyphicon glyphicon-thumbs-down"></span>
				<span id="down-vote-ans-<?php echo $ansid; ?>" class="vote-count-area"><?php echo $downvotes; ?></span>
				<?php } 
			}
					else	{
				?>
			<span class="glyphicon glyphicon-thumbs-down"></span>
			<span id="down-vote-ans-<?php echo $ansid; ?>" class="vote-count-area"><?php echo $downvotes; ?></span>
			<?php } ?>
		</span>
		<a class="comment-link" href="javascript:void(0)" onclick="showComment(<?php echo $ansid; ?>)">View comments</a>
		</br>
		<div class="comment-section" id="comment-front-<?php echo $ansid; ?>">
		</br>
		<div class="comments-list" id="comment-area-front-<?php echo $ansid; ?>" >
			<strong><span id="load-msg-<?php echo $ansid; ?>"></span></strong></br>
			<?php
			try	{
				$comment_array=array();
				$comment_id_str="";
				$sql_fetch_comment_ids="select comment_id from comments where ans_id=".$ansid." order by created_ts asc";
				$stmt_fetch_comment_ids=$conn->prepare($sql_fetch_comment_ids);
				$stmt_fetch_comment_ids->execute();
				if($stmt_fetch_comment_ids->rowCount() > 0)	{
					while($row = $stmt_fetch_comment_ids->fetch())	{
						$cmt_id=$row['comment_id'];
						array_push($comment_array,$cmt_id);
					}
					$comment_id_str=implode("|",$comment_array);
				}
				
				$sql_fetch_comment="select comment_id,comment_desc,posted_by,created_ts from comments where ans_id=".$ansid." order by created_ts asc limit 5";
				$stmt_fetch_comment=$conn->prepare($sql_fetch_comment);
				$stmt_fetch_comment->execute();
				
				if($stmt_fetch_comment->rowCount() > 0)	{
					echo "<div class='cmnt-section' id='cmnt-list-".$ansid."'>";
					while($row_cmnt = $stmt_fetch_comment->fetch())	{
						$comment_id=$row_cmnt['comment_id'];
						$comment=$row_cmnt['comment_desc'];
						$cmnt_posted_by=$row_cmnt['posted_by'];
						$created_ts = $row_cmnt['created_ts'];
						
						echo "<div class='user-comment-sec' id='comment-list-front-".$comment_id."'>".$comment." - <strong>
							<span id='cmn-posted-".$comment_id."' 
							onmouseleave='showUserCard(event,1,".$comment_id.",\"fc\")' 
							onmouseenter='showUserCard(event,0,".$comment_id.",\"fc\")'>
							<a href='".$slashes."profile.php?user=".$cmnt_posted_by."'>".$cmnt_posted_by."</a></span></strong>&nbsp;&nbsp;
							<span class='time-sec'>".get_user_date(convert_utc_to_local($created_ts))."</span></div>";
						
						$user_id_fetch=$cmnt_posted_by;
						
						include $slashes."fetch_user_dtls.php";
						
						$post_type="fc";
						$id=$comment_id;
						$user_card=$cmnt_posted_by;
						$up_vote=$up_user_votes;
						$down_vote=$down_user_votes;
						include $slashes."user_card.php"; 
						
					}
					echo "</div>";
				}
				else	{
					echo "<span style='margin-left:10px;font-size:13px; color:#626262;'>No comments in this answer yet</span>";
				}
			}
			catch(PDOException $e)	{
				echo "Internal server error";
			}
		?>
		<?php
			$comment_count = $stmt_fetch_comment_ids->rowCount();
			if($comment_count > 5)
				echo "<span id='comment-load-front-text-".$ansid."' href='javascript:void(0)' onclick='loadMoreComments(".$ansid.")' class='show-comment-text' style='margin-left:10px;font-size:12px; color:#626262; text-decoration:underline;'>View more comments...</span></br>";
		?></br>
		<input class="form-control comment-textbox" id="comment-front-ans-<?php echo $ansid; ?>" onfocus="showAlert(0,<?php echo $logged_in;?>)" placeholder="Your comment goes here..." onkeypress="addComment(event,<?php echo $ansid.",'".$postedby."',".$qid.",'".$posted_by."'"; ?>)" style="margin-left:10px;"/>
		</br>
		</div></br>
		<input id="cid-front-section-<?php echo $ansid; ?>" type="hidden" value="<?php echo $comment_id_str; ?>"/>
		</div>
	</div></br>
	<?php
	}
}
catch(PDOException $e)	{
	echo "Some error occured";
}
?>
	</div>
</div>
<div class="col-sm-4"></div>
</div>
<?php include "footer.php"; ?>

</body>
</html>
 
