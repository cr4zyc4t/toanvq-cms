/*! 
 *   Core v0.1
 *   Author: vqt907
!*/
/*
    Custom script for Admin CP
----------------------------
*/
$("select#category_select").on("change",function(event) {
	$("select#subcategory_select").load("/admin/manage/subcategoryselect?ctg="+this.value);
});