<?php
session_start();
if(!$_SESSION["logged_in"])	{
	header("location:index.php");
}
include "connectDb.php";
include "forum/functions/get_time.php";


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

try	{
	$sql_fetch_qstn_count="select count(1) as cnt_qstn from questions where posted_by = '".$_SESSION['user']."'";
	$sql_fetch_ans_count="select count(1) as cnt_ans from answers where posted_by = '".$_SESSION['user']."'";
	
	$stmt_qstn=$conn->prepare($sql_fetch_qstn_count);
	$stmt_qstn->execute();
	$result_qstn=$stmt_qstn->fetch();
	$count_qstn=$result_qstn['cnt_qstn'];

	$stmt_ans=$conn->prepare($sql_fetch_ans_count);
	$stmt_ans->execute();
	$result_ans=$stmt_ans->fetch();
	$count_ans=$result_ans['cnt_ans'];
}

catch(PDOException	$e)	{
	echo '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Science Market - Forums. Expert Connect. Collaborate and Favours</title>
<meta name="description" content="Science market. User dashboard." >
<meta charset="utf-8"> 
<meta name="viewport" content="width=device-width, initial-scale=1">
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
<script type="text/javascript" src="js/dashboard.js"></script>
<script type="text/javascript" src="js/qa_forum.js"></script>
<script type="text/javascript" src="js/header.js"></script></head>
<body onload="refreshNotify()">
<div id="block"></div>
<?php include "header.php"; ?>
	
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
			<div id="page-title"><span>User dashboard</span></div></br>
			<div class="row">
				<div class="col-sm-3">
					<div id="row-1">
						<a href="qstn.php" class="btn btn-info"><span class="glyphicon glyphicon-question-sign"></span>&nbsp;&nbsp;Ask </a>&nbsp;
						<a id="notify-mob" href="javascript:void(0)" class="btn btn-info" ><span class="glyphicon glyphicon-bell"></span>&nbsp;&nbsp;Notifications</a>
						<div id="show-notify-section-mobile"></div>
					</div>
				</div>
				<div class="col-sm-6">
					<div id="row-2">
						<input id="srch-qstn-mob" type="text" class="form-control" id="srch-box-media" placeholder="Search questions" onkeypress="fetchQuestionsMobile(this.value,'<?php echo $slashes; ?>')" />
					
						<div id="srch-result-mobile"></div>
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

			<div>upvotes   : <span class="badge"><?php echo $_SESSION["up_votes"]; ?></span></div>
			<div>downvotes : <span class="badge"><?php echo $_SESSION["down_votes"]; ?></span></div>
					
			</br>
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
			<div class="col-sm-2" id="main-side-column" style="background:#FBFBFB;">
				<div class="row" id="first-row">
					<div class="profile-picture">
						<a id="profile-img" href="profile.php" title="Go to my profile">
							<div class="side-user-img" style="background-image:url('<?php echo $_SESSION["pro_img"];?>'); background-size:cover;">
							</div>
						</a>
					</div>
				</div>
				<div class="row" id="second-row">
					<div class="side-data-section">
						</br>
						<div id="vote-section">
							<div class="" id="vote-up">
								<div id="upvote-logo"></div>&nbsp;
								<span class="vote-count-section" style="font-size:12px;"><strong><?php echo $_SESSION["up_votes"]; ?></strong></span>
							</div>
							
							<div class="" id="vote-down">
								<div id="downvote-logo"></div>&nbsp;
								<span class="vote-count-section" style="font-size:12px;"><strong><?php echo $_SESSION["down_votes"]; ?></strong></span>
							</div></br>
						</div>
					</div>
				</div>
				</br>
				<div class="row" id="third-row">
					<div class="col-sm-8">
						<div id="user-data">
						<div class="side-menu-links">Questions</div>
						<div class="side-menu-links">Answers</div>
						<div class="side-menu-links">Followers</div>
						<div class="side-menu-links">Following</div>
						</div>
						<div class="side-menu-links">
							<span id="interest-text">Your interests</span>
							<span id="interest-tabs">
								<?php
									try	{
										$sql_fetch_user_tags="select tag_name from tags t
															  inner join user_tags ut
															  on t.tag_id=ut.tag_id
															  where ut.user_id = '".$_SESSION['user']."'";
										foreach($conn->query($sql_fetch_user_tags) as $result_tags)	{
											echo '<span class="badge">'.$result_tags["tag_name"].'</span>';
										}
									}
									catch(PDOException	$e)	{
										echo "Error fetching interests";
									}
								?>
							</span>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="side-menu-links"><?php echo $count_qstn; ?></div>
						<div class="side-menu-links"><?php echo $count_ans; ?></div>
						<div class="side-menu-links">0</div>
						<div class="side-menu-links">0</div>
						<div class="side-menu-links"></div>
						<div class="side-menu-links"></div>
					</div>
				</div>
			</div>
			<div class="col-sm-6" id="middle-container">
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
					if(strlen(trim($query_string)) != 0)	{
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
							   
							   order by a.created_ts desc";
							   
								include "forum/fetch_answers1.php";
								if($stmt->rowCount() <=0)	{
									echo '<div class="alert alert-info">
										  We can\'t find any relevant questions for you. Please add some interests so that we can display questions which might be relevant to you. <strong><a href="profile.php">Click here</a></strong>
									  </div>';
								}
					}
					else
						echo '<div class="alert alert-info">
							  We can\'t find any relevant questions for you. Please add some interests so that we can display questions which might be relevant to you. <strong><a href="profile.php">Click here</a></strong>
						  </div>';
				}
				catch(PDOException	$e)	{
					echo '';
				}
				?>
			</div>
			<div class="col-sm-4">
				<div id="notification-section">
					<div><strong>Notifications</strong></div></br>
					<div id="show-notify-section"></div>
				</div></br>
				<!--
				<div id="recomm-posts-section">
					<span><strong>Recommended posts</strong></span>
					<div id="show-rcom-posts"></div>
				</div>-->
			</div>
		</div>
	</div>
</body>
</html>
