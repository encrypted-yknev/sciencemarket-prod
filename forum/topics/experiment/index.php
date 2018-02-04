<?php
session_start();
if(isset($_SESSION['logged_in']) and $_SESSION['logged_in'])
	$logged_in=1;
else
	$logged_in=0;

include "../../../connectDb.php";
include "../../functions/get_time.php";
include "../../functions/get_time_offset.php";

$topic_nm = 'All topics';
$sort_order = 'Default';
$parent_topic_id = 9;
$page_title = 'Experimental data';
$page_desc='List of questions with answers on experiment or experimental data. Microscopy, micro engineering and micro fluidics, nanoengineering, micro biology, molecular cell biology, materials. Discuss on these scientific topics';

include "../../qstn_topics.php";
?>
