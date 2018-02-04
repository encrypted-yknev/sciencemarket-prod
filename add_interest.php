<?php 
session_start();

include "connectDb.php";

$interests=htmlspecialchars(stripslashes(trim($_REQUEST['user_interests'])));

if(!empty($interests))	{
	$tag_list=explode(" ",$interests);
	$tag_count=count($tag_list);
	
	try	{
		$sql_delete_user_tags="delete from user_tags where user_id='".$_SESSION['user']."'";
		$conn->exec($sql_delete_user_tags);
	}
	catch(PDOException $e)	{
		echo 'Error updating user interests';
		return;
	}
	foreach($tag_list as $tag_name)	{
		
		try	{
			/* Add to tags repository if its not present already */
			$sql_add_tag="INSERT INTO tags (tag_name, created_by)
			SELECT * FROM (SELECT '".$tag_name."','".$_SESSION["user"]."') AS tmp
			WHERE NOT EXISTS (
				SELECT tag_name FROM tags WHERE tag_name = '".$tag_name."') LIMIT 1";
			$conn->exec($sql_add_tag);
			
			/* Fetch the tag_id of the tag_name inserted */
			$sql_fetch_tag_id="select tag_id from tags where tag_name='".$tag_name."'";
			$stmt=$conn->prepare($sql_fetch_tag_id);
			$stmt->execute();
			$result_tag_id=$stmt->fetch();
			
			/* Check if the interest is already linked to user */
			$sql_check_user_tags="select count(1) as cnt from user_tags where user_id='".$_SESSION['user']."' and tag_id=".$result_tag_id['tag_id'];
			$stmt=$conn->prepare($sql_check_user_tags);
			$stmt->execute();
			$result_count=$stmt->fetch();
			
			/* If interest is not linked to user, link it */ 
			if($result_count['cnt'] == 0)	{
				$sql_add_user_tags="insert into user_tags(user_id,tag_id) values('".$_SESSION['user']."',".$result_tag_id['tag_id'].")";
				$conn->exec($sql_add_user_tags);
			}
		}	
		catch(PDOException $e)	{
			echo '<div class="alert alert-danger">Internal server error</div>'.$e->getMessage();
			return;
		}
	}
	echo '<div class="alert alert-success">Interests Updated!!</div>';
}

else	{
	echo '<span class="alert alert-warning">Please add some interests!!</span>';
}


?>
