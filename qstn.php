<?php
session_start();
if(!$_SESSION["logged_in"])	{
	header("location:index.php");
}

include "connectDb.php";
date_default_timezone_set("Europe/London");
$message="";

$q_topic_id=$q_titl=$q_desc=$topic_id="";
if ($_SERVER["REQUEST_METHOD"] == "POST")	{
	$q_topic_id=(int)htmlspecialchars(stripslashes(trim($_POST['sub_topic'])));
	$q_titl=htmlspecialchars(stripslashes(trim($_POST['qtitl'])));
	$q_desc=trim($_POST['qdesc']);
	$tags=htmlspecialchars(stripslashes(trim($_POST['tags'])));
	#check for empty field)
	if(!empty($q_topic_id) && !empty($q_titl) && !empty($q_desc) && !empty($tags))	{
		$tag_list=explode(" ",$tags);
		$tag_count=count($tag_list);
		foreach($tag_list as $tag_name)	{
			try	{
				$sql_add_tag="insert into tags(tag_name,created_by) 
							select * from (select '".$tag_name."','".$_SESSION['user']."') as temp
							where not exists (select 1 from tags where tag_name='".$tag_name."')";
				$conn->exec($sql_add_tag);
				
			}	
			catch(PDOException $e)	{
				$message = '<div class="alert alert-danger">Internal server error</div>';
				return;
			}
		}
		try	{
			
			$sql="insert into questions (`qstn_titl`, `qstn_desc`, `qstn_status`, `topic_id`, `posted_by`)
				values('".$q_titl."', '".$q_desc."','A',".$q_topic_id.",'".$_SESSION["user"]."')";
			
			$conn->exec($sql);
		}
		catch(PDOException $e)	{
			$message = '<div class="alert alert-danger">Some Error occurred</div>';
			return;
		}
		
		try	{
			$sql_fetch_qid="select qstn_id from questions where posted_by='".$_SESSION["user"]."' order by created_ts desc limit 1";
			foreach($conn->query($sql_fetch_qid) as $row)	
				$qid=$row["qstn_id"];
			foreach($tag_list as $tag_name)	{
				$sql_fetch_tag="select tag_id from tags where tag_name='".$tag_name."'";
				$stmt_fetch_tag=$conn->prepare($sql_fetch_tag);
				$stmt_fetch_tag->execute();
				$row=$stmt_fetch_tag->fetch();
				$tag_id=$row["tag_id"];
				try	{
					$sql_add_qstn_tag="insert into qstn_tags(qstn_id,tag_id) values(".$qid.",".$tag_id.")";
					$conn->exec($sql_add_qstn_tag);
				}
				catch(PDOException $e)	{
					$message = '<div class="alert alert-danger">Error occured while posting question</div>';
				}
			}
			header("location:forum/myposts/");
			
		}
		catch(PDOException $e)	{
			$message = '<div class="alert alert-danger">Some Error occurred</div>';
		}
	}
	else {
		$message = '<div id="no-qstn-msg-section" class="alert alert-danger">Please enter mandatory fields</div>';
	}
}
	
	function get_time_diff($timestamp_ans)	{
#	date_default_timezone_set("Asia/Kolkata");

	$timestamp_cur=date("Y-m-d H:i:sa");
	/* echo $timestamp_ans."</br>"; 
	echo $timestamp_cur; */
	
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
<html>
<head>
<title>Science Market - Question & Answer forum. Post, discuss & comment</title>
<meta charset="utf-8">
<meta name="description" content="Science Market. Having doubts? ask and post questions. discuss. forums. answer questions. comment on users posts. Clear your doubts. online portal to discuss on different topics">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="styles/header.css">
<link rel="stylesheet" type="text/css" href="/styles/qstn.css" >
<link rel="stylesheet" type="text/css" href="styles/footer.css">
<link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type = "text/javascript" src = "https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>

<link rel="stylesheet" href="styles/bootstrap.min.css">
<script src="js/bootstrap.min.js"></script>
<script src="ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="js/qstn.js"></script>
</head>
<body>
<div id="block"></div>
<?php include "header.php"; ?>
<div id="block-container"></div>

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
		<div id="page-title"><span>Ask Question</span></div></br>
		<div class="row">
			<div class="col-sm-3">
				<!--
				<div id="row-1">
					<a href="qstn.php" class="btn btn-info">Ask Questions</a>
				</div> -->
			</div>
			<div class="col-sm-6">
				<div id="row-2">
					<input type="text" class="form-control" id="srch-box-media" placeholder="Search questions" />
				</div>
			</div>
		</div>
	</div>
	<div id="options-menu">
		<div id="proimg">
			<img id="propic" src="<?php echo $_SESSION['pro_img']; ?>" />
		</div></br></br>
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
	</br>
	<div class="row">
		<div id="ask-qstn" class="col-sm-6">
			<h4>Clear your doubts. Ask questions</h4>
			<form id="qstn-form" name="ask-qstn-form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
				Select topic:
				<select class="inp-box" id="q-topic" name="qtopic" onchange="getSubTopics(this.value)" onfocus="getInputInfo(1)">
					<option value="" selected>--- Select topic ---</option>
					<?php
						try	{	
							$sql="select topic_code,topic_desc,topic_id from topics where parent_topic=0";
							foreach($conn->query($sql) as $row)	{
								$row_topic_id=$row["topic_id"];
								$row_topic_desc=$row["topic_desc"];
								echo '<option value="'.$row_topic_id.'">'.$row_topic_desc.'</option>';
							}
						}
						catch(PDOException $e)	{
							
						}
					?>
				</select></br></br>
				Select sub-topic:
					
				<select class="inp-box" id="q-sub-topic" value="<?php echo $q_topic; ?>" name="sub_topic" onfocus="getInputInfo(2)">
					<option value="" selected>--- Select sub-topics ---</option>
				</select></br></br>
				Question title : <input class="inp-box" id="q-titl" type="text" value="<?php echo $q_titl; ?>" name="qtitl" onfocus="getInputInfo(3)"
				onkeyup="showQstnResults(this.value)"/></br></br>
				
				
				Ask your question : <textarea class="inp-box" id="q-desc" rows="5" cols="50" value="<?php echo $q_desc; ?>" name="qdesc" placeholder="Whats on your mind?"  onfocus="getInputInfo(4)"></textarea>
				<script>
					CKEDITOR.replace('qdesc');
				</script>
				<span id="q-msg"></span></br></br>
				Choose at-least 1 tag and max 4 tags : 
				<div id="tag">
					<input class="q-tags" id="user-qstn-tags" type="text" name="q_tags" placeholder="Add tags. Press ENTER"  onfocus="getInputInfo(5)"/>&emsp;
					<span id="alert-msg"></span></br></br>
				</div>
				<div id="tag-res"></div></br>
				<p><em>Before submitting, do check out for tips on how to use tags (Message box) by placing your cursor on the tags textbox and some related questions you might be looking for</em></p>
				
				<input type="hidden" id="tags" name="tags" /> 
				<button type="submit" id="ask-qstn-submit" class="btn btn-default" onclick="getTagsName()">Post Question</button>
			</form>
			<span id="result-section"></span>
			</br>
		</div>
		<div class="col-sm-6">
			<h4>Message box</h4>
			<div id="qstn-info">
			<?php 
				if($message != "")
					echo $message;
				else	{
			?>
			<div class="alert alert-info">
			  Place your cursor over each input section for tips and suggestions on how to enter data. What is preferred and what is restricted
			</div>
				<?php } ?>
			</div>
			<h4>Related questions</h4>
			<div id="qstn-list" class="alert alert-success">
			</div>
		</div>
	</div>
</div>

<?php
	include "footer.php";
?>
</body>
</html>
