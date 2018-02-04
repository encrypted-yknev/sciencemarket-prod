<?php
session_start();

include "connectDb.php";
$qid = trim($_REQUEST['qid']);
$user_id = trim($_REQUEST['user_id']);

?>

<div class="user-card-section" id="user-card-<?php echo $qid; ?>">
	<span>Welcome <?php echo $user_id; ?> </span>
</div>
