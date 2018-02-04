<?php
session_start();

include "connectDb.php";
include "forum/functions/get_time.php";


function get_time_diff($timestamp_ans)	{
	$timestamp_cur=date("Y-m-d H:i:sa");
	
	$year1=substr($timestamp_ans,0,4);
	$month1=substr($timestamp_ans,5,2);
	$day1=substr($timestamp_ans,8,2);
	$hr1=substr($timestamp_ans,11,2);
	$min1=substr($timestamp_ans,14,2);
	$sec1=substr($timestamp_ans,17,2);


	$year2=substr($timestamp_cur,0,4);
	$month2=substr($timestamp_cur,5,2);
	$day2=substr($timestamp_cur,8,2);
	$hr2=substr($timestamp_cur,11,2);
	$min2=substr($timestamp_cur,14,2);
	$sec2=substr($timestamp_cur,17,2);

	if($year1 == $year2)	{
		if($month1 == $month2)	{
			if($day1 == $day2)	{
				if($hr1 == $hr2)	{
					if($min1 == $min2)	{
						if($sec1 == $sec2)	{
							$value=0;	
							$string="seconds";
						}
						else{
							$diff_sec=(int)$sec2-(int)$sec1;
							$value=$diff_sec;	
							$string="seconds";
						}
					}
					else{
						$diff_min=(int)$min2-(int)$min1;
						$value=$diff_min;
						$string="minutes";
					}
				}
				else{
					$diff_hr=(int)$hr2-(int)$hr1;
					$value=$diff_hr;
					$string="hours";
				}
			}
			else	{
				$diff_day=(int)$day2-(int)$day1;
				$value=$diff_day;
				$string="days";
			}
		}
		else	{
			$diff_mon=(int)$month2-(int)$month1;
			$value=$diff_mon;
			$string="months";
		}
	}
	if($value==1)
		$string=substr($string,0,strlen($string)-1);
	return $value.' '.$string.' ago';
}

if(isset($_SESSION['logged_in']) and $_SESSION['logged_in'])	{
	try	{
		$sql_fetch_qstn_count="select count(1) as cnt_qstn from questions where posted_by = '".$_SESSION['user']."'";
		$sql_fetch_ans_count="select count(1) as cnt_ans from answers where posted_by = '".$_SESSION['user']."'";
		
		$stmt_qstn=$conn->prepare($sql_fetch_qstn_count);
		$stmt_qstn->execute();
		$result_qstn=$stmt_qstn->fetch();
		$count_qstn=$result_qstn['cnt_qstn'];

		$stmt_ans=$conn->prepare($sql_fetch_ans_count);
		$stmt_ans->execute();
		$result_ans=$stmt_ans->fetch();
		$count_ans=$result_ans['cnt_ans'];
	}

	catch(PDOException	$e)	{
		echo '';
	}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Science Market - Forums. Expert Connect. Collaborate and Favours</title>
<meta name="description" content="Science market. User dashboard." >
<meta charset="utf-8"> 
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="styles/header.css">
<link rel="stylesheet" type="text/css" href="styles/dashboard.css">
<link rel="stylesheet" type="text/css" href="styles/qa_forum.css">
<link rel="stylesheet" type="text/css" href="styles/footer.css">
<link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Quicksand" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script type = "text/javascript" src = "https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
<link rel="stylesheet" href="styles/bootstrap.min.css">
<!-- Latest compiled JavaScript -->
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/dashboard.js"></script>
<script type="text/javascript" src="js/qa_forum.js"></script>
<script type="text/javascript" src="js/header.js"></script>
</head>
<body onload="refreshNotify()">
<div id="block"></div>
<?php include "header.php"; 
	if(isset($_SESSION['logged_in']) and $_SESSION['logged_in'])	{
		include "dboard_user.php";
	}
	else	{	
		include "dboard_guest.php";
		} 	
	?>
	</br></br>
	<?php include "footer.php"; ?>
</body>
</html>
