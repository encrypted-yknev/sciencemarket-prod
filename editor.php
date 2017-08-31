<?php

include "connectDb.php";
$sql_fetch_topic = "select qstn_desc from questions where qstn_id = 91";
$stmt_fetch_topic=$conn->prepare($sql_fetch_topic);
$stmt_fetch_topic->execute();
$res_topic=$stmt_fetch_topic->fetch();

echo $res_topic['qstn_desc'];

?>
