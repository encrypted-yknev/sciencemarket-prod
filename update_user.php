<?php
session_start();
if(!$_SESSION["logged_in"])	{
	header("location:index.php");
}
include "connectDb.php";

$request_type=htmlspecialchars(stripslashes(trim($_REQUEST["request_type"])));

if($request_type==1)	{
	$name=htmlspecialchars(stripslashes(trim($_REQUEST["name"])));
	$mail=htmlspecialchars(stripslashes(trim($_REQUEST["mail"])));
	$mob=htmlspecialchars(stripslashes(trim($_REQUEST["mob"])));
	$location=htmlspecialchars(stripslashes(trim($_REQUEST["place"])));
	$desc=addslashes(htmlspecialchars(stripslashes(trim($_REQUEST["desc"]))));
	
	try	{
		$sql_update_user_dtls="update users set
							   disp_name='".$name."',
							   email_addr='".$mail."',
							   ph_num='".$mob."',
							   location='".$location."',
							   description='".$desc."'
							   where user_id='".$_SESSION['user']."'";
		$stmt=$conn->prepare($sql_update_user_dtls);
		$stmt->execute();
		$userid=$_SESSION['user'];
		if($stmt->rowCount() >= 0)	{
			include "session.php";
			echo "<div class='alert alert-success'>Details updated successfully</div>";
		}
		else	{
			echo "<div class='alert alert-danger'>Some error occurred</div>";
		}
	}
	catch(PDOException $e)	{
		echo "<div class='alert alert-danger'>Internal server error</div>";
	}
}
else if($request_type==2)	{
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
}

else if($request_type==3)	{
	$old_pwd=htmlspecialchars(stripslashes(trim($_REQUEST['old_pwd'])));
	$new_pwd=htmlspecialchars(stripslashes(trim($_REQUEST['new_pwd'])));
	$conf_pwd=htmlspecialchars(stripslashes(trim($_REQUEST['conf_pwd'])));
	if(strlen($old_pwd)!=0 and strlen($new_pwd)!=0 and strlen($conf_pwd)!=0)	{
		try	{
			$sql_check_old_pwd="select encrypt_pwd from users where user_id='".$_SESSION['user']."'";
			$stmt=$conn->prepare($sql_check_old_pwd);
			$stmt->execute();
			$result_pwd=$stmt->fetch();
			$old_encrypt_pwd=$result_pwd['encrypt_pwd'];
			
			if(md5($old_pwd) != $old_encrypt_pwd)	{
				echo '<div class="alert alert-danger">Please enter correct password</div>';
				return;
			}
			else	{
				if($new_pwd != $conf_pwd)
					echo '<div class="alert alert-danger">Passwords do not match</div>';
				else	{
					$sql_update_pwd="update users set encrypt_pwd='".md5($new_pwd)."' where user_id='".$_SESSION['user']."'";
					$stmt_pwd=$conn->prepare($sql_check_old_pwd);
					$stmt_pwd->execute();
					
					if($stmt_pwd->rowCount() > 0)
						echo '<div class="alert alert-success">Password updated successfully</div>';
					else
						echo '<div class="alert alert-danger">Some error occurred</div>';
				}
			}
			
		}
		catch(PDOException $e)	{
			echo '<div class="alert alert-danger">Internal server error</div>';
		}
	}
	else	
		echo '<div class="alert alert-danger">Enter required fields</div>';
}

