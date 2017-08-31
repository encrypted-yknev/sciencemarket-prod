<?php

	try	{
		$query_string="";
		$sql="select a.qstn_id,a.qstn_titl,a.qstn_desc,a.posted_by,a.up_votes,a.down_votes,a.created_ts from questions a ";
		
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
			   order by a.created_ts desc limit 10";
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
		<div class="row">
			<div class="col-sm-3"><img src="<?php echo $img_url; ?>" width="30" height="35"/></div>
			<div class="col-sm-9">
				<span class="auth-text" id="up-vote-count1">venky</span>
				<span class="auth-text" id="up-vote-count2"><?php echo $up_user_votes; ?></span>
				<span class="auth-text" id="down-vote-count3"><?php echo $down_user_votes; ?></span>
			</div>
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
	<button type="button" class="btn btn-primary" style="padding: 1px 2px; font-size:13px;">Answer</button>
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
		echo 'No questions to fetch';
	}
?>