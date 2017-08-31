 $(document).ready(function()	{
	var tz=new Date();
	var tz_offset=tz.getTimezoneOffset();
	tz_offset=tz_offset == 0 ? 0: -tz_offset;
	$.ajax({
		type:"post",
		url:"get_time.php",
		data:
		{
			offset:tz_offset
		},
		success:function(result)	{
			$("#time-sec").html(result)
		}
	});
	$("#nav-id").click(function(e)	{
		$("#options-menu").show('slide', {direction: 'left'}, 500);
		$("#block").show();
		e.stopPropagation();
	});
	$("#block").click(function()	{
		$("#options-menu").hide('slide', {direction: 'left'}, 500);
		$("#block").hide();
	});
 	$("#notify-mob").click(function()	{
		$("#show-notify-section-mobile").toggle();
	}); 
	/* $("body").click(function()	{
		$("#show-notify-section-mobile").hide();
	}); */
}); 

	
function loadNotify()	{
	$(document).ready(function()	{
		$.ajax({
			type:"post",
			url:"notifications.php",
			data:
			{
				"notify_typ":"DISPLAY"
			},
			success:function(result)	{
				$("#show-notify-section").html(result);
				$("#show-notify-section-mobile").html(result);
			}
		});
	});
}
function refreshNotify()	{
	loadNotify();
	setInterval(loadNotify,2000);
}
function showSubTopics(id)	{
	var id_res="#"+id;
	$(id_res).slideToggle();
}

function increaseCount(id,qid,userid,func)	{
	if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp = new XMLHttpRequest();
	} 
	else {
		// code for IE6, IE5
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			document.getElementById(id).innerHTML = this.responseText;
		}
	};
	xmlhttp.open("GET","update_votes1.php?id="+qid+"&userid="+userid+"&func="+func+"&qaflag=0",true);
	xmlhttp.send();
}