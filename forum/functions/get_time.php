<?php
function get_user_date($time)	{
	$date = substr($time,8,2);
	$month = substr($time,5,2);
	$year = substr($time,0,4);
	$mth_str="";
	switch($month)	{
		case "01": $mth_str="Jan";
			break;
		case "02": $mth_str="Feb";
			break;
		case "03": $mth_str="Mar";
			break;
		case "04": $mth_str="Apr";
			break;
		case "05": $mth_str="May";
			break;
		case "06": $mth_str="Jun";
			break;
		case "07": $mth_str="Jul";
			break;
		case "08": $mth_str="Aug";
			break;
		case "09": $mth_str="Sep";
			break;
		case "10": $mth_str="Oct";
			break;
		case "11": $mth_str="Nov";
			break;
		case "12": $mth_str="Dec";
			break;
		default : $mth_str = "";
		break;
	}
	/* if(substr($date,1,1) == '1' and $date != "11")
		$post_date_str = "ST";
	else if(substr($date,1,1) == '2' and $date != "12")
		$post_date_str = "ND";
	else if(substr($date,1,1) == '3' and $date != "13")
		$post_date_str = "RD";
	else 
		$post_date_str = "TH"; */
	
	if($date == date('d') and substr($time,11,5) == date("H:i"))
		return 'few seconds ago';
	if($date == date('d'))
		return 'Today '.substr($time,11,5);
	
	return $mth_str.' '.$date.', '.$year;
	
}
?>