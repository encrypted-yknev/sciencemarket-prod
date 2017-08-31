<?php 
session_start();
if(!$_SESSION["logged_in"])	{
	echo "Please login </br>";
	header("location:login.php");
}
include "connectDb.php";

$text=htmlspecialchars(addslashes(trim($_REQUEST["text"])));
if(!empty($text))	{
	try	{
		$sql_qstn="select qstn_id,qstn_titl,qstn_desc,qstn_status,topic_id,posted_by,created_ts,last_updt_ts from questions where match(qstn_titl,qstn_desc) against('".$text."' IN BOOLEAN MODE)";
		foreach($conn->query($sql_qstn) as $row_qstn)	{
			$result=$row_qstn["qstn_titl"];
			if(!empty($result))	{
				echo '<a href="qstn_ans.php?qid='.$row_qstn["qstn_id"].'"><span>'.$result.'</span></a>';
				echo '<p class="ask-qstn-desc">'.$row_qstn["qstn_desc"].'</p>';
			}
		}
	}
	catch(PDOException $e)	{
		echo $e->getMessage();
	}
}

?>
 
 