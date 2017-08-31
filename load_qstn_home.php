<?php 
session_start();
if(!$_SESSION["logged_in"])	{
	echo "Please login </br>";
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
$flag=htmlspecialchars(stripslashes(trim($_REQUEST["flag"])));
$sort_order=htmlspecialchars(stripslashes(trim($_REQUEST["token"])));

if($sort_order == 1)	
	$sql_sort=" order by a.up_votes desc";
else if($sort_order == 2)
	$sql_sort=" order by created_ts desc";
else if($sort_order == 3)
	$sql_sort = " order by a.down_votes desc";
	
	try	{
		$query_string="";
		$sql="select a.qstn_id,a.qstn_titl,a.qstn_desc,a.posted_by,a.up_votes,a.down_votes,a.created_ts from questions a ";
		if($flag=='p')	{
			$sql.="where posted_by='".$_SESSION['user']."' order by created_ts desc";
		}
		else if($flag=='m')	{
			
			$sql_fetch_user_interests="select b.tag_name 
									   from user_tags a
									   inner join tags b
									   on a.tag_id=b.tag_id
									   where a.user_id='".$_SESSION['user']."'";
			foreach($conn->query($sql_fetch_user_interests) as $result_user_interest)	{
				$query_string=$query_string.$result_user_interest['tag_name']."|";
			}
			$query_string=substr($query_string,0,strlen($query_string)-1);
			
			$sql.="inner join qstn_tags b
				   on a.qstn_id=b.qstn_id
				   inner join tags c 
				   on b.tag_id=c.tag_id 
				   where c.tag_name REGEXP ('".$query_string."')
				   or a.qstn_titl REGEXP ('".$query_string."')
				   or a.qstn_desc REGEXP ('".$query_string."')
				   order by a.created_ts desc limit 10";
		}
		else if($flag=='e')	{
			$sql.="order by a.up_votes desc limit 10";
		}
		else if($flag=='t')	{
			$sql.="order by a.created_ts desc ";
		}
		else 	{
			?>
			<div class="row">
				<div class="col-sm-6">
					<h4>Questions on topics</h4>
				</div>
				<div class="col-sm-6">
					<!--
					<select class="form-control" id="filter-list" >
						<option>My posts</option>
					</select>
					-->
					<select class="form-control" id="sort-list" onchange="sortQuestions('<?php echo $flag; ?>',this.value)">
						<option value="selected">-- Sort the list --</option>
						<option>Most up-voted</option>
						<option>Recent</option>
						<option>My posts</option>
						<option>Most viewed</option>
					</select>
				</div>
			</div></br>
			<?php
			$sql.="inner join topics b 
				  on a.topic_id=b.topic_id 
				  where b.topic_desc='".$flag."'";
			
		}
		if($sort_order != 0)
			$sql.=$sql_sort;
		foreach($conn->query($sql) as $row)	{
			$qid=$row['qstn_id'];
			$posted_by=$row['posted_by'];
			$created_ts=$row['created_ts'];
			$up_votes=$row['up_votes'];
			$down_votes=$row['down_votes'];
?>
<div class="qstn_row">
	<a id="titl-link" href="<?php echo 'qstn_ans.php?qid='.$qid ?>"><?php echo $row["qstn_titl"]; ?></a>&emsp;
	<span id="titl-subsection">
		<span id="ans-count">
		<?php
			try{
				$sql_ans_count="select count(1) as ans_count from answers where qstn_id=".$qid;
				foreach($conn->query($sql_ans_count) as $row_cnt)
					echo $row_cnt['ans_count'];
			}
			catch(PDOException $e)	{
				echo "Error fetching answer count</br>";
			}
		?>
		</span>
		<span id="ans-count-overlay">answers</span>&emsp;.&emsp;
		<span id="view-count">10</span>
		<span id="view-overlay">views</span>
	</span>	
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
		<div id="img-overlay">
			<img src="<?php echo $img_url; ?>" width="30" height="35"/>
		</div>
		<div class="sub-section" id="sub-section-2">
			<div id="user-name-lay"><?php echo $posted_by; ?>&emsp;</div>
			<span id="up-vote-logo" ></span>
				<span id="up-vote-count"><?php echo $up_user_votes; ?></span>&emsp;
			<span id="down-vote-logo" ></span>
				<span id="down-vote-count"><?php echo $down_user_votes; ?></span>
		</div>
		
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
	</div></br>
	<?php 
		}
	}
	catch(PDOException	$e)	{
		echo 'Error fetching Question'.$e->getMessage();
	}
	?>

