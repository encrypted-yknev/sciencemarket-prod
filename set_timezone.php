<?php
if(isset($_REQUEST['offset']))	{
	$tz_offset = trim($_REQUEST['offset']);
	$tz_str = timezone_name_from_abbr("",$tz_offset*60,false);
	$cookie_name = "user_tz";
	$cookie_val= $tz_str;
	setrawcookie($cookie_name,$cookie_val);
}
?>
