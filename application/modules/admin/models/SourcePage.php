<?php
class Admin_Models_SourcePage extends Model_SourcePage
{
	public function tableData()
	{
		$headers = array("&nbsp;", "Title", "Homepage","Created Time", "Control");
		
		$db = $this->getDefaultAdapter();
		$sql = $db->select()->from($this->_name);
	  	$result = $db->fetchAll($sql);
		
		$rows = array();
		foreach ($result as $key => $value) {
			$control = "<a class=\"nextStep\" href=\"/admin/resource/addpage?id=$value[id]\" >Edit</a>";
			$control.= " | <a onClick=\"deleteRow(this,'page',$value[id])\" class=\"deleteRowBtn\" >Delete</a>";
			
			$row = array();
			$row['noClick col-selector'] = "<input type=\"checkbox\" class=\"simple hidden inline-checkbox\" value=\"$value[id]\">";
			$row['title'] = "<img src=\"$value[icon]\" width=\"40\" alt=\"logo\">".$value['title'];
			$row['homepage'] = $value['homepage'];
			$row['created_time'] = $value['created_time'];
			$row['control'] = $control;

			$rows[] = $row;
		}
		return array('headers' => $headers, 'rows' => $rows);
	}
}