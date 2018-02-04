$(document).ready(function()	{
	var tz_offset = (new Date()).getTimezoneOffset();
	tz_offset = (tz_offset == 0) ? 0: -tz_offset;
	$.ajax({
		type:"post",
		url:"../set_timezone.php",
		data:
		{
			"offset":tz_offset
		}
	});
	refreshNotify();
	$("body").click(function()	{
		$("#head-more-menu").fadeOut(100);
		$("#head-pro-section").fadeOut(100);
		$("#notification-section").fadeOut(100);
		$("#srch-result").fadeOut(100);
		$("#srch-result-mobile").fadeOut(100);
	});

	$(document).on("click","#head-user-pic",function()	{
		$("#head-pro-section").fadeIn(100);
	});
	$(document).on("click","#more-link",function()	{
		$("#head-more-menu").fadeIn(100);
	});
	$(document).on("click","#not-btn",function()	{
		$("#notification-section").fadeIn(100);
	});  
	$("#notify-mob").click(function()	{
		$("#show-notify-section-mobile").toggle();
	}); 
});

 function fetchQuestions(txt,slashes)	{
	$("#srch-result").show();
	$.ajax({
		type:"post",
		url:slashes+"search_questions.php",
		data:
		{
			"val":txt,
			"loc":slashes,
		},
		success:function(result)	{
			$("#srch-result").html(result);
		}
	});
} 
function fetchQuestionsMobile(txt,slashes)	{
	$("#srch-result-mobile").show();
	$.ajax({
		type:"post",
		url:slashes+"search_questions.php",
		data:
		{
			"val":txt,
			"loc":slashes,
		},
		success:function(result)	{
			$("#srch-result-mobile").html(result);
		}
	});
}
function updateNotify(notify_id)	{
	var slash=document.getElementById("slash").value;
	$.ajax({
		type:"post",
		url:slash+"notifications.php",
		data:
		{
			"slash":slash,
			"notify_typ":"UPDATE",
			"notify_id":notify_id
		},
		success:function(res)	{
			if(res==1)	{
				if(notify_id != -1)		{
					$("#notify-message-"+notify_id).removeClass("list-view-on");
					var countEle = $("#show-notify-section .list-view-on").length;
					$("#notify-read-count").html(countEle);
				}
				else	{
					/*if ($("#show-notify-section .list-group-item").hasClass('list-view-on'))
						$("#show-notify-section .list-group-item").removeClass("list-view-on");*/
					$("#notify-read-count").html(0);
				}
			}
		}
	});
}	
function loadNotify()	{
	var slash=document.getElementById("slash").value;
	$.ajax({
		type:"post",
		url:slash+"notifications.php",
		data:
		{
			"slash":slash,
			"notify_typ":"DISPLAY"
		},
		success:function(result)	{
			$("#show-notify-section").html(result);
			$("#show-notify-section-mobile").html(result);
			var countEle = $("#show-notify-section .list-view-on").length;
			$("#notify-read-count").html(countEle);
		}
	});
}
function refreshNotify()	{
	loadNotify();
	setInterval(loadNotify,2000);
}

