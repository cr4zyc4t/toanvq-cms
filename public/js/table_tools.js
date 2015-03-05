/*! 
 *   Core v0.1
 *   Author: vqt907
!*/
/*
    Custom script for DataTable
----------------------------
*/
$(".dataTable tbody, table.display tbody").on("click", "tr td:not(:last-child):not(.col-noClick)",function(){
	var action = $(this).closest("tr").children("td").children(".nextStep").attr("href");
	if (action) {
		window.location.href = action;
	};
});

function getSelectedRow(tableId){
    var selected = [];
    var oTT = TableTools.fnGetInstance( tableId );
    if (oTT) {
    	$(oTT.fnGetSelected()).children("td").children("input.inline-checkbox").each(function(key,value){
	    	selected.push(value.value);
	    });
	    // console.log(selected);
	    return selected;
    };
    return false;
}

function deleteSelectedRows(tableId, type){
    var selected = getSelectedRow(tableId);
	if(selected.length){
		if (confirm("Are you sure to delete these items?")) {
			$.post("/admin/manage/deleteitems",{'ids': selected, 'type': type }, function(response){
				if (response.success) {
					var table = $("table.dataTable").DataTable();
					$("table tbody tr.active").fadeOut("slow",function(){
						table.rows('.active').remove().draw( false );
					});
				} else{
					alert(response.desc);
				};
			},"json");
		};
	}else{
		alert("Select some item first!");
	}
}

function deleteRow(tag,type, id){
	if (confirm("Are you sure to delete these items?")) {
		var row = $(tag).closest("tr");
		var table = $(tag).closest("table");
		var rowPos = -1;
		if(isDataTable(table[0])){
			rowPos = table.dataTable().fnGetPosition(row[0]);
		}
		$.post("/admin/manage/deleteitems",{'id': id, 'type': type }, function(response){
			if (response.success) {
				if (rowPos != -1) {
					$(row).fadeOut("slow",function(){
						table.DataTable().rows(row).remove().draw( false );
					});
				} else{
					row.children('td').animate({ "padding-top": 0, "padding-bottom": 0 }).wrapInner('<div />').children().slideUp(function(){
						row.remove();
					});
				};
			} else{
				alert(response.desc);
			};
		},"json");
	};
}
