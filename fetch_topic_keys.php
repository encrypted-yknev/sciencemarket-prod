<?php
	try	{
		$sql_fetch_topic = "select b.topic_code,
									a.key_id,
									a.key_desc 
							 from topic_keys a
							 inner join topics b 
							 on a.parent_topic = b.topic_id
							 where b.parent_topic = ".$topic;
		foreach($conn->query($sql_fetch_topic) as $row_topic)	{
			$topic_code = $row_topic['topic_code'];
			$key_id = $row_topic['key_id'];
			$key_desc = $row_topic['key_desc'];
			$unique_key=$topic_code.'-'.$key_id;
			?>
			<button onclick="chooseInterest(<?php echo $num.',\''.$unique_key.'\''; ?>)" data-set="0" id="topic-<?php echo $unique_key; ?>" class="btn btn-default btn-user-choice"><?php echo $key_desc; ?></button>
		<?php
		}
	}
	catch(PDOException $e)	{
		
	}
?>
