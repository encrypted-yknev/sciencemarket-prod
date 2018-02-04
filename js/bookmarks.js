var recordCount = 10;
$(document).ready(function()	{
	
	$.ajax({
		url:"../fetch_bookmarks.php",
		type:"post",
		dataType:"json",
		data:
		{
			"start_row":1,
			"first_call":1,
			"record_count":recordCount
		},
		beforeSend:function()	{
			$("#bk-main-section").text("loading bookmarks");
		},
		success:function(res)	{
			rec = res.rcrd_count;
			if(rec > 10)	{
				pageCount = Math.ceil(rec/recordCount);
				i=0;
				x="";
				while(i < pageCount)	{
					if(i==0)
						x+="<li class='active'><a id='pg-bk-"+(i+1)+"' href='javascript:void(0)' onclick='fetchBookmarks("+(i+1)+")'>"+(i+1)+"</a></li>";
					else
						x+="<li><a id='pg-bk-"+(i+1)+"' href='javascript:void(0)' onclick='fetchBookmarks("+(i+1)+")'>"+(i+1)+"</a></li>";
					i+=1;
				}
				document.getElementById("nav-section").innerHTML=x;
			}
			resultData = res.res_bk;
			$("#bk-main-section").html(resultData);
			
		}
	});
});

function fetchBookmarks(pg)	{
	var pgNum=parseInt(document.getElementById("pg-bk-"+pg).innerHTML);
	var strtRow = (pgNum-1)*recordCount+1;
	$.ajax({
		url:"../fetch_bookmarks.php",
		type:"post",
		dataType:"json",
		data:
		{
			"start_row":strtRow,
			"first_call":0,
			"record_count":recordCount
		},
		beforeSend:function()	{
			$("#bk-main-section").text("loading bookmarks...");
		},
		success:function(res)	{
			var activePage = document.getElementById("nav-section").getAttribute("data-active");
			document.getElementById("pg-bk-"+activePage).parentNode.classList.remove("active");
			document.getElementById("pg-bk-"+pg).parentNode.classList.add("active");
			document.getElementById("nav-section").setAttribute("data-active",pg);
			resultData = res.res_bk;
			$("#bk-main-section").html(resultData);
			
		}
	});
}

function updtBookmarks(qid)	{
	var node=document.getElementById("bk-row-"+qid);
	
	var setFlag = 1;
	$.ajax({
		type:"post",
		url:"../add_bookmarks.php",
		dataType:"json",
		data:
		{
			"qid":qid,
			"setFlag":setFlag
		},
		beforeSend:function()	{
			node.style.filter="opacity(20%)";
		},
		success:function(res)	{
			var err_cd=res.err_cd;
			var err_desc=res.err_desc;
			if(err_cd == 0)	{
				node.parentElement.removeChild(node);
			}
			else	{
				node.style.filter="opacity(100%)";
			}
		}
	}); 
}
