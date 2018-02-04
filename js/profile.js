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
	$("#block-bg").click(function(e)	{
		$("#edit-profile-section").hide();
		$("#edit-interest-section").hide();
		$("#upd-img-section").hide();
		$("#block-bg").hide();
		location.reload(true);
	});
	$("#endupld").on('click',function(e)	{
		$("#upd-img-section").hide();
		$("#block-bg").hide();
		var x=$("#endupld").attr("data-refresh");
		if(x=="1")
			location.reload(true);
	});
	
	$("#proimg img").mouseenter(function()	{
		$("#change-image-section").fadeIn(100);
	});
	$("#proimg img").mouseleave(function()	{
		$("#change-image-section").fadeOut(100);
	});
	var intLen=document.getElementsByClassName("tag-name").length;
	
	$("#tag input").on("focusout",function()	{
		var x=$.trim(this.value);
		if(x) {
			$("#message-section").text(""); 
			$("#tag-res").append('<span class="tag-name type1" data-source="input">'+x+'</span>');
			$("#tag-value").append(x+" ");
			$(this).focus();
		}
		else	{
			if(intLen==0)
				$("#message-section-2").html("<strong><span style='color:#B70000;'>Please select at-least one interests to update</span>");
				
		}
		this.value = ""; 
	});
	$("#tag input").on("focusin",function()	{
		$("#message-section-2").html("<strong><span style='color:#009BC5;'>Please select interests to update</span>");
	});
	$(document).on("click",".tag-name",function()	{
		if(this.getAttribute("data-source")=="system")	{
			var idVal=this.getAttribute("data-refid");
			$("#"+idVal).removeClass("user-choice-sel");
			$("#"+idVal).attr("data-set","0");
		}
		$(this).remove();
			
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
	
	$("#imgupld-form").on("submit",(function(e)	{
		e.preventDefault();
		var percent;
		$(".progress").show();
		$("#success-msg").css({"color":"#3c763d","background":"#dff0d8"});
		$.ajax({
			xhr: function () {
				var xhr = new window.XMLHttpRequest();
				xhr.upload.addEventListener("progress", function (evt) {
					if (evt.lengthComputable) {
						var percentComplete = evt.loaded / evt.total;						
						$('.progress-bar').css({
							width: percentComplete * 100 + '%'
						});
						$("#success-msg").html("Uploading image...");
						percent=Math.floor(percentComplete*100);
						$('.progress-bar').html(percent + '%');
						/* if (percentComplete === 1) {
							$('.progress-bar').addClass('hide');
						} */
					}
				}, false);
				xhr.addEventListener("progress", function (evt) {
					if (evt.lengthComputable) {
						var percentComplete = evt.loaded / evt.total;
						percent=Math.floor(percentComplete*100);
						$('.progress-bar').css({
							width: percentComplete * 100 + '%'
						});
						$('.progress-bar').html(percent + '%');
					}
				}, false);
				return xhr;
			},
			type:"post",
			url:"upload_image.php",
			dataType:"json",
			data: new FormData(this), 
			contentType: false,       
			cache: false,             
			processData:false,        
			success: function(res)	{
				$(".progress").hide();
				$('.progress-bar').css({
					width: '0%'
				});
				$('.progress-bar').html(percent + '%');
				$("#success-msg").html(res.textMsg);
				if(res.succ_cd==1)	{
					$("#endupld").attr("data-refresh",1);
					$("#success-msg").css({"color":"#3c763d","background":"#dff0d8"});
				}
				else	{
					$("#endupld").attr("data-refresh",0);
					$("#success-msg").css({"color":"#C70039","background":"#FFD0DD"});
				}
			}
		});
	}));
});

function getTagsName()	{
	var x=document.getElementById("tag-res").childElementCount;
	var counter,tag="";
	tag+=document.getElementsByClassName("tag-name")[0].innerText;
	for(counter=1; counter<x; counter++)	{
		tag+=", "+document.getElementsByClassName("tag-name")[counter].innerText;
	}
	return tag;
}

