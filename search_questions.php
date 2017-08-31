<?php
session_start();
include "connectDb.php";


if(isset($_REQUEST['val']))
	$srch_txt = $_REQUEST['val'];
if(isset($_REQUEST['loc']))
	$slashes = $_REQUEST['loc'];

try	{
	$sql_fetch_qstns = "select qstn_id,qstn_titl,qstn_desc from questions where
						match(qstn_titl,qstn_desc) against('".$srch_txt."' in natural language mode)";
	$stmt_fetch_qstns=$conn->prepare($sql_fetch_qstns);
	$stmt_fetch_qstns->execute();
	
	if($stmt_fetch_qstns->rowCount() <= 0)
		echo "No results found!";
	else	{
		while($result = $stmt_fetch_qstns->fetch())	{
			echo "<div class='qstn-list'>";
			echo "<strong><a href='".$slashes."qstn.php?qid=".$result['qstn_id']."'>".$result['qstn_titl']."</a></strong></br>";
			echo "<span class='srch-qstn-desc'>".$result['qstn_desc']."</span>";
			echo "</div>";
		}
	}
}
catch(PDOException $e)	{
	echo $e->getMessage();
}
?>