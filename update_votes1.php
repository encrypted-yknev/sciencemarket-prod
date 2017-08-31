<?php 
session_start();
if(!$_SESSION["logged_in"])	{
	echo "Please login </br>";
	header("location:login.php");
}
include "connectDb.php";

$id=$_REQUEST["id"];
$userid=$_REQUEST["userid"];
$func=$_REQUEST["func"];
$req_type=$_REQUEST["requestType"];
$qa_flag=$_REQUEST["qaflag"];

$message="";

if($qa_flag==0)	{
	$tab_name="questions";
	$tab_col="qstn_id";
	$post_type="Q";
}
else if($qa_flag==1)	{
	$tab_name="answers";
	$tab_col="ans_id";
	$post_type="A";
}

if($func==0)	{
	$col_name="up_votes";
	$vote_type=0;
}
else if($func==1)	{	
	$col_name="down_votes";
	$vote_type=1;
}

try	{
	
	if($req_type == 'A')	{
		$sql_updt_user_posts = "insert into user_posts_votes(user_id,post_id,post_type,vote_type)
						values('".$_SESSION['user']."',".$id.",'".$post_type."',".$vote_type.")";
	}
	else if($req_type == 'D')	{
		$sql_updt_user_posts = "delete from user_posts_votes where user_id = '".$_SESSION['user']."' 
								and post_id = ".$id." and post_type = '".$post_type."' and vote_type = ".$vote_type;
	}
	$stmt_updt_user_posts=$conn->prepare($sql_updt_user_posts);
	$stmt_updt_user_posts->execute();
	
	if($stmt_updt_user_posts->rowCount() > 0)	{
		try	{
			$sql_fetch_votes="select ".$col_name." from ".$tab_name." where ".$tab_col."=".$id;
			foreach($conn->query($sql_fetch_votes) as $row_votes)
				$tot_votes=$row_votes["".$col_name.""];
			
			if($req_type == 'A')
				$votes=(int)$tot_votes+1;	
			else if($req_type == 'D')
				$votes=(int)$tot_votes-1;
		}
		catch(PDOException $e)	{
			/*echo "Some error occurred";*/
		}
		try	{
			$sql_qa_updt="update ".$tab_name." set ".$col_name."=".$votes." where ".$tab_col."=".$id;
			$stmt1=$conn->prepare($sql_qa_updt);
			$stmt1->execute();

			if($stmt1->rowCount()>0)	{
				try	{
					$sql_fetch_user_votes="select ".$col_name." from users where user_id='".$userid."'";
					foreach($conn->query($sql_fetch_user_votes) as $row_user_votes)
						$tot_user_votes=$row_user_votes["".$col_name.""];
					if($req_type == 'A')
						$user_votes=(int)$tot_user_votes+1;
					else if($req_type == 'D')
						$user_votes=(int)$tot_user_votes-1;
					
				}
				catch(PDOException $e)	{
					
				}
				try	{
					$sql_user_updt="update users set ".$col_name."=".$user_votes." where user_id='".$userid."'";
					$stmt2=$conn->prepare($sql_user_updt);
					$stmt2->execute();
					
					if($stmt1->rowCount()>0)	{
						$_SESSION[''.$col_name.'']=$user_votes;
						echo "Internal server error</br>";
					}
				}
				catch(PDOException $e)	{
					
				}
			}
			else
				echo "Internal server error</br>";

		}

		catch(PDOException $e)	{
			
		}
	}
}
catch(PDOException $e)	{
	
}

?>


 
 