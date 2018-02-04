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

function loadExperts()	{
	var topicId=document.getElementById("topic-list").value;
	if(topicId > 0)	{
		$.ajax({
			type:"get",
			url:"topic_expert.php",
			data:
			{
				"topic_id":topicId
			},
			beforeSend:function()	{
				document.getElementById("block-bg").style.display="block";
			},
			success:function(res)	{
				document.getElementById("block-bg").style.display="none";
				document.getElementById("main-expert-section").innerHTML=res;
			}
		});
	}
}
