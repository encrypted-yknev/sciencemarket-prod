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
	$("#change-image-section").hide();
	
	$("#proimg img").mouseenter(function()	{
		$("#change-image-section").fadeIn(100);
	});
	$("#proimg img").mouseleave(function()	{
		$("#change-image-section").fadeOut(100);
	});
	var focusOutCnt=document.getElementById("tag-res").childElementCount;
	
	$("#tag input").on("focusout",function()	{
		var x=$.trim(this.value);
		if(x) {
			focusOutCnt++;
			$("#message-section").text(""); 
			if(focusOutCnt==7)	{
				$("#message-section-2").html("<div class='alert alert-danger'>You have added maximum interests!!</div>");
				$(".q-tags").attr("disabled","disabled");
			}
				
			$("#tag-res").append('<span class="tag-name">'+x+'</span>');
			$("#tag-value").append(x+" ");
			$(this).focus();
		}
		else	{
			if(focusOutCnt==0)
				$("#message-section-2").html("<div class='alert alert-danger'>Please select at-least one interests to update</div>");
				
		}
		this.value = ""; 
	//	$(this).focus();
	});
	$("#tag input").on("focusin",function()	{
		$("#message-section-2").html("<div class='alert alert-info'>Please select at-least one and at-most 7 interests to update</div>");
	});
	$(document).on("click",".tag-name",function()	{
		$(this).remove();
		focusOutCnt--;
		if(focusOutCnt < 7)	{
			$("#message-section-2").text("");
			$(".q-tags").removeAttr("disabled");
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
	
	/* $("name").on("focusin",function()	{
		$("#message-section-1").html("<div class='alert alert-info'>Please write your full name separated by spaces. Only alphabets and spaces are allowed.</div>");
	}); */
	
	$('.q-tags').on('keypress', function(e) {
		var keyCode = e.keyCode || e.which;
		if(keyCode == 13)	{
			$(this).blur();
		}
	});
});

$(document).on("click","#proimg",function()	{
	//alert('hi');
	load("upload.php");
});

function getTagsName()	{
	var x=document.getElementById("tag-res").childElementCount;
	var counter,tag="";
	for(counter=0; counter<x; counter++)	{
		tag+=document.getElementsByClassName("tag-name")[counter].innerText+" ";
	}
	return tag;
}

function addInterests(x)	{
	$.ajax(
	{
		type:"post",
		url:"update_user.php",
		data:
		{
			"user_interests":x,
			"request_type":2
		},
		success:function(result)	{
			$("#message-section-2").html(result);
		}
	}
	);
}

function resetPwd(pwd,newPwd,confPwd)	{
	//alert(pwd+' '+newPwd+' '+confPwd);
	
	if(newPwd != confPwd)
		$("#message-section-3").html("<div class='alert alert-warning'>Passwords do not match</div>")
	else	{
		$.ajax({
			type:"post",
			url:"update_user.php",
			data:
			{
				"old_pwd":pwd,
				"new_pwd":newPwd,
				"conf_pwd":confPwd,
				"request_type":3
			},
			success:function(result)	{
				$("#message-section-3").html(result)
			}
		});
	}
}

/* function showEditSection(x)	{
	if(x==1)
		$("#row-1").slideToggle();
	else if(x==2)
		$("#row-2").slideToggle();
	else if(x==3)
		$("#row-3").slideToggle();
	else if(x==4)
		$("#row-4").slideToggle();
	
} */

function showTip(x)	{
	var txt=" 	";
	if(x==1)	
		txt="Please write your full name separated by spaces. Only alphabets and spaces are allowed.";
	/*
	else if(x==2)
		txt="Choose a unique user id. Avoid using special characters. '_' can be used. ";
	else if(x==3)
		txt="Password must have a minimum of 8 characters. Try choosing a password with a combination of letters(uppercase/lowercase), numbers and symbols.";
	*/
	else if(x==2)
		txt="Enter your personal E-mail. We will use this e-mail for sending notication alerts and other communications";
	else if(x==3)
		txt="Enter a valid mobile number";
	else if(x==4)
		txt="Enter your country of residence";
	else if(x==5)
		txt="Enter a brief description about yourself. (E.g., Your current profession and designation).";
	
	document.getElementById('message-section-1').innerHTML="<div class='alert alert-info'>"+txt+"</div>";
}

function updateUser(name,mail,mob,place,desc)	{
	$.ajax({
		type:"post",
		url:"update_user.php",
		data:
		{
			"name":name,
			"mail":mail,
			"mob":mob,
			"place":place,
			"desc":desc,
			"request_type":1
		},
		beforeSend:function()	{
			$("#message-section-1").text("Updating..");
		},
		success:function(result)	{
			$("#message-section-1").html(result);
		}
	})
}