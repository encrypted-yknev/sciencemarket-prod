function showMessage(count,userid)	{
	$.ajax({
		type:"post",
		url:"load_message.php",
		data:
		{
			"userid":userid
		},
		beforeSend:function()	{
			
		},
		success:function(res)	{
			$(".user-row-section").css({"border-left":"0px","background-color":"#fff"});
			$("#user-nav-"+count).css({"border-left":"2px solid #204d74","background-color":"#eee"});
			$("#msg-box textarea").removeAttr("disabled");
			$("#main-section").css("background","#fff");
			$("#main-section").html(res);
			var scrollHeight = document.getElementById("main-section").scrollHeight;
			var clientHeight = document.getElementById("main-section").clientHeight;
			document.getElementById("main-section").scrollTop=(scrollHeight-clientHeight);			
			$("#user-val").val(userid);
		}
	});
}

function postMessageKeyPress(e,sender)	{
	var recp=document.getElementById("user-val").value;
	var keyCode = e.keyCode || e.which;
	if(keyCode == 13)	{
		postMessage(sender,recp);
	}
}
function postMessage(sender,recp)	{
	msgTxt=document.getElementById("msg-text").value;
	if(msgTxt.trim()!="")	{
		$.ajax({
			type:"post",
			url:"send_message.php",
			data:
			{
				"sender_id":sender,
				"recp_id":recp,
				"msg-text":msgTxt
			},
			beforeSend:function()	{
				$("#msg-text").attr("disabled","disabled");
				//$("#load-"+divId).show();
			},
			success:function()	{
				$.ajax({
					type:"post",
					url:"load_message.php",
					data:
					{
						"userid":recp
					},
					beforeSend:function()	{
						
					},
					success:function(res)	{						
						$("#main-section").html(res);
						var scrollHeight = document.getElementById("main-section").scrollHeight;
						var clientHeight = document.getElementById("main-section").clientHeight;
						document.getElementById("main-section").scrollTop=(scrollHeight-clientHeight);	
						$("#msg-text").removeAttr("disabled");
						$("#msg-text").val("");
					}
				});
			}
		});
	}
}

function searchUsers(userid)	{
	if(userid.trim()!="")	{
		$.ajax({
			type:"post",
			url:"search_users.php",
			data:
			{
				"userText":userid
			},
			beforeSend:function()	{
			},
			success:function(res)	{
				document.getElementById("user-list").innerHTML=res;
			}
		});
	}
}

