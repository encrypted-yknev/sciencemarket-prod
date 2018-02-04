<div id="msg-box-<?php echo $msg_div_id; ?>" class="message-section">
	<div id="<?php echo $msg_div_id; ?>"></div>
	<span id="main-sec-<?php echo $msg_div_id; ?>">
		<textarea id="msg-text-<?php echo $msg_div_id; ?>" name="msg-text" class="form-control msg-textarea" placeholder="Write your message here" maxlength="250"></textarea>
		<span class="sub-text-section">Maximum 250 characters</span></br></br>
	</span>
	<div id="button-sec-<?php echo $msg_div_id; ?>" class="button-section" >
		<button id="btn1-<?php echo $msg_div_id; ?>" style="margin-right:10px;" class="btn btn-primary" onclick="sendMessage(<?php echo "'".$slashes."','".$msg_div_id."','".$_SESSION['user']."','".$user_card."'"; ?>)">Send</button>
		<button id="btn2-<?php echo $msg_div_id; ?>" class="btn btn-danger" onclick="showMessageBox(1,'<?php echo $msg_div_id; ?>')">Close</button>
	</div>
	<span id="load-<?php echo $msg_div_id; ?>" class="sending-load" style="display:none;">Sending message...</span>
	
</form>
</div>
