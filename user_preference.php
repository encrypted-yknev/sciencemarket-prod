<?php
session_start();
if(!isset($_SESSION["logged_in"]))	{
	header("location:index.php");
}
include "connectDb.php";
$message="";
if ($_SERVER["REQUEST_METHOD"] == "POST")	{
	$interest_list=trim($_POST['int-val']);
	if(empty($interest_list))	{
		$message="<div class='alert alert-danger login-message'>Please choose at-least 4 tags</div>";
	}
	else	{
		$count_comma = substr_count($interest_list,",");
		if($count_comma < 3)	{
			$message="<div class='alert alert-danger login-message'>Please choose at-least 4 tags</div>";
		}
		else	{
			try	{
				$sql_call_sp_interests = "call add_user_interests('".$interest_list."','".$_SESSION['user']."',@out_err_code,@out_err_desc)";
				$stmt_call_sp_interests=$conn->prepare($sql_call_sp_interests);
				$stmt_call_sp_interests->execute();
				$stmt_call_sp_interests->closeCursor();
				$result_query = $conn->query("select @out_err_code as error_code,@out_err_desc as error_desc")->fetch();
				if($result_query)	{
					$error_code=$result_query['error_code'];
					$error_desc=$result_query['error_desc'];
				}
				
				if(!strcmp($error_code,'00000'))	{
					$message= "<div class='alert alert-success login-message'>Cool!! You are good to go now. <a href='dashboard.php'>Click here</a> to go to dashboard</div>";
					$_SESSION["interest"]=$interest_list;
					$_SESSION["interest_list"]=explode($interest_list,",");
					header("location:dashboard.php");
				}
				else	{
					$message= "<div class='alert alert-danger login-message'>Some error occurred. Please try again</div>";
				}
			}
			catch(PDOException $e)	{
				$message= "<div class='alert alert-danger login-message'>Internal Server error</div> ";
			}
		}
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Science Market - Home | Where people discuss and collaborate with experts</title>
<meta name="description" content="Science Market is an online market place to connect with peers, people, groups or expert. Discuss topics in question answer forum, connect with experts under expert connect, collaborate with people and provide favours." >
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="styles/header.css">
<link rel="stylesheet" type="text/css" href="/styles/login.css" >
<link rel="stylesheet" type="text/css" href="/styles/user_interest.css" >
<link rel="stylesheet" type="text/css" href="styles/footer.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="js/user_interest.js"></script>
<link rel="stylesheet" href="styles/bootstrap.min.css">
<script src="js/bootstrap.min.js"></script>
<link href="https://fonts.googleapis.com/css?family=Quicksand" rel="stylesheet">

</head>
<body style="background:url(../img/bg.jpg);">
<div id="bg-window"></div>
<div id="main-container">

	<?php 
	$title="Choose at least 4 topics of interest";
	include "user_interest_snippet.php"; 
	?>
	<div id="control-sec">
		<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
			<input id="res-int-list" name="int-val" type="hidden" value="" />
			<input type="submit" class="btn btn-success" onclick="processInterests()" value="Add Interests" />
		</form>
	</div></br>
</div>
</body>
</html>
