/*! 
 *   Core v0.1
 *   Author: vqt907
!*/
/*
    Custom script for AdminLTE
----------------------------
*/
function isDataTable ( nTable )
{
    var settings = $.fn.dataTableSettings;
    for ( var i=0, iLen=settings.length ; i<iLen ; i++ )
    {
        if ( settings[i].nTable == nTable )
        {
            return true;
        }
    }
    return false;
}

if($(".dataTable").length > 0){
	$(".dataTable").dataTable({
		"aaSorting": [],
		"iDisplayLength": 50
	});
}

if ($(".hasDatePicker").length > 0) {
	$(".hasDatePicker").datepicker({
		format : 'yyyy-mm-dd',
		autoclose : true,
		startDate : '1950-01-01'
	});
}
if ($(".hasTimePicker").length > 0) {
	$(".hasTimePicker").timepicker({
		timeFormat : 'H:i'
	});
}
if ($(".hasDateTimePicker").length > 0) {
	$(".hasDateTimePicker").datetimepicker({
		format:'Y-m-d H:i:s'
	});
}

$(".hasCKEditor").each(function(){
	CKEDITOR.replace(this.id);
});

if ($(".tag-it").length > 0) {
	$(".tag-it").tagit({
	    fieldName: "tags[]"
	});
}

$("body").on('keypress',".integer-input",function(evt) {
	var charCode = (evt.which) ? evt.which : event.keyCode;
	if (charCode > 31 && (charCode < 48 || charCode > 57))
		return false;
	return true;
});

$(".float-input").keypress(function(evt) {
	var charCode = (evt.which) ? evt.which : event.keyCode;
	if (charCode > 31 && (charCode < 48 || charCode > 57) && (charCode != 46))
		return false;
	return true;
});

$(".integer-negative-input").keypress(function(evt) {
	var charCode = (evt.which) ? evt.which : event.keyCode;
	if (charCode > 31 && (charCode < 48 || charCode > 57))
		return false;
	return true;
});

$(".float-negative-input").keypress(function(evt) {
	var charCode = (evt.which) ? evt.which : event.keyCode;
	if (charCode > 31 && (charCode < 48 || charCode > 57) && (charCode != 110) && (charCode != 109))
		return false;
	return true;
});

$("#input_logo").change(function(){
	$(".logo-container span").hide();
	$(".progress").show();
	$("#uploadlogoForm").ajaxSubmit({
		dataType: 'json',
		/* progress bar call back*/
		uploadProgress: function(event, position, total, percentComplete) {
			var pVel = percentComplete + '%';
			
			$('.bar').width(pVel);
			$('.percent').html(pVel);
		},
		success: function(data) {
			$(".logo-container span").show();
			$(".progress").hide();
			$('.bar').width('0%');
			$('.percent').html('0%');
			if(data.code != 0){
				alert(data.description);
			}else{
				$(".footer-logo")[0].src = data.logo;
				alert("Update logo success");
			}
		}
	});
});