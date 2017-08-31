 $(document).ready(function()	{
	$("#nav-id").click(function(e)	{
		$("#options-menu").show('slide', {direction: 'left'}, 500);
		$("#block").show();
		e.stopPropagation();
	});
	$("#block").click(function()	{
		$("#options-menu").hide('slide', {direction: 'left'}, 500);
		$("#block").hide();
	});
}); 

	
function updateFollower(userid,flag,id)	{
	$.ajax({
		type:"post",
		url:"updt_follower.php",
		data:
		{
			"user_id":userid,
			"flag":flag
		},
		success:function(result)	{
			if(flag == 0)	{
				$("#unfollow-"+id).removeClass("disabled btn-disabled");
				$("#follow-"+id).addClass("disabled btn-disabled");
				$("#follow-"+id).removeAttr("onclick");
				$("#unfollow-"+id).attr("onclick","updateFollower('"+userid+"',1,'"+id+"')");
			}
			else if(flag == 1)	{
				$("#follow-"+id).removeClass("disabled btn-disabled");
				$("#unfollow-"+id).addClass("disabled btn-disabled");
				$("#follow-"+id).attr("onclick","updateFollower('"+userid+"',0,'"+id+"')");
				$("#unfollow-"+id).removeAttr("onclick");
			}
			$("#user-follow-"+id).html(result);
		}
	});
}