function addInterests(x)	{
	$.ajax({
		type:"post",
		url:"add_user_interest.php",
		dataType:"json",
		data: 
		{
			"int-val":x
		},
		beforeSend:function()	{
			document.body.scrollTop=0;
			document.getElementById("message-section-2").innerHTML="Updating interests....";
		},
		success:function(res)	{
			if(res.suc_chk==0)
				eleColor="#B70000";
			else
				eleColor="#00BB30";
			
			int_arr=res.int_list;
			divSec=document.getElementById("user-interest-sec");
			divSec.innerHTML="";
			for(i=0; i<int_arr.length; i++)	{
				var intNode=document.createElement("span");
				var txtNode=document.createTextNode(""+int_arr[i]+"");
				intNode.appendChild(txtNode);
				intNode.setAttribute("class","badge disp-tags");
				divSec.appendChild(intNode);
			}
			document.getElementById("message-section-2").innerHTML="<strong><span style='color:"+eleColor+";'>"+res.msg+"</span></strong>";
		}
	});
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

function userUpdate(reqType)	{
	if(reqType == 1)	{
		$.ajax({
			type:"post",
			url:"user_update.php",
			data:
			{
				"request_type":1,
				"user":document.getElementById("user").value,
				"name":document.getElementById("name").value,
				"mail":document.getElementById("mail").value,
				"mob":document.getElementById("mob").value,
				"place":document.getElementById("location").value,
				"desc":document.getElementById("desc").value,
				"shrt_bio":document.getElementById("shrt_bio").value,
				"dob":document.getElementById("dob").value
			},
			beforeSend:function()	{
				$("#message-section-1").text("Updating..");
			},
			success:function(result)	{
				$("#message-section-1").html(result);
			}
		});
	}
	else if(reqType == 2)	{
		
	}
	else if(reqType == 3)	{
		var oldPwd=document.getElementById("pwd").value;
		var newPwd=document.getElementById("new-pwd").value;
		var confPwd=document.getElementById("conf-pwd").value;
		if(oldPwd=="" || newPwd=="" || confPwd=="")
			$("#message-section-3").html("<div class='alert alert-danger msg-profile'>All fields are mandatory</div>");
		else if(newPwd != confPwd)
			$("#message-section-3").html("<div class='alert alert-warning msg-profile'>Passwords do not match</div>");
		else if(newPwd.length < 8)
			$("#message-section-3").html("<div class='alert alert-danger msg-profile'>Password should have minimum 8 characters</div>");
		else	{
			$.ajax({
				type:"post",
				url:"user_update.php",
				data:
				{
					"request_type":3,
					"old_pwd":oldPwd,
					"new_pwd":newPwd,
					"conf_pwd":confPwd
				},
				beforeSend:function()	{
					$("#message-section-3").text("Updating..");
				},
				success:function(result)	{
					$("#message-section-3").html(result);
					document.getElementById("pwd").value="";
					document.getElementById("new-pwd").value="";
					document.getElementById("conf-pwd").value="";
				}
			});
		}
	}
	else if(reqType == 4)	{
		var pwd=document.getElementById("deacc-account-pwd").value;
		if(pwd=="")
			$("#message-section-4").html("<div class='alert alert-danger msg-profile'>All fields are mandatory</div>");
		else	{
			$.ajax({
				type:"post",
				url:"user_update.php",
				data:
				{
					"request_type":4,
					"pwd":pwd
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
	}
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
function updateFollower(userid,flag,loggedIn)	{
	if(loggedIn == 1)	{
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
					$("#unfollow").removeClass("disabled btn-disabled");
					$("#follow").addClass("disabled btn-disabled");
					$("#follow").removeAttr("onclick");
					$("#unfollow").attr("onclick","updateFollower('"+userid+"',1,1)");
				}
				else if(flag == 1)	{
					$("#follow").removeClass("disabled btn-disabled");
					$("#unfollow").addClass("disabled btn-disabled");
					$("#follow").attr("onclick","updateFollower('"+userid+"',0,1)");
					$("#unfollow").removeAttr("onclick");
				}
				$("#follow-message").html(result);
			}
		});
	}
	else	{
		$("#follow-message-"+id).html("Please login to follow/unfollow");
	}
}

function showMessageBox(num,divId)	{
	if(num==0)	{
		$("#block-bg").show();
		$("#msg-box-"+divId).show();
	}
	else if(num==1)	{
		$("#msg-box-"+divId).hide();
		$("#block-bg").hide();
	}
}

function sendMessage(root,divId,sender,recp)	{
	msgTxt=document.getElementById("msg-text-"+divId).value;
	if(msgTxt.trim()!="")	{
		$.ajax({
			type:"post",
			url:root+"send_message.php",
			data:
			{
				"sender_id":sender,
				"recp_id":recp,
				"msg-text":msgTxt
			},
			beforeSend:function()	{
				$("#msg-text-"+divId).attr("disabled","disabled");
				$("#load-"+divId).show();
			},
			success:function(res)	{
				$("#load-"+divId).hide();
				$("#main-sec-"+divId).hide();
				$("#btn1-"+divId).hide();
				document.getElementById("msg-text-"+divId).value="";
				$("#msg-text-"+divId).removeAttr("disabled");
				document.getElementById(divId).innerHTML=res;
			}
		});
	}
}

function showEditWindow(x)	{
	$("#block-bg").show();
	if(x==1)
		$("#edit-profile-section").show();
	else if(x==2)
		$("#edit-interest-section").show();
	else if(x==3)
		$("#upd-img-section").show();
}

function uploadImage()	{
	
}
function hideEditWindow(x)	{
	if(x==1)
		$("#edit-profile-section").hide();
	else if(x==2)
		$("#edit-interest-section").hide();
	$("#block-bg").hide();
	location.reload(true);
}

function chooseInterest(sec,tagId)	{
	
	if(sec==1)	{
		idVal="qstn-int-"+tagId;
		eleVal=document.getElementById(idVal).innerHTML;
	}
	else if(sec==2)	{
		idVal="user-int-"+tagId;
		eleVal=document.getElementById(idVal).innerHTML;
	}
	else 	{
		idVal="topic-"+tagId;
		eleVal=document.getElementById("topic-"+tagId).innerHTML;
	}
	var checkElement = parseInt($("#"+idVal).attr("data-set"));
	var mainElement = document.getElementById("tag-res");
	if(checkElement==0)	{
		$("#"+idVal).addClass("user-choice-sel");
		$("#"+idVal).attr("data-set","1");
		var node=document.createElement("span");
		var txtNode=document.createTextNode(eleVal);
		node.appendChild(txtNode);
		node.setAttribute("class","tag-name");
		node.setAttribute("data-source","system");
		node.setAttribute("data-refid",idVal);
		node.setAttribute("id","list-"+idVal);
		mainElement.appendChild(node);
	}
	else if(checkElement==1)	{
		$("#"+idVal).removeClass("user-choice-sel");
		$("#"+idVal).attr("data-set","0");
		tagNode=document.getElementById("tag-res");
		childNode=document.getElementById("list-"+idVal);
		tagNode.removeChild(childNode);
	}
}

function processInterests()	{
	var lenFeature = $("#feature-tags .user-choice-sel").length;
	var lenUser = $("#user-tags .user-choice-sel").length;
	var lenTopic1 = $("#topic-keys-1 .user-choice-sel").length;
	var lenTopic2 = $("#topic-keys-2 .user-choice-sel").length;
	var lenTopic3 = $("#topic-keys-3 .user-choice-sel").length;
	var lenTopic4 = $("#topic-keys-4 .user-choice-sel").length;
	var totLen = lenFeature+lenUser+lenTopic1+lenTopic2+lenTopic3+lenTopic4;
	var intVal="",finalList="";
	for(var i=0; i<totLen; i++)	{
		intVal=document.getElementsByClassName("user-choice-sel")[i].innerHTML;
		if(i==0)	{
			finalList+=intVal;
		}
		else	{
			finalList+=", "+intVal;
		}
	}
	$.ajax({
		type:"post",
		url:"add_user_interest.php",
		data:
		{
			"int-val":finalList
		},
		beforeSend:function()	{
			document.body.scrollTop=0;
			document.getElementById("msg-log").innerHTML="Updating interests....";
		},
		success:function(res)	{
			document.getElementById("msg-log").innerHTML=res;
		}
	});
}








