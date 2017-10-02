<?php 
session_start();

include "connectDb.php";
include "forum/functions/get_time.php";
include "forum/functions/get_time_offset.php";
function convert_utc_to_local($utc_timestamp)	{
	$date_utc=new DateTime($utc_timestamp,new DateTimeZone('UTC'));
	$date_utc->setTimeZone(new DateTimeZone($_COOKIE['user_tz']));
	$date_final = $date_utc->format('Y-m-d H:i:s');
	return $date_final;
}
$ans_list="";
if(isset($_REQUEST['flag']))	{
	$token=$_REQUEST['flag'];
}
if(isset($_REQUEST['ansid']))	{
	$ansid=$_REQUEST['ansid'];
}
if(isset($_REQUEST['cid_list']))	{
	$cmnt_list=explode("|",$_REQUEST['cid_list']);
}
if(isset($_REQUEST['element_count']))	{
	$element_count=$_REQUEST['element_count'];
}


$comment_id_name = "";
if($token == 0)
	$comment_id_name="comment-list-recent-";
else if($token == 1)
	$comment_id_name="comment-list-top-";
else if($token == 2)
	$comment_id_name="comment-list-front-";

$array_len=sizeof($cmnt_list);

if($element_count >= $array_len)	{
	echo 0;
	return;
}
$start_cmnt=$end_cmnt=0;
if($element_count < $array_len)  {
	$start_cmnt = $element_count;
}

if($start_cmnt+5 > $array_len)	{
	$end_cmnt = $array_len - 1;
}
else	{
	$end_cmnt = $start_cmnt+4;
}

try	{
	$sql="select comment_id,comment_desc,posted_by,created_ts 
		  from comments 
		  where ans_id=".$ansid." 
		  and comment_id between ".$cmnt_list[$end_cmnt]." and ".$cmnt_list[$start_cmnt]."
		  order by created_ts desc limit 5";
	$stmt=$conn->prepare($sql);
	$stmt->execute();
	if($stmt->rowCount() > 0)	{
		while($row_cmnt = $stmt->fetch())	{
			$comment_id=$row_cmnt['comment_id'];
			$comment=$row_cmnt['comment_desc'];
			$posted_by=$row_cmnt['posted_by'];
			$created_ts = $row_cmnt['created_ts'];
			echo '<div class="user-comment-sec" id="'.$comment_id_name.$comment_id.'">'.$comment.' - <strong>'.$posted_by.'</strong>&nbsp;&nbsp;<span class="time-sec">'.get_user_date(convert_utc_to_local($created_ts)).'</span></div>';
		}
	}						
	
}
catch(PDOException $e)	{
	echo $e->getMessage();
}


	?>
