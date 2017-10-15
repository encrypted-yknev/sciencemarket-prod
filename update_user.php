<?php
session_start();
if(!$_SESSION["logged_in"])	{
	header("location:index.php");
}
include "connectDb.php";

$request_type=htmlspecialchars(stripslashes(trim($_REQUEST["request_type"])));

if($request_type==1)	{
	$user=addslashes(htmlspecialchars(stripslashes(trim($_REQUEST["user"]))));
	if(strlen(trim($user))==0)
		$user=$_SESSION['user'];
	$name=addslashes(htmlspecialchars(stripslashes(trim($_REQUEST["name"]))));
	if(strlen(trim($name))==0)
		$name=$_SESSION['name'];
	$mail=addslashes(htmlspecialchars(stripslashes(trim($_REQUEST["mail"]))));
	if(strlen(trim($mail))==0)
		$mail=$_SESSION['mail'];
	$mob=htmlspecialchars(stripslashes(trim($_REQUEST["mob"])));
	$location=addslashes(htmlspecialchars(stripslashes(trim($_REQUEST["place"]))));
	$desc=addslashes(htmlspecialchars(stripslashes(trim($_REQUEST["desc"]))));
	if(strlen(trim($desc))==0)
		$desc=$_SESSION['desc'];
	
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
			if($user != $userid)	{
				$sql_call_sp_user_updt="call update_user('".$userid."','".$user."',@err_cd,@err_desc)";
				
				$stmt_call_sp_user_updt=$conn->prepare($sql_call_sp_user_updt);
				$stmt_call_sp_user_updt->execute();
				
				$row_sp = $conn->query("select @err_cd as error_code,@err_desc as error_desc")->fetch();
				
				$error_code=$row_sp['error_code'];
				$error_desc=$row_sp['error_desc'];
				if(!strcmp($error_code,'00000'))	{
					$userid=$user;
					include "session.php";
					echo "<div class='alert alert-success msg-profile'>Details updated successfully</div>";
				}
				else	{
					echo "<div class='alert alert-success msg-profile'>Error occurred. Please try again</div>";
				}
			}
			else	{
				include "session.php";
				echo "<div class='alert alert-success msg-profile'>Details updated successfully</div>";
			}
		}
		else	{
			echo "<div class='alert alert-danger msg-profile'>Some error occurred</div>";
		}
	}
	catch(PDOException $e)	{
		echo "<div class='alert alert-danger msg-profile'>Internal server error</div>".$e->getMessage();
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
				echo '<div class="alert alert-danger msg-profile">Internal server error</div>'.$e->getMessage();
				return;
			}
		}
		echo '<div class="alert alert-success msg-profile">Interests Updated!!</div>';
	}

	else	{
		echo '<span class="alert alert-warning msg-profile">Please add some interests!!</span>';
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
				echo '<div class="alert alert-danger msg-profile">Please enter correct password</div>';
				return;
			}
			else	{
				if($new_pwd != $conf_pwd)
					echo '<div class="alert alert-danger">Passwords do not match</div>';
				else	{
					$sql_update_pwd="update users set encrypt_pwd='".md5($new_pwd)."' where user_id='".$_SESSION['user']."'";
					$stmt_pwd=$conn->prepare($sql_update_pwd);
					$stmt_pwd->execute();
					
					if($stmt_pwd->rowCount() > 0)
						echo '<div class="alert alert-success msg-profile">Password updated successfully</div>';
					else
						echo '<div class="alert alert-danger msg-profile">Some error occurred</div>';
				}
			}
			
		}
		catch(PDOException $e)	{
			echo '<div class="alert alert-danger msg-profile">Internal server error</div>';
		}
	}
	else	
		echo '<div class="alert alert-danger msg-profile">Enter required fields</div>';
}

else if($request_type == 4)	{
	
	$pwd_deactvt=htmlspecialchars(trim($_REQUEST['pwd']));
	try	{
		$sql_check_pwd="select encrypt_pwd from users where user_id='".$_SESSION['user']."'";
		$stmt=$conn->prepare($sql_check_pwd);
		$stmt->execute();
		$result_pwd=$stmt->fetch();
		$encrypt_pwd=$result_pwd['encrypt_pwd'];
		
		if(md5($pwd_deactvt)==$encrypt_pwd)	{
			$sql_check_act="select count(1) as cnt_user from users where status='I' and user_id='".$_SESSION['user']."'";
			foreach($conn->query($sql_check_act) as $row_count)
				$cnt_check=$row_count['cnt_user'];
			
			if($cnt_check > 0)
				echo "<div class='alert alert-warning msg-profile'>Account is already deactivated.</div>";
			
			else	{
				$sql_deactivate_ac="update users set status='I' where user_id='".$_SESSION['user']."'";
				$stmt_deactivate_ac=$conn->prepare($sql_deactivate_ac);
				$stmt_deactivate_ac->execute();
				
				if($stmt_deactivate_ac->rowCount() > 0)
					echo "<div class='alert alert-success msg-profile'>Account deactivated. The moment you logout, next time you won't be able to login. You can activate your account anytime you wish.</div>";
				else
					echo "<div class='alert alert-warning msg-profile'>Account not deactivated. Something is fishy.</div>";
			}
		}
		else	{
			echo "<div class='alert alert-danger msg-profile'>Incorrect password entered</div>";
		}
	}
	catch(PDOException $e)	{
		echo '<div class="alert alert-danger msg-profile">Some error occurred in the server</div>'.$e->getMessage();
	}
}
