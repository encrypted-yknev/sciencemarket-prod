<?php

session_start();
if(!$_SESSION["logged_in"])	{
	header("location:login.php");
}
include "connectDb.php";

$topic_name=htmlspecialchars(stripslashes(trim($_REQUEST['topic'])));
if(strlen($topic_name)!=0)	{
	try	{
		$sql_fetch_sub_topics="select a.topic_desc from topics a
							   inner join topics b 
							   on a.parent_topic=b.topic_id
							   where b.topic_desc='".$topic_name."'";
		foreach($conn->query($sql_fetch_sub_topics) as $result_topics)	{
			echo '<option>'.$result_topics["topic_desc"].'</option>';
		}
	}
	catch(PDOException $e)	{
		echo $e->getMessage();
	}
}
?>