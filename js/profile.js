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
				$("#message-section-3").html(result);
			}
		});
	}
}

function deactivateAcc()	{
	var pwd=document.getElementById("deacc-account-pwd").value;
	$.ajax({
		type:"post",
		url:"update_user.php",
		data:
		{
			"pwd":pwd,
			"request_type":4
		},
		beforeSend:function()	{
			$("#message-section-4").text("Deactivating..");
			$("#button-4").addClass("btn-disabled");
		},
		success:function(result)	{
			$("#deacc-account-pwd").val("");
			$("#button-4").removeClass("btn-disabled");
			$("#message-section-4").html(result);
		}
	});
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
	if(x==0)	
		txt="Please choose unique username for your account";
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
	
	document.getElementById('message-section-1').innerHTML="<div class='alert alert-info msg-profile'>"+txt+"</div>";
}

function updateUser(user,name,mail,mob,place,desc)	{
	$.ajax({
		type:"post",
		url:"update_user.php",
		data:
		{
			"user":user,
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

function validateField(value,num)	{
	if(num==1)	{
		if(value.trim()!="")	{
			$.ajax({
				type:"post",
				url:"check_user.php",
				data:
				{
					"user":value
				},
				success:function(res)	{
					if(res=="1")	{
						$("#profile-edit-1").html("<span class='alert alert-warning msg-profile'>Username already exists. Leave blank if no edit is required</span>");
						$("#user").val("");
					}
					else if(res!="0")	{
						$("#profile-edit-1").html("<span class='alert alert-warning msg-profile'>Internal server error</span>");
						$("#user").val("");
					}
					else	{
						$("#profile-edit-1").html("");
					}
				}
			});
		}
	}
	else if(num==2)	{
		if(value.trim() != "")	{
			if(/^[a-z\s]+$/i.test(value)==false)	{
				$("#profile-edit-2").html("<span class='alert alert-warning msg-profile'>Invalid name format. Leave blank if no edit is required</span>");
				$("#name").val("");
			}
			else	{
				$("#profile-edit-2").html("");
			}
		}
	}
	else if(num==4)	{
		if(value.trim() != "")	{
			if(/^[0-9]+$/.test(value)==false)	{
				$("#profile-edit-4").html("<span class='alert alert-warning msg-profile'>Invalid mobile number. Leave blank if no edit is required</span>");
				$("#mob").val("");
			}
			else	{
				$("#profile-edit-4").html("");
			}
		}
	}
}
