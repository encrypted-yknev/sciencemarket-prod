$(document).ready(function()	{
	var tz_offset = (new Date()).getTimezoneOffset();
	tz_offset = (tz_offset == 0) ? 0: -tz_offset;
	$.ajax({
		type:"post",
		url:"set_timezone.php",
		data:
		{
			"offset":tz_offset
		}
	});
});

