<?php

session_start();
if(!$_SESSION["logged_in"])	{
	header("location:login.php");
}
include "connectDb.php";

$topic_id=htmlspecialchars(stripslashes(trim($_REQUEST['topic'])));
if(!empty($topic_id))	{
	try	{
		$sql_fetch_sub_topics="select a.topic_id,a.topic_desc from topics a
							   inner join topics b 
							   on a.parent_topic=b.topic_id
							   where b.topic_id=".$topic_id;
		echo '<option value="" selected>--- Select sub-topics ---</option>';
		foreach($conn->query($sql_fetch_sub_topics) as $result_topics)	{
			echo '<option value="'.$result_topics['topic_id'].'">'.$result_topics['topic_desc'].'</option>';
		}
	}
	catch(PDOException $e)	{
		
	}
}
?>
