
	<div id="main-title"><?php echo $title; ?></div></br>
	<div id="msg-log">
		<?php echo $message; ?>
	</div></br>
	<div id="topic-keys-1">
		<span class="sub-title">Theoretical areas</span></br>
		<?php
			$topic=7;
			$num=3;
			include "fetch_topic_keys.php";
		?>
	</div>
	<div id="topic-keys-2">
		<span class="sub-title">Data analysis</span></br>
		<?php
			$topic=8;
			$num=4;
			include "fetch_topic_keys.php";
		?>
	</div>
	<div id="topic-keys-3">
		<span class="sub-title">Experimental topics</span></br>
		<?php
			$topic=9;
			$num=5;
			include "fetch_topic_keys.php";
		?>
	</div>
	<div id="topic-keys-4">
		<span class="sub-title">Cell culture</span></br>
		<?php
			$topic=10;
			$num=6;
			include "fetch_topic_keys.php";
		?>
	</div>
	<div id="feature-tags">
		<span class="sub-title">Featured and trending</span></br>
		<?php
		try	{
			$sql_fetch_top_tags = "select count(1) as qstn_tag_count,
										  t2.tag_id,
										  t2.tag_name
								   from qstn_tags t1
								   inner join tags t2
								   on t1.tag_id = t2.tag_id
								   group by t2.tag_id,
											t2.tag_name
								   order by 1 desc limit 30";
			foreach($conn->query($sql_fetch_top_tags) as $row_tags)	{
				$cnt_qstn_tags = $row_tags['qstn_tag_count'];
				$tag_id = $row_tags['tag_id'];
				$tag_name = $row_tags['tag_name'];
				?>
				<button title="<?php echo $cnt_qstn_tags; ?> posts" onclick="chooseInterest(1,<?php echo $tag_id; ?>)" data-set="0" id="qstn-int-<?php echo $tag_id; ?>" class="btn btn-default btn-user-choice"><?php echo $tag_name; ?></button>
			<?php
			}
		}
		catch(PDOException $e)	{
			
		}
		?>
	</div>
	<div id="user-tags">
		<span class="sub-title">Users having common interests that might interest you.</span></br>
		<?php
		try	{
			$sql_fetch_user_tags = " select count(1) as user_tag_count,
											t2.tag_id,
											t2.tag_name
									from user_tags t1
									inner join tags t2 
									on t1.tag_id=t2.tag_id
									group by t2.tag_id,
											 t2.tag_name
									
									order by 1 desc
									limit 10";
			foreach($conn->query($sql_fetch_user_tags) as $row_user_tags)	{
				$cnt_user_tags = $row_user_tags['user_tag_count'];
				$user_tag_id = $row_user_tags['tag_id'];
				$user_tag_name = $row_user_tags['tag_name'];
				?>
				<button title="<?php echo $cnt_user_tags; ?> Followers" onclick="chooseInterest(2,<?php echo $user_tag_id; ?>)" data-set="0" id="user-int-<?php echo $user_tag_id; ?>" class="btn btn-default btn-user-choice"><?php echo $user_tag_name; ?></button>
			<?php
			}
		}
		catch(PDOException $e)	{
			
		}
		?>
	</div>
