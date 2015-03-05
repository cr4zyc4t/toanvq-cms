<?php
class Admin_Models_SourceTemplate extends Model_SourceTemplate{
	
	public function tableData()
	{
		$model_source = new Model_SourcePage;
		$site_typies = array("Desktop", "Mobile");
		
		$headers = array("&nbsp;","Title","Source","Site Type","Created Time","Control");
		$db = $this->getDefaultAdapter();
		$query = $db->select()->from(array("t" => $this->_name))
			  ->joinLeft( array("s"	=> $model_source->_name), "s.id = t.source_id",array("source" => "s.title", "source_icon" => "s.icon", "homepage"));
  		$result = $db->fetchAll($query);
		
		$rows = array();
		foreach ($result as $key => $value) {
			$action = "<a href=\"/admin/resource/add-template?id=$value[id]\" class=\"nextStep \" >Edit</a>";
			$action.= " | <a onClick=\"deleteRow(this,'template', $value[id])\" class=\"deleteRowBtn\" >Delete</a>";
			// $action.= " | <a href=\"/$value[module_name]/import/getcontent?id=$value[id]\" class=\"getContentBtn\" >GetNow</a>";
			
			$row = array();
			$row['noClick col-selector'] = "<input type=\"checkbox\" class=\"simple hidden inline-checkbox\" value=\"$value[id]\">";
			$row['title'] = $value['template'];
			$row['noClick source'] = "<a href=\"$value[homepage]\" target=\"_blank\">$value[source]</a>";
			$row['site_type'] = $site_typies[$value['mobile_page']];
			$row['created_time'] = $value['created_time'];
			$row['control'] = $action;
			
			$rows[] = $row;
		}
		return array('headers' => $headers, 'rows' => $rows);
	}
}