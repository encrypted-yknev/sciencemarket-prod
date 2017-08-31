<?php
session_start();
if(!$_SESSION["logged_in"])	{
	echo "Please login </br>";
	header("location:login.php");
	#exit();
	#echo '<script type="text/javascript">window.location="home.php";</script>';
}
include "connectDb.php";
$message="";
$q_topic=htmlspecialchars(stripslashes(trim($_REQUEST['qtopic'])));
$q_titl=htmlspecialchars(stripslashes(trim($_REQUEST['qtitl'])));
$q_desc=htmlspecialchars(stripslashes(trim($_REQUEST['qdesc'])));
$page=htmlspecialchars(stripslashes(trim($_REQUEST['page'])));
$tags=htmlspecialchars(stripslashes(trim($_REQUEST['tags'])));

	if(!empty($q_topic) && !empty($q_titl) && !empty($q_desc) && !empty($tags))	{
		$tag_list=explode(" ",$tags);
		$tag_count=count($tag_list);
		foreach($tag_list as $tag_name)	{
			try	{
				$sql_add_tag="insert into tags(tag_name,created_by) 
				select * from (SELECT '".$tag_name."','".$_SESSION["user"]."') AS tmp
				WHERE NOT EXISTS (
					SELECT tag_name FROM tags WHERE tag_name = '".$tag_name."') LIMIT 1";
				$conn->exec($sql_add_tag);
			}	
			catch(PDOException $e)	{
				echo '<div class="alert alert-danger">Internal server error</div>';
				return;
			}
		}
		try	{
			$sql="select topic_id from topics where topic_desc='".$q_topic."'";
			foreach($conn->query($sql) as $row)	
				$topic_id=$row["topic_id"];
			$sql="insert into questions (`qstn_titl`, `qstn_desc`, `qstn_status`, `topic_id`, `posted_by`)
				values('".$q_titl."', '".$q_desc."','A',".$topic_id.",'".$_SESSION["user"]."')";
			$conn->exec($sql);
		}
		catch(PDOException $e)	{
			echo '<div class="alert alert-danger">Some Error occurred</div>';
			return;
		}
		
		try	{
			$sql_fetch_qid="select qstn_id from questions where posted_by='".$_SESSION["user"]."' order by created_ts desc limit 1";
			foreach($conn->query($sql_fetch_qid) as $row)	
				$qid=$row["qstn_id"];
			$sql_fetch_tags="select tag_id from tags where created_by='".$_SESSION["user"]."' order by created_ts desc limit ".$tag_count;
			foreach($conn->query($sql_fetch_tags) as $row)	{
				$tag_id=$row["tag_id"];
				try	{
					$sql_add_qstn_tag="insert into qstn_tags(qstn_id,tag_id) values(".$qid.",".$tag_id.")";
					$conn->exec($sql_add_qstn_tag);
				}
				catch(PDOException $e)	{
					echo '<div class="alert alert-danger">Error occured while posting question</div>';
				}
			}
			echo '1';
			
		}
		catch(PDOException $e)	{
			echo '<div class="alert alert-danger">Some Error occurred</div>';
		}
	}
	else if($page != 'load'){
		echo '<div id="no-qstn-msg-section" class="alert alert-danger">Please type some question</div>';
	}
	
	
?>

