/*function showTip(x)	{
	var txt="";
	if(x==1)	
		txt="Please write your full name separated by spaces. Only alphabets and spaces are allowed.";
	else if(x==2)
		txt="Choose a unique user id. Avoid using special characters. '_' can be used. ";
	else if(x==3)
		txt="Password must have a minimum of 8 characters. Try choosing a password with a combination of letters(uppercase/lowercase), numbers and symbols.";
	else if(x==4)
		txt="Re-type it again."
	else if(x==5)
		txt="Enter a valid e-mail address.";
	else if(x==6)
		txt="Enter your personal E-mail. We will use this e-mail for sending notication alerts and other communications";
	else if(x==7)
		txt="Enter a brief description about yourself. (E.g., Your current profession and designation).";
	else
		txt="No info to show";
	document.getElementById('tip-section').innerHTML=txt;
}*/
function validateData(data,id)	{
	var x="",checked=true;
	if(data.length==0)	{
		x="Name cannot be empty";
		checked=false;
	}
	else {
		if(/^[a-z\s]+$/i.test(data)==false)	{
			x="Only Alphabets allowed";
			checked=false;
		}
		else
			x="<span class='glyphicon glyphicon-ok'></span>";
	}
	
	if(checked==false)	{
		document.getElementById("name").value="";
		document.getElementById(id).style.background="#FFEBEB";
		document.getElementById(id).style.color="#000";
	}
	else	{
		document.getElementById(id).style.background="#fff";
		document.getElementById(id).style.color="#46A143";
		//document.getElementById(id).style.border-radius="5px";
	}
	document.getElementById(id).innerHTML=x;
}

function validatePassFld(data,id)	{
	var x="",checked=true; 
	if(data.length==0)	{
		x="Password cannot be empty";
		checked=false;
	}
	else if(data.length < 8) {
		x="Minimum 8 characters"
		checked=false;
	}
	else
		x="<span class='glyphicon glyphicon-ok'></span>";
	
	if(checked==false)	{
		document.getElementById("pwd").value="";
		document.getElementById(id).style.background="#FFEBEB";
		document.getElementById(id).style.color="#000";
	}
	else	{
		document.getElementById(id).style.background="#fff";
		document.getElementById(id).style.color="#46A143";
		//document.getElementById(id).style.border-radius="5px";
	}
	
	document.getElementById(id).innerHTML=x;
}

function validateRePassFld(data1,data2,id)	{
	var x="",checked=true;
	if(data2.length==0)	{
		x="Password cannot be empty";
		checked=false;
	}
	else {
		if(data1 != data2)	{
			x="Incorrect Password";
			checked=false;
		}
		else
			x="<span class='glyphicon glyphicon-ok'></span>";
	}
	if(checked==false)	{
		document.getElementById("repwd").value="";
		document.getElementById(id).style.background="#FFEBEB";
		document.getElementById(id).style.color="#000";
	}
	else	{
		document.getElementById(id).style.background="#fff";
		document.getElementById(id).style.color="#46A143";
		//document.getElementById(id).style.border-radius="5px";
	}
	
	document.getElementById(id).innerHTML=x;
}

function validateEmail(data,id)	{
	
}

function validateMob(data,id)	{
	var x="",checked=true;
	if(data.length != 10)	{
		x="Invalid mobile number";
		checked=false;
	}
	else	{
		if(/^[0-9]+$/.test(data)==false)	{
			x="Enter correct mobile";
			checked=false;
		}
		else
			x="<span class='glyphicon glyphicon-ok'></span>";
	}
	if(checked==false)	{
		document.getElementById(id).style.background="#FFEBEB";
		document.getElementById(id).style.color="#000";
	}
	else	{
		document.getElementById(id).style.background="#fff";
		document.getElementById(id).style.color="#46A143";
		//document.getElementById(id).style.border-radius="5px";
	}
	
	document.getElementById(id).innerHTML=x;
}

function validateUser(data)	{
	$.ajax(
		{
			type:"post",
			url:"check_user.php",
			data:
			{
				"user":data
			},
			success:function(result)	{
				if(result=="0")	{
					$("#user-error").html("<span class='glyphicon glyphicon-ok'></span>");
					$("#user-error").css({"background":"#fff","color":"#46A143"});
				}
				else if(result=="1")	{
					$("#user").val("");
					$("#user-error").html("Username already exists");
					$("#user-error").css({"background":"#FFEBEB","color":"#000"});
				}
				else	{
					$("#user-error").html(result);
					$("#user-error").css({"background":"#FFEBEB","color":"#000"});
				}
			}
		}
	);
}

function postData(name,user,pwd,mail,phone,age,countries,desc)	{
	
	$.ajax(
	{
		type:"post",
		url:"register_user.php",
		data:
		{
			"name":name,
			"user":user,
			"pwd":pwd,
			"mail":mail,
			"mob":phone,
			"age":age,
			"location":countries,
			"desc":desc
			
		},
		success:function(result)	{
			$('#tip-section').html(result);
			window.location.href = 'dashboard.php';
		}
	}
	);
}
